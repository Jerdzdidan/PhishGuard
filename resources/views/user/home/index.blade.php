

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

  <div class="card mb-6">
    <div class="card-header d-flex flex-wrap justify-content-between gap-4">
      <div class="card-title mb-0 me-1">
        <h5 class="mb-0">Lessons</h5>
        <p class="mb-0">Total of {{ $total }} {{ $total > 1 ? 'lessons' : 'lesson' }}</p>
      </div>
      {{-- <div class="d-flex justify-content-md-end align-items-sm-center align-items-start column-gap-6 flex-sm-row flex-column row-gap-4">
        <select class="form-select">
          <option value="">All Courses</option>
          <option value="ui/ux">UI/UX</option>
          <option value="seo">SEO</option>
          <option value="web">Web</option>
          <option value="music">Music</option>
          <option value="painting">Painting</option>
        </select>

        <div class="form-check form-switch my-2 ms-2">
          <input type="checkbox" class="form-check-input" id="CourseSwitch" />
          <label class="form-check-label text-nowrap mb-0" for="CourseSwitch">Hide completed</label>
        </div>
      </div> --}}
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
       
        {{-- <div class="col-sm-6 col-lg-4">
          <div class="card p-2 h-100 shadow-none border">
            <div class="rounded-2 text-center mb-4">
              <a href="app-academy-course-details.html"><img class="img-fluid" src="{{ asset('img/lessons/default.png') }}" alt="tutor image 2" /></a>
            </div>
            <div class="card-body p-4 pt-2">
              <div class="d-flex justify-content-between align-items-center mb-4 pe-xl-4 pe-xxl-0">
                <span class="badge bg-label-danger">UI/UX</span>
                <p class="d-flex align-items-center justify-content-center fw-medium gap-1 mb-0">
                  4.2 <span class="text-warning"><i class="icon-base bx bxs-star me-1 mb-1_5"></i></span><span class="fw-normal"> (424)</span>
                </p>
              </div>
              <a class="h5" href="app-academy-course-details.html">Figma & More</a>
              <p class="mt-1">Introductory course for design and framework basics in web development.</p>
              <p class="d-flex align-items-center mb-1"><i class="icon-base bx bx-time-five me-1"></i>16 hours</p>
              <div class="progress mb-4" style="height: 8px">
                <div class="progress-bar w-25" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <div class="d-flex flex-column flex-md-row gap-4 text-nowrap flex-wrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
                <a class="w-100 btn btn-label-secondary d-flex align-items-center" href="app-academy-course-details.html"> <i class="icon-base bx bx-rotate-right icon-sm align-middle me-2"></i><span>Start Over</span> </a>
                <a class="w-100 btn btn-label-primary d-flex align-items-center" href="app-academy-course-details.html"> <span class="me-2">Continue</span><i class="icon-base bx bx-chevron-right icon-sm lh-1 scaleX-n1-rtl"></i> </a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-4">
          <div class="card p-2 h-100 shadow-none border">
            <div class="rounded-2 text-center mb-4">
              <a href="app-academy-course-details.html"><img class="img-fluid" src="{{ asset('img/lessons/default.png') }}" alt="tutor image 3" /></a>
            </div>
            <div class="card-body p-4 pt-2">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="badge bg-label-success">SEO</span>
                <p class="d-flex align-items-center justify-content-center fw-medium gap-1 mb-0">
                  5 <span class="text-warning"><i class="icon-base bx bxs-star me-1 mb-1_5"></i></span><span class="fw-normal"> (12)</span>
                </p>
              </div>
              <a class="h5" href="app-academy-course-details.html">Keyword Research</a>
              <p class="mt-1">Keyword suggestion tool provides comprehensive details & keyword suggestions.</p>
              <p class="d-flex align-items-center mb-1"><i class="icon-base bx bx-time-five me-1"></i>7 hours</p>
              <div class="progress mb-4" style="height: 8px">
                <div class="progress-bar w-50" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <div class="d-flex flex-column flex-md-row gap-4 text-nowrap flex-wrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
                <a class="w-100 btn btn-label-secondary d-flex align-items-center" href="app-academy-course-details.html"> <i class="icon-base bx bx-rotate-right icon-sm align-middle me-2"></i><span>Start Over</span> </a>
                <a class="w-100 btn btn-label-primary d-flex align-items-center" href="app-academy-course-details.html"> <span class="me-2">Continue</span><i class="icon-base bx bx-chevron-right icon-sm lh-1 scaleX-n1-rtl"></i> </a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-4">
          <div class="card p-2 h-100 shadow-none border">
            <div class="rounded-2 text-center mb-4">
              <a href="app-academy-course-details.html"><img class="img-fluid" src="{{ asset('img/lessons/default.png') }}" alt="tutor image 4" /></a>
            </div>
            <div class="card-body p-4 pt-2">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="badge bg-label-info">Music</span>
                <p class="d-flex align-items-center justify-content-center gap-1 mb-0">
                  3.8 <span class="text-warning"><i class="icon-base bx bxs-star me-1 mb-1_5"></i></span><span class="fw-normal"> (634)</span>
                </p>
              </div>
              <a class="h5" href="app-academy-course-details.html">Basics to Advanced</a>
              <p class="mt-1">20 more lessons like this about music production, writing, mixing, mastering</p>
              <p class="d-flex align-items-center mb-1"><i class="icon-base bx bx-time-five me-1"></i>30 minutes</p>
              <div class="progress mb-4" style="height: 8px">
                <div class="progress-bar w-75" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <div class="d-flex flex-column flex-md-row gap-4 text-nowrap flex-wrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
                <a class="w-100 btn btn-label-secondary d-flex align-items-center" href="app-academy-course-details.html"> <i class="icon-base bx bx-rotate-right icon-sm align-middle me-2"></i><span>Start Over</span> </a>
                <a class="w-100 btn btn-label-primary d-flex align-items-center" href="app-academy-course-details.html"> <span class="me-2">Continue</span><i class="icon-base bx bx-chevron-right icon-sm lh-1 scaleX-n1-rtl"></i> </a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-4">
          <div class="card p-2 h-100 shadow-none border">
            <div class="rounded-2 text-center mb-4">
              <a href="app-academy-course-details.html"><img class="img-fluid" src="{{ asset('img/lessons/default.png') }}" alt="tutor image 5" /></a>
            </div>
            <div class="card-body p-4 pt-2">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="badge bg-label-warning">Painting</span>
                <p class="d-flex align-items-center justify-content-center gap-1 mb-0">
                  4.7 <span class="text-warning"><i class="icon-base bx bxs-star me-1 mb-1_5"></i></span><span class="fw-normal"> (34)</span>
                </p>
              </div>
              <a class="h5" href="app-academy-course-details.html">Art & Drawing</a>
              <p class="mt-1">Easy-to-follow video & guides show you how to draw animals, people & more.</p>
              <p class="d-flex align-items-center text-success mb-1"><i class="icon-base bx bx-check me-1"></i>Completed</p>
              <div class="progress mb-4" style="height: 8px">
                <div class="progress-bar w-100" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <a class="w-100 btn btn-label-primary" href="app-academy-course-details.html"><i class="icon-base bx bx-rotate-right icon-sm me-1_5"></i>Start Over</a>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-4">
          <div class="card p-2 h-100 shadow-none border">
            <div class="rounded-2 text-center mb-4">
              <a href="app-academy-course-details.html"><img class="img-fluid" src="{{ asset('img/lessons/default.png') }}" alt="tutor image 6" /></a>
            </div>
            <div class="card-body p-4 pt-2">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="badge bg-label-danger">UI/UX</span>
                <p class="d-flex align-items-center justify-content-center gap-1 mb-0">
                  3.6 <span class="text-warning"><i class="icon-base bx bxs-star me-1 mb-1_5"></i></span><span class="fw-normal"> (2.5k)</span>
                </p>
              </div>
              <a class="h5" href="app-academy-course-details.html">Basics Fundamentals</a>
              <p class="mt-1">This guide will help you develop a systematic approach user interface.</p>
              <p class="d-flex align-items-center mb-1"><i class="icon-base bx bx-time-five me-1"></i>16 hours</p>
              <div class="progress mb-4" style="height: 8px">
                <div class="progress-bar w-25" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <div class="d-flex flex-column flex-md-row gap-4 text-nowrap flex-wrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
                <a class="w-100 btn btn-label-secondary d-flex align-items-center" href="app-academy-course-details.html"> <i class="icon-base bx bx-rotate-right icon-sm align-middle me-2"></i><span>Start Over</span> </a>
                <a class="w-100 btn btn-label-primary d-flex align-items-center" href="app-academy-course-details.html"> <span class="me-2">Continue</span><i class="icon-base bx bx-chevron-right icon-sm lh-1 scaleX-n1-rtl"></i> </a>
              </div>
            </div>
          </div>
        </div>
      </div> --}}
      @if($lessons->hasPages())
        <nav aria-label="Page navigation" class="d-flex align-items-center justify-content-center">
          {{ $lessons->links('vendor.pagination.custom') }}
        </nav>
      @endif
    </div>
  </div>

</div>
@endsection

@section('script')
<script src="{{ asset('themes/sneat/assets/js/app-academy-course.js') }}"></script>
@endsection