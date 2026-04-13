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
        'google_id',
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

    /**
     * Get user's certificate
     */
    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    /**
     * Get sections created by this teacher
     */
    public function sections()
    {
        return $this->hasMany(Section::class, 'teacher_id');
    }

    /**
     * Get sections this student is enrolled in
     */
    public function enrolledSections()
    {
        return $this->belongsToMany(Section::class, 'section_students')->withTimestamps();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'ADMIN';
    }

    /**
     * Check if user is teacher
     */
    public function isTeacher(): bool
    {
        return $this->user_type === 'TEACHER';
    }

    /**
     * Check if user is a regular user
     */
    public function isUser(): bool
    {
        return $this->user_type === 'USER';
    }

    /**
     * Check if user has admin-level access (admin or teacher)
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->user_type, ['ADMIN', 'TEACHER']);
    }

    public function assessmentAttempts()
    {
        return $this->hasMany(AssessmentAttempt::class);
    }

    /**
     * Check if user has completed all lessons in a section
     */
    public function hasCompletedAllLessons($sectionId = null): bool
    {
        if ($sectionId) {
            $section = Section::find($sectionId);
            if (!$section) return false;
            $lessonIds = $section->lessons()->pluck('lessons.id');
            $totalLessons = $lessonIds->count();
            $completedLessons = $this->studentLessons()
                ->whereIn('lesson_id', $lessonIds)
                ->whereNotNull('completed_at')
                ->count();
        } else {
            $totalLessons = Lesson::where('is_active', true)->count();
            $completedLessons = $this->studentLessons()
                ->whereNotNull('completed_at')
                ->count();
        }
        
        return $totalLessons > 0 && $completedLessons >= $totalLessons;
    }

    /**
     * Check if user has completed pre-assessment for a section
     */
    public function hasCompletedPreAssessment($sectionId): bool
    {
        return $this->assessmentAttempts()
            ->where('section_id', $sectionId)
            ->where('type', 'pre')
            ->whereNotNull('completed_at')
            ->exists();
    }

    /**
     * Check if user has completed post-assessment for a section
     */
    public function hasCompletedPostAssessment($sectionId): bool
    {
        return $this->assessmentAttempts()
            ->where('section_id', $sectionId)
            ->where('type', 'post')
            ->whereNotNull('completed_at')
            ->exists();
    }

    /**
     * Check if user is eligible for certificate
     */
    public function isEligibleForCertificate(): bool
    {
        // Must complete all lessons
        if (!$this->hasCompletedAllLessons()) {
            return false;
        }

        // Must have completed at least one post-assessment
        $hasPostAssessment = $this->assessmentAttempts()
            ->where('type', 'post')
            ->whereNotNull('completed_at')
            ->exists();

        if (!$hasPostAssessment) {
            return false;
        }

        // Check if already has certificate
        if ($this->certificate) {
            return false;
        }

        return true;
    }

    /**
     * Issue certificate to user
     */
    public function issueCertificate()
    {
        if (!$this->isEligibleForCertificate()) {
            return null;
        }

        return Certificate::create([
            'user_id' => $this->id,
            'certificate_number' => Certificate::generateCertificateNumber(),
            'issued_at' => now(),
            'total_lessons_completed' => $this->completedLessonsCount(),
            'average_quiz_score' => $this->averageQuizScore(),
            'average_simulation_score' => $this->averageSimulationScore()
        ]);
    }
}

