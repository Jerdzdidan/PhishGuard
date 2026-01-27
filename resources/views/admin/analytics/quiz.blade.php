{{-- @extends('admin.layout.base')

@section('title')
QUIZ ANALYTICS
@endsection

@section('nav_title')
QUIZ ANALYTICS
@endsection

@section('style')
<style>
.difficulty-badge {
    font-size: 0.875rem;
    padding: 0.35rem 0.65rem;
}
.question-card {
    transition: all 0.3s;
    border-left: 4px solid transparent;
}
.question-card.very-hard {
    border-left-color: #dc3545;
}
.question-card.hard {
    border-left-color: #fd7e14;
}
.question-card.medium {
    border-left-color: #ffc107;
}
.question-card.easy {
    border-left-color: #28a745;
}
</style>
@endsection

@section('body')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.analytics.quiz') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Select Lesson</label>
                        <select class="form-select" name="lesson_id" id="lessonSelect">
                            <option value="">All Lessons (Aggregated)</option>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ $lessonId == $lesson->id ? 'selected' : '' }}>
                                    {{ $lesson->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ri-filter-line me-1"></i> Apply Filter
                        </button>
                        <a href="{{ route('admin.analytics.quiz') }}" class="btn btn-label-secondary">
                            <i class="ri-refresh-line me-1"></i> Reset
                        </a>
                    </div>
                    <div class="col-md-4 d-flex align-items-end justify-content-end">
                        <a href="{{ route('admin.analytics.export', ['type' => 'quiz', 'lesson_id' => $lessonId]) }}" class="btn btn-success">
                            <i class="ri-download-line me-1"></i> Export CSV
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($lessonId)
    <!-- Per-Lesson Question Difficulty -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Question Difficulty Analysis - {{ $lessons->find($lessonId)->title }}</h5>
                    <span class="badge bg-label-primary">{{ count($questionDifficulty) }} Questions</span>
                </div>
                <div class="card-body">
                    @if(count($questionDifficulty) > 0)
                        <div id="questionDifficultyChart" class="mb-4"></div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Success Rate</th>
                                        <th>Attempts</th>
                                        <th>Correct</th>
                                        <th>Difficulty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($questionDifficulty as $question)
                                        <tr class="question-card {{ strtolower(str_replace(' ', '-', $question['difficulty_level'])) }}">
                                            <td>{{ Str::limit($question['question_text'], 80) }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 8px; width: 100px;">
                                                        <div class="progress-bar" role="progressbar" 
                                                             style="width: {{ $question['success_rate'] }}%"
                                                             aria-valuenow="{{ $question['success_rate'] }}" 
                                                             aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="fw-bold">{{ $question['success_rate'] }}%</span>
                                                </div>
                                            </td>
                                            <td>{{ $question['total_attempts'] }}</td>
                                            <td class="text-success">{{ $question['correct_attempts'] }}</td>
                                            <td>
                                                <span class="badge difficulty-badge 
                                                    @if($question['difficulty_level'] == 'Very Hard') bg-danger
                                                    @elseif($question['difficulty_level'] == 'Hard') bg-warning
                                                    @elseif($question['difficulty_level'] == 'Medium') bg-info
                                                    @else bg-success
                                                    @endif">
                                                    {{ $question['difficulty_level'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri-file-list-line ri-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No quiz data available for this lesson</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Aggregated Question Difficulty -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Questions Difficulty Analysis (Aggregated)</h5>
                    <span class="badge bg-label-primary">{{ count($aggregatedQuestions) }} Questions</span>
                </div>
                <div class="card-body">
                    @if(count($aggregatedQuestions) > 0)
                        <div id="aggregatedQuestionChart" class="mb-4"></div>
                        
                        <!-- Difficulty Distribution -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6 class="text-white mb-0">Easy</h6>
                                        <h3 class="text-white">{{ collect($aggregatedQuestions)->where('difficulty_level', 'Easy')->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6 class="text-white mb-0">Medium</h6>
                                        <h3 class="text-white">{{ collect($aggregatedQuestions)->where('difficulty_level', 'Medium')->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h6 class="text-white mb-0">Hard</h6>
                                        <h3 class="text-white">{{ collect($aggregatedQuestions)->where('difficulty_level', 'Hard')->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h6 class="text-white mb-0">Very Hard</h6>
                                        <h3 class="text-white">{{ collect($aggregatedQuestions)->where('difficulty_level', 'Very Hard')->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover" id="aggregatedTable">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Success Rate</th>
                                        <th>Attempts</th>
                                        <th>Correct</th>
                                        <th>Difficulty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($aggregatedQuestions as $question)
                                        <tr class="question-card {{ strtolower(str_replace(' ', '-', $question['difficulty_level'])) }}">
                                            <td>{{ Str::limit($question['question_text'], 80) }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 8px; width: 100px;">
                                                        <div class="progress-bar" role="progressbar" 
                                                             style="width: {{ $question['success_rate'] }}%"
                                                             aria-valuenow="{{ $question['success_rate'] }}" 
                                                             aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="fw-bold">{{ $question['success_rate'] }}%</span>
                                                </div>
                                            </td>
                                            <td>{{ $question['total_attempts'] }}</td>
                                            <td class="text-success">{{ $question['correct_attempts'] }}</td>
                                            <td>
                                                <span class="badge difficulty-badge 
                                                    @if($question['difficulty_level'] == 'Very Hard') bg-danger
                                                    @elseif($question['difficulty_level'] == 'Hard') bg-warning
                                                    @elseif($question['difficulty_level'] == 'Medium') bg-info
                                                    @else bg-success
                                                    @endif">
                                                    {{ $question['difficulty_level'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri-file-list-line ri-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No quiz data available yet</h6>
                        </div>
                    @endif
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
    @if($lessonId && count($questionDifficulty) > 0)
        // Per-lesson chart
        const questionData = @json($questionDifficulty);
        const questionChart = new ApexCharts(document.querySelector("#questionDifficultyChart"), {
            series: [{
                name: 'Success Rate',
                data: questionData.map(q => q.success_rate)
            }],
            chart: {
                type: 'bar',
                height: 400,
                toolbar: { show: true }
            },
            plotOptions: {
                bar: {
                    horizontal:true,
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
                categories: questionData.map((q, i) => Q${i + 1}),
                title: {
                    text: 'Success Rate (%)'
                },
                max: 100
            },
            colors: questionData.map(q => {
                if (q.success_rate >= 80) return '#28a745';
                if (q.success_rate >= 60) return '#17a2b8';
                if (q.success_rate >= 40) return '#ffc107';
                return '#dc3545';
            }),
            tooltip: {
                y: {
                    formatter: function(val, opts) {
                        const index = opts.dataPointIndex;
                        const data = questionData[index];
                        return val.toFixed(1) + '% (' + data.correct_attempts + '/' + data.total_attempts + ')';
                    }
                }
            },
            legend: {
                show: false
            }
        });
        questionChart.render();
    @endif
    @if(!$lessonId && count($aggregatedQuestions) > 0)
        // Aggregated chart - show top 20 hardest questions
        const aggregatedData = @json($aggregatedQuestions);
        const topHardest = aggregatedData.slice(0, 20);
        
        const aggregatedChart = new ApexCharts(document.querySelector("#aggregatedQuestionChart"), {
            series: [{
                name: 'Success Rate',
                data: topHardest.map(q => q.success_rate)
            }],
            chart: {
                type: 'bar',
                height: 500,
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
                categories: topHardest.map((q, i) => `Q${i + 1}`),
                title: {
                    text: 'Success Rate (%)'
                },
                max: 100
            },
            colors: topHardest.map(q => {
                if (q.success_rate >= 80) return '#28a745';
                if (q.success_rate >= 60) return '#17a2b8';
                if (q.success_rate >= 40) return '#ffc107';
                return '#dc3545';
            }),
            title: {
                text: 'Top 20 Hardest Questions',
                align: 'center'
            },
            tooltip: {
                y: {
                    formatter: function(val, opts) {
                        const index = opts.dataPointIndex;
                        const data = topHardest[index];
                        return val.toFixed(1) + '% (' + data.correct_attempts + '/' + data.total_attempts + ')';
                    }
                }
            },
            legend: {
                show: false
            }
        });
        aggregatedChart.render();

        // Initialize DataTable for aggregated view
        $('#aggregatedTable').DataTable({
            order: [[1, 'asc']], // Sort by success rate ascending (hardest first)
            pageLength: 25,
            language: {
                search: "Search questions:"
            }
        });
    @endif
});
</script>
@endsection --}}

@extends('admin.layout.base')

@section('title')
QUIZ ANALYTICS
@endsection

@section('nav_title')
QUIZ ANALYTICS
@endsection

@section('style')
<style>
.difficulty-badge {
    font-size: 0.875rem;
    padding: 0.35rem 0.65rem;
}
.question-card {
    transition: all 0.3s;
    border-left: 4px solid transparent;
}
.question-card.very-hard {
    border-left-color: #dc3545;
}
.question-card.hard {
    border-left-color: #fd7e14;
}
.question-card.medium {
    border-left-color: #ffc107;
}
.question-card.easy {
    border-left-color: #28a745;
}
</style>
@endsection

@section('body')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.analytics.quiz') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Select Lesson *</label>
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
                        <a href="{{ route('admin.analytics.quiz') }}" class="btn btn-label-secondary">
                            <i class="ri-refresh-line me-1"></i> Reset
                        </a>
                    </div>
                    @if($lessonId)
                    <div class="col-md-4 d-flex align-items-end justify-content-end">
                        <a href="{{ route('admin.analytics.export', ['type' => 'quiz', 'lesson_id' => $lessonId]) }}" class="btn btn-success">
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
    <!-- Lesson Question Difficulty -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Question Difficulty Analysis - {{ $lessons->find($lessonId)->title }}</h5>
                    <span class="badge bg-label-primary">{{ count($questionDifficulty) }} Questions</span>
                </div>
                <div class="card-body">
                    @if(count($questionDifficulty) > 0)
                        <div id="questionDifficultyChart" class="mb-4"></div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Success Rate</th>
                                        <th>Attempts</th>
                                        <th>Correct</th>
                                        <th>Difficulty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($questionDifficulty as $question)
                                        <tr class="question-card {{ strtolower(str_replace(' ', '-', $question['difficulty_level'])) }}">
                                            <td>{{ Str::limit($question['question_text'], 80) }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 8px; width: 100px;">
                                                        <div class="progress-bar" role="progressbar" 
                                                             style="width: {{ $question['success_rate'] }}%"
                                                             aria-valuenow="{{ $question['success_rate'] }}" 
                                                             aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="fw-bold">{{ $question['success_rate'] }}%</span>
                                                </div>
                                            </td>
                                            <td>{{ $question['total_attempts'] }}</td>
                                            <td class="text-success">{{ $question['correct_attempts'] }}</td>
                                            <td>
                                                <span class="badge difficulty-badge 
                                                    @if($question['difficulty_level'] == 'Very Hard') bg-danger
                                                    @elseif($question['difficulty_level'] == 'Hard') bg-warning
                                                    @elseif($question['difficulty_level'] == 'Medium') bg-info
                                                    @else bg-success
                                                    @endif">
                                                    {{ $question['difficulty_level'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri-file-list-line ri-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No quiz data available for this lesson</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    <!-- No Lesson Selected -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ri-bar-chart-line ri-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Select a Lesson to View Analytics</h5>
                    <p class="text-muted mb-0">Choose a lesson from the dropdown above to see detailed quiz question analytics.</p>
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
    @if($lessonId && count($questionDifficulty) > 0)
        // Lesson-specific chart
        const questionData = @json($questionDifficulty);
        const questionChart = new ApexCharts(document.querySelector("#questionDifficultyChart"), {
            series: [{
                name: 'Success Rate',
                data: questionData.map(q => q.success_rate)
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
                categories: questionData.map((q, i) => `Q${i + 1}`),
                title: {
                    text: 'Success Rate (%)'
                },
                max: 100
            },
            colors: questionData.map(q => {
                if (q.success_rate >= 80) return '#28a745';
                if (q.success_rate >= 60) return '#17a2b8';
                if (q.success_rate >= 40) return '#ffc107';
                return '#dc3545';
            }),
            tooltip: {
                y: {
                    formatter: function(val, opts) {
                        const index = opts.dataPointIndex;
                        const data = questionData[index];
                        return val.toFixed(1) + '% (' + data.correct_attempts + '/' + data.total_attempts + ')';
                    }
                }
            },
            legend: {
                show: false
            }
        });
        questionChart.render();
    @endif
});
</script>
@endsection
