<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLesson extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'is_unlocked',
        'content_viewed',
        'quiz_passed',
        'simulations_completed',
        'simulation_progress',
        'best_score',
        'completed_at'
    ];

    protected $casts = [
        'is_unlocked' => 'boolean',
        'content_viewed' => 'boolean',
        'quiz_passed' => 'boolean',
        'simulations_completed' => 'boolean',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Check if lesson is completed
     */
    public function isCompleted(): bool
    {
        $lesson = $this->lesson;
        
        // Must view content
        if (!$this->content_viewed) {
            return false;
        }

        // If lesson has quiz, must pass it
        if ($lesson->quiz && $lesson->quiz->is_active) {
            if (!$this->quiz_passed) {
                return false;
            }
        }

        // If lesson has simulations, must complete them
        if ($lesson->has_simulation) {
            if (!$this->simulations_completed) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mark content as viewed
     */
    public function markContentViewed(): void
    {
        $this->content_viewed = true;
        
        // Check if lesson is now completed
        if ($this->isCompleted() && !$this->completed_at) {
            $this->completed_at = now();
        }
        
        $this->save();
    }

    /**
     * Update quiz results
     */
    public function updateQuizResults(int $score, bool $passed): void
    {
        // Update best score if this is better
        if ($this->best_score === null || $score > $this->best_score) {
            $this->best_score = $score;
        }

        // Mark quiz as passed if they passed
        if ($passed && !$this->quiz_passed) {
            $this->quiz_passed = true;
        }

        // Check if lesson is now completed
        if ($this->isCompleted() && !$this->completed_at) {
            $this->completed_at = now();
        }

        $this->save();
    }
}
