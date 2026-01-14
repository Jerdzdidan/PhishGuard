<div class="col-sm-6 col-lg-4">
    <div class="card p-2 h-100 shadow-none border">
    <div class="rounded-2 text-center mb-4">
        <a href="{{ $route }}">
            @if ($img)
                <img class="img-fluid" src="{{ $img }}" />
            @else
                <img class="img-fluid" src="{{ asset('img/lessons/default.png') }}" />
            @endif
        </a>
    </div>
    <div class="card-body p-4 pt-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
        <span class="badge bg-label-primary">Easy</span>
        {{-- <p class="d-flex align-items-center justify-content-center fw-medium gap-1 mb-0">
            4.4 <span class="text-warning"><i class="icon-base bx bxs-star me-1 mb-1_5"></i></span><span class="fw-normal">(1.23k)</span>
        </p> --}}
        </div>
        <a href="app-academy-course-details.html" class="h5">{{ $name }}</a>
        <p class="mt-1">{{ $description }}</p>
        <p class="d-flex align-items-center mb-1"><i class="icon-base bx bx-time-five me-1"></i>{{ $time }}</p>
        <div class="progress mb-4" style="height: 8px">
        <div class="progress-bar" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="d-flex flex-column flex-md-row gap-4 text-nowrap flex-wrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
        <a class="w-100 btn btn-primary d-flex align-items-center" href="{{ $route }}"> <i class="icon-base bx bx-rotate-right icon-sm align-middle scaleX-n1-rtl me-2"></i><span>Start</span> </a>
        </div>
    </div>
    </div>
</div>