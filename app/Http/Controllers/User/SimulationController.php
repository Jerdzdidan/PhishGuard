<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\SimulationAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class SimulationController extends Controller
{
        /**
     * Get simulation configurations for each lesson
     */
    private function getSimulationConfig($lessonId)
    {
        $configs = [
            1 => [ // Lesson 1 simulations
                [
                    'id' => 'sim-1',
                    'title' => 'GCash Phishing Detection',
                    'description' => 'Learn to identify phishing attempts through SMS and email',
                    'total_scenarios' => 5,
                    'view' => 'user.simulations.lesson-1-sim-1'
                ],
                [
                    'id' => 'sim-2',
                    'title' => 'Fake Bank Website',
                    'description' => 'Identify fake banking websites and protect your credentials',
                    'total_scenarios' => 5,
                    'view' => 'user.simulations.lesson-1-sim-2'
                ],
                [
                    'id' => 'sim-3',
                    'title' => 'Marketplace Scam',
                    'description' => 'Avoid online marketplace scams and fraudulent sellers',
                    'total_scenarios' => 5,
                    'view' => 'user.simulations.lesson-1-sim-3'
                ]
            ],
            2 => [ // Lesson 2 simulations
                [
                    'id' => 'sim-1',
                    'title' => 'Job Offer Scam Detection',
                    'description' => 'Identify fake job offers and employment scams',
                    'total_scenarios' => 4,
                    'view' => 'user.simulations.lesson-2-sim-1'
                ],
                [
                    'id' => 'sim-2',
                    'title' => 'Public WiFi Safety',
                    'description' => 'Learn safe practices when using public WiFi networks',
                    'total_scenarios' => 3,
                    'view' => 'user.simulations.lesson-2-sim-2'
                ]
            ],
            3 => [ // Lesson 3 simulations
                [
                    'id' => 'sim-1',
                    'title' => 'Password Security',
                    'description' => 'Create strong passwords and manage them securely',
                    'total_scenarios' => 4,
                    'view' => 'user.simulations.lesson-3-sim-1'
                ]
            ]
        ];

        return $configs[$lessonId] ?? [];
    }

    /**
     * Show all simulations for a lesson
     */
    public function index($id)
    {
        try {
            $lessonId = Crypt::decryptString($id);
            $lesson = Lesson::findOrFail($lessonId);

            if (!$lesson->has_simulation) {
                return redirect()->route('lessons.show', $id)
                    ->with('error', 'This lesson does not have simulations.');
            }

            // Check if lesson is unlocked
            if (!$lesson->isUnlocked()) {
                return redirect()->route('user.home')
                    ->with('error', 'This lesson is locked.');
            }

            $simulations = $this->getSimulationConfig($lessonId);
            $progress = $lesson->getStudentProgress();

            // Get user's attempts for each simulation
            $attempts = [];
            foreach ($simulations as $sim) {
                $attempts[$sim['id']] = SimulationAttempt::where('user_id', Auth::id())
                    ->where('lesson_id', $lessonId)
                    ->where('simulation_id', $sim['id'])
                    ->where('completed_at', '!=', null)
                    ->latest()
                    ->first();
            }

            return view('user.home.lesson.simulations', compact('lesson', 'simulations', 'attempts', 'progress'));

        } catch (\Exception $e) {
            return redirect()->route('user.home')
                ->with('error', 'Simulation not found.');
        }
    }

    /**
     * Show specific simulation
     */
    public function show($id, $simId)
    {
        try {
            $lessonId = Crypt::decryptString($id);
            $lesson = Lesson::findOrFail($lessonId);

            if (!$lesson->has_simulation) {
                return redirect()->route('lessons.show', $id)
                    ->with('error', 'This lesson does not have simulations.');
            }

            // Check if lesson is unlocked
            if (!$lesson->isUnlocked()) {
                return redirect()->route('user.home')
                    ->with('error', 'This lesson is locked.');
            }

            $simulations = $this->getSimulationConfig($lessonId);
            $simulation = collect($simulations)->firstWhere('id', $simId);

            if (!$simulation) {
                return redirect()->route('lessons.simulations.index', $id)
                    ->with('error', 'Simulation not found.');
            }

            // Check if previous simulations are completed (sequential requirement)
            $simIndex = array_search($simId, array_column($simulations, 'id'));
            if ($simIndex > 0) {
                $previousSim = $simulations[$simIndex - 1];
                $previousAttempt = SimulationAttempt::where('user_id', Auth::id())
                    ->where('lesson_id', $lessonId)
                    ->where('simulation_id', $previousSim['id'])
                    ->where('completed_at', '!=', null)
                    ->latest()
                    ->first();

                if (!$previousAttempt) {
                    return redirect()->route('lessons.simulations.index', $id)
                        ->with('error', 'Complete the previous simulation first.');
                }
            }

            return view($simulation['view'], compact('lesson', 'simulation'));

        } catch (\Exception $e) {
            return redirect()->route('user.home')
                ->with('error', 'Simulation not found.');
        }
    }

    /**
     * Start a simulation attempt
     */
    public function start(Request $request, $id, $simId)
    {
        try {
            $lessonId = Crypt::decryptString($id);
            $lesson = Lesson::findOrFail($lessonId);

            $simulations = $this->getSimulationConfig($lessonId);
            $simulation = collect($simulations)->firstWhere('id', $simId);

            if (!$simulation) {
                return response()->json(['success' => false, 'message' => 'Simulation not found'], 404);
            }

            // Get attempt number
            $attemptNumber = SimulationAttempt::where('user_id', Auth::id())
                ->where('lesson_id', $lessonId)
                ->where('simulation_id', $simId)
                ->max('attempt_number') ?? 0;

            $attempt = SimulationAttempt::create([
                'user_id' => Auth::id(),
                'lesson_id' => $lessonId,
                'simulation_id' => $simId,
                'started_at' => now(),
                'total_scenarios' => $simulation['total_scenarios'],
                'attempt_number' => $attemptNumber + 1
            ]);

            return response()->json([
                'success' => true,
                'attempt_id' => $attempt->id
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Submit simulation results
     */
    public function submit(Request $request, $id, $simId)
    {
        $validated = $request->validate([
            'attempt_id' => 'required|exists:simulation_attempts,id',
            'score' => 'required|integer|min:0',
            'time_taken' => 'required|integer|min:0',
            'click_data' => 'nullable|array',
            'scenario_results' => 'required|array'
        ]);

        try {
            $lessonId = Crypt::decryptString($id);
            $attempt = SimulationAttempt::where('id', $validated['attempt_id'])
                ->where('user_id', Auth::id())
                ->firstOrFail();

            DB::beginTransaction();

            $attempt->update([
                'completed_at' => now(),
                'score' => $validated['score'],
                'time_taken' => $validated['time_taken'],
                'click_data' => $validated['click_data'] ?? [],
                'scenario_results' => $validated['scenario_results']
            ]);

            // Update student lesson progress
            $lesson = Lesson::findOrFail($lessonId);
            $progress = $lesson->getStudentProgress();
            
            // Get all simulations for this lesson
            $simulations = $this->getSimulationConfig($lessonId);
            
            // Count completed simulations
            $completedCount = SimulationAttempt::where('user_id', Auth::id())
                ->where('lesson_id', $lessonId)
                ->whereNotNull('completed_at')
                ->distinct('simulation_id')
                ->count('simulation_id');

            $progress->simulation_progress = $completedCount;
            
            // Mark all simulations complete if all are done
            if ($completedCount >= count($simulations)) {
                $progress->simulations_completed = true;
                
                // Check if lesson is now complete
                if ($progress->isCompleted()) {
                    $progress->completed_at = now();
                    $lesson->unlockDependentLessons();
                }
            }
            
            $progress->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Simulation completed successfully',
                'passed' => $attempt->isPassed(),
                'percentage' => $attempt->getPercentage(),
                'all_completed' => $progress->simulations_completed,
                'redirect_url' => route('lessons.simulations.results', [
                    'id' => $id,
                    'simId' => $simId,
                    'attempt' => Crypt::encryptString($attempt->id)
                ])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show simulation results
     */
    public function results($id, $simId, $attempt)
    {
        try {
            $lessonId = Crypt::decryptString($id);
            $attemptId = Crypt::decryptString($attempt);
            
            $lesson = Lesson::findOrFail($lessonId);
            $simulationAttempt = SimulationAttempt::where('id', $attemptId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $simulations = $this->getSimulationConfig($lessonId);
            $simulation = collect($simulations)->firstWhere('id', $simId);

            $progress = $lesson->getStudentProgress();

            return view('user.home.lesson.simulation-results', compact(
                'lesson',
                'simulation',
                'simulationAttempt',
                'progress'
            ));

        } catch (\Exception $e) {
            return redirect()->route('user.home')
                ->with('error', 'Results not found.');
        }
    }
}
