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
            1 => [ // Lesson 1 - ONE simulation with 5 diverse scenarios
                [
                    'id' => 'lesson-1-sim',
                    'title' => 'Phishing & Online Scams Detection',
                    'description' => 'Learn to identify various types of phishing attempts and online scams',
                    'total_scenarios' => 5,
                    'view' => 'user.simulations.lesson-1-simulation'
                ]
            ],
            2 => [ // Lesson 2 - ONE simulation
                [
                    'id' => 'lesson-2-sim',
                    'title' => 'Advanced Threat Recognition',
                    'description' => 'Identify sophisticated scams and security threats',
                    'total_scenarios' => 5,
                    'view' => 'user.simulations.lesson-2-simulation'
                ]
            ],
            3 => [ // Lesson 3 - ONE simulation
                [
                    'id' => 'lesson-3-sim',
                    'title' => 'Password & Account Security',
                    'description' => 'Practice secure password management and account protection',
                    'total_scenarios' => 5,
                    'view' => 'user.simulations.lesson-3-simulation'
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
            
            // Since we only have 1 simulation per lesson now, redirect directly to it
            if (count($simulations) === 1) {
                return redirect()->route('lessons.simulations.show', [
                    'id' => $id,
                    'simId' => $simulations[0]['id']
                ]);
            }

            // Fallback if multiple simulations (shouldn't happen with new structure)
            $progress = $lesson->getStudentProgress();
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
     * If user has already completed this simulation, show their last attempt results instead
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

            // Check if this is a retake (session flag set)
            if (session()->has('allow_retake')) {
                session()->forget('allow_retake');
                // Allow them to take simulation again
            } else {
                // Check if user has already attempted this simulation (get latest attempt only)
                $latestAttempt = SimulationAttempt::where('user_id', Auth::id())
                    ->where('lesson_id', $lessonId)
                    ->where('simulation_id', $simId)
                    ->where('completed_at', '!=', null)
                    ->latest()
                    ->first();

                if ($latestAttempt) {
                    // Redirect to results page if valid attempt exists
                    return redirect()->route('lessons.simulations.results', [
                        'id' => $id,
                        'simId' => $simId,
                        'attempt' => Crypt::encryptString($latestAttempt->id)
                    ]);
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

            // ✅ FIX: Convert string booleans to actual booleans
            $scenarioResults = array_map(function($result) {
                return [
                    'scenario' => $result['scenario'],
                    'correct' => $result['correct'] === 'true' || $result['correct'] === true,
                    'selected_action' => $result['selected_action']
                ];
            }, $validated['scenario_results']);

            // Determine if passed (70% or higher)
            $totalScenarios = count($scenarioResults);
            // ✅ FIX: Count actual correct boolean values
            $correctCount = collect($scenarioResults)->where('correct', true)->count();
            $percentage = $totalScenarios > 0 ? round(($correctCount / $totalScenarios) * 100) : 0;
            $passed = $percentage >= 70;

            $attempt->update([
                'completed_at' => now(),
                'score' => $correctCount,  // ✅ Store the count of correct answers, not the input score
                'time_taken' => $validated['time_taken'],
                'click_data' => $validated['click_data'] ?? [],
                'scenario_results' => $scenarioResults  // ✅ Store the converted array
            ]);

            // Update student lesson progress
            $lesson = Lesson::findOrFail($lessonId);
            $progress = $lesson->getStudentProgress();
            
            // Update simulation results (only count if passed)
            $progress->updateSimulationResults($passed);

            // If completed, unlock dependent lessons
            if ($progress->isCompleted()) {
                $lesson->unlockDependentLessons();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Simulation completed successfully',
                'passed' => $passed,
                'percentage' => $percentage,
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

    /**
     * Allow retaking a simulation
     */
    public function retake($id, $simId)
    {
        try {
            $lessonId = Crypt::decryptString($id);
            $lesson = Lesson::findOrFail($lessonId);

            $simulations = $this->getSimulationConfig($lessonId);
            $simulation = collect($simulations)->firstWhere('id', $simId);

            if (!$simulation) {
                return redirect()->route('user.home')
                    ->with('error', 'Simulation not found.');
            }

            // Set session flag to allow retake (bypass latest attempt check)
            session()->put('allow_retake', true);

            return redirect()->route('lessons.simulations.show', [
                'id' => $id,
                'simId' => $simId
            ]);

        } catch (\Exception $e) {
            return redirect()->route('user.home')
                ->with('error', 'Unable to retake simulation.');
        }
    }
}
