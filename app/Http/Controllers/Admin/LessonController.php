<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with('prerequisiteLesson')->paginate(6); 
        $total = Lesson::count();

        return view('admin.lessons.index', [
            'lessons' => $lessons,
            'total' => $total,
        ]);
    }

    public function edit($id)
    {
        $lessonId = Crypt::decryptString($id);
        $lesson = Lesson::findOrFail($lessonId);
        
        // Get all lessons except the current one for prerequisite selection
        $availableLessons = Lesson::where('id', '!=', $lessonId)
            ->orderBy('created_at')
            ->get();

        return view('admin.lessons.edit', compact('lesson', 'availableLessons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'required|integer',
            'difficulty' => 'required|string',
            'prerequisite_lesson_id' => 'nullable|exists:lessons,id'
        ]);

        Lesson::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'time' => $validated['time'],
            'difficulty' => $validated['difficulty'],
            'prerequisite_lesson_id' => $validated['prerequisite_lesson_id'] ?? null,
            'content' => 'Change content here',
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        // Decrypt the ID
        try {
            $lessonId = Crypt::decryptString($id);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Invalid lesson ID');
        }

        // Validate
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'difficulty' => 'required|in:EASY,MEDIUM,HARD',
            'time' => 'required|integer|min:1',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'is_active' => 'nullable|boolean',
            'prerequisite_lesson_id' => 'nullable|exists:lessons,id',
            'has_simulation' => 'nullable|boolean',
        ]);

        $lesson = Lesson::findOrFail($lessonId);

        // Prevent circular dependencies
        if ($validated['prerequisite_lesson_id']) {
            $prerequisite = Lesson::find($validated['prerequisite_lesson_id']);
            
            // Check if the prerequisite has this lesson as its prerequisite (direct circular)
            if ($prerequisite && $prerequisite->prerequisite_lesson_id == $lessonId) {
                return redirect()->back()->with('error', 'Cannot create circular prerequisite dependency.');
            }
            
            // Check for indirect circular dependencies
            $current = $prerequisite;
            while ($current && $current->prerequisite_lesson_id) {
                if ($current->prerequisite_lesson_id == $lessonId) {
                    return redirect()->back()->with('error', 'Cannot create circular prerequisite dependency.');
                }
                $current = Lesson::find($current->prerequisite_lesson_id);
            }
        }

        // Handle image upload using Storage
        if ($request->hasFile('image')) {
            try {
                // Delete old image if exists
                if ($lesson->image_path && Storage::disk('public')->exists($lesson->image_path)) {
                    Storage::disk('public')->delete($lesson->image_path);
                }

                // Store new image in storage/app/public/lessons
                $path = $request->file('image')->store('lessons', 'public');
                $validated['image_path'] = $path;
                
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Image upload failed: ' . $e->getMessage());
            }
        }

        // Handle checkbox - if not checked, it won't be in request
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['has_simulation'] = $request->has('has_simulation') ? 1 : 0;

        $lesson->update($validated);

        return redirect()
            ->route('admin.lessons.edit', Crypt::encryptString($lesson->id))
            ->with('success', 'Lesson updated successfully!');
    }

    public function destroy($id)
    {
        $decrypted = Crypt::decryptString($id);
        Lesson::findOrFail($decrypted)->delete();

        return redirect()
            ->route('admin.lessons.index')
            ->with('success', 'Lesson deleted successfully.');
    }

    /**
     * Show simulation management page for a lesson
     */
    public function simulation($id)
    {
        $lessonId = Crypt::decryptString($id);
        $lesson = Lesson::findOrFail($lessonId);

        // Get or create the simulation scenario for this lesson
        $scenario = \App\Models\Scenario::where('lesson_id', $lessonId)
            ->where('type', 'simulation')
            ->first();

        $items = collect();
        if ($scenario) {
            $items = \App\Models\ScenarioItem::where('scenario_id', $scenario->id)
                ->orderBy('order')
                ->get();
        }

        return view('admin.lessons.simulation', compact('lesson', 'scenario', 'items'));
    }

    /**
     * Toggle simulation status and auto-create scenario if needed
     */
    public function toggleSimulation(Request $request, $id)
    {
        $lessonId = Crypt::decryptString($id);
        $lesson = Lesson::findOrFail($lessonId);

        $enabled = $request->input('has_simulation', 0);
        $lesson->update(['has_simulation' => $enabled]);

        // Auto-create scenario record if enabling and none exists
        if ($enabled) {
            $existing = \App\Models\Scenario::where('lesson_id', $lessonId)
                ->where('type', 'simulation')
                ->first();

            if (!$existing) {
                \App\Models\Scenario::create([
                    'lesson_id' => $lessonId,
                    'title' => $lesson->title . ' Simulation',
                    'slug' => \Illuminate\Support\Str::slug($lesson->title) . '-sim-' . \Illuminate\Support\Str::random(6),
                    'type' => 'simulation',
                    'is_active' => true,
                    'order' => 1,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => $enabled ? 'Simulations enabled. You can now add scenarios.' : 'Simulations disabled.',
        ]);
    }
}
