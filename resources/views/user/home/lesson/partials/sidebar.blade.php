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
                    @if(($activeStep ?? 'lesson') === 'lesson')
                        <label class="ms-4">
                            <span class="mb-0 h6 text-primary">1. Lesson</span>
                            <small class="text-body d-block">content</small>
                        </label>
                    @else
                        <a href="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}">
                            <label class="form-check-label ms-4">
                                <span class="mb-0 h6">1. Lesson</span>
                                <small class="text-body d-block">content</small>
                            </label>
                        </a>
                    @endif
                </div>

                @if ($lesson->quiz && $lesson->quiz->is_active)
                    <hr>
                    <div class="mb-4">
                        @if(($activeStep ?? '') === 'quiz')
                            <label class="ms-4">
                                <span class="mb-0 h6 text-primary">2. Quiz</span>
                                <small class="text-body d-block">assessment</small>
                            </label>
                        @else
                            <a href="{{ route('lessons.quiz.show', Crypt::encryptString($lesson->id)) }}">
                                <label class="form-check-label ms-4">
                                    <span class="mb-0 h6">2. Quiz</span>
                                    <small class="text-body d-block">assessment</small>
                                </label>
                            </a>
                        @endif
                    </div>
                @endif

                @if ($lesson->has_simulation)
                    <hr>
                    <div class="mb-4">
                        @if(($activeStep ?? '') === 'simulation')
                            <label class="ms-4">
                                <span class="mb-0 h6 text-primary">3. Simulations</span>
                                <small class="text-body d-block">interactive practice</small>
                            </label>
                        @else
                            <a href="{{ route('lessons.simulations.index', Crypt::encryptString($lesson->id)) }}">
                                <label class="form-check-label ms-4">
                                    <span class="mb-0 h6">3. Simulations</span>
                                    <small class="text-body d-block">interactive practice</small>
                                </label>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
