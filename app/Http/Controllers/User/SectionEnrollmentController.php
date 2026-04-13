<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionEnrollmentController extends Controller
{
    /**
     * Show the join section page
     */
    public function showJoin()
    {
        $user = Auth::user();
        $enrolledSections = $user->enrolledSections()->with('teacher', 'lessons')->get();
        
        return view('user.sections.join', compact('enrolledSections'));
    }

    /**
     * Join a section using section code
     */
    public function join(Request $request)
    {
        $validated = $request->validate([
            'section_code' => 'required|string|max:10',
        ]);

        $section = Section::where('section_code', strtoupper($validated['section_code']))
            ->where('is_active', true)
            ->first();

        if (!$section) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid section code. Please check and try again.'
            ], 404);
        }

        $user = Auth::user();

        // Check if already enrolled
        if ($section->students()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already enrolled in this section.'
            ], 400);
        }

        // Enroll student
        $section->students()->attach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined section: ' . $section->name
        ]);
    }

    /**
     * Leave a section
     */
    public function leave($sectionId)
    {
        $user = Auth::user();
        $section = Section::findOrFail($sectionId);

        $section->students()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'You have left the section: ' . $section->name
        ]);
    }
}
