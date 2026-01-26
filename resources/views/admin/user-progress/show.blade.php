<!-- resources/views/admin/user-progress/show.blade.php -->
@extends('admin.layout.base')

@section('title')
USER PROGRESS - {{ $user->first_name }} {{ $user->last_name }}
@endsection

@section('nav_title')
USER PROGRESS DETAIL
@endsection

@section('style')
<style>
.lesson-progress-card {
    transition: all 0.3s;
    border-left: 4px solid transparent;
}
.lesson-progress-card.completed {
    border-left-color: #28c76f;
    background: #f0fdf4;
}
.lesson-progress-card.in-progress {
    border-left-color: #ffc107;
    background: #fffbf0;
}
.lesson-progress-card.not-started {
    border-left-color: #e0e0e0;
}
.attempt-card {
    transition: all 0.3s;
}
.attempt-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #696cff;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #696cff;
}
.stat-card {
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-5px);
}
</style>
@endsection

@section('body')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-xl">
                            <img src="{{ asset('img/profile/default.png') }}" alt="User" class="rounded-circle">
                        </div>
                        <div>
                            <h4 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h4>
                            <p class="mb-0 text-muted">{{ $user->email }}</p>
                            <small class="text-muted">Joined {{ $user->created_at->format('M d, Y') }}</small>
                        </div>
                    </div>
                    <a href="{{ route('admin.user-progress.index') }}" class="btn btn-label-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-0 text-white">Lessons Completed</h6>
                        <h2 class="mb-0 text-white mt-2">{{ $lessons->where('is_completed', true)->count() }}/{{ $lessons->count() }}</h2>
                        <small class="text-white">
                            {{ $lessons->count() > 0 ? round(($lessons->where('is_completed', true)->count() / $lessons->count()) * 100) : 0 }}% Complete
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="ri-book-line ri-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-0 text-white">Quiz Average</h6>
                        @php
                            $avgQuiz = $quizAttempts->avg('score');
                        @endphp
                        <h2 class="mb-0 text-white mt-2">{{ $avgQuiz ? round($avgQuiz, 1) : 0 }}%</h2>
                        <small class="text-white">{{ $quizAttempts->count() }} attempts</small>
                    </div>
                    <div class="align-self-center">
                        <i class="ri-file-list-line ri-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-0 text-white">Simulation Average</h6>
                        @php
                            $avgSim = $simulationAttempts->avg(function($attempt) {
                                return ($attempt->score / $attempt->total_scenarios) * 100;
                            });
                        @endphp
                        <h2 class="mb-0 text-white mt-2">{{ $avgSim ? round($avgSim, 1) : 0 }}%</h2>
                        <small class="text-white">{{ $simulationAttempts->count() }} attempts</small>
                    </div>
                    <div class="align-self-center">
                        <i class="ri-gamepad-line ri-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-0 text-white">Total Time Spent</h6>
                        @php
                            $totalSeconds = $timeSpent->sum('time_seconds');
                            $hours = floor($totalSeconds / 3600);
                            $minutes = floor(($totalSeconds % 3600) / 60);
                        @endphp
                        <h2 class="mb-0 text-white mt-2">{{ $hours }}h {{ $minutes }}m</h2>
                        <small class="text-white">Learning time</small>
                    </div>
                    <div class="align-self-center">
                        <i class="ri-time-line ri-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Lessons Progress -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Lessons Progress</h5>
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                @forelse($lessons as $lesson)
                    @php
                        $statusClass = 'not-started';
                        $statusText = 'Not Started';
                        $statusIcon = 'ri-lock-line';
                        $statusColor = 'text-muted';
                        
                        if ($lesson['is_completed']) {
                            $statusClass = 'completed';
                            $statusText = 'Completed';
                            $statusIcon = 'ri-checkbox-circle-fill';
                            $statusColor = 'text-success';
                        } elseif ($lesson['content_viewed']) {
                            $statusClass = 'in-progress';
                            $statusText = 'In Progress';
                            $statusIcon = 'ri-time-line';
                            $statusColor = 'text-warning';
                        }
                    @endphp
                    
                    <div class="card lesson-progress-card mb-3 {{ $statusClass }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $lesson['title'] }}</h6>
                                    <span class="badge bg-label-{{ $lesson['difficulty'] === 'EASY' ? 'primary' : ($lesson['difficulty'] === 'MEDIUM' ? 'warning' : 'danger') }}">
                                        {{ $lesson['difficulty'] }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <i class="{{ $statusIcon }} {{ $statusColor }} ri-lg"></i>
                                    <div class="small {{ $statusColor }}">{{ $statusText }}</div>
                                </div>
                            </div>
                            
                            @if($lesson['completed_at'])
                                <small class="text-muted">
                                    <i class="ri-calendar-line me-1"></i>
                                    Completed: {{ \Carbon\Carbon::parse($lesson['completed_at'])->format('M d, Y') }}
                                </small>
                            @endif
                            
                            <div class="mt-2">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Content</small>
                                        <i class="ri-{{ $lesson['content_viewed'] ? 'check' : 'close' }}-line {{ $lesson['content_viewed'] ? 'text-success' : 'text-danger' }}"></i>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Quiz</small>
                                        <i class="ri-{{ $lesson['quiz_passed'] ? 'check' : 'close' }}-line {{ $lesson['quiz_passed'] ? 'text-success' : 'text-danger' }}"></i>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Simulation</small>
                                        <i class="ri-{{ $lesson['simulations_completed'] ? 'check' : 'close' }}-line {{ $lesson['simulations_completed'] ? 'text-success' : 'text-danger' }}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="ri-book-line ri-3x text-muted mb-3"></i>
                        <p class="text-muted">No lessons found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quiz Attempts Timeline -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Quiz Attempts</h5>
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                @forelse($quizAttempts->take(10) as $attempt)
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="card attempt-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">{{ $attempt->quiz->lesson->title }}</h6>
                                            <small class="text-muted">
                                                {{ $attempt->completed_at->format('M d, Y • h:i A') }}
                                            </small>
                                        </div>
                                        <span class="badge {{ $attempt->passed ? 'bg-success' : 'bg-danger' }}">
                                            {{ $attempt->score }}%
                                        </span>
                                    </div>
                                    <div class="d-flex gap-3 mt-2">
                                        <small class="text-muted">
                                            <i class="ri-time-line me-1"></i>
                                            {{ gmdate('i:s', $attempt->completion_time) }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="ri-{{ $attempt->passed ? 'check' : 'close' }}-circle-line me-1"></i>
                                            {{ $attempt->passed ? 'Passed' : 'Failed' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="ri-file-list-line ri-3x text-muted mb-3"></i>
                        <p class="text-muted">No quiz attempts yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Simulation Attempts -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Simulation Attempts</h5>
            </div>
            <div class="card-body">
                @forelse($simulationAttempts->take(10) as $attempt)
                    <div class="card attempt-card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <h6 class="mb-1">{{ $attempt->lesson->title }}</h6>
                                    <small class="text-muted">Simulation #{{ $attempt->simulation_id }}</small>
                                </div>
                                <div class="col-md-2 text-center">
                                    <small class="text-muted d-block">Score</small>
                                    <span class="badge {{ $attempt->isPassed() ? 'bg-success' : 'bg-warning' }}">
                                        {{ $attempt->score }}/{{ $attempt->total_scenarios }}
                                    </span>
                                </div>
                                <div class="col-md-2 text-center">
                                    <small class="text-muted d-block">Percentage</small>
                                    <strong>{{ $attempt->getPercentage() }}%</strong>
                                </div>
                                <div class="col-md-2 text-center">
                                    <small class="text-muted d-block">Time</small>
                                    <strong>{{ gmdate('i:s', $attempt->time_taken) }}</strong>
                                </div>
                                <div class="col-md-2 text-center">
                                    <small class="text-muted d-block">Date</small>
                                    <small>{{ $attempt->completed_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="ri-gamepad-line ri-3x text-muted mb-3"></i>
                        <p class="text-muted">No simulation attempts yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Time Spent Per Lesson -->
@if($timeSpent->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Time Spent Per Lesson</h5>
            </div>
            <div class="card-body">
                <div id="timeSpentChart"></div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="{{ asset('themes/sneat/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
$(document).ready(function() {
    @if($timeSpent->count() > 0)
    // Time Spent Chart
    const timeSpentData = @json($timeSpent);
    
    const timeSpentChart = new ApexCharts(document.querySelector("#timeSpentChart"), {
        series: [{
            name: 'Time (minutes)',
            data: timeSpentData.map(t => (t.time_seconds / 60).toFixed(2))
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 5,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val + " min";
            },
            offsetX: 30,
            style: {
                fontSize: '12px',
                colors: ["#304758"]
            }
        },
        xaxis: {
            categories: timeSpentData.map(t => t.lesson_title),
            title: {
                text: 'Time (minutes)'
            }
        },
        colors: ['#696cff'],
        tooltip: {
            y: {
                formatter: function(val) {
                    const hours = Math.floor(val / 60);
                    const minutes = Math.floor(val % 60);
                    const seconds = Math.floor((val % 1) * 60);
                    return hours > 0 
                        ? `${hours}h ${minutes}m ${seconds}s`
                        : `${minutes}m ${seconds}s`;
                }
            }
        }
    });
    timeSpentChart.render();
    @endif
});
</script>
@endsection