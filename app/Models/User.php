<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

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
    public function completedLessonsCount($lessonIds = null)
    {
        $query = $this->studentLessons()->whereNotNull('completed_at');

        $lessonIds = $this->normalizeLessonIds($lessonIds);
        if ($lessonIds !== null) {
            $query->whereIn('lesson_id', $lessonIds);
        }

        return $query->count();
    }

    /**
     * Get average quiz score
     */
    public function averageQuizScore($lessonIds = null)
    {
        $query = $this->quizAttempts()->whereNotNull('completed_at');

        $lessonIds = $this->normalizeLessonIds($lessonIds);
        if ($lessonIds !== null) {
            $query->whereHas('quiz', function ($quizQuery) use ($lessonIds) {
                $quizQuery->whereIn('lesson_id', $lessonIds);
            });
        }

        return $query->avg('score');
    }

    /**
     * Get average simulation score
     */
    public function averageSimulationScore($lessonIds = null)
    {
        $query = $this->simulationAttempts()->whereNotNull('completed_at');

        $lessonIds = $this->normalizeLessonIds($lessonIds);
        if ($lessonIds !== null) {
            $query->whereIn('lesson_id', $lessonIds);
        }

        $attempts = $query->get();
        
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
     * Get the unique lesson IDs assigned to this student across enrolled sections.
     */
    public function getAssignedLessonIds(?array $sectionIds = null): Collection
    {
        $sections = $this->enrolledSections()->with('lessons');

        if ($sectionIds !== null) {
            $sections->whereIn('sections.id', $sectionIds);
        }

        return $sections->get()
            ->flatMap(fn($section) => $section->lessons->where('is_active', true)->pluck('id'))
            ->unique()
            ->values();
    }

    /**
     * Check if user has completed all lessons in a section
     */
    public function hasCompletedAllLessons($sectionId = null): bool
    {
        if ($sectionId) {
            $section = $this->enrolledSections()
                ->with('lessons')
                ->find($sectionId);

            if (!$section) {
                return false;
            }

            $lessonIds = $section->lessons->where('is_active', true)->pluck('id')->unique();
        } else {
            $lessonIds = $this->getAssignedLessonIds();
        }

        $totalLessons = $lessonIds->count();

        if ($totalLessons === 0) {
            return false;
        }

        return $this->completedLessonsCount($lessonIds) >= $totalLessons;
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
        if ($this->certificate) {
            return false;
        }

        return $this->getCertificateEligibleSection() !== null;
    }

    /**
     * Issue certificate to user
     */
    public function issueCertificate($sectionId = null)
    {
        if ($this->certificate) {
            return $this->certificate;
        }

        $section = $sectionId
            ? $this->enrolledSections()->with('lessons')->find($sectionId)
            : $this->getCertificateEligibleSection();

        if (!$section || !$this->isEligibleForCertificateForSection($section->id)) {
            return null;
        }

        $lessonIds = $section->lessons->where('is_active', true)->pluck('id')->unique();

        return Certificate::create([
            'user_id' => $this->id,
            'certificate_number' => Certificate::generateCertificateNumber(),
            'issued_at' => now(),
            'total_lessons_completed' => $this->completedLessonsCount($lessonIds),
            'average_quiz_score' => $this->averageQuizScore($lessonIds),
            'average_simulation_score' => $this->averageSimulationScore($lessonIds)
        ]);
    }

    /**
     * Get the first section where the student finished every assigned lesson
     * but still needs to take the post-assessment.
     */
    public function getPendingPostAssessmentSection(): ?Section
    {
        return $this->enrolledSections()
            ->with('lessons', 'teacher')
            ->get()
            ->first(function ($section) {
                return $section->lessons->where('is_active', true)->isNotEmpty()
                    && $this->hasCompletedPreAssessment($section->id)
                    && $this->hasCompletedAllLessons($section->id)
                    && !$this->hasCompletedPostAssessment($section->id);
            });
    }

    /**
     * Get the first section that qualifies the student for a certificate.
     */
    public function getCertificateEligibleSection(): ?Section
    {
        return $this->enrolledSections()
            ->with('lessons', 'teacher')
            ->get()
            ->first(function ($section) {
                return $section->lessons->where('is_active', true)->isNotEmpty()
                    && $this->isEligibleForCertificateForSection($section->id);
            });
    }

    private function isEligibleForCertificateForSection(int $sectionId): bool
    {
        return $this->hasCompletedAllLessons($sectionId)
            && $this->hasCompletedPostAssessment($sectionId);
    }

    private function normalizeLessonIds($lessonIds): ?array
    {
        if ($lessonIds === null) {
            return null;
        }

        if ($lessonIds instanceof Collection) {
            return $lessonIds->values()->all();
        }

        return collect($lessonIds)->unique()->values()->all();
    }
}
