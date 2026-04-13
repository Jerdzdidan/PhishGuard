<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scenario extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'slug',
        'title',
        'description',
        'type',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function items()
    {
        return $this->hasMany(ScenarioItem::class)->orderBy('order');
    }

    public function activeItems()
    {
        return $this->items()->where('is_active', true);
    }

    /**
     * Get total scenarios count (for backward-compatibility with existing SimulationController)
     */
    public function getTotalScenariosAttribute(): int
    {
        return $this->activeItems()->count();
    }

    /**
     * Scope for active scenarios
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
