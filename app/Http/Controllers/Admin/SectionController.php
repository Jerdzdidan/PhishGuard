<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class SectionController extends Controller
{
    public function index()
    {
        return view('admin.sections.index');
    }

    public function getData(Request $request)
    {
        $user = Auth::user();

        $sections = Section::with('teacher')
            ->withCount('students');

        // Teachers can only see their own sections
        if ($user->isTeacher()) {
            $sections->where('teacher_id', $user->id);
        }

        return DataTables::of($sections)
            ->editColumn('id', function ($row) {
                return Crypt::encryptString($row->id);
            })
            ->addColumn('teacher_name', function ($row) {
                return $row->teacher->first_name . ' ' . $row->teacher->last_name;
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Section::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'teacher_id' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Section created successfully.']);
    }

    public function edit($id)
    {
        $decrypted = Crypt::decryptString($id);
        $section = Section::findOrFail($decrypted);

        // Teachers can only edit their own sections
        if (Auth::user()->isTeacher() && $section->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => Crypt::encryptString($section->id),
            'name' => $section->name,
            'description' => $section->description,
        ]);
    }

    public function update(Request $request, $id)
    {
        $decrypted = Crypt::decryptString($id);
        $section = Section::findOrFail($decrypted);

        if (Auth::user()->isTeacher() && $section->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $section->update($validated);

        return response()->json(['success' => true, 'message' => 'Section updated successfully.']);
    }

    public function destroy($id)
    {
        $decrypted = Crypt::decryptString($id);
        $section = Section::findOrFail($decrypted);

        if (Auth::user()->isTeacher() && $section->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted successfully.']);
    }

    /**
     * Show students management page for a section
     */
    public function students($id)
    {
        $decrypted = Crypt::decryptString($id);
        $section = Section::with('students')->findOrFail($decrypted);

        if (Auth::user()->isTeacher() && $section->teacher_id !== Auth::id()) {
            return redirect()->route('admin.sections.index')->with('error', 'Unauthorized.');
        }

        return view('admin.sections.students', compact('section'));
    }

    /**
     * Get students data for DataTable
     */
    public function getStudentsData(Request $request, $id)
    {
        $decrypted = Crypt::decryptString($id);
        $section = Section::findOrFail($decrypted);

        $students = $section->students()->select(['users.id', 'first_name', 'last_name', 'email']);

        return DataTables::of($students)
            ->editColumn('id', function ($row) {
                return Crypt::encryptString($row->id);
            })
            ->make(true);
    }

    /**
     * Add student to section
     */
    public function addStudent(Request $request, $id)
    {
        $decrypted = Crypt::decryptString($id);
        $section = Section::findOrFail($decrypted);

        if (Auth::user()->isTeacher() && $section->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $student = User::where('email', $validated['email'])
            ->where('user_type', 'USER')
            ->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found. Only users with USER type can be added.'], 404);
        }

        if ($section->students()->where('user_id', $student->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Student is already in this section.'], 422);
        }

        $section->students()->attach($student->id);

        return response()->json(['success' => true, 'message' => 'Student added successfully.']);
    }

    /**
     * Remove student from section
     */
    public function removeStudent($id, $studentId)
    {
        $decrypted = Crypt::decryptString($id);
        $section = Section::findOrFail($decrypted);

        if (Auth::user()->isTeacher() && $section->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $studentDecrypted = Crypt::decryptString($studentId);
        $section->students()->detach($studentDecrypted);

        return response()->json(['success' => true, 'message' => 'Student removed from section.']);
    }

    /**
     * Get available students (not yet in the section)
     */
    public function availableStudents(Request $request, $id)
    {
        $decrypted = Crypt::decryptString($id);
        $section = Section::findOrFail($decrypted);

        $existingIds = $section->students()->pluck('users.id')->toArray();

        $students = User::where('user_type', 'USER')
            ->where('status', true)
            ->whereNotIn('id', $existingIds)
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('first_name', 'like', "%{$request->search}%")
                      ->orWhere('last_name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%");
                });
            })
            ->limit(20)
            ->get(['id', 'first_name', 'last_name', 'email']);

        return response()->json($students);
    }

    /**
     * Show lessons management page for a section
     */
    public function manageLessons($id)
    {
        $decrypted = Crypt::decryptString($id);
        $section = Section::with('lessons')->findOrFail($decrypted);

        if (Auth::user()->isTeacher() && $section->teacher_id !== Auth::id()) {
            return redirect()->route('admin.sections.index')->with('error', 'Unauthorized.');
        }

        return view('admin.sections.lessons', compact('section'));
    }

    /**
     * Add lesson to section
     */
    public function addLesson(Request $request, $id)
    {
        $decrypted = Crypt::decryptString($id);
        $section = Section::findOrFail($decrypted);

        if (Auth::user()->isTeacher() && $section->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
        ]);

        if ($section->lessons()->where('lesson_id', $validated['lesson_id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Lesson is already in this section.'], 422);
        }

        $section->lessons()->attach($validated['lesson_id']);

        return response()->json(['success' => true, 'message' => 'Lesson added to section.']);
    }

    /**
     * Remove lesson from section
     */
    public function removeLesson($id, $lessonId)
    {
        $decrypted = Crypt::decryptString($id);
        $section = Section::findOrFail($decrypted);

        if (Auth::user()->isTeacher() && $section->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $section->lessons()->detach($lessonId);

        return response()->json(['success' => true, 'message' => 'Lesson removed from section.']);
    }

    /**
     * Get available lessons (not yet in the section)
     */
    public function availableLessons(Request $request, $id)
    {
        $decrypted = Crypt::decryptString($id);
        $section = Section::findOrFail($decrypted);

        $existingIds = $section->lessons()->pluck('lessons.id')->toArray();

        $lessons = \App\Models\Lesson::where('is_active', true)
            ->whereNotIn('id', $existingIds)
            ->when($request->search, function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->search}%");
            })
            ->limit(20)
            ->get(['id', 'title', 'difficulty']);

        return response()->json($lessons);
    }
}
