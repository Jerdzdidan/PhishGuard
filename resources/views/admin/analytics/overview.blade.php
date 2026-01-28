@extends('admin.layout.base')

@section('title')
ANALYTICS OVERVIEW
@endsection

@section('nav_title')
ANALYTICS OVERVIEW
@endsection

@section('style')
<style>
.stat-card {
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-5px);
}
.chart-container {
    position: relative;
    height: 500px;
}
</style>
@endsection

@section('body')
<div class="row mb-4">
    <!-- Date Range Filter -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.analytics.overview') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ri-filter-line me-1"></i> Apply Filter
                        </button>
                        <a href="{{ route('admin.analytics.overview') }}" class="btn btn-label-secondary">
                            <i class="ri-refresh-line me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-0 text-white">Total Users</h6>
                        <h2 class="mb-0 text-white">{{ $stats['total_users'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="bx bx-user bx-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-0 text-white">Active Lessons</h6>
                        <h2 class="mb-0 text-white">{{ $stats['total_lessons'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="bx bx-book bx-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-0 text-white">Quiz Attempts</h6>
                        <h2 class="mb-0 text-white">{{ $stats['total_quizzes'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="bx bx-file bx-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-0 text-white">Simulation Attempts</h6>
                        <h2 class="mb-0 text-white">{{ $stats['total_simulations'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="bx bx-game bx-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Completion Rates by Lesson -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lesson Completion Rates</h5>
            </div>
            <div class="card-body">
                <div id="lessonCompletionChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- Quiz vs Simulation Performance -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quiz Performance</h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <h6 class="text-muted">Average Score</h6>
                        <h3 class="text-primary">{{ $quizStats->avg_score ? round($quizStats->avg_score, 2) : 0 }}%</h3>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted">Avg. Time</h6>
                        <h3 class="text-info">{{ $quizStats->avg_time ? gmdate('i:s', $quizStats->avg_time) : '00:00' }}</h3>
                    </div>
                </div>
                <div id="quizPassFailChart"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Simulation Performance</h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <h6 class="text-muted">Average Score</h6>
                        <h3 class="text-success">{{ $simulationStats->avg_percentage ? round($simulationStats->avg_percentage, 2) : 0 }}%</h3>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted">Avg. Time</h6>
                        <h3 class="text-warning">{{ $simulationStats->avg_time ? gmdate('i:s', $simulationStats->avg_time) : '00:00' }}</h3>
                    </div>
                </div>
                <div id="simulationPassFailChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- Time Spent Per Lesson -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Average Time Spent Per Lesson</h5>
            </div>
            <div class="card-body">
                <div id="timeSpentChart"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('themes/sneat/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
$(document).ready(function() {
    // Lesson Completion Rate Chart
    const lessonCompletionData = @json($lessonCompletionRates);
    
    const lessonCompletionChart = new ApexCharts(document.querySelector("#lessonCompletionChart"), {
        series: [{
            name: 'Completion Rate',
            data: lessonCompletionData.map(l => l.completion_rate)
        }],
        chart: {
            type: 'bar',
            height: 500,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 5,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "%";
            },
            offsetY: -20,
            style: {
                fontSize: '12px',
                colors: ["#304758"]
            }
        },
        xaxis: {
            categories: lessonCompletionData.map(l => l.title),
            labels: {
                rotate: -30,
                style: {
                    fontSize: '9px'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Completion Rate (%)'
            },
            max: 100
        },
        colors: ['#1E7F5C'],
        tooltip: {
            y: {
                formatter: function(val, opts) {
                    const index = opts.dataPointIndex;
                    const data = lessonCompletionData[index];
                    return val.toFixed(1) + '% (' + data.completed + '/' + data.started + ' users)';
                }
            }
        }
    });
    lessonCompletionChart.render();

    // Quiz Pass/Fail Chart
    const quizPassFailChart = new ApexCharts(document.querySelector("#quizPassFailChart"), {
        series: [{{ $quizStats->passed_count }}, {{ $quizStats->failed_count }}],
        chart: {
            type: 'donut',
            height: 250
        },
        labels: ['Passed', 'Failed'],
        colors: ['#28c76f', '#ea5455'],
        legend: {
            position: 'bottom'
        },
        plotOptions: {
            pie: {
                donut: {
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Attempts',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        }
    });
    quizPassFailChart.render();

    // Simulation Pass/Fail Chart
    const simulationPassFailChart = new ApexCharts(document.querySelector("#simulationPassFailChart"), {
        series: [{{ $simulationStats->passed_count }}, {{ $simulationStats->failed_count }}],
        chart: {
            type: 'donut',
            height: 250
        },
        labels: ['Passed', 'Failed'],
        colors: ['#28c76f', '#ea5455'],
        legend: {
            position: 'bottom'
        },
        plotOptions: {
            pie: {
                donut: {
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Attempts',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        }
    });
    simulationPassFailChart.render();

    // Time Spent Chart
const timeSpentData = @json($timeSpentPerLesson);

console.log('Raw data:', timeSpentData);
console.log('Converted data:', timeSpentData.map(t => {
    const converted = t.avg_time_seconds / 60;
    console.log(`${t.title}: ${t.avg_time_seconds} seconds = ${converted} minutes`);
    return parseFloat(converted.toFixed(2));
}));

const timeSpentChart = new ApexCharts(document.querySelector("#timeSpentChart"), {
    series: [{
        name: 'Avg. Time (minutes)',
        data: timeSpentData.map(t => parseFloat((t.avg_time_seconds / 60).toFixed(2)))
    }],
    chart: {
        type: 'bar',
        height: 350,
        toolbar: { show: false }
    },
    plotOptions: {
        bar: {
            horizontal: true,
            borderRadius: 5
        }
    },
    dataLabels: {
        enabled: true,
        formatter: function (val) {
            return val.toFixed(1) + " min";
        }
    },
    xaxis: {
        categories: timeSpentData.map(t => t.title),
        title: {
            text: 'Time (minutes)'
        }
    },
    colors: ['#696cff'],
    tooltip: {
        y: {
            formatter: function(val) {
                const minutes = Math.floor(val);
                const seconds = Math.round((val % 1) * 60);
                return `${minutes}m ${seconds}s`;
            }
        }
    }
});
timeSpentChart.render();
});
</script>
@endsection