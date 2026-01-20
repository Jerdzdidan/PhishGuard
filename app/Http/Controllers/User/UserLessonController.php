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
        $lessons = Lesson::where('is_active', true)
            ->with(['quiz', 'progress']) // Eager load relationships
            ->paginate(6); 
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

        // Check if lesson is unlocked
        if (!$lesson->isUnlocked()) {
            return redirect()->route('user.home')
                ->with('error', 'This lesson is locked. Complete the prerequisite lesson first.');
        }

        // Mark content as viewed
        $progress = $lesson->getStudentProgress();
        if (!$progress->content_viewed) {
            $progress->markContentViewed();
            
            // If lesson has no quiz or quiz is inactive, it's now complete
            // so unlock dependent lessons
            if ($progress->isCompleted()) {
                $lesson->unlockDependentLessons();
            }
        }

        return view('user.home.lesson.show', compact('lesson'));
    }
}
