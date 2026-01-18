<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'passing_score',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'passing_score' => 'integer'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }
}
