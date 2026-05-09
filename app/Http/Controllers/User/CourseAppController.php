<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseAppController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $enrolledSections = $user->enrolledSections()->with('teacher', 'lessons')->get();

        // If user is not enrolled in any section → redirect to join page
        if ($enrolledSections->isEmpty()) {
            return redirect()->route('sections.join');
        }

        // Get the active section (first enrolled section or selected one)
        $activeSectionId = request('section');
        $activeSection = null;

        if ($activeSectionId) {
            $activeSection = $enrolledSections->firstWhere('id', $activeSectionId);
        }
        if (!$activeSection) {
            $activeSection = $enrolledSections->first();
        }

        // Check if pre-assessment is completed for this section
        $preAssessmentCompleted = $user->hasCompletedPreAssessment($activeSection->id);

        // Get lessons for this section
        $lessonIds = $activeSection->lessons()
            ->where('lessons.is_active', true)
            ->pluck('lessons.id');
        $lessons = Lesson::whereIn('id', $lessonIds)
            ->where('is_active', true)
            ->paginate(9);

        $total = $lessons->total();
        $completedLessonsCount = $user->completedLessonsCount($lessonIds);
        $sectionProgressPercentage = $total > 0
            ? (int) round(($completedLessonsCount / $total) * 100)
            : 0;

        // Check if all lessons are completed for post-assessment eligibility
        $allLessonsCompleted = $user->hasCompletedAllLessons($activeSection->id);
        $postAssessmentCompleted = $user->hasCompletedPostAssessment($activeSection->id);

        // Load the user's certificate if they have one
        $certificate = $user->certificate;

        return view('user.home.index', compact(
            'lessons',
            'total',
            'enrolledSections',
            'activeSection',
            'preAssessmentCompleted',
            'completedLessonsCount',
            'sectionProgressPercentage',
            'allLessonsCompleted',
            'postAssessmentCompleted',
            'certificate'
        ));
    }
}
