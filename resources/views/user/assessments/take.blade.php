@extends('user.layout.base')

@section('title')
{{ $title }}
@endsection

@section('nav_title')
{{ strtoupper($title) }}
@endsection

@section('style')
<style>
    .question-card {
        transition: all 0.3s ease;
    }
    .question-card.active {
        display: block;
    }
    .question-card.hidden {
        display: none;
    }
    .option-label {
        display: block;
        padding: 12px 16px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 10px;
    }
    .option-label:hover {
        border-color: #696cff;
        background: #f8f9ff;
    }
    .option-label input:checked + .option-text {
        color: #696cff;
        font-weight: 600;
    }
    .option-label:has(input:checked) {
        border-color: #696cff;
        background: #f0f1ff;
    }
    .progress-indicator {
        font-size: 0.9rem;
        color: #666;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body text-center py-4">
                <h4 class="mb-2">{{ $title }}</h4>
                <p class="text-muted mb-3">{{ $description }}</p>
                <div class="row justify-content-center mb-3">
                    <div class="col-md-4">
                        <div class="border rounded p-2">
                            <strong>{{ $questions->count() }}</strong> questions
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-2">
                            Section: <strong>{{ $section->name }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="assessmentForm" method="POST" action="{{ route('assessment.submit') }}">
            @csrf
            <input type="hidden" name="section_id" value="{{ Crypt::encryptString($section->id) }}">
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="started_at" value="{{ now()->toISOString() }}">

            @foreach($questions as $index => $question)
            <div class="card mb-4 question-card {{ $index === 0 ? 'active' : 'hidden' }}" data-index="{{ $index }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-label-primary">Question {{ $index + 1 }} of {{ $questions->count() }}</span>
                        <span class="progress-indicator">{{ round(($index / $questions->count()) * 100) }}% complete</span>
                    </div>
                    
                    <div class="progress mb-4" style="height: 6px;">
                        <div class="progress-bar" style="width: {{ round(($index / $questions->count()) * 100) }}%"></div>
                    </div>

                    <h5 class="mb-4">{{ $question->question_text }}</h5>

                    <div class="options-container">
                        @foreach($question->answers as $answer)
                        <label class="option-label">
                            <input type="radio" 
                                   name="answers[{{ $question->id }}]" 
                                   value="{{ $answer->id }}"
                                   class="d-none answer-radio"
                                   data-question-index="{{ $index }}"
                                   required>
                            <span class="option-text">
                                <strong>{{ $answer->option_letter }}.</strong> {{ $answer->answer_text }}
                            </span>
                        </label>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        @if($index > 0)
                        <button type="button" class="btn btn-outline-secondary btn-prev" data-index="{{ $index }}">
                            <i class="bx bx-left-arrow-alt me-1"></i> Previous
                        </button>
                        @else
                        <div></div>
                        @endif

                        @if($index < $questions->count() - 1)
                        <button type="button" class="btn btn-primary btn-next" data-index="{{ $index }}" disabled>
                            Next <i class="bx bx-right-arrow-alt ms-1"></i>
                        </button>
                        @else
                        <button type="submit" class="btn btn-success btn-submit" disabled>
                            <i class="bx bx-check me-1"></i> Submit Assessment
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Enable next/submit button when answer is selected
    $('.answer-radio').on('change', function() {
        const index = $(this).data('question-index');
        const card = $(`.question-card[data-index="${index}"]`);
        card.find('.btn-next, .btn-submit').prop('disabled', false);
    });

    // Next button
    $('.btn-next').on('click', function() {
        const currentIndex = $(this).data('index');
        $(`.question-card[data-index="${currentIndex}"]`).removeClass('active').addClass('hidden');
        $(`.question-card[data-index="${currentIndex + 1}"]`).removeClass('hidden').addClass('active');
        window.scrollTo(0, 0);
    });

    // Previous button
    $('.btn-prev').on('click', function() {
        const currentIndex = $(this).data('index');
        $(`.question-card[data-index="${currentIndex}"]`).removeClass('active').addClass('hidden');
        $(`.question-card[data-index="${currentIndex - 1}"]`).removeClass('hidden').addClass('active');
        window.scrollTo(0, 0);
    });

    // Submit confirmation
    $('#assessmentForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        
        Swal.fire({
            title: 'Submit Assessment?',
            text: 'Are you sure you want to submit your answers? This cannot be undone.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28c76f',
            confirmButtonText: 'Yes, Submit',
            cancelButtonText: 'Review Answers'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection
