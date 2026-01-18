<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class UserLessonController extends Controller
{
    //
    public function index()
    {
        $lessons = Lesson::where('is_active', true)->paginate(6); 
        $total = Lesson::where('is_active', true)->count();

        return view('user.home.index', [
            'lessons' => $lessons,
            'total' => $total,
        ]);
    }

    public function show($id)
    {
        $lessonId = Crypt::decryptString($id);

        $lesson = Lesson::with('quiz')->findOrFail($lessonId);

        return view('user.home.lesson.show', compact('lesson'));
    }
}
