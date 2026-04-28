@extends('user.layout.base')

@section('title')
Assessment Results
@endsection

@section('nav_title')
ASSESSMENT RESULTS
@endsection

@section('style')
<style>
    .score-circle {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 2.5rem;
        font-weight: 700;
    }
    .comparison-card {
        text-align: center;
    }
    .improvement-badge {
        font-size: 1.2rem;
        padding: 8px 16px;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body text-center py-5">
                <h4 class="mb-2">
                    {{ $attempt->type === 'pre' ? 'Pre-Assessment' : 'Post-Assessment' }} Results
                </h4>
                <p class="text-muted">{{ $attempt->section->name }}</p>

                @php
                    $percentage = $attempt->percentage;
                    $scoreColor = $percentage >= 80 ? '#28c76f' : ($percentage >= 60 ? '#ff9f43' : '#ea5455');
                @endphp

                <div class="score-circle mb-4" style="border: 6px solid {{ $scoreColor }}; color: {{ $scoreColor }};">
                    {{ $percentage }}%
                </div>

                <h5>{{ $attempt->score }} / {{ $attempt->total_questions }} correct</h5>
                <p class="text-muted">Completed in {{ gmdate('i:s', $attempt->completion_time) }}</p>

                {{-- Pre vs Post Comparison --}}
                @if($preAttempt)
                <hr class="my-4">
                <h5 class="mb-3"><i class="bx bx-stats me-1"></i> Pre vs Post Comparison</h5>
                <div class="row g-3 justify-content-center">
                    <div class="col-md-4">
                        <div class="card border comparison-card">
                            <div class="card-body">
                                <small class="text-muted d-block mb-2">Pre-Assessment</small>
                                <h3 class="mb-0" style="color: #ff9f43;">{{ $preAttempt->percentage }}%</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border comparison-card">
                            <div class="card-body">
                                <small class="text-muted d-block mb-2">Post-Assessment</small>
                                <h3 class="mb-0" style="color: #28c76f;">{{ $attempt->percentage }}%</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        @php
                            $improvement = $attempt->percentage - $preAttempt->percentage;
                            $improvementColor = $improvement >= 0 ? 'success' : 'danger';
                            $improvementIcon = $improvement >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt';
                        @endphp
                        <div class="card border comparison-card">
                            <div class="card-body">
                                <small class="text-muted d-block mb-2">Improvement</small>
                                <h3 class="mb-0 text-{{ $improvementColor }}">
                                    <i class="bx {{ $improvementIcon }}"></i>
                                    {{ abs($improvement) }}%
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Answer Review --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Answer Review</h5>
            </div>
            <div class="card-body">
                @foreach($attempt->answers_data as $index => $answer)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong>Q{{ $index + 1 }}. {{ $answer['question_text'] }}</strong>
                        @if($answer['is_correct'])
                            <span class="badge bg-success">✓ Correct</span>
                        @else
                            <span class="badge bg-danger">✕ Wrong</span>
                        @endif
                    </div>
                    <p class="mb-1">
                        <small><strong>Your answer:</strong> {{ $answer['selected_answer_text'] ?? 'Not answered' }}</small>
                    </p>
                    @if(!$answer['is_correct'])
                    <p class="mb-0 text-success">
                        <small><strong>Correct answer:</strong> {{ $answer['correct_answer_text'] }}</small>
                    </p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="text-center mb-4">
            @if($attempt->type === 'post')
                <a href="{{ route('certificate.view') }}" class="btn btn-success me-2">
                    <i class="bx bx-award me-1"></i> Claim Certificate
                </a>
            @endif
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="bx bx-home me-1"></i> Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
