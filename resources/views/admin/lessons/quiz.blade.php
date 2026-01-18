@extends('admin.layout.base')

@section('title')
LESSONS
@endsection

@section('nav_title')
LESSONS
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
<style>
.question-card {
    border-left: 4px solid #1E7F5C;
    transition: all 0.3s ease;
}
.question-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.answer-option {
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 8px;
    border: 2px solid #e0e0e0;
    transition: all 0.3s ease;
    cursor: pointer;
}
.answer-option:hover {
    border-color: #696cff;
    background: #f8f9ff;
}
.answer-option.correct {
    border-color: #28c76f;
    background: #f0fdf4;
}
.order-controls {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.order-controls .btn {
    padding: 2px 8px;
    font-size: 12px;
}
</style>
@endsection

@section('body')
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Please check the form for errors.
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="row g-6">
        <div class="col-lg-8">
            <!-- Quiz Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-1">{{ $lesson->title }}</h4>
                            <p class="text-muted mb-0">Quiz Assessment</p>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Quiz Settings Form -->
                    <form action="{{ route('admin.lessons.quiz.store', Crypt::encryptString($lesson->id)) }}" method="POST" id="quizSettingsForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Quiz Title</label>
                                <input type="text" class="form-control" name="title" 
                                    value="{{ $quiz->title ?? $lesson->title . ' Quiz' }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Passing Score (%)</label>
                                <input type="number" class="form-control" name="passing_score" 
                                    value="{{ $quiz->passing_score ?? 70 }}" min="0" max="100" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description (Optional)</label>
                                <textarea class="form-control" name="description" rows="2">{{ $quiz->description ?? '' }}</textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" 
                                        id="quizActive" value="1" {{ ($quiz->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="quizActive">Quiz Active</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i> Save Quiz Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Questions List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Questions ({{ $questions->count() }})</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="ri-add-line me-1"></i> Add Question
                    </button>
                </div>
                <div class="card-body" id="questionsList">
                    @forelse($questions as $index => $question)
                        <div class="card question-card mb-3" data-question-id="{{ $question->id }}">
                            <div class="card-body">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="order-controls">
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-secondary move-up" 
                                                onclick="moveQuestion({{ $question->id }}, 'up')"
                                                {{ $index === 0 ? 'disabled' : '' }}>
                                            <i class="ri-arrow-up-line"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-secondary move-down" 
                                                onclick="moveQuestion({{ $question->id }}, 'down')"
                                                {{ $index === $questions->count() - 1 ? 'disabled' : '' }}>
                                            <i class="ri-arrow-down-line"></i>
                                        </button>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h6 class="mb-1">Question {{ $index + 1 }}</h6>
                                                <p class="mb-0">{{ $question->question_text }}</p>
                                                <small class="text-muted">{{ $question->points }} point(s)</small>
                                            </div>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-icon btn-label-primary" 
                                                        onclick="editQuestion({{ $question->id }})">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-label-danger" 
                                                        onclick="deleteQuestion({{ $question->id }})">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Answers -->
                                        <div class="mt-3">
                                            @foreach($question->answers as $answer)
                                                <div class="answer-option {{ $answer->is_correct ? 'correct' : '' }}">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-label-secondary">{{ $answer->option_letter }}</span>
                                                        <span class="flex-grow-1">{{ $answer->answer_text }}</span>
                                                        @if($answer->is_correct)
                                                            <i class="ri-check-line text-success"></i>
                                                        @endif
                                                    </div>
                                                    @if($answer->explanation)
                                                        <small class="text-muted d-block mt-2">
                                                            <i class="ri-information-line"></i> {{ $answer->explanation }}
                                                        </small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="ri-questionnaire-line ri-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No questions yet</h6>
                            <p class="text-muted mb-3">Add your first question to get started</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                                <i class="ri-add-line me-1"></i> Add Question
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
            
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="accordion stick-top accordion-custom-button mb-4" id="courseContent">
                <div class="accordion-item active mb-0">
                    <div class="accordion-header" id="headingOne">
                        <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#chapterOne" aria-expanded="true" aria-controls="chapterOne">
                            <span class="d-flex flex-column">
                                <span class="h5 mb-0">Lesson Content</span>
                                <!-- Time Duration -->
                                <span class="inline-edit-field text-body fw-normal" data-field="time">
                                    <span class="view-mode">
                                        {{ $lesson->time }} min
                                    </span>
                                    <span class="edit-mode d-none">
                                        <input type="number" 
                                               class="form-control form-control-sm" 
                                               name="time" 
                                               id="timeEdit"
                                               value="{{ $lesson->time }}"
                                               min="1"
                                               style="width: 80px; display: inline-block;">
                                        <span class="ms-1">min</span>
                                    </span>
                                </span>
                            </span>
                        </button>
                    </div>
                    <div id="chapterOne" class="accordion-collapse collapse show" data-bs-parent="#courseContent">
                        <div class="accordion-body py-4">
                            <div class="mb-4">
                                <a href="{{ route('admin.lessons.edit', Crypt::encryptString($lesson->id)) }}">
                                <label class="form-check-label ms-4">
                                    <span class="mb-0 h6">1. Lesson</span>
                                    <small class="text-body d-block text-primary">content</small>
                                </label>
                                </a>
                            </div>
                            <hr>
                            <div class="mb-4">
                                <label class="ms-4">
                                    <span class="mb-0 h6 text-primary">2. Quiz</span>
                                    <small class="text-body d-block">assessment</small>
                                </label>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Edit Button -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3">Quiz Statistics</h6>
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
                        <strong>{{ $quiz->passing_score ?? 70 }}%</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Status</span>
                        <span class="badge {{ ($quiz->is_active ?? true) ? 'bg-success' : 'bg-danger' }}">
                            {{ ($quiz->is_active ?? true) ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Question Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="questionForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add Question</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="question_id" id="question_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Question Text *</label>
                            <textarea class="form-control" name="question_text" id="question_text" rows="3" required></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Points</label>
                                <input type="number" class="form-control" name="points" id="points" value="1" min="1">
                            </div>
                        </div>

                        <h6 class="mb-3">Answer Options</h6>
                        
                        @foreach(['A', 'B', 'C', 'D'] as $letter)
                            <div class="card mb-3 answer-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-label-primary">{{ $letter }}</span>
                                        <input type="text" class="form-control" 
                                            name="answers[{{ $letter }}][text]" 
                                            id="answer_{{ $letter }}_text"
                                            placeholder="Enter answer option {{ $letter }}" required>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" 
                                                name="correct_answer" 
                                                value="{{ $letter }}" 
                                                id="correct_{{ $letter }}"
                                                {{ $letter === 'A' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="correct_{{ $letter }}">
                                                Correct
                                            </label>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" 
                                        name="answers[{{ $letter }}][explanation]" 
                                        id="answer_{{ $letter }}_explanation"
                                        placeholder="Explanation (optional)">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Save Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

    // Question form submission
    $('#questionForm').on('submit', function(e) {
        e.preventDefault();
        
        const questionId = $('#question_id').val();
        const url = questionId 
            ? '{{ route("admin.lessons.question.update", ":id") }}'.replace(':id', questionId)
            : '{{ route("admin.lessons.question.store", Crypt::encryptString($lesson->id)) }}';
        
        const formData = new FormData(this);
        if (questionId) {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
            }
        });
    });

    // Reset modal when opening for new question
    $('#addQuestionModal').on('show.bs.modal', function(e) {
        // Only reset if not triggered by editQuestion function
        if (!$(e.relatedTarget).hasClass('edit-question-btn')) {
            $('#questionForm')[0].reset();
            $('#question_id').val('');
            $('#modalTitle').text('Add Question');
            $('input[name="correct_answer"][value="A"]').prop('checked', true);
        }
    });
});

function editQuestion(questionId) {
    // Show modal first
    $('#addQuestionModal').modal('show');
    
    // Small delay to ensure modal is rendered
    setTimeout(function() {
        // Fetch question data and populate modal
        $.ajax({
            url: '{{ route("admin.lessons.question.edit", ":id") }}'.replace(':id', questionId),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('Question data:', data);
                
                // Set question details
                $('#question_id').val(data.id);
                $('#question_text').val(data.question_text);
                $('#points').val(data.points);
                $('#modalTitle').text('Edit Question');
                
                // Clear all answer fields first
                ['A', 'B', 'C', 'D'].forEach(function(letter) {
                    $('#answer_' + letter + '_text').val('');
                    $('#answer_' + letter + '_explanation').val('');
                    $('input[name="correct_answer"][value="' + letter + '"]').prop('checked', false);
                });
                
                // Populate answers
                if (data.answers && data.answers.length > 0) {
                    data.answers.forEach(function(answer) {
                        $('#answer_' + answer.option_letter + '_text').val(answer.answer_text);
                        $('#answer_' + answer.option_letter + '_explanation').val(answer.explanation || '');
                        
                        if (answer.is_correct) {
                            $('input[name="correct_answer"][value="' + answer.option_letter + '"]').prop('checked', true);
                        }
                    });
                }
            },
            error: function(xhr) {
                console.error('Error fetching question:', xhr);
                alert('Error loading question data');
            }
        });
    }, 100); // 100ms delay
}

function deleteQuestion(questionId) {
    if (confirm('Are you sure you want to delete this question?')) {
        $.ajax({
            url: '{{ route("admin.lessons.question.destroy", ":id") }}'.replace(':id', questionId),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function() {
                location.reload();
            },
            error: function(xhr) {
                alert('Error deleting question: ' + (xhr.responseJSON?.message || 'Something went wrong'));
            }
        });
    }
}

function moveQuestion(questionId, direction) {
    $.ajax({
        url: '{{ route("admin.lessons.question.reorder") }}',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data: {
            question_id: questionId,
            direction: direction
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            }
        },
        error: function(xhr) {
            console.error('Error reordering question:', xhr);
            alert('Error moving question');
        }
    });
}
</script>
@endsection