<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    //
    public function index()
    {
        $lessons = Lesson::get();

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

        return view('admin.lessons.edit', compact('lesson'));
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
            'is_active' => 'nullable|boolean'
        ]);

        $lesson = Lesson::findOrFail($lessonId);

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

        $lesson->update($validated);

        return redirect()
            ->route('admin.lessons.edit', Crypt::encryptString($lesson->id))
            ->with('success', 'Lesson updated successfully!');
    }
}
