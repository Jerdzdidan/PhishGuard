

@extends('user.layout.base')

@section('nav_title')
HOME
@endsection

@section('content')
<div class="app-academy">
  <div class="card p-0 mb-6">
    <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0 pt-6">
      <div class="app-academy-md-25 card-body py-0 pt-6 ps-12">
        <img src="{{ asset('img/illustrations/bulb-light.png') }}" class="img-fluid app-academy-img-height scaleX-n1-rtl" alt="Bulb in hand" data-app-light-img="illustrations/bulb-light.png" data-app-dark-img="illustrations/bulb-dark.png" height="90" />
      </div>
      <div class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center mb-6 py-6">
        <span class="card-title mb-4 px-md-12 h4">
          Education, talents, and career<br />
          opportunities. <span class="text-primary text-nowrap">All in one place</span>.
        </span>
        <p class="mb-4">Grow your skill with the most reliable online courses and certification in cybersecurity</p>
      </div>
      <div class="app-academy-md-25 d-flex align-items-end justify-content-end">
        <img src="{{ asset('img/illustrations/pencil-rocket.png') }}" alt="pencil rocket" height="180" class="scaleX-n1-rtl" />
      </div>
    </div>
  </div>

  {{-- Section Switcher --}}
  @if($enrolledSections->count() > 1)
  <div class="card mb-4">
    <div class="card-body py-3">
      <div class="d-flex align-items-center gap-3 flex-wrap">
        <span class="text-muted"><i class="bx bx-grid-alt me-1"></i> Section:</span>
        @foreach($enrolledSections as $section)
          <a href="{{ route('home', ['section' => $section->id]) }}" 
             class="btn btn-sm {{ $activeSection->id == $section->id ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $section->name }}
          </a>
        @endforeach
        <a href="{{ route('sections.join') }}" class="btn btn-sm btn-outline-primary ms-auto">
          <i class="bx bx-plus me-1"></i> Join Another Section
        </a>
      </div>
    </div>
  </div>
  @endif

  {{-- Active Section Info --}}
  <div class="card mb-4">
    <div class="card-body py-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-1">{{ $activeSection->name }}</h5>
          <small class="text-muted">
            <i class="bx bx-user me-1"></i> Teacher: {{ $activeSection->teacher->first_name }} {{ $activeSection->teacher->last_name }}
            <span class="mx-2">|</span>
            <i class="bx bx-hash me-1"></i> Code: <strong>{{ $activeSection->section_code }}</strong>
          </small>
        </div>
        <div>
          @if($enrolledSections->count() <= 1)
          <a href="{{ route('sections.join') }}" class="btn btn-sm btn-outline-primary">
            <i class="bx bx-plus me-1"></i> Join Section
          </a>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
        <div>
          <h5 class="mb-1">Your Progress</h5>
          <p class="text-muted mb-0">
            {{ $completedLessonsCount }} of {{ $total }} lesson{{ $total === 1 ? '' : 's' }} completed in this section
          </p>
        </div>
        <div class="text-lg-end">
          <span class="badge bg-label-primary mb-2">{{ $sectionProgressPercentage }}% Complete</span>
          <div class="progress" style="width: 240px; height: 10px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $sectionProgressPercentage }}%"></div>
          </div>
        </div>
      </div>
      <div class="row g-3 mt-1">
        <div class="col-md-4">
          <div class="border rounded p-3 h-100">
            <small class="text-muted d-block mb-1">Pre-Assessment</small>
            <strong class="{{ $preAssessmentCompleted ? 'text-success' : 'text-warning' }}">
              {{ $preAssessmentCompleted ? 'Completed' : 'Required' }}
            </strong>
          </div>
        </div>
        <div class="col-md-4">
          <div class="border rounded p-3 h-100">
            <small class="text-muted d-block mb-1">Lessons</small>
            <strong class="{{ $allLessonsCompleted ? 'text-success' : 'text-primary' }}">
              {{ $completedLessonsCount }}/{{ $total }} complete
            </strong>
          </div>
        </div>
        <div class="col-md-4">
          <div class="border rounded p-3 h-100">
            <small class="text-muted d-block mb-1">Certificate Path</small>
            @if($certificate)
            <strong class="text-success">
              <i class="bx bxs-award me-1"></i>Certificate Earned!
            </strong>
            @elseif($postAssessmentCompleted)
            <strong class="text-primary">
              Post-Assessment Completed
            </strong>
            @else
            <strong class="text-warning">
              Waiting for Post-Assessment
            </strong>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Pre-Assessment Required --}}
  @if(!$preAssessmentCompleted)
  <div class="card mb-4 border-warning">
    <div class="card-body text-center py-5">
      <div class="mb-4">
        <i class="bx bxs-notepad" style="font-size: 4rem; color: #ff9f43;"></i>
      </div>
      <h3 class="mb-2">Pre-Assessment Required</h3>
      <p class="text-muted mb-4">
        Before you can access the lessons, please complete the pre-assessment test.<br>
        This helps evaluate your current knowledge level.
      </p>
      <a href="{{ route('assessment.pre', Crypt::encryptString($activeSection->id)) }}" class="btn btn-warning btn-lg">
        <i class="bx bx-play-circle me-1"></i> Take Pre-Assessment
      </a>
    </div>
  </div>
  @else
    {{-- Lessons --}}
    <div class="card mb-6">
      <div class="card-header d-flex flex-wrap justify-content-between gap-4">
        <div class="card-title mb-0 me-1">
          <h5 class="mb-0">Lessons</h5>
          <p class="mb-0">Total of {{ $total }} {{ $total > 1 ? 'lessons' : 'lesson' }}</p>
        </div>
      </div>
      <div class="card-body">
        <div class="row gy-6 mb-6">
          @foreach ($lessons as $lesson)
            <x-lesson.card
                :title="$lesson->title"
                :img="$lesson->img"
                :description="$lesson->description"
                :difficulty="$lesson->difficulty"
                :time="$lesson->time"
                route="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}"
                :lesson="$lesson"
            />
          @endforeach
        </div>
        @if($lessons->hasPages())
          <nav aria-label="Page navigation" class="d-flex align-items-center justify-content-center">
            {{ $lessons->appends(['section' => $activeSection->id])->links('vendor.pagination.custom') }}
          </nav>
        @endif
      </div>
    </div>

    {{-- Post-Assessment Card --}}
    @if($allLessonsCompleted && !$postAssessmentCompleted)
    <div class="card mb-4 border-success">
      <div class="card-body text-center py-5">
        <div class="mb-4">
          <i class="bx bxs-trophy" style="font-size: 4rem; color: #28c76f;"></i>
        </div>
        <h3 class="mb-2">🎉 All Lessons Completed!</h3>
        <p class="text-muted mb-4">
          Congratulations! You've completed all lessons.<br>
          Take the post-assessment to earn your certificate.
        </p>
        <a href="{{ route('assessment.post', Crypt::encryptString($activeSection->id)) }}" class="btn btn-success btn-lg">
          <i class="bx bx-check-circle me-1"></i> Take Post-Assessment
        </a>
      </div>
    </div>
    @endif

    @if($certificate)
    {{-- Certificate Display Section --}}
    <div class="card mb-4 border-0" style="overflow: visible;">
      <div class="card-body p-0 p-md-4">
        {{-- Celebration header --}}
        <div class="text-center mb-4">
          <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill" style="background: linear-gradient(135deg, #1E7F5C, #28c76f); color: white;">
            <i class="bx bxs-award" style="font-size: 1.25rem;"></i>
            <span class="fw-bold" style="letter-spacing: 1px; font-size: 0.85rem;">CERTIFICATE EARNED</span>
          </div>
          <h4 class="mt-3 mb-1">Congratulations, {{ auth()->user()->first_name }}! 🎉</h4>
          <p class="text-muted mb-0">Your certificate of completion is ready</p>
        </div>

        {{-- Certificate Render --}}
        <div style="max-width: 900px; margin: 0 auto;">
          <x-certificate.preview
              :userName="trim(auth()->user()->first_name . ' ' . auth()->user()->last_name)"
              :certificate="$certificate"
              :compact="true"
          />
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mt-4" style="max-width: 600px; margin-left: auto; margin-right: auto;">
          <a href="{{ route('certificate.view') }}" class="btn btn-primary flex-fill">
            <i class="bx bx-show me-1"></i> View Full Certificate
          </a>
          <a href="{{ route('certificate.download') }}" class="btn btn-success flex-fill" target="_blank">
            <i class="bx bx-download me-1"></i> Download PDF
          </a>
          <button type="button" class="btn btn-outline-primary flex-fill" id="dashboardSendEmailBtn">
            <i class="bx bx-envelope me-1"></i> Send to Email
          </button>
        </div>

        {{-- Certificate details summary --}}
        <div class="row g-3 mt-3" style="max-width: 600px; margin-left: auto; margin-right: auto;">
          <div class="col-4">
            <div class="text-center p-2 rounded" style="background: #f0fdf4;">
              <small class="text-muted d-block">Lessons</small>
              <strong class="text-success">{{ $certificate->total_lessons_completed }}</strong>
            </div>
          </div>
          <div class="col-4">
            <div class="text-center p-2 rounded" style="background: #f0fdf4;">
              <small class="text-muted d-block">Quiz Avg</small>
              <strong class="text-success">{{ number_format($certificate->average_quiz_score, 1) }}%</strong>
            </div>
          </div>
          <div class="col-4">
            <div class="text-center p-2 rounded" style="background: #f0fdf4;">
              <small class="text-muted d-block">Sim Avg</small>
              <strong class="text-success">{{ number_format($certificate->average_simulation_score ?? 0, 1) }}%</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
    @elseif($postAssessmentCompleted)
    <div class="card mb-4 border-primary">
      <div class="card-body text-center py-4">
        <i class="bx bxs-certification" style="font-size: 3rem; color: #696cff;"></i>
        <h4 class="mt-2 mb-1">Assessment Complete!</h4>
        <p class="text-muted mb-3">You have completed both assessments. You may now claim your certificate.</p>
        <a href="{{ route('certificate.view') }}" class="btn btn-primary">
          <i class="bx bx-award me-1"></i> Claim Certificate
        </a>
      </div>
    </div>
    @endif
  @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('themes/sneat/assets/js/app-academy-course.js') }}"></script>

@if($certificate)
<script>
$(document).ready(function() {
    $('#dashboardSendEmailBtn').on('click', function() {
        Swal.fire({
            title: 'Send Certificate to Email',
            html: `Your certificate will be sent to <strong>{{ Auth::user()->email }}</strong>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1E7F5C',
            confirmButtonText: '<i class="bx bx-envelope me-1"></i> Send',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: "{{ route('certificate.send-email') }}",
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                }).then(response => {
                    return response;
                }).catch(error => {
                    Swal.showValidationMessage(error.responseJSON?.message || 'Failed to send email');
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent!',
                    text: result.value.message,
                    confirmButtonColor: '#1E7F5C',
                });
            }
        });
    });
});
</script>
@endif
@endsection
