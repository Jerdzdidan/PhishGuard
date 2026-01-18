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
    //
    public function show($id)
    {
        $lessonId = Crypt::decryptString($id);
        $lesson = Lesson::findOrFail($lessonId);
        
        $quiz = Quiz::where('lesson_id', $lesson->id)
            ->where('is_active', true)
            ->first();
        
        if (!$quiz) {
            return redirect()->route('lessons.show', $id)
                ->with('error', 'Quiz not available for this lesson.');
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
                ->with(['answers' => function($query) {
                    $query->where('is_correct', true);
                }])
                ->get();

            $totalPoints = $questions->sum('points');
            $earnedPoints = 0;
            $results = [];

            foreach ($questions as $question) {
                $userAnswer = $validated['answers'][$question->id] ?? null;
                $correctAnswer = $question->answers->first();
                
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
                    'explanation' => $question->answers->firstWhere('option_letter', $userAnswer)?->explanation ?? ''
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
                'passed' => $passed
            ]);

            DB::commit();

            return redirect()->route('lessons.quiz.results', [
                'id' => $id,
                'attempt' => Crypt::encryptString($attempt->id)
            ])->with([
                'results' => $results,
                'score' => $score,
                'passed' => $passed,
                'total_points' => $totalPoints,
                'earned_points' => $earnedPoints
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to submit quiz: ' . $e->getMessage())
                ->withInput();
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

            // Get results from session
            $results = session('results', []);
            $score = session('score', $quizAttempt->score);
            $passed = session('passed', $quizAttempt->passed);
            $totalPoints = session('total_points', 0);
            $earnedPoints = session('earned_points', 0);

            return view('user.home.lesson.quiz-results', compact(
                'lesson',
                'quiz',
                'quizAttempt',
                'results',
                'score',
                'passed',
                'totalPoints',
                'earnedPoints'
            ));

        } catch (\Exception $e) {
            return redirect()->route('user.home')
                ->with('error', 'Quiz results not found.');
        }
    }
}
