<div class="{{ $class }} my-2 my-md-0">
    <div class="card {{ $bgColor }} h-100 text-white">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="card-title mb-0 text-white">Units Progress</h6>
                    <h4 class="mb-0 text-white mt-2">
                        <span id="{{ $numeratorId }}">0</span>/<span id="{{ $denominatorId }}">0</span>
                    </h4>
                    <div class="progress mt-2" style="height: 8px;">
                        <div id="{{ $progressBarId }}" class="progress-bar" role="progressbar" 
                                style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-white mt-1" id="{{ $percentageId }}">0%</small>
                </div>
                <div class="align-self-center">
                    <i class="fa-solid fa-book fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>