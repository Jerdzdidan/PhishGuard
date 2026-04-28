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
     * BOTH quiz AND simulation must be completed if they exist
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
     * Get the lesson completion percentage based on required steps.
     */
    public function completionPercentage(): int
    {
        $lesson = $this->lesson;
        $requirements = 1; // Lesson content is always required
        $completed = $this->content_viewed ? 1 : 0;

        if ($lesson->quiz && $lesson->quiz->is_active) {
            $requirements++;
            if ($this->quiz_passed) {
                $completed++;
            }
        }

        if ($lesson->has_simulation) {
            $requirements++;
            if ($this->simulations_completed) {
                $completed++;
            }
        }

        return $requirements > 0
            ? (int) round(($completed / $requirements) * 100)
            : 0;
    }

    /**
     * Mark content as viewed
     */
    public function markContentViewed(): void
    {
        $this->content_viewed = true;
        $this->syncCompletionTimestamp();
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

        $this->syncCompletionTimestamp();
        $this->save();
    }

    /**
     * Update simulation progress for the lesson.
     */
    public function updateSimulationProgress(int $completedSimulations, int $totalSimulations): void
    {
        $this->simulation_progress = max(0, $completedSimulations);
        $this->simulations_completed = $totalSimulations > 0
            ? $completedSimulations >= $totalSimulations
            : false;

        $this->syncCompletionTimestamp();
        $this->save();
    }

    private function syncCompletionTimestamp(): void
    {
        if ($this->isCompleted() && !$this->completed_at) {
            $this->completed_at = now();
        }
    }
}
