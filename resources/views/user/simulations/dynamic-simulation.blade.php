@extends('user.layout.base')

@section('title')
SIMULATION - {{ $lesson->title }}
@endsection

@section('nav_title')
SIMULATION - {{ $lesson->title }}
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
<style>
.scenario-card {
    border-left: 4px solid #1E7F5C;
}
.sim-image-container {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e7e7e8;
    background: #f8f9fa;
    margin-bottom: 1.5rem;
}
.sim-image-container img {
    width: 100%;
    max-height: 420px;
    object-fit: contain;
    display: block;
}
.sim-option {
    display: block;
    width: 100%;
    text-align: left;
    padding: 14px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 12px;
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
    font-size: 0.95rem;
}
.timer {
    font-size: 1.5rem;
    font-weight: 600;
}
.ready-screen {
    min-height: 420px;
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
                <div id="readyScreen" class="ready-screen">
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="ri-shield-check-line" style="font-size: 4rem; color: #696cff;"></i>
                        </div>
                        <h3 class="mb-3">{{ $simulation['title'] }}</h3>
                        <p class="text-muted mb-4">{{ $simulation['description'] }}</p>

                        <div class="row justify-content-center mb-4">
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="text-muted mb-1">Scenarios</h6>
                                    <h4 class="mb-0">{{ count($scenarios) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="text-muted mb-1">Passing Score</h6>
                                    <h4 class="mb-0">70%</h4>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-4">Are you ready to begin this simulation?</h5>
                        <button type="button" class="btn btn-primary btn-lg" id="startSimulationBtn">
                            <i class="ri-play-line me-2"></i> Start Simulation
                        </button>
                        <div class="mt-3">
                            <a href="{{ route('lessons.simulations.index', $lessonEncId) }}" class="btn btn-label-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Back to Simulations
                            </a>
                        </div>
                    </div>
                </div>

                <div id="simulationScreen" class="d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <div>
                            <h4 class="mb-1">{{ $simulation['title'] }}</h4>
                            <p class="mb-0 text-muted">{{ $simulation['description'] }}</p>
                        </div>
                        <div class="text-center">
                            <div class="timer text-primary" id="timer">00:00</div>
                            <small class="text-muted">Time Elapsed</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="scenario-progress text-muted" id="progressText">
                            Scenario 1 of {{ count($scenarios) }}
                        </span>
                        <span class="badge bg-label-primary" id="completedBadge">0/{{ count($scenarios) }} answered</span>
                    </div>
                    <div class="progress mb-4" style="height: 8px;">
                        <div class="progress-bar" id="progressBar" style="width: {{ count($scenarios) > 0 ? (100 / count($scenarios)) : 0 }}%"></div>
                    </div>

                    <div id="scenarioContainer">
                        @foreach($scenarios as $index => $item)
                            @php
                                $options = is_string($item->options) ? json_decode($item->options, true) : ($item->options ?? []);
                                $scenarioTitle = $item->title ?: 'Scenario ' . ($index + 1);
                            @endphp
                            <div class="card scenario-card mb-4 scenario-slide {{ $index > 0 ? 'd-none' : '' }}" data-index="{{ $index }}">
                                <div class="card-body">
                                    @if ($item->image_path)
                                        <div class="sim-image-container">
                                            <img src="{{ route('storage.serve', ['path' => $item->image_path]) }}" alt="{{ $scenarioTitle }}">
                                        </div>
                                    @endif

                                    <h5 class="mb-2">{{ $scenarioTitle }}</h5>

                                    @if($item->description)
                                        <p class="text-muted mb-3">{{ $item->description }}</p>
                                    @endif

                                    @if($item->content)
                                        <div class="mb-4">
                                            <h6 class="mb-0">{{ $item->content }}</h6>
                                        </div>
                                    @endif

                                    <div class="options-area" data-scenario="{{ $index }}">
                                        @foreach($options as $optIdx => $option)
                                            @php
                                                $optionText = is_array($option) ? ($option['text'] ?? '') : $option;
                                                $isCorrect = is_array($option) ? ($option['is_correct'] ?? false) : false;
                                            @endphp
                                            <button
                                                type="button"
                                                class="sim-option"
                                                data-option-index="{{ $optIdx }}"
                                                data-option-text="{{ $optionText }}"
                                                data-is-correct="{{ $isCorrect ? 1 : 0 }}"
                                                onclick="selectScenarioOption(this, {{ $index }})"
                                            >
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="badge bg-label-primary">{{ chr(65 + $optIdx) }}</span>
                                                    <span>{{ $optionText }}</span>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>

                                    <div class="feedback-area mt-3" id="feedback-{{ $index }}" style="display: none;"></div>

                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                        <button type="button" class="btn btn-outline-secondary {{ $index === 0 ? 'd-none' : '' }}" onclick="goToScenario({{ $index - 1 }})">
                                            <i class="ri-arrow-left-line me-1"></i> Previous
                                        </button>

                                        @if($index < count($scenarios) - 1)
                                            <button type="button" class="btn btn-primary ms-auto" id="next-{{ $index }}" onclick="goToScenario({{ $index + 1 }})" disabled>
                                                Next <i class="ri-arrow-right-line ms-1"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-success ms-auto" id="finishBtn" onclick="finishSimulation()" disabled>
                                                <i class="ri-check-line me-1"></i> Finish Simulation
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @include('user.home.lesson.partials.sidebar', ['lesson' => $lesson, 'activeStep' => 'simulation'])

        <div class="card stick-top">
            <div class="card-body">
                <h6 class="mb-3">Simulation Progress</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Answered</span>
                    <strong id="answeredCount">0/{{ count($scenarios) }}</strong>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar bg-success" id="answeredProgressBar" role="progressbar" style="width: 0%"></div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Status</span>
                    <strong class="text-warning" id="simulationStatus">Not Started</strong>
                </div>
                <hr>
                <div class="alert alert-info mb-0">
                    <i class="ri-information-line me-2"></i>
                    <small>Pass at least 70% of the scenarios to complete this simulation and count it toward lesson progress.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const totalScenarios = {{ count($scenarios) }};
    const startUrl = '{{ route("lessons.simulations.start", ["id" => $lessonEncId, "simId" => $simulationRouteId]) }}';
    const submitUrl = '{{ route("lessons.simulations.submit", ["id" => $lessonEncId, "simId" => $simulationRouteId]) }}';
    const csrfToken = '{{ csrf_token() }}';
    let attemptId = null;
    let startTime = null;
    let timerInterval = null;
    let currentScenario = 0;
    let submitted = false;
    const results = new Array(totalScenarios).fill(null);
    const answered = new Array(totalScenarios).fill(false);
    const clickData = [];

    $('#startSimulationBtn').on('click', function() {
        $.ajax({
            url: startUrl,
            type: 'POST',
            data: {
                _token: csrfToken
            },
            success: function(response) {
                attemptId = response.attempt_id;
                startTime = Date.now();
                timerInterval = setInterval(updateTimer, 1000);
                $('#readyScreen').addClass('d-none');
                $('#simulationScreen').removeClass('d-none');
                $('#simulationStatus').removeClass('text-warning').addClass('text-primary').text('In Progress');
                goToScenario(0);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to start simulation.',
                    confirmButtonColor: '#ea5455',
                });
            }
        });
    });

    function updateTimer() {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;

        $('#timer').text(
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0')
        );
    }

    function updateAnsweredProgress() {
        const answeredCount = answered.filter(Boolean).length;
        const answeredPercent = totalScenarios > 0
            ? Math.round((answeredCount / totalScenarios) * 100)
            : 0;

        $('#completedBadge').text(`${answeredCount}/${totalScenarios} answered`);
        $('#answeredCount').text(`${answeredCount}/${totalScenarios}`);
        $('#answeredProgressBar').css('width', `${answeredPercent}%`);
    }

    window.selectScenarioOption = function(element, scenarioIndex) {
        if (answered[scenarioIndex]) {
            return;
        }

        const $option = $(element);
        const $optionsArea = $option.closest('.options-area');
        const isCorrect = parseInt($option.data('is-correct'), 10) === 1;
        const optionText = $option.data('option-text');
        const scenarioTitle = $option.closest('.scenario-slide').find('h5').first().text().trim();

        answered[scenarioIndex] = true;
        results[scenarioIndex] = {
            scenario: scenarioTitle,
            correct: isCorrect,
            selected_action: optionText
        };

        clickData.push({
            scenario: scenarioTitle,
            option_index: parseInt($option.data('option-index'), 10),
            selected_action: optionText,
            correct: isCorrect,
            timestamp: Date.now() - startTime,
        });

        $optionsArea.find('.sim-option').each(function() {
            const $btn = $(this);
            const optionIsCorrect = parseInt($btn.data('is-correct'), 10) === 1;
            $btn.prop('disabled', true);

            if (optionIsCorrect) {
                $btn.addClass('correct');
            }
        });

        $option.addClass('selected');
        if (!isCorrect) {
            $option.addClass('incorrect');
        }

        const $feedback = $(`#feedback-${scenarioIndex}`);
        $feedback
            .html(
                isCorrect
                    ? '<div class="alert alert-success mb-0"><i class="ri-check-line me-1"></i> Correct. Good catch.</div>'
                    : '<div class="alert alert-danger mb-0"><i class="ri-close-line me-1"></i> Incorrect. Review the correct option before moving on.</div>'
            )
            .show();

        $(`#next-${scenarioIndex}, #finishBtn`).prop('disabled', false);
        updateAnsweredProgress();
    };

    window.goToScenario = function(index) {
        currentScenario = index;
        $('.scenario-slide').addClass('d-none');
        $(`.scenario-slide[data-index="${index}"]`).removeClass('d-none');

        const percent = totalScenarios > 0
            ? ((index + 1) / totalScenarios) * 100
            : 0;

        $('#progressBar').css('width', `${percent}%`);
        $('#progressText').text(`Scenario ${index + 1} of ${totalScenarios}`);

        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    window.finishSimulation = function() {
        const scenarioResults = results.filter(Boolean);

        if (scenarioResults.length !== totalScenarios) {
            Swal.fire({
                icon: 'warning',
                title: 'Finish all scenarios first',
                text: 'Answer every scenario before submitting the simulation.',
                confirmButtonColor: '#696cff',
            });
            return;
        }

        const score = scenarioResults.filter(result => result.correct).length;
        const timeTaken = Math.floor((Date.now() - startTime) / 1000);

        Swal.fire({
            title: 'Submit Simulation?',
            html: `You answered <strong>${score}</strong> out of <strong>${totalScenarios}</strong> scenarios correctly.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28c76f',
            confirmButtonText: 'Submit',
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            submitted = true;
            clearInterval(timerInterval);

            $.ajax({
                url: submitUrl,
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({
                    _token: csrfToken,
                    attempt_id: attemptId,
                    score: score,
                    time_taken: timeTaken,
                    click_data: clickData,
                    scenario_results: scenarioResults,
                }),
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect_url;
                    }
                },
                error: function() {
                    submitted = false;

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to submit simulation. Please try again.',
                        confirmButtonColor: '#ea5455',
                    });
                }
            });
        });
    };

    window.addEventListener('beforeunload', function(event) {
        if (attemptId && !submitted && !$('#simulationScreen').hasClass('d-none')) {
            event.preventDefault();
            event.returnValue = '';
        }
    });
});
</script>
@endsection
