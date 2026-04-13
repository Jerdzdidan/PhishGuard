<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'section_id',
        'type',
        'score',
        'total_questions',
        'answers_data',
        'started_at',
        'completed_at',
        'completion_time',
    ];

    protected $casts = [
        'answers_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function getPercentageAttribute(): float
    {
        return $this->total_questions > 0
            ? round(($this->score / $this->total_questions) * 100, 2)
            : 0;
    }

    public function scopePreAssessment($query)
    {
        return $query->where('type', 'pre');
    }

    public function scopePostAssessment($query)
    {
        return $query->where('type', 'post');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }
}
