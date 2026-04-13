<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\AssessmentAttempt;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AssessmentController extends Controller
{
    /**
     * Show the pre-assessment page
     */
    public function showPreAssessment($sectionId)
    {
        $sectionId = Crypt::decryptString($sectionId);
        $section = Section::findOrFail($sectionId);
        $user = Auth::user();

        // Check if already completed
        if ($user->hasCompletedPreAssessment($section->id)) {
            return redirect()->route('home')->with('info', 'You have already completed the pre-assessment for this section.');
        }

        // Get all questions from all lessons in this section
        $lessonIds = $section->lessons()->pluck('lessons.id');
        $questions = Question::whereHas('quiz', function ($q) use ($lessonIds) {
            $q->whereIn('lesson_id', $lessonIds)->where('is_active', true);
        })->with('answers')->inRandomOrder()->get();

        if ($questions->isEmpty()) {
            return redirect()->route('home')->with('error', 'No assessment questions available yet.');
        }

        return view('user.assessments.take', [
            'section' => $section,
            'questions' => $questions,
            'type' => 'pre',
            'title' => 'Pre-Assessment Test',
            'description' => 'This test evaluates your current knowledge before starting the lessons. Answer all questions to the best of your ability.'
        ]);
    }

    /**
     * Show the post-assessment page
     */
    public function showPostAssessment($sectionId)
    {
        $sectionId = Crypt::decryptString($sectionId);
        $section = Section::findOrFail($sectionId);
        $user = Auth::user();

        // Must have completed all lessons first
        if (!$user->hasCompletedAllLessons($section->id)) {
            return redirect()->route('home')->with('error', 'You must complete all lessons before taking the post-assessment.');
        }

        // Check if already completed
        if ($user->hasCompletedPostAssessment($section->id)) {
            return redirect()->route('home')->with('info', 'You have already completed the post-assessment for this section.');
        }

        // Get all questions from all lessons in this section
        $lessonIds = $section->lessons()->pluck('lessons.id');
        $questions = Question::whereHas('quiz', function ($q) use ($lessonIds) {
            $q->whereIn('lesson_id', $lessonIds)->where('is_active', true);
        })->with('answers')->inRandomOrder()->get();

        return view('user.assessments.take', [
            'section' => $section,
            'questions' => $questions,
            'type' => 'post',
            'title' => 'Post-Assessment Test',
            'description' => 'This test evaluates what you have learned after completing all lessons. Your results will be compared with the pre-assessment.'
        ]);
    }

    /**
     * Submit assessment answers
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required',
            'type' => 'required|in:pre,post',
            'answers' => 'required|array',
            'started_at' => 'required',
        ]);

        $sectionId = Crypt::decryptString($validated['section_id']);
        $section = Section::findOrFail($sectionId);
        $user = Auth::user();

        $score = 0;
        $totalQuestions = count($validated['answers']);
        $answersData = [];

        foreach ($validated['answers'] as $questionId => $answerId) {
            $question = Question::with('answers')->find($questionId);
            if (!$question) continue;

            $selectedAnswer = Answer::find($answerId);
            $correctAnswer = $question->answers->where('is_correct', true)->first();

            $isCorrect = $selectedAnswer && $selectedAnswer->is_correct;
            if ($isCorrect) $score++;

            $answersData[] = [
                'question_id' => $questionId,
                'question_text' => $question->question_text,
                'selected_answer_id' => $answerId,
                'selected_answer_text' => $selectedAnswer ? $selectedAnswer->answer_text : null,
                'correct_answer_text' => $correctAnswer ? $correctAnswer->answer_text : null,
                'is_correct' => $isCorrect,
            ];
        }

        $startedAt = \Carbon\Carbon::parse($validated['started_at']);
        $completionTime = now()->diffInSeconds($startedAt);

        $attempt = AssessmentAttempt::create([
            'user_id' => $user->id,
            'section_id' => $section->id,
            'type' => $validated['type'],
            'score' => $score,
            'total_questions' => $totalQuestions,
            'answers_data' => $answersData,
            'started_at' => $startedAt,
            'completed_at' => now(),
            'completion_time' => $completionTime,
        ]);

        return redirect()->route('assessment.results', Crypt::encryptString($attempt->id));
    }

    /**
     * Show assessment results
     */
    public function results($attemptId)
    {
        $attemptId = Crypt::decryptString($attemptId);
        $attempt = AssessmentAttempt::with('section', 'user')->findOrFail($attemptId);

        // Ensure user owns this attempt
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        // Get pre-assessment for comparison if this is post
        $preAttempt = null;
        if ($attempt->type === 'post') {
            $preAttempt = AssessmentAttempt::where('user_id', Auth::id())
                ->where('section_id', $attempt->section_id)
                ->where('type', 'pre')
                ->whereNotNull('completed_at')
                ->first();
        }

        return view('user.assessments.results', compact('attempt', 'preAttempt'));
    }
}
