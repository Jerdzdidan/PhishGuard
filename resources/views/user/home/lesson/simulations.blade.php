@extends('user.layout.base')

@section('title')
SIMULATIONS - {{ $lesson->title }}
@endsection

@section('nav_title')
SIMULATIONS - {{ $lesson->title }}
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
<style>
.simulation-card {
    border-left: 4px solid #1E7F5C;
    transition: all 0.3s ease;
    position: relative;
}
.simulation-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.simulation-card.locked {
    opacity: 0.6;
    cursor: not-allowed;
}
.simulation-card.completed {
    border-left-color: #28c76f;
}
.lock-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    z-index: 10;
}
</style>
@endsection

@section('content')
<div class="row g-6">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">{{ $lesson->title }}</h4>
                        <p class="mb-0">Interactive Simulations</p>
                    </div>
                    <a href="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}" class="btn btn-label-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Back to Lesson
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="alert alert-info mb-4">
                    <i class="ri-information-line me-2"></i>
                    <strong>Note:</strong> You must complete all simulations in order to proceed to the next lesson. Each simulation tests your ability to identify and respond to cyber threats.
                </div>

                <h5 class="mb-3">Available Simulations ({{ count($simulations) }})</h5>

                @foreach($simulations as $index => $sim)
                    @php
                        $attempt = $attempts[$sim['id']] ?? null;
                        $isPassed = in_array($sim['id'], $passedSimulationIds->all(), true);
                        $hasAttempt = $attempt !== null;
                        $isCompleted = $isPassed;
                        $isLocked = $index > 0 && !in_array($simulations[$index - 1]['id'], $passedSimulationIds->all(), true);
                    @endphp

                    <div class="card simulation-card mb-3 {{ $isCompleted ? 'completed' : '' }} {{ $isLocked ? 'locked' : '' }}">
                        @if($isLocked)
                            <div class="lock-overlay">
                                <div class="text-center">
                                    <i class="ri-lock-line" style="font-size: 2rem; color: #696cff;"></i>
                                    <p class="mb-0 mt-2">Pass the previous simulation to unlock</p>
                                </div>
                            </div>
                        @endif

                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <h6 class="mb-0">Simulation {{ $index + 1 }}: {{ $sim['title'] }}</h6>
                                        @if($isCompleted)
                                            <span class="badge bg-success">
                                                <i class="ri-check-line me-1"></i> Passed
                                            </span>
                                        @elseif($hasAttempt)
                                            <span class="badge bg-warning text-dark">
                                                <i class="ri-restart-line me-1"></i> Needs Retry
                                            </span>
                                        @elseif($isLocked)
                                            <span class="badge bg-secondary">
                                                <i class="ri-lock-line me-1"></i> Locked
                                            </span>
                                        @else
                                            <span class="badge bg-primary">
                                                <i class="ri-play-line me-1"></i> Available
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mb-2 text-muted">{{ $sim['description'] }}</p>
                                    <div class="d-flex gap-3 text-sm">
                                        <span><i class="ri-file-list-line me-1"></i> {{ $sim['total_scenarios'] }} Scenarios</span>
                                        @if($hasAttempt && $attempt)
                                            <span><i class="ri-trophy-line me-1 text-warning"></i> Score: {{ $attempt->score }}/{{ $attempt->total_scenarios }}</span>
                                            <span><i class="ri-time-line me-1"></i> {{ gmdate('i:s', $attempt->time_taken) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    @if(!$isLocked)
                                        <a href="{{ route('lessons.simulations.show', ['id' => Crypt::encryptString($lesson->id), 'simId' => $sim['id']]) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="ri-play-line me-1"></i> {{ $hasAttempt ? 'Retake' : 'Start' }}
                                        </a>
                                        @if($hasAttempt)
                                            <a href="{{ route('lessons.simulations.results', [
                                                'id' => Crypt::encryptString($lesson->id), 
                                                'simId' => $sim['id'],
                                                'attempt' => Crypt::encryptString($attempt->id)
                                            ]) }}" 
                                               class="btn btn-label-secondary btn-sm">
                                                <i class="ri-file-chart-line me-1"></i> View Results
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            @if($hasAttempt && $attempt)
                                <div class="progress mt-3" style="height: 8px">
                                    <div class="progress-bar {{ $attempt->isPassed() ? 'bg-success' : 'bg-warning' }}" 
                                         role="progressbar" 
                                         style="width: {{ $attempt->getPercentage() }}%" 
                                         aria-valuenow="{{ $attempt->getPercentage() }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $attempt->getPercentage() }}% - {{ $attempt->isPassed() ? 'Passed' : 'Not Passed (70% required)' }}</small>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if($progress->simulations_completed)
                    <div class="alert alert-success mt-4">
                        <i class="ri-check-circle-line me-2"></i>
                        <strong>All simulations completed!</strong> You can now proceed to the next lesson.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @include('user.home.lesson.partials.sidebar', ['lesson' => $lesson, 'activeStep' => 'simulation'])

        <div class="card stick-top">
            <div class="card-body">
                <h6 class="mb-3">Simulation Progress</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Completed</span>
                    <strong>{{ $progress->simulation_progress }}/{{ count($simulations) }}</strong>
                </div>
                <div class="progress mb-3" style="height: 10px">
                    <div class="progress-bar bg-success" 
                         role="progressbar" 
                         style="width: {{ count($simulations) > 0 ? ($progress->simulation_progress / count($simulations) * 100) : 0 }}%" 
                         aria-valuenow="{{ $progress->simulation_progress }}" 
                         aria-valuemin="0" 
                         aria-valuemax="{{ count($simulations) }}">
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Status</span>
                    <strong class="{{ $progress->simulations_completed ? 'text-success' : 'text-warning' }}">
                        {{ $progress->simulations_completed ? 'Complete' : 'In Progress' }}
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
