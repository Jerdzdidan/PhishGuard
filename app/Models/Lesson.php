<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    //
    use HasFactory;

     protected $fillable = [
        'title',
        'image_path',
        'difficulty',
        'description',
        'time',
        'content',
        'is_active'
    ];

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }
}
