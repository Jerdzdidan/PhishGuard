<!-- resources/views/admin/analytics/simulation.blade.php -->
@extends('admin.layout.base')

@section('title')
SIMULATION ANALYTICS
@endsection

@section('nav_title')
SIMULATION ANALYTICS
@endsection

@section('style')
<style>
.difficulty-badge {
    font-size: 0.875rem;
    padding: 0.35rem 0.65rem;
}
.scenario-card {
    transition: all 0.3s;
    border-left: 4px solid transparent;
}
.scenario-card.very-hard {
    border-left-color: #dc3545;
}
.scenario-card.hard {
    border-left-color: #fd7e14;
}
.scenario-card.medium {
    border-left-color: #ffc107;
}
.scenario-card.easy {
    border-left-color: #28a745;
}
.ctr-gauge {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 0 auto;
}
</style>
@endsection

@section('body')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.analytics.simulation') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Select Lesson <span class="text-danger">*</span></label>
                        <select class="form-select" name="lesson_id" id="lessonSelect" required>
                            <option value="">-- Select a Lesson --</option>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ $lessonId == $lesson->id ? 'selected' : '' }}>
                                    {{ $lesson->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ri-filter-line me-1"></i> View Analytics
                        </button>
                        <a href="{{ route('admin.analytics.simulation') }}" class="btn btn-label-secondary">
                            <i class="ri-refresh-line me-1"></i> Reset
                        </a>
                    </div>
                    @if($lessonId)
                    <div class="col-md-4 d-flex align-items-end justify-content-end">
                        <a href="{{ route('admin.analytics.export', ['type' => 'simulation', 'lesson_id' => $lessonId]) }}" class="btn btn-success">
                            <i class="ri-download-line me-1"></i> Export CSV
                        </a>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

@if($lessonId)
    <!-- CTR (Click-Through Rate) Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Click-Through Rate (CTR) Analysis</h5>
                    <p class="text-muted mb-0">Measures user engagement with action menus</p>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <div class="ctr-gauge">
                                <canvas id="ctrGauge"></canvas>
                            </div>
                            <h3 class="mt-3 mb-0">{{ $ctrData['ctr'] }}%</h3>
                            <p class="text-muted">Click-Through Rate</p>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h6 class="text-white mb-0">Total Clicks</h6>
                                            <h3 class="text-white mb-0">{{ $ctrData['total_clicks'] }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h6 class="text-white mb-0">Action Menu Opens</h6>
                                            <h3 class="text-white mb-0">{{ $ctrData['action_menu_clicks'] }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h6 class="text-white mb-0">Engagement Rate</h6>
                                            <h3 class="text-white mb-0">{{ $ctrData['ctr'] }}%</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Per-Lesson Scenario Difficulty -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Scenario Difficulty Analysis - {{ $lessons->find($lessonId)->title }}</h5>
                        <p class="text-muted mb-0">Lower success rate = harder scenario</p>
                    </div>
                    <span class="badge bg-label-primary">{{ count($scenarioDifficulty) }} Scenarios</span>
                </div>
                <div class="card-body">
                    @if(count($scenarioDifficulty) > 0)
                        <div id="scenarioDifficultyChart" class="mb-4"></div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Scenario</th>
                                        <th>Success Rate</th>
                                        <th>Attempts</th>
                                        <th>Correct</th>
                                        <th>Difficulty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scenarioDifficulty as $scenario)
                                        <tr class="scenario-card {{ strtolower(str_replace(' ', '-', $scenario['difficulty_level'])) }}">
                                            <td>{{ $scenario['scenario'] }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 8px; width: 100px;">
                                                        <div class="progress-bar" role="progressbar" 
                                                             style="width: {{ $scenario['success_rate'] }}%"
                                                             aria-valuenow="{{ $scenario['success_rate'] }}" 
                                                             aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="fw-bold">{{ $scenario['success_rate'] }}%</span>
                                                </div>
                                            </td>
                                            <td>{{ $scenario['total_attempts'] }}</td>
                                            <td class="text-success">{{ $scenario['correct_attempts'] }}</td>
                                            <td>
                                                <span class="badge difficulty-badge 
                                                    @if($scenario['difficulty_level'] == 'Very Hard') bg-danger
                                                    @elseif($scenario['difficulty_level'] == 'Hard') bg-warning
                                                    @elseif($scenario['difficulty_level'] == 'Medium') bg-info
                                                    @else bg-success
                                                    @endif">
                                                    {{ $scenario['difficulty_level'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri-game-line ri-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No simulation data available for this lesson</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Placeholder State -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="avatar avatar-xl mb-3 mx-auto">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ri-bar-chart-line ri-48px"></i>
                        </span>
                    </div>
                    <h4 class="mb-2">Select a Lesson to View Analytics</h4>
                    <p class="text-muted mb-0">Choose a lesson from the filter above to see detailed simulation analytics for that lesson</p>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script src="{{ asset('themes/sneat/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    @if($lessonId)
        // CTR Gauge Chart
        const ctrValue = {{ $ctrData['ctr'] }};
        const ctx = document.getElementById('ctrGauge');
        
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [ctrValue, 100 - ctrValue],
                        backgroundColor: [
                            ctrValue >= 70 ? '#28c76f' : ctrValue >= 50 ? '#ffc107' : '#ea5455',
                            '#e9ecef'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    circumference: 180,
                    rotation: 270,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });
        }

        @if(count($scenarioDifficulty) > 0)
            // Per-lesson scenario chart
            const scenarioData = @json($scenarioDifficulty);
            const scenarioChart = new ApexCharts(document.querySelector("#scenarioDifficultyChart"), {
                series: [{
                    name: 'Success Rate',
                    data: scenarioData.map(s => s.success_rate)
                }],
                chart: {
                    type: 'bar',
                    height: 400,
                    toolbar: { show: true }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 4,
                        distributed: true
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val.toFixed(1) + "%";
                    }
                },
                xaxis: {
                    categories: scenarioData.map((s, i) => `S${i + 1}`),
                    title: {
                        text: 'Success Rate (%)'
                    },
                    max: 100
                },
                colors: scenarioData.map(s => {
                    if (s.success_rate >= 80) return '#28a745';
                    if (s.success_rate >= 60) return '#17a2b8';
                    if (s.success_rate >= 40) return '#ffc107';
                    return '#dc3545';
                }),
                tooltip: {
                    y: {
                        formatter: function(val, opts) {
                            const index = opts.dataPointIndex;
                            const data = scenarioData[index];
                            return val.toFixed(1) + '% (' + data.correct_attempts + '/' + data.total_attempts + ')';
                        }
                    }
                },
                legend: {
                    show: false
                }
            });
            scenarioChart.render();
        @endif
    @endif
});
</script>
@endsection
