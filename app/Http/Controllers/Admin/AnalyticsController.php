<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\SimulationAttempt;
use App\Models\StudentLesson;
use App\Models\User;
use App\Models\UserQuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
        /**
     * Analytics Overview
     */
    public function overview(Request $request)
    {
        // Date filtering
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Teacher scoping
        $studentIds = $this->getTeacherStudentIds();
        
        // General Statistics
        $stats = [
            'total_users' => $studentIds
                ? count($studentIds)
                : User::where('user_type', 'USER')->count(),
            'total_lessons' => Lesson::where('is_active', true)->count(),
            'total_quizzes' => UserQuizAttempt::when($studentIds, fn($q) => $q->whereIn('user_id', $studentIds))
                ->when($startDate, function($q) use ($startDate) {
                    return $q->whereDate('completed_at', '>=', $startDate);
                })->when($endDate, function($q) use ($endDate) {
                    return $q->whereDate('completed_at', '<=', $endDate);
                })->count(),
            'total_simulations' => SimulationAttempt::whereNotNull('completed_at')
                ->when($studentIds, fn($q) => $q->whereIn('user_id', $studentIds))
                ->when($startDate, function($q) use ($startDate) {
                    return $q->whereDate('completed_at', '>=', $startDate);
                })->when($endDate, function($q) use ($endDate) {
                    return $q->whereDate('completed_at', '<=', $endDate);
                })->count(),
        ];

        // Completion Rates by Lesson
        $lessonCompletionRates = Lesson::withCount([
            'studentLessons as completed_count' => function($q) use ($studentIds) {
                $q->whereNotNull('completed_at');
                if ($studentIds) $q->whereIn('user_id', $studentIds);
            },
            'studentLessons as started_count' => function($q) use ($studentIds) {
                if ($studentIds) $q->whereIn('user_id', $studentIds);
            }
        ])->where('is_active', true)->get()->map(function($lesson) {
            return [
                'lesson_id' => $lesson->id,
                'title' => $lesson->title,
                'completion_rate' => $lesson->started_count > 0 
                    ? round(($lesson->completed_count / $lesson->started_count) * 100, 2)
                    : 0,
                'completed' => $lesson->completed_count,
                'started' => $lesson->started_count
            ];
        });

        // Quiz Statistics
        $quizStats = UserQuizAttempt::when($studentIds, fn($q) => $q->whereIn('user_id', $studentIds))
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('completed_at', '>=', $startDate);
            })->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('completed_at', '<=', $endDate);
            })
            ->selectRaw('
                AVG(score) as avg_score,
                AVG(completion_time) as avg_time,
                COUNT(CASE WHEN passed = 1 THEN 1 END) as passed_count,
                COUNT(CASE WHEN passed = 0 THEN 1 END) as failed_count
            ')
            ->first();

        // Simulation Statistics
        $simulationStats = SimulationAttempt::whereNotNull('completed_at')
            ->when($studentIds, fn($q) => $q->whereIn('user_id', $studentIds))
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('completed_at', '>=', $startDate);
            })->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('completed_at', '<=', $endDate);
            })
            ->selectRaw('
                AVG(score / total_scenarios * 100) as avg_percentage,
                AVG(time_taken) as avg_time,
                COUNT(CASE WHEN (score / total_scenarios * 100) >= 70 THEN 1 END) as passed_count,
                COUNT(CASE WHEN (score / total_scenarios * 100) < 70 THEN 1 END) as failed_count
            ')
            ->first();

        // Time spent per lesson
        $timeSpentPerLesson = Lesson::where('is_active', true)
            ->select('lessons.id', 'lessons.title')
            ->selectRaw('
                (
                    SELECT AVG(completion_time) 
                    FROM user_quiz_attempts 
                    JOIN quizzes ON user_quiz_attempts.quiz_id = quizzes.id 
                    WHERE quizzes.lesson_id = lessons.id 
                    AND user_quiz_attempts.completed_at IS NOT NULL
                ) as quiz_time,
                (
                    SELECT AVG(time_taken) 
                    FROM simulation_attempts 
                    WHERE lesson_id = lessons.id 
                    AND completed_at IS NOT NULL
                ) as sim_time
            ')
            ->get()
            ->map(function($lesson) {
                $quizTime = $lesson->quiz_time ?? 0;
                $simTime = $lesson->sim_time ?? 0;
                $avgTime = ($quizTime + $simTime) / 2; // Average of both, or pick whichever exists
                
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'avg_time_seconds' => $avgTime
                ];
            });

        return view('admin.analytics.overview', compact(
            'stats',
            'lessonCompletionRates',
            'quizStats',
            'simulationStats',
            'timeSpentPerLesson'
        ));
    }

    /**
     * Quiz Analytics with Question Difficulty
     */
    public function quizAnalytics(Request $request)
    {
        $lessonId = $request->input('lesson_id');
        $lessons = Lesson::where('is_active', true)->get();
        $studentIds = $this->getTeacherStudentIds();

        $questionDifficulty = [];
        $aggregatedQuestions = [];

        if ($lessonId) {
            // Per-lesson question analysis
            $attempts = UserQuizAttempt::whereHas('quiz', function($q) use ($lessonId) {
                $q->where('lesson_id', $lessonId);
            })->when($studentIds, fn($q) => $q->whereIn('user_id', $studentIds))
              ->whereNotNull('answers_data')->get();

            $questionStats = [];
            foreach ($attempts as $attempt) {
                $results = json_decode($attempt->answers_data, true);
                foreach ($results as $result) {
                    $qId = $result['question_id'];
                    if (!isset($questionStats[$qId])) {
                        $questionStats[$qId] = [
                            'question_text' => $result['question_text'],
                            'total_attempts' => 0,
                            'correct_attempts' => 0
                        ];
                    }
                    $questionStats[$qId]['total_attempts']++;
                    if ($result['is_correct']) {
                        $questionStats[$qId]['correct_attempts']++;
                    }
                }
            }

            foreach ($questionStats as $qId => $stats) {
                $successRate = $stats['total_attempts'] > 0 
                    ? round(($stats['correct_attempts'] / $stats['total_attempts']) * 100, 2)
                    : 0;
                
                $questionDifficulty[] = [
                    'question_id' => $qId,
                    'question_text' => $stats['question_text'],
                    'success_rate' => $successRate,
                    'total_attempts' => $stats['total_attempts'],
                    'correct_attempts' => $stats['correct_attempts'],
                    'difficulty_level' => $this->getDifficultyLevel($successRate)
                ];
            }
        } else {
            // Aggregated across all lessons
            $attempts = UserQuizAttempt::whereNotNull('answers_data')
                ->when($studentIds, fn($q) => $q->whereIn('user_id', $studentIds))
                ->get();

            $questionStats = [];
            foreach ($attempts as $attempt) {
                $results = json_decode($attempt->answers_data, true);
                foreach ($results as $result) {
                    $qId = $result['question_id'];
                    if (!isset($questionStats[$qId])) {
                        $questionStats[$qId] = [
                            'question_text' => $result['question_text'],
                            'total_attempts' => 0,
                            'correct_attempts' => 0
                        ];
                    }
                    $questionStats[$qId]['total_attempts']++;
                    if ($result['is_correct']) {
                        $questionStats[$qId]['correct_attempts']++;
                    }
                }
            }

            foreach ($questionStats as $qId => $stats) {
                $successRate = $stats['total_attempts'] > 0 
                    ? round(($stats['correct_attempts'] / $stats['total_attempts']) * 100, 2)
                    : 0;
                
                $aggregatedQuestions[] = [
                    'question_id' => $qId,
                    'question_text' => $stats['question_text'],
                    'success_rate' => $successRate,
                    'total_attempts' => $stats['total_attempts'],
                    'correct_attempts' => $stats['correct_attempts'],
                    'difficulty_level' => $this->getDifficultyLevel($successRate)
                ];
            }
        }

        // Sort by difficulty (lowest success rate first = hardest)
        usort($questionDifficulty, function($a, $b) {
            return $a['success_rate'] <=> $b['success_rate'];
        });

        usort($aggregatedQuestions, function($a, $b) {
            return $a['success_rate'] <=> $b['success_rate'];
        });

        return view('admin.analytics.quiz', compact(
            'lessons',
            'lessonId',
            'questionDifficulty',
            'aggregatedQuestions'
        ));
    }

    /**
     * Simulation Analytics with CTR and Scenario Difficulty
     */
    public function simulationAnalytics(Request $request)
    {
        $lessonId = $request->input('lesson_id');
        $lessons = Lesson::where('is_active', true)->where('has_simulation', true)->get();
        $studentIds = $this->getTeacherStudentIds();

        $scenarioDifficulty = [];
        $aggregatedScenarios = [];
        $ctrData = [];

        if ($lessonId) {
            // Per-lesson scenario analysis
            $attempts = SimulationAttempt::where('lesson_id', $lessonId)
                ->when($studentIds, fn($q) => $q->whereIn('user_id', $studentIds))
                ->whereNotNull('completed_at')
                ->get();

            $scenarioStats = [];
            $totalClicks = 0;
            $actionMenuClicks = 0;

            foreach ($attempts as $attempt) {
                $results = $attempt->scenario_results;
                foreach ($results as $result) {
                    $scenario = $result['scenario'];
                    if (!isset($scenarioStats[$scenario])) {
                        $scenarioStats[$scenario] = [
                            'total_attempts' => 0,
                            'correct_attempts' => 0
                        ];
                    }
                    $scenarioStats[$scenario]['total_attempts']++;
                    if ($result['correct'] === true || $result['correct'] === 'true') {
                        $scenarioStats[$scenario]['correct_attempts']++;
                    }
                }

                // CTR Analysis
                if ($attempt->click_data) {
                    foreach ($attempt->click_data as $click) {
                        $totalClicks++;
                        if (isset($click['action']) && $click['action'] === 'opened_action_menu') {
                            $actionMenuClicks++;
                        }
                    }
                }
            }

            foreach ($scenarioStats as $scenario => $stats) {
                $successRate = $stats['total_attempts'] > 0 
                    ? round(($stats['correct_attempts'] / $stats['total_attempts']) * 100, 2)
                    : 0;
                
                $scenarioDifficulty[] = [
                    'scenario' => $scenario,
                    'success_rate' => $successRate,
                    'total_attempts' => $stats['total_attempts'],
                    'correct_attempts' => $stats['correct_attempts'],
                    'difficulty_level' => $this->getDifficultyLevel($successRate)
                ];
            }

            $ctrData = [
                'total_clicks' => $totalClicks,
                'action_menu_clicks' => $actionMenuClicks,
                'ctr' => $totalClicks > 0 ? round(($actionMenuClicks / $totalClicks) * 100, 2) : 0
            ];
        } else {
            // Aggregated across all lessons
            $attempts = SimulationAttempt::whereNotNull('completed_at')
                ->when($studentIds, fn($q) => $q->whereIn('user_id', $studentIds))
                ->get();

            $scenarioStats = [];
            $totalClicks = 0;
            $actionMenuClicks = 0;

            foreach ($attempts as $attempt) {
                $results = $attempt->scenario_results;
                foreach ($results as $result) {
                    $scenario = $result['scenario'];
                    if (!isset($scenarioStats[$scenario])) {
                        $scenarioStats[$scenario] = [
                            'total_attempts' => 0,
                            'correct_attempts' => 0
                        ];
                    }
                    $scenarioStats[$scenario]['total_attempts']++;
                    if ($result['correct'] === true || $result['correct'] === 'true') {
                        $scenarioStats[$scenario]['correct_attempts']++;
                    }
                }

                // CTR Analysis
                if ($attempt->click_data) {
                    foreach ($attempt->click_data as $click) {
                        $totalClicks++;
                        if (isset($click['action']) && $click['action'] === 'opened_action_menu') {
                            $actionMenuClicks++;
                        }
                    }
                }
            }

            foreach ($scenarioStats as $scenario => $stats) {
                $successRate = $stats['total_attempts'] > 0 
                    ? round(($stats['correct_attempts'] / $stats['total_attempts']) * 100, 2)
                    : 0;
                
                $aggregatedScenarios[] = [
                    'scenario' => $scenario,
                    'success_rate' => $successRate,
                    'total_attempts' => $stats['total_attempts'],
                    'correct_attempts' => $stats['correct_attempts'],
                    'difficulty_level' => $this->getDifficultyLevel($successRate)
                ];
            }

            $ctrData = [
                'total_clicks' => $totalClicks,
                'action_menu_clicks' => $actionMenuClicks,
                'ctr' => $totalClicks > 0 ? round(($actionMenuClicks / $totalClicks) * 100, 2) : 0
            ];
        }

        // Sort by difficulty
        usort($scenarioDifficulty, function($a, $b) {
            return $a['success_rate'] <=> $b['success_rate'];
        });

        usort($aggregatedScenarios, function($a, $b) {
            return $a['success_rate'] <=> $b['success_rate'];
        });

        return view('admin.analytics.simulation', compact(
            'lessons',
            'lessonId',
            'scenarioDifficulty',
            'aggregatedScenarios',
            'ctrData'
        ));
    }

    /**
     * Difficulty Heatmap
     */
    public function heatmap(Request $request)
    {
        $studentIds = $this->getTeacherStudentIds();

        // Quiz Heatmap Data
        $quizHeatmap = $this->getQuizHeatmapData($studentIds);
        
        // Simulation Heatmap Data
        $simulationHeatmap = $this->getSimulationHeatmapData($studentIds);

        return view('admin.analytics.heatmap', compact('quizHeatmap', 'simulationHeatmap'));
    }

    /**
     * Export Analytics Data
     */
    public function export(Request $request)
    {
        $type = $request->input('type'); // 'quiz' or 'simulation'
        
        if ($type === 'quiz') {
            return $this->exportQuizAnalytics($request);
        } elseif ($type === 'simulation') {
            return $this->exportSimulationAnalytics($request);
        }
        
        return $this->exportOverview($request);
    }

    // Helper Methods

    /**
     * Assessment Analytics - Pre vs Post comparison
     */
    public function assessmentAnalytics(Request $request)
    {
        $user = auth()->user();
        $sectionId = $request->input('section_id');

        // Get relevant sections
        $sectionsQuery = \App\Models\Section::query();
        if ($user->isTeacher()) {
            $sectionsQuery->where('teacher_id', $user->id);
        }
        $sections = $sectionsQuery->get();

        // Get assessment data
        $attemptsQuery = \App\Models\AssessmentAttempt::whereNotNull('completed_at');
        
        if ($user->isTeacher()) {
            $sectionIds = $sections->pluck('id');
            $attemptsQuery->whereIn('section_id', $sectionIds);
        }

        if ($sectionId) {
            $attemptsQuery->where('section_id', $sectionId);
        }

        $attempts = $attemptsQuery->with('user', 'section')->get();

        // Separate pre and post
        $preAttempts = $attempts->where('type', 'pre');
        $postAttempts = $attempts->where('type', 'post');

        // Stats
        $stats = [
            'total_pre' => $preAttempts->count(),
            'total_post' => $postAttempts->count(),
            'avg_pre_score' => $preAttempts->count() > 0 
                ? round($preAttempts->avg(fn($a) => $a->percentage), 2) : 0,
            'avg_post_score' => $postAttempts->count() > 0 
                ? round($postAttempts->avg(fn($a) => $a->percentage), 2) : 0,
        ];
        $stats['avg_improvement'] = $stats['avg_post_score'] - $stats['avg_pre_score'];

        // Per-student comparison (students who took both)
        $studentComparisons = [];
        $postByUser = $postAttempts->groupBy('user_id');
        foreach ($preAttempts as $pre) {
            $post = $postByUser->get($pre->user_id)?->first();
            if ($post) {
                $studentComparisons[] = [
                    'student' => $pre->user->first_name . ' ' . $pre->user->last_name,
                    'section' => $pre->section->name,
                    'pre_score' => $pre->percentage,
                    'post_score' => $post->percentage,
                    'improvement' => $post->percentage - $pre->percentage,
                ];
            }
        }

        return view('admin.analytics.assessment', compact('sections', 'stats', 'studentComparisons', 'sectionId'));
    }

    private function getDifficultyLevel($successRate)
    {
        if ($successRate >= 80) return 'Easy';
        if ($successRate >= 60) return 'Medium';
        if ($successRate >= 40) return 'Hard';
        return 'Very Hard';
    }

    /**
     * Get student IDs scoped to teacher's sections.
     * Returns null for admins (no filtering needed).
     */
    private function getTeacherStudentIds(): ?array
    {
        $user = auth()->user();
        if (!$user->isTeacher()) {
            return null; // Admin sees everything
        }

        $sectionIds = \App\Models\Section::where('teacher_id', $user->id)->pluck('id');
        return DB::table('section_students')
            ->whereIn('section_id', $sectionIds)
            ->pluck('user_id')
            ->unique()
            ->toArray();
    }

    private function getQuizHeatmapData($studentIds = null)
    {
        $attempts = UserQuizAttempt::whereNotNull('answers_data')
            ->when($studentIds, fn($q) => $q->whereIn('user_id', $studentIds))
            ->get();
        $questionStats = [];

        foreach ($attempts as $attempt) {
            $results = json_decode($attempt->answers_data, true);
            foreach ($results as $result) {
                $qId = $result['question_id'];
                if (!isset($questionStats[$qId])) {
                    $questionStats[$qId] = [
                        'question_text' => substr($result['question_text'], 0, 50) . '...',
                        'total' => 0,
                        'correct' => 0
                    ];
                }
                $questionStats[$qId]['total']++;
                if ($result['is_correct']) {
                    $questionStats[$qId]['correct']++;
                }
            }
        }

        $heatmapData = [];
        foreach ($questionStats as $qId => $stats) {
            $successRate = $stats['total'] > 0 
                ? round(($stats['correct'] / $stats['total']) * 100, 2)
                : 0;
            
            $heatmapData[] = [
                'id' => $qId,
                'label' => $stats['question_text'],
                'value' => $successRate
            ];
        }

        return $heatmapData;
    }

    private function getSimulationHeatmapData($studentIds = null)
    {
        $attempts = SimulationAttempt::whereNotNull('completed_at')
            ->when($studentIds, fn($q) => $q->whereIn('user_id', $studentIds))
            ->get();
        $scenarioStats = [];

        foreach ($attempts as $attempt) {
            foreach ($attempt->scenario_results as $result) {
                $scenario = $result['scenario'];
                if (!isset($scenarioStats[$scenario])) {
                    $scenarioStats[$scenario] = [
                        'total' => 0,
                        'correct' => 0
                    ];
                }
                $scenarioStats[$scenario]['total']++;
                if ($result['correct'] === true || $result['correct'] === 'true') {
                    $scenarioStats[$scenario]['correct']++;
                }
            }
        }

        $heatmapData = [];
        foreach ($scenarioStats as $scenario => $stats) {
            $successRate = $stats['total'] > 0 
                ? round(($stats['correct'] / $stats['total']) * 100, 2)
                : 0;
            
            $heatmapData[] = [
                'label' => $scenario,
                'value' => $successRate
            ];
        }

        return $heatmapData;
    }

    private function exportOverview($request)
    {
        // Implementation for CSV export
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="analytics_overview.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Metric', 'Value']);
            
            // Add data rows here
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportQuizAnalytics($request)
    {
        // Similar implementation for quiz analytics export
    }

    private function exportSimulationAnalytics($request)
    {
        // Similar implementation for simulation analytics export
    }
}
