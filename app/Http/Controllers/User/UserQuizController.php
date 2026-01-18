<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class UserQuizController extends Controller
{
    //
    public function show($id)
    {
        $lessonId = Crypt::decryptString($id);
        $lesson = Lesson::findOrFail($lessonId);
        
        // Get or create quiz for this lesson
        $quiz = Quiz::where('lesson_id', $lesson->id)->first();
        
        // Get all questions with answers
        $questions = $quiz 
            ? Question::where('quiz_id', $quiz->id)
                ->with('answers')
                ->orderBy('order')
                ->get()
            : collect();

        return view('admin.lessons.quiz', compact('lesson', 'quiz', 'questions'));
    }
}
