@extends('user.layout.base')

@section('title')
SIMULATION RESULTS - {{ $lesson->title }}
@endsection

@section('nav_title')
SIMULATION RESULTS - {{ $lesson->title }}
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
<style>
.result-card {
    border-left: 4px solid #1E7F5C;
}
.result-card.failed {
    border-left-color: #ea5455;
}
.result-card.incorrect {
    border-left-color: #ffc107;
    background: #fffbf0;
}
.score-summary {
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
}
.score-summary.passed {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 2px solid #28c76f;
}
.score-summary.failed {
    background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
    border: 2px solid #ea5455;
}
.scenario-status {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.scenario-status.correct {
    background: #d4edda;
    color: #155724;
}
.scenario-status.incorrect {
    background: #f8d7da;
    color: #721c24;
}
</style>
@endsection

@section('content')
<div class="row g-6">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                @php
                    $scenarioResults = $simulationAttempt->scenario_results;
                    $correctCount = collect($scenarioResults)->where('correct', true)->count();
                    $totalScenarios = count($scenarioResults);
                    $percentage = $totalScenarios > 0 ? round(($correctCount / $totalScenarios) * 100) : 0;
                    $passed = $percentage >= 70;
                @endphp

                <div class="score-summary text-center {{ $passed ? 'passed' : 'failed' }}">
                    <h3 class="mb-3">
                        @if($passed)
                            <i class="ri-checkbox-circle-line me-2"></i>
                            Simulation Completed!
                        @else
                            <i class="ri-information-line me-2"></i>
                            Simulation Complete - Practice More
                        @endif
                    </h3>
                    <div class="display-4 mb-3">{{ $percentage }}%</div>
                    <p class="mb-0">
                        You scored {{ $correctCount }} out of {{ $totalScenarios }} scenarios correctly.
                        @if($passed)
                            Great job identifying cyber threats!
                        @else
                            You need 70% to pass. Review the scenarios and try again!
                        @endif
                    </p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted mb-1">Score</h6>
                            <h4 class="mb-0">{{ $correctCount }}/{{ $totalScenarios }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted mb-1">Percentage</h6>
                            <h4 class="mb-0 {{ $passed ? 'text-success' : 'text-danger' }}">
                                {{ $percentage }}%
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted mb-1">Time Taken</h6>
                            <h4 class="mb-0">{{ gmdate('i:s', $simulationAttempt->time_taken) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted mb-1">Attempt</h6>
                            <h4 class="mb-0">#{{ $simulationAttempt->attempt_number }}</h4>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3">Scenario Results</h5>
                @foreach($scenarioResults as $index => $result)
                    <div class="card result-card {{ $result['correct'] ? '' : 'incorrect' }} mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Scenario {{ $index + 1 }}: {{ $result['scenario'] }}</h6>
                                    <p class="mb-0 text-muted">
                                        <strong>Your Action:</strong> {{ $result['selected_action'] }}
                                    </p>
                                </div>
                                <div>
                                    @if($result['correct'])
                                        <span class="scenario-status correct">
                                            <i class="ri-check-line me-1"></i> Correct
                                        </span>
                                    @else
                                        <span class="scenario-status incorrect">
                                            <i class="ri-close-line me-1"></i> Incorrect
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('lessons.simulations.index', Crypt::encryptString($lesson->id)) }}" 
                               class="btn btn-label-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Back to Simulations
                            </a>
                            @if(!$passed)
                                <a href="{{ route('lessons.simulations.show', ['id' => Crypt::encryptString($lesson->id), 'simId' => $simulation['id']]) }}" 
                                   class="btn btn-primary">
                                    <i class="ri-restart-line me-1"></i> Try Again
                                </a>
                            @else
                                <a href="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}" 
                                   class="btn btn-primary">
                                    <i class="ri-arrow-right-line me-1"></i> Back to Lesson
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="accordion stick-top accordion-custom-button mb-4" id="courseContent">
            <div class="accordion-item active mb-0">
                <div class="accordion-header" id="headingOne">
                    <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#chapterOne" aria-expanded="true" aria-controls="chapterOne">
                        <span class="d-flex flex-column">
                            <span class="h5 mb-0">Lesson Content</span>
                            <span class="text-body fw-normal">{{ $lesson->time }} min</span>
                        </span>
                    </button>
                </div>
                <div id="chapterOne" class="accordion-collapse collapse show" data-bs-parent="#courseContent">
                    <div class="accordion-body py-4">
                        <div class="mb-4">
                            <a href="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}">
                                <label class="form-check-label ms-4">
                                    <span class="mb-0 h6">1. Lesson</span>
                                    <small class="text-body d-block">content</small>
                                </label>
                            </a>
                        </div>
                        @if ($lesson->quiz && $lesson->quiz->is_active)
                            <hr>
                            <div class="mb-4">
                                <a href="{{ route('lessons.quiz.show', Crypt::encryptString($lesson->id)) }}">
                                    <label class="form-check-label ms-4">
                                        <span class="mb-0 h6">2. Quiz</span>
                                        <small class="text-body d-block">assessment</small>
                                    </label>
                                </a>
                            </div>
                        @endif
                        @if ($lesson->has_simulation)
                            <hr>
                            <div class="mb-4">
                                <label class="ms-4">
                                    <span class="mb-0 h6 text-primary">3. Simulations</span>
                                    <small class="text-body d-block">interactive practice</small>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card stick-top">
            <div class="card-body">
                <h6 class="mb-3">{{ $simulation['title'] }}</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Status</span>
                    <strong class="{{ $passed ? 'text-success' : 'text-warning' }}">
                        {{ $passed ? 'Passed' : 'Not Passed' }}
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Score</span>
                    <strong>{{ $correctCount }}/{{ $totalScenarios }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Passing Requirement</span>
                    <strong>70%</strong>
                </div>
                <hr>
                @if($progress->simulations_completed)
                    <div class="alert alert-success mb-0">
                        <i class="ri-check-circle-line me-2"></i>
                        <small>Simulation passed! You can proceed to the next lesson.</small>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="ri-information-line me-2"></i>
                        <small>You need to achieve 70% to pass. Try again!</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection