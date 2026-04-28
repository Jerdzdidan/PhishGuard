<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAttempt;
use App\Models\Lesson;
use App\Models\SimulationAttempt;
use App\Models\StudentLesson;
use App\Models\User;
use App\Models\UserQuizAttempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class UserProgressController extends Controller
{
    private array $progressContextCache = [];
    private array $assessmentStatsCache = [];

     /**
     * User Progress List
     */
    public function index()
    {
        return view('admin.user-progress.index');
    }

    /**
     * Get User Progress Data for DataTable
     */
    public function getData(Request $request)
    {
        $sections = $this->getScopedSectionsQuery()
            ->with([
                'lessons' => function ($query) {
                    $query->where('is_active', true);
                },
                'students' => function ($query) {
                    $query->where('user_type', 'USER')
                        ->with([
                            'studentLessons',
                            'assessmentAttempts' => function ($assessmentQuery) {
                                $assessmentQuery->completed();
                            },
                            'quizAttempts' => function ($quizQuery) {
                                $quizQuery->whereNotNull('completed_at')->with('quiz');
                            },
                            'simulationAttempts' => function ($simulationQuery) {
                                $simulationQuery->whereNotNull('completed_at');
                            },
                        ]);
                },
            ])
            ->get();

        $rows = $sections->flatMap(function ($section) {
            $lessonIds = $section->lessons->pluck('id')->unique()->values()->all();
            $totalLessons = count($lessonIds);

            return $section->students->map(function ($student) use ($section, $lessonIds, $totalLessons) {
                $completedLessons = $this->getCompletedLessonsForSection($student, $lessonIds);
                $progressPercentage = $totalLessons > 0
                    ? (int) round(($completedLessons / $totalLessons) * 100)
                    : 0;

                $preAttempt = $this->getLatestSectionAssessment($student, $section->id, 'pre');
                $postAttempt = $this->getLatestSectionAssessment($student, $section->id, 'post');
                $improvement = ($preAttempt && $postAttempt)
                    ? round($postAttempt->percentage - $preAttempt->percentage, 2)
                    : null;

                return [
                    'id' => Crypt::encryptString($student->id),
                    'student_name' => trim($student->first_name . ' ' . $student->last_name),
                    'email' => $student->email,
                    'section_name' => $section->name,
                    'lessons_completed' => $completedLessons,
                    'total_lessons' => $totalLessons,
                    'progress_percentage' => $progressPercentage,
                    'quiz_avg' => $this->formatAverage($this->getLoadedQuizAverage($student, $lessonIds)),
                    'simulation_avg' => $this->formatAverage($this->getLoadedSimulationAverage($student, $lessonIds)),
                    'pre_assessment_score' => $preAttempt ? round($preAttempt->percentage, 2) . '%' : 'N/A',
                    'post_assessment_score' => $postAttempt ? round($postAttempt->percentage, 2) . '%' : 'N/A',
                    'assessment_improvement' => $this->formatImprovement($improvement),
                    'created_at' => $student->created_at->format('Y-m-d H:i:s'),
                    'actions' => '<a href="' . route('admin.user-progress.show', Crypt::encryptString($student->id)) . '" class="btn btn-sm btn-primary">
                        <i class="ri-eye-line me-1"></i> View Progress
                    </a>',
                ];
            });
        })->values();

        return DataTables::of($rows)
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Show Individual User Progress
     */
    public function show($id)
    {
        $userId = Crypt::decryptString($id);
        $user = $this->getScopedStudentsQuery()
            ->with(['studentLessons' => function($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->with([
                'enrolledSections' => function ($query) {
                    $this->applySectionScope($query);
                    $query->with('lessons');
                },
            ])
            ->findOrFail($userId);

        $context = $this->getProgressContext($user);
        $lessonIds = collect($context['lesson_ids']);

        $lessonsQuery = Lesson::query();
        if ($lessonIds->isNotEmpty()) {
            $lessonsQuery->whereIn('id', $lessonIds)->where('is_active', true);
        } else {
            $lessonsQuery->whereRaw('1 = 0');
        }

        $lessons = $lessonsQuery
            ->with(['studentLessons' => function($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->get()
            ->map(function($lesson) use ($userId) {
                $progress = $lesson->studentLessons->first();
                
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'difficulty' => $lesson->difficulty,
                    'is_completed' => $progress ? $progress->completed_at !== null : false,
                    'content_viewed' => $progress ? $progress->content_viewed : false,
                    'quiz_passed' => $progress ? $progress->quiz_passed : false,
                    'simulations_completed' => $progress ? $progress->simulations_completed : false,
                    'completed_at' => $progress ? $progress->completed_at : null,
                ];
            });

        // Get quiz attempts
        $quizAttempts = UserQuizAttempt::where('user_id', $userId)
            ->with('quiz.lesson')
            ->whereNotNull('completed_at')
            ->when($lessonIds->isNotEmpty(), function ($query) use ($lessonIds) {
                $query->whereHas('quiz', function ($quizQuery) use ($lessonIds) {
                    $quizQuery->whereIn('lesson_id', $lessonIds);
                });
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->orderBy('completed_at', 'desc')
            ->get();

        // Get simulation attempts
        $simulationAttempts = SimulationAttempt::where('user_id', $userId)
            ->with('lesson')
            ->whereNotNull('completed_at')
            ->when($lessonIds->isNotEmpty(), function ($query) use ($lessonIds) {
                $query->whereIn('lesson_id', $lessonIds);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->orderBy('completed_at', 'desc')
            ->get();

        // Calculate time spent per lesson
        $timeSpent = StudentLesson::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->when($lessonIds->isNotEmpty(), function ($query) use ($lessonIds) {
                $query->whereIn('lesson_id', $lessonIds);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->with('lesson')
            ->get()
            ->map(function($progress) {
                $seconds = $progress->created_at->diffInSeconds($progress->completed_at);
                return [
                    'lesson_id' => $progress->lesson_id,
                    'lesson_title' => $progress->lesson->title,
                    'time_seconds' => $seconds,
                    'time_formatted' => gmdate('H:i:s', $seconds)
                ];
            });

        $sectionIds = collect($context['section_ids']);
        $assessmentAttempts = AssessmentAttempt::where('user_id', $userId)
            ->with('section')
            ->completed()
            ->when($sectionIds->isNotEmpty(), function ($query) use ($sectionIds) {
                $query->whereIn('section_id', $sectionIds);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->orderBy('completed_at', 'desc')
            ->get();

        $assessmentStats = $this->getAssessmentStats($user, $assessmentAttempts);
        $assessmentComparisons = $assessmentAttempts
            ->groupBy('section_id')
            ->map(function ($attempts, $sectionId) {
                $preAttempt = $attempts
                    ->where('type', 'pre')
                    ->sortByDesc('completed_at')
                    ->first();
                $postAttempt = $attempts
                    ->where('type', 'post')
                    ->sortByDesc('completed_at')
                    ->first();
                $section = $attempts->first()?->section;

                return [
                    'section_name' => $section?->name ?? 'Unknown Section',
                    'pre_attempt' => $preAttempt,
                    'post_attempt' => $postAttempt,
                    'improvement' => ($preAttempt && $postAttempt)
                        ? round($postAttempt->percentage - $preAttempt->percentage, 2)
                        : null,
                ];
            })
            ->sortBy('section_name')
            ->values();

        return view('admin.user-progress.show', compact(
            'user',
            'lessons',
            'quizAttempts',
            'simulationAttempts',
            'timeSpent',
            'context',
            'assessmentAttempts',
            'assessmentStats',
            'assessmentComparisons'
        ));
    }

    private function getScopedStudentsQuery(): Builder
    {
        $query = User::where('user_type', 'USER');

        if (auth()->user()->isTeacher()) {
            $query->whereHas('enrolledSections', function ($sectionQuery) {
                $sectionQuery->where('teacher_id', auth()->id());
            });
        }

        return $query;
    }

    private function getScopedSectionsQuery(): Builder
    {
        $query = \App\Models\Section::query();

        if (auth()->user()->isTeacher()) {
            $query->where('teacher_id', auth()->id());
        }

        return $query;
    }

    private function applySectionScope($query): void
    {
        if (auth()->user()->isTeacher()) {
            $query->where('teacher_id', auth()->id());
        }
    }

    private function getProgressContext(User $user): array
    {
        if (isset($this->progressContextCache[$user->id])) {
            return $this->progressContextCache[$user->id];
        }

        $sections = $user->enrolledSections ?? collect();
        $lessonIds = $sections
            ->flatMap(fn($section) => $section->lessons->where('is_active', true)->pluck('id'))
            ->unique()
            ->values();

        $lessonIdLookup = $lessonIds->all();
        $completedLessons = $user->studentLessons
            ->filter(function ($progress) use ($lessonIdLookup) {
                return in_array($progress->lesson_id, $lessonIdLookup, true)
                    && $progress->completed_at !== null;
            })
            ->count();

        $totalLessons = $lessonIds->count();
        $progressPercentage = $totalLessons > 0
            ? (int) round(($completedLessons / $totalLessons) * 100)
            : 0;

        return $this->progressContextCache[$user->id] = [
            'section_ids' => $sections->pluck('id')->values()->all(),
            'section_count' => $sections->count(),
            'lesson_ids' => $lessonIdLookup,
            'sections_label' => $sections->pluck('name')->join(', ') ?: 'No section assigned',
            'completed_lessons' => $completedLessons,
            'total_lessons' => $totalLessons,
            'progress_percentage' => $progressPercentage,
        ];
    }

    private function getQuizAverage(int $userId, array $lessonIds): ?float
    {
        if (empty($lessonIds)) {
            return null;
        }

        return UserQuizAttempt::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->whereHas('quiz', function ($query) use ($lessonIds) {
                $query->whereIn('lesson_id', $lessonIds);
            })
            ->avg('score');
    }

    private function getSimulationAverage(int $userId, array $lessonIds): ?float
    {
        if (empty($lessonIds)) {
            return null;
        }

        $attempts = SimulationAttempt::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->whereIn('lesson_id', $lessonIds)
            ->get();

        if ($attempts->isEmpty()) {
            return null;
        }

        return $attempts->avg(function ($attempt) {
            return $attempt->total_scenarios > 0
                ? ($attempt->score / $attempt->total_scenarios) * 100
                : 0;
        });
    }

    private function formatAverage(?float $average): string
    {
        return $average !== null ? round($average, 2) . '%' : 'N/A';
    }

    private function getCompletedLessonsForSection(User $student, array $lessonIds): int
    {
        if (empty($lessonIds)) {
            return 0;
        }

        return $student->studentLessons
            ->filter(function ($progress) use ($lessonIds) {
                return in_array($progress->lesson_id, $lessonIds, true)
                    && $progress->completed_at !== null;
            })
            ->count();
    }

    private function getLatestSectionAssessment(User $student, int $sectionId, string $type): ?AssessmentAttempt
    {
        return $student->assessmentAttempts
            ->where('section_id', $sectionId)
            ->where('type', $type)
            ->sortByDesc('completed_at')
            ->first();
    }

    private function getLoadedQuizAverage(User $student, array $lessonIds): ?float
    {
        if (empty($lessonIds)) {
            return null;
        }

        $attempts = $student->quizAttempts->filter(function ($attempt) use ($lessonIds) {
            return $attempt->quiz
                && in_array($attempt->quiz->lesson_id, $lessonIds, true)
                && $attempt->completed_at !== null;
        });

        return $attempts->isNotEmpty()
            ? $attempts->avg('score')
            : null;
    }

    private function getLoadedSimulationAverage(User $student, array $lessonIds): ?float
    {
        if (empty($lessonIds)) {
            return null;
        }

        $attempts = $student->simulationAttempts->filter(function ($attempt) use ($lessonIds) {
            return in_array($attempt->lesson_id, $lessonIds, true)
                && $attempt->completed_at !== null;
        });

        if ($attempts->isEmpty()) {
            return null;
        }

        return $attempts->avg(function ($attempt) {
            return $attempt->total_scenarios > 0
                ? ($attempt->score / $attempt->total_scenarios) * 100
                : 0;
        });
    }

    private function getAssessmentStats(User $user, $attempts = null): array
    {
        if (isset($this->assessmentStatsCache[$user->id])) {
            return $this->assessmentStatsCache[$user->id];
        }

        $context = $this->getProgressContext($user);
        $sectionIds = $context['section_ids'];

        $attempts = $attempts ?? AssessmentAttempt::where('user_id', $user->id)
            ->completed()
            ->when(!empty($sectionIds), function ($query) use ($sectionIds) {
                $query->whereIn('section_id', $sectionIds);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->get();

        $preAttempts = $attempts->where('type', 'pre');
        $postAttempts = $attempts->where('type', 'post');
        $avgPreScore = $preAttempts->count() > 0
            ? round($preAttempts->avg(fn($attempt) => $attempt->percentage), 2)
            : null;
        $avgPostScore = $postAttempts->count() > 0
            ? round($postAttempts->avg(fn($attempt) => $attempt->percentage), 2)
            : null;

        return $this->assessmentStatsCache[$user->id] = [
            'total_pre' => $preAttempts->count(),
            'total_post' => $postAttempts->count(),
            'avg_pre_score' => $avgPreScore,
            'avg_post_score' => $avgPostScore,
            'avg_improvement' => ($avgPreScore !== null && $avgPostScore !== null)
                ? round($avgPostScore - $avgPreScore, 2)
                : null,
        ];
    }

    private function formatImprovement(?float $improvement): string
    {
        if ($improvement === null) {
            return 'N/A';
        }

        return ($improvement > 0 ? '+' : '') . round($improvement, 2) . '%';
    }
}
