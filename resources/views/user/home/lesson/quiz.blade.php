@extends('user.layout.base')

@section('title')
QUIZ - {{ $lesson->title }}
@endsection

@section('nav_title')
QUIZ - {{ $lesson->title }}
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
<style>
.answer-option {
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
    border: 2px solid #e0e0e0;
    cursor: pointer;
    transition: all 0.3s ease;
}
.answer-option:hover {
    border-color: #696cff;
    background: #f8f9ff;
}
.answer-option.selected {
    border-color: #696cff;
    background: #f8f9ff;
}
.question-card {
    border-left: 4px solid #1E7F5C;
}
.timer {
    font-size: 1.5rem;
    font-weight: 600;
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
                        <h4 class="mb-1">{{ $quiz->title }}</h4>
                        <p class="mb-0">{{ $quiz->description }}</p>
                    </div>
                    <div class="text-center">
                        <div class="timer text-primary" id="timer">00:00</div>
                        <small class="text-muted">Time Elapsed</small>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form id="quizForm" action="{{ route('lessons.quiz.submit', Crypt::encryptString($lesson->id)) }}" method="POST">
                    @csrf
                    <input type="hidden" name="time_taken" id="timeTaken" value="0">

                    @foreach($questions as $index => $question)
                        <div class="card question-card mb-4">
                            <div class="card-body">
                                <h6 class="mb-3">Question {{ $index + 1 }} ({{ $question->points }} point{{ $question->points > 1 ? 's' : '' }})</h6>
                                <p class="mb-4">{{ $question->question_text }}</p>

                                <div class="answers">
                                    @foreach($question->answers as $answer)
                                        <div class="answer-option" data-question="{{ $question->id }}" data-option="{{ $answer->option_letter }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <input type="radio" 
                                                       class="form-check-input" 
                                                       name="answers[{{ $question->id }}]" 
                                                       value="{{ $answer->option_letter }}" 
                                                       id="q{{ $question->id }}_{{ $answer->option_letter }}"
                                                       required>
                                                <label class="flex-grow-1 mb-0" for="q{{ $question->id }}_{{ $answer->option_letter }}">
                                                    <span class="badge bg-label-primary me-2">{{ $answer->option_letter }}</span>
                                                    {{ $answer->answer_text }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}" class="btn btn-label-secondary">
                                    <i class="ri-arrow-left-line me-1"></i> Back to Lesson
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-send-plane-fill me-1"></i> Submit Quiz
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
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
                        <label for="defaultCheck1" class="form-check-label ms-4">
                        <span class="mb-0 h6 text-primary">1. Lesson</span>
                        <small class="text-body d-block">content</small>
                        </label>
                    </a>
                </div>
                @if ($lesson->quiz && $lesson->quiz->is_active)
                    <hr>
                    <div class="mb-4">
                        <label for="defaultCheck2" class="ms-4">
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
                <h6 class="mb-3">Quiz Information</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Questions</span>
                    <strong>{{ $questions->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Points</span>
                    <strong>{{ $questions->sum('points') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Passing Score</span>
                    <strong>{{ $quiz->passing_score }}%</strong>
                </div>
                <hr>
                <div class="alert alert-info mb-0">
                    <i class="ri-information-line me-2"></i>
                    <small>Answer all questions before submitting. You can review your answers before final submission.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let startTime = Date.now();
    let timerInterval;

    // Timer
    function updateTimer() {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;
        $('#timer').text(
            String(minutes).padStart(2, '0') + ':' + 
            String(seconds).padStart(2, '0')
        );
        $('#timeTaken').val(elapsed);
    }

    timerInterval = setInterval(updateTimer, 1000);

    // Answer selection
    $('.answer-option').on('click', function() {
        const questionId = $(this).data('question');
        const option = $(this).data('option');
        
        // Remove selected class from all options for this question
        $(`.answer-option[data-question="${questionId}"]`).removeClass('selected');
        
        // Add selected class to clicked option
        $(this).addClass('selected');
        
        // Check the radio button
        $(`#q${questionId}_${option}`).prop('checked', true);
    });

    // Form submission confirmation
    $('#quizForm').on('submit', function(e) {
        const totalQuestions = {{ $questions->count() }};
        const answeredQuestions = $('input[type="radio"]:checked').length;
        
        if (answeredQuestions < totalQuestions) {
            e.preventDefault();
            if (!confirm(`You have only answered ${answeredQuestions} out of ${totalQuestions} questions. Do you want to submit anyway?`)) {
                return false;
            }
        }

        if (!confirm('Are you sure you want to submit your quiz? This action cannot be undone.')) {
            e.preventDefault();
            return false;
        }

        clearInterval(timerInterval);
    });

    // Prevent accidental page leave
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = '';
    });
});
</script>
@endsection