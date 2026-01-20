<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\UserQuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class UserQuizController extends Controller
{
    public function show($id)
    {
        $lessonId = Crypt::decryptString($id);
        $lesson = Lesson::findOrFail($lessonId);
        
        // Check if lesson is unlocked
        if (!$lesson->isUnlocked()) {
            return redirect()->route('lessons.show', $id)
                ->with('error', 'This lesson is locked.');
        }
        
        $quiz = Quiz::where('lesson_id', $lesson->id)
            ->where('is_active', true)
            ->first();
        
        if (!$quiz) {
            return redirect()->route('lessons.show', $id)
                ->with('error', 'Quiz not available for this lesson.');
        }

        // Check if this is a retake (session flag set)
        if (session()->has('allow_retake')) {
            session()->forget('allow_retake');
            // Allow them to take quiz again
        } else {
            // Check if user has already attempted this quiz (get latest attempt only)
            $latestAttempt = UserQuizAttempt::where('user_id', Auth::id())
                ->where('quiz_id', $quiz->id)
                ->latest()
                ->first();

            if ($latestAttempt && $latestAttempt->answers_data !== null) {
                // Redirect to results page if valid attempt exists
                return redirect()->route('lessons.quiz.results', [
                    'id' => $id,
                    'attempt' => Crypt::encryptString($latestAttempt->id)
                ]);
            }
        }

        // Get all questions with answers
        $questions = Question::where('quiz_id', $quiz->id)
            ->with('answers')
            ->orderBy('order')
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('lessons.show', $id)
                ->with('error', 'No questions available for this quiz.');
        }

        return view('user.home.lesson.quiz', compact('lesson', 'quiz', 'questions'));
    }

    public function submit(Request $request, $id)
    {
        $lessonId = Crypt::decryptString($id);
        $lesson = Lesson::findOrFail($lessonId);
        
        $quiz = Quiz::where('lesson_id', $lesson->id)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string',
            'time_taken' => 'required|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Get all questions with correct answers
            $questions = Question::where('quiz_id', $quiz->id)
                ->with(['answers'])
                ->orderBy('order')
                ->get();

            $totalPoints = $questions->sum('points');
            $earnedPoints = 0;
            $results = [];

            foreach ($questions as $question) {
                $userAnswer = $validated['answers'][$question->id] ?? null;
                $correctAnswer = $question->answers->firstWhere('is_correct', true);
                
                $isCorrect = $userAnswer === $correctAnswer->option_letter;
                
                if ($isCorrect) {
                    $earnedPoints += $question->points;
                }

                $results[] = [
                    'question_id' => $question->id,
                    'question_text' => $question->question_text,
                    'user_answer' => $userAnswer,
                    'correct_answer' => $correctAnswer->option_letter,
                    'is_correct' => $isCorrect,
                    'points' => $question->points,
                    'earned_points' => $isCorrect ? $question->points : 0,
                    'answers' => $question->answers->map(function($answer) {
                        return [
                            'option_letter' => $answer->option_letter,
                            'answer_text' => $answer->answer_text,
                            'is_correct' => $answer->is_correct,
                            'explanation' => $answer->explanation
                        ];
                    })
                ];
            }

            $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
            $passed = $score >= $quiz->passing_score;

            // Create quiz attempt record
            $attempt = UserQuizAttempt::create([
                'user_id' => Auth::id(),
                'quiz_id' => $quiz->id,
                'started_at' => now()->subSeconds($validated['time_taken']),
                'completed_at' => now(),
                'completion_time' => $validated['time_taken'],
                'score' => $score,
                'passed' => $passed,
                'answers_data' => json_encode($results)
            ]);

            // Update student lesson progress
            $progress = $lesson->getStudentProgress();
            $progress->updateQuizResults($score, $passed);

            // If completed, unlock dependent lessons
            if ($progress->isCompleted()) {
                $lesson->unlockDependentLessons();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quiz submitted successfully',
                'redirect_url' => route('lessons.quiz.results', [
                    'id' => $id,
                    'attempt' => Crypt::encryptString($attempt->id)
                ])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    public function results($id, $attempt)
    {
        try {
            $lessonId = Crypt::decryptString($id);
            $attemptId = Crypt::decryptString($attempt);
            
            $lesson = Lesson::findOrFail($lessonId);
            $quizAttempt = UserQuizAttempt::where('id', $attemptId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $quiz = Quiz::findOrFail($quizAttempt->quiz_id);

            // Decode the stored results
            $results = json_decode($quizAttempt->answers_data, true);
            
            // Check if answers_data is null (old attempts before migration)
            if ($results === null) {
                return redirect()->route('lessons.quiz.retake', $id)
                    ->with('info', 'This quiz attempt is from an older version. Please retake the quiz.');
            }
            
            $totalQuestions = count($results);
            $correctAnswers = collect($results)->where('is_correct', true)->count();
            $totalPoints = collect($results)->sum('points');
            $earnedPoints = collect($results)->sum('earned_points');

            return view('user.home.lesson.quiz-results', compact(
                'lesson',
                'quiz',
                'quizAttempt',
                'results',
                'totalQuestions',
                'correctAnswers',
                'totalPoints',
                'earnedPoints'
            ));

        } catch (\Exception $e) {
            return redirect()->route('user.home')
                ->with('error', 'Quiz results not found.');
        }
    }

    public function retake($id)
    {
        try {
            $lessonId = Crypt::decryptString($id);
            $lesson = Lesson::findOrFail($lessonId);
            
            $quiz = Quiz::where('lesson_id', $lesson->id)
                ->where('is_active', true)
                ->firstOrFail();

            // Set session flag to allow retake (bypass latest attempt check)
            session()->put('allow_retake', true);

            return redirect()->route('lessons.quiz.show', $id);

        } catch (\Exception $e) {
            return redirect()->route('user.home')
                ->with('error', 'Unable to retake quiz.');
        }
    }
}
