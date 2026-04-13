<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScenarioItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'scenario_id',
        'title',
        'description',
        'image_path',
        'content',
        'correct_action',
        'options',
        'hints',
        'metadata',
        'order',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'hints' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class);
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
