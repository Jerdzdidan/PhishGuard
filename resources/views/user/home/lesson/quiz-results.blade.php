@extends('user.layout.base')

@section('title')
QUIZ RESULTS - {{ $lesson->title }}
@endsection

@section('nav_title')
QUIZ RESULTS
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
<style>
.result-card {
    border-left: 4px solid #1E7F5C;
}
.result-card.correct {
    border-left-color: #28c76f;
    background: #f0fdf4;
}
.result-card.incorrect {
    border-left-color: #ea5455;
    background: #fff5f5;
}
.score-circle {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 auto;
}
.score-circle.passed {
    background: linear-gradient(135deg, #28c76f 0%, #48da89 100%);
    color: white;
}
.score-circle.failed {
    background: linear-gradient(135deg, #ea5455 0%, #f08182 100%);
    color: white;
}
</style>
@endsection

@section('content')
<div class="row g-6">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body text-center">
                <h4 class="mb-2">{{ $quiz->title }}</h4>
                <p class="text-muted mb-4">{{ $lesson->title }}</p>

                <div class="row justify-content-center">
                    <div class="col-md-3">
                        <div class="score-circle {{ $passed ? 'passed' : 'failed' }}">
                            {{ $score }}%
                        </div>
                        <h5 class="mt-3 mb-1">
                            @if($passed)
                                <span class="text-success">Passed!</span>
                            @else
                                <span class="text-danger">Failed</span>
                            @endif
                        </h5>
                        <p class="text-muted">Passing score: {{ $quiz->passing_score }}%</p>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h6 class="text-muted mb-1">Score</h6>
                            <h4 class="mb-0">{{ $earnedPoints }}/{{ $totalPoints }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h6 class="text-muted mb-1">Correct Answers</h6>
                            <h4 class="mb-0">{{ collect($results)->where('is_correct', true)->count() }}/{{ count($results) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h6 class="text-muted mb-1">Time Taken</h6>
                            <h4 class="mb-0">{{ gmdate('i:s', $quizAttempt->completion_time) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h6 class="text-muted mb-1">Completed</h6>
                            <h4 class="mb-0">{{ $quizAttempt->completed_at->format('M d, Y') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Detailed Results</h5>
            </div>
            <div class="card-body">
                @foreach($results as $index => $result)
                    <div class="card result-card mb-3 {{ $result['is_correct'] ? 'correct' : 'incorrect' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="mb-1">Question {{ $index + 1 }}</h6>
                                    <p class="mb-0">{{ $result['question_text'] }}</p>
                                </div>
                                <div class="text-end">
                                    @if($result['is_correct'])
                                        <span class="badge bg-success">
                                            <i class="ri-check-line me-1"></i> Correct
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="ri-close-line me-1"></i> Incorrect
                                        </span>
                                    @endif
                                    <div class="text-muted mt-1">
                                        <small>{{ $result['earned_points'] }}/{{ $result['points'] }} points</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <strong>Your Answer:</strong>
                                        <span class="badge bg-label-{{ $result['is_correct'] ? 'success' : 'danger' }} ms-2">
                                            {{ $result['user_answer'] ?? 'Not answered' }}
                                        </span>
                                    </div>
                                </div>
                                @if(!$result['is_correct'])
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Correct Answer:</strong>
                                            <span class="badge bg-label-success ms-2">
                                                {{ $result['correct_answer'] }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if($result['explanation'])
                                <div class="mt-3 p-3 bg-label-primary rounded">
                                    <strong><i class="ri-information-line me-2"></i>Explanation:</strong>
                                    <p class="mb-0 mt-2">{{ $result['explanation'] }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}" class="btn btn-label-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Back to Lesson
                    </a>
                    @if(!$passed)
                        <a href="{{ route('lessons.quiz.show', Crypt::encryptString($lesson->id)) }}" class="btn btn-primary">
                            <i class="ri-restart-line me-1"></i> Retake Quiz
                        </a>
                    @else
                        <a href="{{ route('user.home') }}" class="btn btn-success">
                            <i class="ri-home-line me-1"></i> Back to Home
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection