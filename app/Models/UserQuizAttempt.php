<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuizAttempt extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'started_at',
        'completed_at',
        'completion_time',
        'score',
        'passed',
        'answers_data'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'completion_time' => 'integer',
        'score' => 'integer',
        'passed' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
