@extends('user.layout.base')

@section('title')
LESSONS
@endsection

@section('nav_title')
LESSONS
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
@endsection

@section('content')
    <div class="row g-6">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap mb-6 gap-2">
            <div class="me-1">
              <h5 class="mb-0">{{ $lesson->title }}</h5>
              <p class="mb-0">{{ $lesson->description }}</p>
            </div>
            <div class="d-flex align-items-center">
              <span class="badge {{ $lesson->difficulty === 'EASY' ? 'bg-label-primary' : ($lesson->difficulty === 'MEDIUM' ? 'bg-label-warning' : 'bg-label-danger') }}">{{ $lesson->difficulty }}</span>
            </div>
          </div>
          <div class="card academy-content shadow-none border">
            <div class="p-2">
                @if ($lesson->image_path)
                    <img class="w-100" src="{{ asset('storage/' . $lesson->image_path) }}" id="lessonImage" style="height: 560px; object-fit: cover;" />
                @else
                    <img class="w-100" src="{{ asset('img/lessons/default.png') }}" id="lessonImage" style="height: 560px; object-fit: cover;" />
                @endif
            </div>
            <div class="card-body pt-4">
              {!! $lesson->content !!}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="accordion stick-top accordion-custom-button" id="courseContent">
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
                <label for="defaultCheck1" class="form-check-label ms-4">
                  <span class="mb-0 h6 text-primary">1. Lesson</span>
                  <small class="text-body d-block">content</small>
                </label>
              </div>
              @if ($lesson->quiz && $lesson->quiz->is_active)
                <hr>
                <div class="mb-4">

                  <label for="defaultCheck2" class="form-check-label ms-4">
                    <span class="mb-0 h6">2. Quiz</span>
                    <small class="text-body d-block">assessment</small>
                  </label>
                </div>
              @endif
            </div>
          </div>
        </div>
        
      </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('themes/sneat/assets/js/app-academy-course.js') }}"></script>
@endsection