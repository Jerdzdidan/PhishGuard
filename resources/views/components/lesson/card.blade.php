<div class="col-sm-6 col-lg-4">
    <div class="card p-2 h-100 shadow-none border {{ !$lesson->isUnlocked() ? 'opacity-75' : '' }}">
        <div class="rounded-2 text-center mb-4 position-relative">
            @if($lesson->isUnlocked())
                <a href="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}">
            @else
                <div class="position-relative" style="cursor: not-allowed;">
            @endif
                @if ($lesson->image_path)
                    <img class="img-fluid" src="{{ asset('storage/' . $lesson->image_path) }}" style="height: 290px; object-fit: cover;" />
                @else
                    <img class="img-fluid" src="{{ asset('img/lessons/default.png') }}" style="height: 290px; object-fit: cover;" />
                @endif
                
                @if(!$lesson->isUnlocked())
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                         style="background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(2px);">
                        <div class="text-center text-white">
                            <i class="bx bx-lock-alt" style="font-size: 3rem;"></i>
                            <p class="mb-0 mt-2 fw-bold">Locked</p>
                        </div>
                    </div>
                @elseif($lesson->isCompleted())
                    <span class="badge bg-success position-absolute top-0 end-0 m-2">
                        <i class="bx bx-check-circle me-1"></i> Completed
                    </span>
                @endif
            @if($lesson->isUnlocked())
                </a>
            @else
                </div>
            @endif
        </div>
        <div class="card-body p-4 pt-2">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="badge {{ $lesson->difficulty === 'EASY' ? 'bg-label-primary' : ($lesson->difficulty === 'MEDIUM' ? 'bg-label-warning' : 'bg-label-danger') }}">
                    {{ $lesson->difficulty }}
                </span>
            </div>
            
            <a href="{{ $lesson->isUnlocked() ? route('lessons.show', Crypt::encryptString($lesson->id)) : 'javascript:void(0)' }}" 
               class="h5 {{ !$lesson->isUnlocked() ? 'text-muted' : '' }}">
                {{ $lesson->title }}
            </a>
            
            <p class="mt-1 {{ !$lesson->isUnlocked() ? 'text-muted' : '' }}">{{ $lesson->description }}</p>
            
            @if(!$lesson->isUnlocked() && $lesson->prerequisiteLesson)
                <div class="alert alert-warning p-2 mb-2">
                    <small>
                        <i class="bx bx-info-circle me-1"></i>
                        Complete <strong>{{ $lesson->prerequisiteLesson->title }}</strong> first
                    </small>
                </div>
            @endif
    
            @if($lesson->isUnlocked())
                @php
                    $progress = $lesson->progress;
                    $progressPercent = 0;
                    
                    if ($progress) {
                        $requirements = [];
                        $completed = [];
                        
                        // Content is always required
                        $requirements[] = 'content';
                        if ($progress->content_viewed) {
                            $completed[] = 'content';
                        }
                        
                        // Quiz if active
                        if ($lesson->quiz && $lesson->quiz->is_active) {
                            $requirements[] = 'quiz';
                            if ($progress->quiz_passed) {
                                $completed[] = 'quiz';
                            }
                        }
                        
                        // Simulations if enabled
                        if ($lesson->has_simulation) {
                            $requirements[] = 'simulation';
                            if ($progress->simulations_completed) {
                                $completed[] = 'simulation';
                            }
                        }
                        
                        // Calculate percentage
                        if (count($requirements) > 0) {
                            $progressPercent = round((count($completed) / count($requirements)) * 100);
                        }
                    }
                @endphp
                <p class="d-flex align-items-center mb-1">
                    {{ $progressPercent }}% Complete
                </p>
                <div class="progress mb-4" style="height: 8px">
                    <div class="progress-bar {{ $progressPercent === 100 ? 'bg-success' : '' }}" 
                        role="progressbar" 
                        style="width: {{ $progressPercent }}%" 
                        aria-valuenow="{{ $progressPercent }}" 
                        aria-valuemin="0" 
                        aria-valuemax="100">
                    </div>
                </div>
            @else
                <div class="progress mb-4" style="height: 8px">
                    <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            @endif
            
            <div class="d-flex flex-column flex-md-row gap-4 text-nowrap flex-wrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
                @if($lesson->isUnlocked())
                    <a class="w-100 btn btn-primary d-flex align-items-center" 
                       href="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}">
                        <i class="icon-base bx bx-rotate-right icon-sm align-middle scaleX-n1-rtl me-2"></i>
                        <span>{{ $lesson->isCompleted() ? 'Review' : 'Start' }}</span>
                    </a>
                @else
                    <button class="w-100 btn btn-secondary d-flex align-items-center" disabled>
                        <i class="icon-base bx bx-lock-alt icon-sm align-middle me-2"></i>
                        <span>Locked</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>