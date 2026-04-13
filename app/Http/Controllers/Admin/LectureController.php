<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LectureFile;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class LectureController extends Controller
{
    /**
     * Get lectures for a lesson
     */
    public function index($lessonId)
    {
        $decrypted = Crypt::decryptString($lessonId);
        $lesson = Lesson::findOrFail($decrypted);
        $lectures = $lesson->lectureFiles()->with('uploader')->latest()->get();

        return response()->json($lectures->map(function ($lecture) {
            return [
                'id' => Crypt::encryptString($lecture->id),
                'title' => $lecture->title,
                'file_type' => $lecture->file_type,
                'file_size' => $lecture->formatted_size,
                'file_icon' => $lecture->file_icon,
                'uploaded_by' => $lecture->uploader->first_name . ' ' . $lecture->uploader->last_name,
                'created_at' => $lecture->created_at->format('M d, Y h:i A'),
                'download_url' => route('admin.lectures.download', Crypt::encryptString($lecture->id)),
            ];
        }));
    }

    /**
     * Upload a lecture file
     */
    public function store(Request $request, $lessonId)
    {
        $decrypted = Crypt::decryptString($lessonId);
        $lesson = Lesson::findOrFail($decrypted);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:51200|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,gif,webp,mp4,avi,mov',
        ]);

        $file = $request->file('file');
        $path = $file->store('lectures/' . $decrypted, 'public');

        LectureFile::create([
            'lesson_id' => $decrypted,
            'title' => $validated['title'],
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Lecture file uploaded successfully.']);
    }

    /**
     * Download a lecture file
     */
    public function download($id)
    {
        $decrypted = Crypt::decryptString($id);
        $lecture = LectureFile::findOrFail($decrypted);

        $filePath = Storage::disk('public')->path($lecture->file_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $lecture->title . '.' . $lecture->file_type);
    }

    /**
     * Delete a lecture file
     */
    public function destroy($id)
    {
        $decrypted = Crypt::decryptString($id);
        $lecture = LectureFile::findOrFail($decrypted);

        // Delete the file from storage
        if (Storage::disk('public')->exists($lecture->file_path)) {
            Storage::disk('public')->delete($lecture->file_path);
        }

        $lecture->delete();

        return response()->json(['success' => true, 'message' => 'Lecture file deleted successfully.']);
    }
}
