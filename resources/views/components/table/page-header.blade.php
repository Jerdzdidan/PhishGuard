<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-8">

            @if($showBackButton)
                <a href="{{ $backUrl ?? url()->previous() }}" class="btn btn-outline-secondary mb-3">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Back
                </a>
            @endif
            
            <h3 class="page-title"> {{ $title ?? '' }}</h3>
            <p class="page-subtitle">{{ $subtitle ?? '' }}</p>
        </div>
        <div class="col-md-4 text-end">
            {{-- BUTTONS --}}
            {{ $slot }}
        </div>
    </div>
</div>