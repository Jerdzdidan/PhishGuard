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
.ready-screen {
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection

@section('content')
<div class="row g-6">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <!-- Ready Screen -->
                <div id="readyScreen" class="ready-screen">
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="ri-questionnaire-line" style="font-size: 4rem; color: #696cff;"></i>
                        </div>
                        <h3 class="mb-3">{{ $quiz->title }}</h3>
                        <p class="text-muted mb-4">{{ $quiz->description }}</p>
                        
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="text-muted mb-1">Questions</h6>
                                    <h4 class="mb-0">{{ $questions->count() }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="text-muted mb-1">Passing Score</h6>
                                    <h4 class="mb-0">{{ $quiz->passing_score }}%</h4>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-4">Are you ready to take the quiz?</h5>
                        <button type="button" class="btn btn-primary btn-lg" id="startQuizBtn">
                            <i class="ri-play-line me-2"></i> Start Quiz
                        </button>
                        <div class="mt-3">
                            <a href="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}" class="btn btn-label-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Back to Lesson
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quiz Screen -->
                <div id="quizScreen" class="d-none">
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

                    <form id="quizForm">
                        @csrf
                        <input type="hidden" name="time_taken" id="timeTaken" value="0">

                        @foreach($questions as $index => $question)
                            <div class="card question-card mb-4">
                                <div class="card-body">
                                    <h6 class="mb-3">Question {{ $index + 1 }} ({{ $question->points }} point{{ $question->points > 1 ? 's' : '' }})</h6>
                                    <p class="mb-4">{{ $question->question_text }}</p>

                                    <div class="answers">
                                        @foreach($question->answers as $answer)
                                            <div class="answer-option" 
                                                 data-question="{{ $question->id }}" 
                                                 data-option="{{ $answer->option_letter }}">
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
                                    <button type="submit" class="btn btn-primary" id="submitQuizBtn">
                                        <i class="ri-send-plane-fill me-1"></i> Submit Quiz
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
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
                                <label class="ms-4">
                                    <span class="mb-0 h6 text-primary">2. Quiz</span>
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
                    <span>Questions Shown</span>
                    <strong>5</strong>
                </div>
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
                    <small>5 random questions will be shown from the question bank. Answer all questions before submitting.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let startTime;
    let timerInterval;
    
    $('#startQuizBtn').on('click', function() {
        $('#readyScreen').addClass('d-none');
        $('#quizScreen').removeClass('d-none');
        startQuiz();
    });

    function startQuiz() {
        startTime = Date.now();
        timerInterval = setInterval(updateTimer, 1000);
    }

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

    // Answer selection
    $('.answer-option').on('click', function() {
        const questionId = $(this).data('question');
        const option = $(this).data('option');
        
        $(`.answer-option[data-question="${questionId}"]`).removeClass('selected');
        $(this).addClass('selected');
        $(`#q${questionId}_${option}`).prop('checked', true);
    });

    $('#quizForm').on('submit', function(e) {
        e.preventDefault();
        
        const totalQuestions = {{ $questions->count() }};
        const answeredQuestions = $('input[type="radio"]:checked').length;
        
        if (answeredQuestions < totalQuestions) {
            Swal.fire({
                title: 'Incomplete Quiz',
                html: `You have only answered <strong>${answeredQuestions}</strong> out of <strong>${totalQuestions}</strong> questions.<br><br>Do you want to submit anyway?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#F27474",
                cancelButtonColor: "#91a8b3ff",
                confirmButtonText: "Submit Anyway",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    showSubmitConfirmation();
                }
            });
        } else {
            showSubmitConfirmation();
        }
    });

    function showSubmitConfirmation() {
        Swal.fire({
            icon: 'warning',
            title: 'Confirm Submission',
            html: 'Are you sure you want to submit your quiz?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#F8BB86',
            cancelButtonColor: '#91a8b3ff',
        }).then((result) => {
            if (result.isConfirmed) {
                clearInterval(timerInterval);
                submitQuiz();
            }
        });
    }

    function submitQuiz() {
        const formData = new FormData($('#quizForm')[0]);
        
        $.ajax({
            url: '{{ route("lessons.quiz.submit", Crypt::encryptString($lesson->id)) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Quiz Submitted!',
                        text: response.message,
                        confirmButtonColor: '#696cff',
                    }).then(() => {
                        window.location.href = response.redirect_url;
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to submit quiz. Please try again.',
                    confirmButtonColor: '#ea5455',
                });
            }
        });
    }

    let quizInProgress = false;
    $('#startQuizBtn').on('click', function() {
        quizInProgress = true;
    });
    $('#quizForm').on('submit', function() {
        quizInProgress = false;
    });
    window.addEventListener('beforeunload', function(e) {
        if (quizInProgress && !$('#quizScreen').hasClass('d-none')) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});
</script>
@endsection