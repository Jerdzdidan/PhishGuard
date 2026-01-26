<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all student lesson progress records for this user
     */
    public function studentLessons()
    {
        return $this->hasMany(StudentLesson::class);
    }

    /**
     * Get all quiz attempts for this user
     */
    public function quizAttempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }

    /**
     * Get all simulation attempts for this user
     */
    public function simulationAttempts()
    {
        return $this->hasMany(SimulationAttempt::class);
    }

    /**
     * Get completed lessons count
     */
    public function completedLessonsCount()
    {
        return $this->studentLessons()->whereNotNull('completed_at')->count();
    }

    /**
     * Get average quiz score
     */
    public function averageQuizScore()
    {
        return $this->quizAttempts()
            ->whereNotNull('completed_at')
            ->avg('score');
    }

    /**
     * Get average simulation score
     */
    public function averageSimulationScore()
    {
        $attempts = $this->simulationAttempts()
            ->whereNotNull('completed_at')
            ->get();
        
        if ($attempts->isEmpty()) {
            return null;
        }
        
        return $attempts->avg(function($attempt) {
            return ($attempt->score / $attempt->total_scenarios) * 100;
        });
    }
}
