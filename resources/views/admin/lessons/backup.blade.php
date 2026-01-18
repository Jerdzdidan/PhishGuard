@extends('admin.layout.base')

@section('title')
QUIZ MANAGEMENT
@endsection

@section('nav_title')
QUIZ MANAGEMENT
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
<style>
.question-card {
    border-left: 4px solid #696cff;
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
.drag-handle {
    cursor: move;
    color: #999;
}
.drag-handle:hover {
    color: #696cff;
}
</style>
@endsection

@section('body')
<div class="row g-6">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Quiz Info Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">{{ $lesson->title }}</h4>
                        <p class="text-muted mb-0">Quiz Assessment</p>
                    </div>
                    <a href="{{ route('admin.lessons.edit', Crypt::encryptString($lesson->id)) }}" class="btn btn-label-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Back to Lesson
                    </a>
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
                                <div class="drag-handle">
                                    <i class="ri-draggable ri-xl"></i>
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
        <!-- Quick Stats -->
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
                    <span class="badge {{ ($quiz->is_active ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($quiz->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Quick Actions</h6>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-label-primary w-100" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="ri-add-line me-1"></i> Add Question
                    </button>
                    {{-- @if($questions->count() > 0)
                        <a href="{{ route('admin.quiz.preview', Crypt::encryptString($lesson->id)) }}" class="btn btn-label-secondary w-100">
                            <i class="ri-eye-line me-1"></i> Preview Quiz
                        </a>
                    @endif --}}
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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Make questions sortable
    const questionsList = document.getElementById('questionsList');
    if (questionsList && questionsList.children.length > 0) {
        new Sortable(questionsList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function() {
                updateQuestionOrder();
            }
        });
    }

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
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
            }
        });
    });

    // Reset modal when opening
    $('#addQuestionModal').on('show.bs.modal', function() {
        $('#questionForm')[0].reset();
        $('#question_id').val('');
        $('#modalTitle').text('Add Question');
        $('input[name="correct_answer"][value="A"]').prop('checked', true);
    });
});

function editQuestion(questionId) {
    // Fetch question data and populate modal
    $.get('{{ route("admin.lessons.question.edit", ":id") }}'.replace(':id', questionId), function(data) {
        $('#question_id').val(data.id);
        $('#question_text').val(data.question_text);
        $('#points').val(data.points);
        $('#modalTitle').text('Edit Question');
        
        // Populate answers
        data.answers.forEach(function(answer) {
            $('#answer_' + answer.option_letter + '_text').val(answer.answer_text);
            $('#answer_' + answer.option_letter + '_explanation').val(answer.explanation);
            if (answer.is_correct) {
                $('input[name="correct_answer"][value="' + answer.option_letter + '"]').prop('checked', true);
            }
        });
        
        $('#addQuestionModal').modal('show');
    });
}

function deleteQuestion(questionId) {
    if (confirm('Are you sure you want to delete this question?')) {
        $.ajax({
            url: '{{ route("admin.lessons.question.destroy", ":id") }}'.replace(':id', questionId),
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                location.reload();
            }
        });
    }
}

function updateQuestionOrder() {
    const order = [];
    $('#questionsList .question-card').each(function(index) {
        order.push({
            id: $(this).data('question-id'),
            order: index + 1
        });
    });

    $.post('{{ route("admin.lessons.question.reorder") }}', {
        _token: '{{ csrf_token() }}',
        order: order
    });
}
</script>
@endsection