<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'section_code',
        'teacher_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($section) {
            if (!$section->section_code) {
                $section->section_code = strtoupper(Str::random(6));
            }
        });
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'section_students')->withTimestamps();
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'section_lessons')->withTimestamps();
    }

    public function assessmentAttempts()
    {
        return $this->hasMany(AssessmentAttempt::class);
    }

    public function studentCount()
    {
        return $this->students()->count();
    }
}
