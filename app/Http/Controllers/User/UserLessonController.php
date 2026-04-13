<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LectureFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

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

        $lesson = Lesson::with(['quiz', 'lectureFiles'])->findOrFail($lessonId);

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

    public function downloadLecture($id)
    {
        $decrypted = Crypt::decryptString($id);
        $lecture = LectureFile::findOrFail($decrypted);

        $filePath = Storage::disk('public')->path($lecture->file_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $lecture->title . '.' . $lecture->file_type);
    }
}
