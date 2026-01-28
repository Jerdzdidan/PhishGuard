<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'certificate_number',
        'issued_at',
        'total_lessons_completed',
        'average_quiz_score',
        'average_simulation_score'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'total_lessons_completed' => 'integer',
        'average_quiz_score' => 'decimal:2',
        'average_simulation_score' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique certificate number
     */
    public static function generateCertificateNumber(): string
    {
        do {
            $number = 'CERT-' . date('Y') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (self::where('certificate_number', $number)->exists());

        return $number;
    }
}
