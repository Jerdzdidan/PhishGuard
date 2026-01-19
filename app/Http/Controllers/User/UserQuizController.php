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

    public function checkAttempt($id)
    {
        $lessonId = Crypt::decryptString($id);
        $lesson = Lesson::findOrFail($lessonId);
        
        $quiz = Quiz::where('lesson_id', $lesson->id)
            ->where('is_active', true)
            ->firstOrFail();

        // Get the latest attempt for this user and quiz
        $attempt = UserQuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->latest()
            ->first();

        if (!$attempt) {
            return response()->json(['has_attempt' => false]);
        }

        // Get questions with correct answers
        $questions = Question::where('quiz_id', $quiz->id)
            ->with(['answers' => function($query) {
                $query->orderBy('option_letter');
            }])
            ->orderBy('order')
            ->get();

        // Reconstruct results from the attempt
        // Note: We need to store user answers in the database to reconstruct this
        // For now, we'll just mark questions as answered
        $results = [];
        
        // Since we don't have individual answer records, we'll need to add that
        // For now, return basic info
        foreach ($questions as $question) {
            $correctAnswer = $question->answers->firstWhere('is_correct', true);
            
            $results[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'user_answer' => null, // We'll need to store this
                'correct_answer' => $correctAnswer->option_letter,
                'is_correct' => false, // We'll need to calculate this
                'points' => $question->points,
                'earned_points' => 0, // We'll need to calculate this
            ];
        }

        return response()->json([
            'has_attempt' => true,
            'attempt' => $attempt,
            'results' => $results
        ]);
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
                    'user_answer' => $userAnswer,
                    'is_correct' => $isCorrect,
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

            return response()->json([
                'success' => true,
                'message' => 'Quiz submitted successfully',
                'attempt_id' => $attempt->id,
                'score' => $score,
                'passed' => $passed
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
