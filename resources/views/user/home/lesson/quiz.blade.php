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
.answer-option:hover:not(.disabled) {
    border-color: #696cff;
    background: #f8f9ff;
}
.answer-option.selected {
    border-color: #696cff;
    background: #f8f9ff;
}
.answer-option.correct {
    border-color: #28c76f;
    background: #f0fdf4;
}
.answer-option.incorrect {
    border-color: #ea5455;
    background: #fff5f5;
}
.answer-option.disabled {
    cursor: not-allowed;
    opacity: 0.7;
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

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

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
                                                 data-option="{{ $answer->option_letter }}"
                                                 data-correct="{{ $answer->is_correct ? 'true' : 'false' }}">
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
                                                    <i class="ri-check-line text-success d-none correct-icon" style="font-size: 1.5rem;"></i>
                                                    <i class="ri-close-line text-danger d-none incorrect-icon" style="font-size: 1.5rem;"></i>
                                                </div>
                                                @if($answer->explanation)
                                                    <div class="explanation-text d-none mt-3 p-3 bg-label-primary rounded">
                                                        <strong><i class="ri-information-line me-2"></i>Explanation:</strong>
                                                        <p class="mb-0 mt-2">{{ $answer->explanation }}</p>
                                                    </div>
                                                @endif
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

                <!-- Results Screen -->
                <div id="resultsScreen" class="d-none">
                    <div class="score-summary text-center" id="scoreSummary">
                        <h3 class="mb-3" id="resultTitle"></h3>
                        <div class="display-4 mb-3" id="scoreDisplay"></div>
                        <p class="mb-0" id="scoreMessage"></p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-1">Score</h6>
                                <h4 class="mb-0" id="pointsDisplay"></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-1">Correct</h6>
                                <h4 class="mb-0 text-success" id="correctDisplay"></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-1">Time</h6>
                                <h4 class="mb-0" id="timeDisplay"></h4>
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
                    <div id="reviewQuestions"></div>

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
                    <small id="quizInfoText">Answer all questions before submitting. You can review your answers before final submission.</small>
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
    let quizData = @json($questions);
    let totalPoints = {{ $questions->sum('points') }};
    let passingScore = {{ $quiz->passing_score }};
    
    // Start Quiz Button
    $('#startQuizBtn').on('click', function() {
        $('#readyScreen').addClass('d-none');
        $('#quizScreen').removeClass('d-none');
        startQuiz();
    });

    // Retake Quiz Button
    $('#retakeQuizBtn').on('click', function() {
        location.reload();
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
        if ($(this).hasClass('disabled')) return;
        
        const questionId = $(this).data('question');
        const option = $(this).data('option');
        
        $(`.answer-option[data-question="${questionId}"]`).removeClass('selected');
        $(this).addClass('selected');
        $(`#q${questionId}_${option}`).prop('checked', true);
    });

    // Form submission
    $('#quizForm').on('submit', function(e) {
        e.preventDefault();
        
        const totalQuestions = {{ $questions->count() }};
        const answeredQuestions = $('input[type="radio"]:checked').length;
        
        if (answeredQuestions < totalQuestions) {
            // Show incomplete quiz warning
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
        const answers = {};
        
        $('input[type="radio"]:checked').each(function() {
            const name = $(this).attr('name');
            const questionId = name.match(/\d+/)[0];
            answers[questionId] = $(this).val();
        });

        // Calculate results
        let earnedPoints = 0;
        let correctAnswers = 0;
        const results = [];

        quizData.forEach(function(question) {
            const userAnswer = answers[question.id];
            const correctAnswer = question.answers.find(a => a.is_correct);
            const isCorrect = userAnswer === correctAnswer.option_letter;

            if (isCorrect) {
                earnedPoints += question.points;
                correctAnswers++;
            }

            results.push({
                question_id: question.id,
                question_text: question.question_text,
                user_answer: userAnswer,
                correct_answer: correctAnswer.option_letter,
                is_correct: isCorrect,
                points: question.points,
                earned_points: isCorrect ? question.points : 0
            });
        });

        const score = totalPoints > 0 ? Math.round((earnedPoints / totalPoints) * 100) : 0;
        const passed = score >= passingScore;
        const timeTaken = parseInt($('#timeTaken').val());

        // Save to database
        saveQuizAttempt(answers, timeTaken, score, passed);

        // Show results
        showResults(score, passed, earnedPoints, correctAnswers, timeTaken, results);
    }

    function saveQuizAttempt(answers, timeTaken, score, passed) {
        $.ajax({
            url: '{{ route("lessons.quiz.submit", Crypt::encryptString($lesson->id)) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                answers: answers,
                time_taken: timeTaken
            },
            success: function(response) {
                console.log('Quiz saved successfully');
            },
            error: function(xhr) {
                console.error('Error saving quiz:', xhr);
            }
        });
    }

    function showResults(score, passed, earnedPoints, correctAnswers, timeTaken, results) {
        $('#quizScreen').addClass('d-none');
        $('#resultsScreen').removeClass('d-none');

        // Update summary
        const $summary = $('#scoreSummary');
        $summary.removeClass('passed failed').addClass(passed ? 'passed' : 'failed');
        
        $('#resultTitle').text(passed ? 'Congratulations! You Passed!' : 'Quiz Complete');
        $('#scoreDisplay').html(`${score}%`);
        $('#scoreMessage').text(passed ? 
            `You've successfully passed the quiz with a score of ${score}%!` : 
            `You scored ${score}%. You need ${passingScore}% to pass. Keep trying!`
        );

        $('#pointsDisplay').text(`${earnedPoints}/${totalPoints}`);
        $('#correctDisplay').text(`${correctAnswers}/${quizData.length}`);
        
        const minutes = Math.floor(timeTaken / 60);
        const seconds = timeTaken % 60;
        $('#timeDisplay').text(`${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`);

        // Show review
        showReview(results);
        
        // Update info text
        $('#quizInfoText').text('Review your answers below. You can retake the quiz if you want to improve your score.');
    }

    function showReview(results) {
        const $review = $('#reviewQuestions');
        $review.empty();

        results.forEach(function(result, index) {
            const question = quizData.find(q => q.id == result.question_id);
            
            let html = `
                <div class="card question-card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-1">Question ${index + 1}</h6>
                                <p class="mb-0">${result.question_text}</p>
                            </div>
                            <div class="text-end">
                                ${result.is_correct ? 
                                    '<span class="badge bg-success"><i class="ri-check-line me-1"></i> Correct</span>' :
                                    '<span class="badge bg-danger"><i class="ri-close-line me-1"></i> Incorrect</span>'
                                }
                                <div class="text-muted mt-1">
                                    <small>${result.earned_points}/${result.points} points</small>
                                </div>
                            </div>
                        </div>
                        <div class="answers">
            `;

            question.answers.forEach(function(answer) {
                const isUserAnswer = answer.option_letter === result.user_answer;
                const isCorrect = answer.is_correct;
                
                let optionClass = '';
                let icon = '';
                
                if (isCorrect) {
                    optionClass = 'correct';
                    icon = '<i class="ri-check-line text-success" style="font-size: 1.5rem;"></i>';
                } else if (isUserAnswer && !isCorrect) {
                    optionClass = 'incorrect';
                    icon = '<i class="ri-close-line text-danger" style="font-size: 1.5rem;"></i>';
                }

                html += `
                    <div class="answer-option ${optionClass} disabled">
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge ${isCorrect ? 'bg-success' : (isUserAnswer ? 'bg-danger' : 'bg-label-secondary')} me-2">${answer.option_letter}</span>
                            <span class="flex-grow-1">${answer.answer_text}</span>
                            ${icon}
                        </div>
                        ${answer.explanation && (isCorrect || isUserAnswer) ? `
                            <div class="mt-3 p-3 bg-label-primary rounded">
                                <strong><i class="ri-information-line me-2"></i>Explanation:</strong>
                                <p class="mb-0 mt-2">${answer.explanation}</p>
                            </div>
                        ` : ''}
                    </div>
                `;
            });

            html += `
                        </div>
                    </div>
                </div>
            `;

            $review.append(html);
        });
    }

    // Prevent accidental page leave during quiz
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