@extends('admin.layout.base')

@section('title')
LESSONS
@endsection

@section('nav_title')
LESSONS
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
@endsection

@section('body')

<div class="app-academy">

    <div class="card p-0 mb-6">
    <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0 pt-6">
      <div class="app-academy-md-25 card-body py-0 pt-6 ps-12">
        <img src="{{ asset('img/illustrations/bulb-light.png') }}" class="img-fluid app-academy-img-height scaleX-n1-rtl" alt="Bulb in hand" data-app-light-img="illustrations/bulb-light.png" data-app-dark-img="illustrations/bulb-dark.png" height="90" />
      </div>
      <div class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center mb-6 py-6">
        <span class="card-title mb-4 px-md-12 h4">
          Lessons management - create, update, and delete lessons. <span class="text-primary text-nowrap">All in one place</span>.
        </span>
        <p class="mb-4">Create intriguing, well-structured lessons that transform users into competent cybersecurity experts.</p>

      </div>
      <div class="app-academy-md-25 d-flex align-items-end justify-content-end">
        <img src="{{ asset('img/illustrations/pencil-rocket.png') }}" alt="pencil rocket" height="180" class="scaleX-n1-rtl" />
      </div>
    </div>
  </div>

  <div class="card mb-6">
    <div class="card-header">
      <div class="card-title mb-0 me-1">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h5 class="mb-0">Lessons</h5>
            <p class="mb-0">Total of {{ $total }} {{ $total > 1 ? 'lessons' : 'lesson' }}</p>
          </div>
          <div class="col-md-4 text-end">
              <button class="btn btn-primary" data-bs-toggle="offcanvas" id="btn-add" data-bs-target="#add-or-update-modal">
                <i class="fa-solid fa-plus fa-1x me-2"></i>
                Add New Lesson
              </button>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row gy-6 mb-6">

        @foreach ($lessons as $lesson)
          <x-admin.lesson-card
              :title="$lesson->title"
              :img="$lesson->image_path"
              :description="$lesson->description"
              :difficulty="$lesson->difficulty"
              :time="$lesson->time"
              :status="$lesson->is_active"
              route="{{ route('admin.lessons.edit', Crypt::encryptString($lesson->id)) }}"
          />
        @endforeach

      @if($lessons->hasPages())
        <nav aria-label="Page navigation" class="d-flex align-items-center justify-content-center">
          {{ $lessons->links('vendor.pagination.custom') }}
        </nav>
      @endif

    </div>
  </div>

  @include('admin.lessons.form')

</div>
@endsection

@section('scripts')
<script src="{{ asset('themes/sneat/assets/js/app-academy-course.js') }}"></script>
<script src="{{ asset('js/shared/generic-datatable.js') }}"></script>
<script src="{{ asset('js/shared/generic-crud.js') }}"></script>

<script>
  $('#difficulty').select2({
      minimumResultsForSearch: -1,
      placeholder: 'Difficulty'
  });

  $('#prerequisite_lesson_id').select2({
      placeholder: 'None - First Lesson',
      allowClear: true
  });

  // Load available lessons for prerequisite selection
  function loadPrerequisiteLessons() {
      $.ajax({
          url: "{{ route('admin.lessons.index') }}",
          type: 'GET',
          success: function(response) {
              // Extract lessons from the response (you may need to adjust based on your response structure)
              // For now, we'll populate from the current page data
              const lessonsData = @json($lessons->items());
              
              const select = $('#prerequisite_lesson_id');
              select.find('option:not(:first)').remove(); // Clear existing options except the first
              
              lessonsData.forEach(lesson => {
                  select.append(new Option(lesson.title, lesson.id, false, false));
              });
              
              select.trigger('change');
          }
      });
  }

  // Load lessons when modal opens
  $('#add-or-update-modal').on('shown.bs.offcanvas', function() {
      loadPrerequisiteLessons();
  });

  window.lessonCRUD = new GenericCRUD({
      storeUrl: "{{ route('admin.lessons.store') }}",
      entityName: 'Lesson',
      csrfToken: "{{ csrf_token() }}",
      form: '#add-or-update-form',
      modal: '#add-or-update-modal'
  });

  $('#add-or-update-form').on('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);

    lessonCRUD.create(fd);
  });

  lessonCRUD.onCreateSuccess = function(response) {
      window.location.reload();
  };
</script>
@endsection