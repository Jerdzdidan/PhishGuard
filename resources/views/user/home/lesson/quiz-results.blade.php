@extends('user.layout.base')

@section('title')
QUIZ RESULTS - {{ $lesson->title }}
@endsection

@section('nav_title')
QUIZ RESULTS - {{ $lesson->title }}
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
<style>
.answer-option {
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
    border: 2px solid #e0e0e0;
    transition: all 0.3s ease;
}
.answer-option.user-answer {
    border-color: #ea5455;
    background: #fff5f5;
}
.answer-option.user-answer.correct {
    border-color: #28c76f;
    background: #f0fdf4;
}
.question-card {
    border-left: 4px solid #1E7F5C;
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
</style>
@endsection

@section('content')
<div class="row g-6">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="score-summary text-center {{ $quizAttempt->passed ? 'passed' : 'failed' }}">
                    <h3 class="mb-3">
                        @if($quizAttempt->passed)
                            <i class="ri-checkbox-circle-line me-2"></i>
                            Congratulations! You Passed!
                        @else
                            <i class="ri-information-line me-2"></i>
                            Quiz Complete
                        @endif
                    </h3>
                    <div class="display-4 mb-3">{{ $quizAttempt->score }}%</div>
                    <p class="mb-0">
                        @if($quizAttempt->passed)
                            You've successfully passed the quiz with a score of {{ $quizAttempt->score }}%!
                        @else
                            You scored {{ $quizAttempt->score }}%. You need {{ $quiz->passing_score }}% to pass. Keep trying!
                        @endif
                    </p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted mb-1">Score</h6>
                            <h4 class="mb-0">{{ $earnedPoints }}/{{ $totalPoints }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted mb-1">Correct</h6>
                            <h4 class="mb-0 text-success">{{ $correctAnswers }}/{{ $totalQuestions }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted mb-1">Time</h6>
                            <h4 class="mb-0">
                                {{ sprintf('%02d:%02d', floor($quizAttempt->completion_time / 60), $quizAttempt->completion_time % 60) }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted mb-1">Passing Score</h6>
                            <h4 class="mb-0">{{ $quiz->passing_score }}%</h4>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3">Review Your Answers</h5>
                @foreach($results as $index => $result)
                    <div class="card question-card mb-4">
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
                            
                            <div class="answers">
                                @foreach($result['answers'] as $answer)
                                    @php
                                        $isUserAnswer = $answer['option_letter'] === $result['user_answer'];
                                        $isCorrect = $answer['is_correct'];
                                        $optionClass = '';
                                        
                                        if ($isUserAnswer) {
                                            $optionClass = 'user-answer';
                                            if ($isCorrect) {
                                                $optionClass .= ' correct';
                                            }
                                        }
                                    @endphp
                                    
                                    <div class="answer-option {{ $optionClass }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge {{ $isUserAnswer ? ($isCorrect ? 'bg-success' : 'bg-danger') : 'bg-label-secondary' }} me-2">
                                                {{ $answer['option_letter'] }}
                                            </span>
                                            <span class="flex-grow-1">{{ $answer['answer_text'] }}</span>
                                            @if($isUserAnswer)
                                                @if($isCorrect)
                                                    <i class="ri-check-line text-success" style="font-size: 1.5rem;"></i>
                                                @else
                                                    <i class="ri-close-line text-danger" style="font-size: 1.5rem;"></i>
                                                @endif
                                            @endif
                                        </div>
                                        
                            
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}" class="btn btn-label-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Back to Lesson
                            </a>
                            <button type="button" class="btn btn-primary" id="retakeQuizBtn">
                                <i class="ri-restart-line me-1"></i> Retake Quiz
                            </button>
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
                                    <span class="mb-0 h6 text-primary">1. Lesson</span>
                                    <small class="text-body d-block">content</small>
                                </label>
                            </a>
                        </div>
                        @if ($lesson->quiz && $lesson->quiz->is_active)
                            <hr>
                            <div class="mb-4">
                                <label class="ms-4">
                                    <span class="mb-0 h6">2. Quiz</span>
                                    <small class="text-body d-block">assessment</small>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card stick-top">
            <div class="card-body">
                <h6 class="mb-3">Quiz Summary</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Status</span>
                    <strong class="{{ $quizAttempt->passed ? 'text-success' : 'text-danger' }}">
                        {{ $quizAttempt->passed ? 'Passed' : 'Failed' }}
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Score</span>
                    <strong>{{ $quizAttempt->score }}%</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Correct Answers</span>
                    <strong>{{ $correctAnswers }}/{{ $totalQuestions }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Points Earned</span>
                    <strong>{{ $earnedPoints }}/{{ $totalPoints }}</strong>
                </div>
                <hr>
                <div class="alert alert-info mb-0">
                    <i class="ri-information-line me-2"></i>
                    <small>Review your answers above. You can retake the quiz to improve your score.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#retakeQuizBtn').on('click', function() {
        Swal.fire({
            title: 'Retake Quiz?',
            html: 'Are you sure you want to retake this quiz? Your current attempt will be saved, and a new attempt will be recorded.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Retake Quiz',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#91a8b3ff',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("lessons.quiz.retake", Crypt::encryptString($lesson->id)) }}';
            }
        });
    });
});
</script>
@endsection