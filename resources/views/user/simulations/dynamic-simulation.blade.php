@extends('user.layout.base')

@section('title')
Simulation - {{ $lesson->title }}
@endsection

@section('nav_title')
SIMULATION
@endsection

@section('style')
<style>
    .sim-card {
        max-width: 700px;
        margin: 0 auto;
    }
    .sim-image-container {
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #e0e0e0;
        margin-bottom: 20px;
    }
    .sim-image-container img {
        width: 100%;
        height: auto;
        display: block;
    }
    .sim-option {
        display: block;
        padding: 12px 16px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 10px;
    }
    .sim-option:hover {
        border-color: #696cff;
        background: #f8f9ff;
    }
    .sim-option.selected {
        border-color: #696cff;
        background: #f0f1ff;
    }
    .sim-option.correct {
        border-color: #28c76f;
        background: #e8faf0;
    }
    .sim-option.incorrect {
        border-color: #ea5455;
        background: #fde8e8;
    }
    .scenario-progress {
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-3 sim-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">{{ $lesson->title }} - Simulation</h5>
                    <span class="scenario-progress text-muted" id="progressText">
                        Scenario 1 of {{ count($scenarios) }}
                    </span>
                </div>
                <div class="progress mb-4" style="height: 6px;">
                    <div class="progress-bar" id="progressBar" style="width: {{ 100 / count($scenarios) }}%"></div>
                </div>
            </div>
        </div>

        <div id="scenarioContainer">
            @foreach($scenarios as $index => $item)
            <div class="card mb-4 sim-card scenario-slide" data-index="{{ $index }}" style="{{ $index > 0 ? 'display:none;' : '' }}">
                <div class="card-body">
                    {{-- Image --}}
                    <div class="sim-image-container">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="Scenario {{ $index + 1 }}">
                    </div>
                    
                    {{-- Title & Description --}}
                    @if($item->title)
                    <h5 class="mb-2">{{ $item->title }}</h5>
                    @endif
                    @if($item->description)
                    <p class="text-muted mb-3">{{ $item->description }}</p>
                    @endif

                    {{-- Question --}}
                    <h6 class="mb-3">{{ $item->content }}</h6>

                    {{-- Options --}}
                    <div class="options-area" data-scenario="{{ $index }}">
                        @php
                            $options = is_string($item->options) ? json_decode($item->options, true) : $item->options;
                        @endphp
                        @foreach($options as $optIdx => $option)
                        <div class="sim-option" 
                             data-option-index="{{ $optIdx }}"
                             data-is-correct="{{ is_array($option) ? ($option['is_correct'] ?? false) : false }}"
                             onclick="selectOption(this, {{ $index }})">
                            {{ is_array($option) ? ($option['text'] ?? $option) : $option }}
                        </div>
                        @endforeach
                    </div>

                    {{-- Feedback area --}}
                    <div class="feedback-area mt-3" id="feedback-{{ $index }}" style="display: none;"></div>

                    {{-- Navigation --}}
                    <div class="d-flex justify-content-between mt-4">
                        @if($index > 0)
                        <button class="btn btn-outline-secondary btn-prev" onclick="goToScenario({{ $index - 1 }})">
                            <i class="bx bx-left-arrow-alt me-1"></i> Previous
                        </button>
                        @else
                        <div></div>
                        @endif

                        @if($index < count($scenarios) - 1)
                        <button class="btn btn-primary btn-next" id="next-{{ $index }}" onclick="goToScenario({{ $index + 1 }})" disabled>
                            Next <i class="bx bx-right-arrow-alt ms-1"></i>
                        </button>
                        @else
                        <button class="btn btn-success btn-finish" id="finish-btn" onclick="finishSimulation()" disabled>
                            <i class="bx bx-check me-1"></i> Finish Simulation
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Hidden form for submission --}}
<form id="simForm" method="POST" action="{{ route('lessons.simulations.submit', [$lessonEncId, $simEncId]) }}" style="display:none;">
    @csrf
    <input type="hidden" name="results" id="resultsInput">
</form>
@endsection

@section('scripts')
<script>
const totalScenarios = {{ count($scenarios) }};
let results = [];
let answered = new Array(totalScenarios).fill(false);

function selectOption(el, scenarioIndex) {
    if (answered[scenarioIndex]) return; // Already answered
    
    const area = $(el).closest('.options-area');
    const isCorrect = $(el).data('is-correct') == true || $(el).data('is-correct') == 1;
    
    answered[scenarioIndex] = true;
    
    // Mark all options
    area.find('.sim-option').each(function() {
        const optCorrect = $(this).data('is-correct') == true || $(this).data('is-correct') == 1;
        if (optCorrect) {
            $(this).addClass('correct');
        }
        $(this).css('pointer-events', 'none');
    });
    
    if (!isCorrect) {
        $(el).addClass('incorrect');
    }
    
    // Show feedback
    const feedback = $(`#feedback-${scenarioIndex}`);
    if (isCorrect) {
        feedback.html('<div class="alert alert-success"><i class="bx bx-check-circle me-1"></i> Correct!</div>');
    } else {
        feedback.html('<div class="alert alert-danger"><i class="bx bx-x-circle me-1"></i> Incorrect</div>');
    }
    feedback.show();
    
    // Store result
    results.push({
        scenario: scenarioIndex,
        correct: isCorrect
    });
    
    // Enable next/finish button
    $(`#next-${scenarioIndex}, #finish-btn`).prop('disabled', false);
}

function goToScenario(index) {
    $('.scenario-slide').hide();
    $(`.scenario-slide[data-index="${index}"]`).show();
    
    // Update progress
    const percent = ((index + 1) / totalScenarios) * 100;
    $('#progressBar').css('width', percent + '%');
    $('#progressText').text(`Scenario ${index + 1} of ${totalScenarios}`);
    
    window.scrollTo(0, 0);
}

function finishSimulation() {
    Swal.fire({
        title: 'Submit Simulation?',
        text: `You got ${results.filter(r => r.correct).length} out of ${totalScenarios} correct.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28c76f',
        confirmButtonText: 'Submit'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#resultsInput').val(JSON.stringify(results));
            $('#simForm').submit();
        }
    });
}
</script>
@endsection
