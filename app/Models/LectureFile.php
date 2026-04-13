<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get human-readable file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Get file icon class based on file type
     */
    public function getFileIconAttribute(): string
    {
        return match($this->file_type) {
            'pdf' => 'bx bxs-file-pdf text-danger',
            'doc', 'docx' => 'bx bxs-file-doc text-primary',
            'ppt', 'pptx' => 'bx bxs-file text-warning',
            'xls', 'xlsx' => 'bx bxs-file text-success',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'bx bxs-image text-info',
            'mp4', 'avi', 'mov' => 'bx bxs-video text-danger',
            default => 'bx bxs-file text-secondary',
        };
    }
}
