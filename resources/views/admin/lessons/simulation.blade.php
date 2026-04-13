@extends('admin.layout.base')

@section('title')
LESSONS
@endsection

@section('nav_title')
LESSONS
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
<style>
.sim-item-card {
    border-left: 4px solid #696cff;
    transition: all 0.3s ease;
}
.sim-item-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.sim-item-image {
    width: 100%;
    max-height: 200px;
    object-fit: cover;
    border-radius: 8px;
}
.option-badge-correct {
    background: #e8faf0;
    border: 1px solid #28c76f;
    color: #28c76f;
}
.option-badge-wrong {
    background: #f8f8f8;
    border: 1px solid #e0e0e0;
    color: #666;
}
</style>
@endsection

@section('body')
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="row g-6">
        <div class="col-lg-8">
            <!-- Simulation Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-1">{{ $lesson->title }}</h4>
                            <p class="text-muted mb-0">Simulation Scenarios</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="hasSimulation" 
                                {{ $lesson->has_simulation ? 'checked' : '' }}
                                onchange="toggleSimulation()">
                            <label class="form-check-label" for="hasSimulation">Enabled</label>
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        Each simulation scenario shows an image (e.g. phishing email screenshot) and asks a multiple-choice question about it.
                    </div>
                </div>
            </div>

            <!-- Scenario Items List -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Scenarios ({{ $items->count() }})</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="bx bx-plus me-1"></i> Add Scenario
                    </button>
                </div>
                <div class="card-body" id="scenariosList">
                    @forelse($items as $index => $item)
                        <div class="card sim-item-card mb-4" id="item-{{ $item->id }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="badge bg-label-primary me-2">{{ $index + 1 }}</span>
                                            {{ $item->title }}
                                        </h6>
                                        @if($item->description)
                                        <small class="text-muted">{{ $item->description }}</small>
                                        @endif
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-icon btn-label-danger" 
                                                onclick="deleteItem('{{ Crypt::encryptString($item->id) }}', {{ $item->id }}, '{{ addslashes($item->title) }}')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                @if($item->image_path)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $item->image_path) }}" class="sim-item-image" alt="{{ $item->title }}">
                                </div>
                                @endif

                                <div class="mb-3">
                                    <strong>Question:</strong> {{ $item->content }}
                                </div>

                                @php
                                    $options = is_string($item->options) ? json_decode($item->options, true) : $item->options;
                                @endphp
                                @if($options)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($options as $opt)
                                    <span class="px-3 py-1 rounded {{ (is_array($opt) && ($opt['is_correct'] ?? false)) ? 'option-badge-correct' : 'option-badge-wrong' }}">
                                        @if(is_array($opt))
                                            {{ $opt['text'] ?? $opt }}
                                            @if($opt['is_correct'] ?? false)
                                                <i class="bx bx-check ms-1"></i>
                                            @endif
                                        @else
                                            {{ $opt }}
                                        @endif
                                    </span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bx bxs-image fa-3x text-muted mb-3 d-block"></i>
                            <h6 class="text-muted">No scenarios yet</h6>
                            <p class="text-muted mb-3">Add your first simulation scenario</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                <i class="bx bx-plus me-1"></i> Add Scenario
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
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
                                <a href="{{ route('admin.lessons.edit', Crypt::encryptString($lesson->id)) }}">
                                <label class="form-check-label ms-4">
                                    <span class="mb-0 h6">1. Lesson</span>
                                    <small class="text-body d-block">content</small>
                                </label>
                                </a>
                            </div>
                            <hr>
                            <div class="mb-4">
                                <a href="{{ route('admin.lessons.quiz.show', Crypt::encryptString($lesson->id)) }}">
                                    <label class="form-check-label ms-4">
                                        <span class="mb-0 h6">2. Quiz</span>
                                        <small class="text-body d-block">assessment</small>
                                    </label>
                                </a>
                            </div>
                            <hr>
                            <div class="mb-4">
                                <label class="ms-4">
                                    <span class="mb-0 h6 text-primary">3. Simulation</span>
                                    <small class="text-body d-block">scenarios</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3">Simulation Statistics</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Scenarios</span>
                        <strong>{{ $items->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>With Images</span>
                        <strong>{{ $items->whereNotNull('image_path')->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Status</span>
                        <span class="badge {{ $lesson->has_simulation ? 'bg-success' : 'bg-secondary' }}">
                            {{ $lesson->has_simulation ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Scenario Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="scenarioForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Simulation Scenario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Image Upload -->
                        <div class="mb-4">
                            <label class="form-label">Scenario Image <span class="text-danger">*</span></label>
                            <div class="border-2 border-dashed rounded p-4 text-center" id="dropZone" style="cursor:pointer; border: 2px dashed #ccc;">
                                <div id="uploadPlaceholder">
                                    <i class="bx bxs-cloud-upload text-primary" style="font-size: 3rem;"></i>
                                    <p class="mb-1">Click or drag & drop to upload</p>
                                    <small class="text-muted">PNG, JPG, GIF, WEBP up to 5MB</small>
                                </div>
                                <img id="imagePreview" src="" class="img-fluid rounded" style="max-height: 250px; display: none;">
                            </div>
                            <input type="file" name="image" id="imageInput" class="d-none" accept="image/*" required>
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" placeholder="e.g. Suspicious Email from Bank" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Brief context for this scenario"></textarea>
                        </div>

                        <!-- Question -->
                        <div class="mb-4">
                            <label class="form-label">Question <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="content" rows="2" placeholder="e.g. What should you do when you receive this message?" required></textarea>
                        </div>

                        <!-- Multiple Choice Options -->
                        <div class="mb-3">
                            <label class="form-label">Answer Options <span class="text-danger">*</span></label>
                            <small class="text-muted d-block mb-2"><i class="bx bx-radio-circle-marked me-1"></i> Select the radio button for the correct answer</small>
                            <div id="optionsContainer">
                                <div class="input-group mb-2 option-row">
                                    <div class="input-group-text">
                                        <input type="radio" name="correct_option" value="0" class="form-check-input" checked>
                                    </div>
                                    <input type="text" class="form-control" name="options[]" placeholder="Option A (correct)" required>
                                </div>
                                <div class="input-group mb-2 option-row">
                                    <div class="input-group-text">
                                        <input type="radio" name="correct_option" value="1" class="form-check-input">
                                    </div>
                                    <input type="text" class="form-control" name="options[]" placeholder="Option B" required>
                                </div>
                                <div class="input-group mb-2 option-row">
                                    <div class="input-group-text">
                                        <input type="radio" name="correct_option" value="2" class="form-check-input">
                                    </div>
                                    <input type="text" class="form-control" name="options[]" placeholder="Option C" required>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption()">
                                <i class="bx bx-plus me-1"></i> Add Option
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitScenarioBtn">
                            <i class="bx bx-save me-1"></i> Save Scenario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
const lessonEncId = "{{ Crypt::encryptString($lesson->id) }}";
const scenarioId = "{{ $scenario ? Crypt::encryptString($scenario->id) : '' }}";

// Drop zone image upload
const dropZone = document.getElementById('dropZone');
const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const uploadPlaceholder = document.getElementById('uploadPlaceholder');

dropZone.addEventListener('click', () => imageInput.click());
dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.style.borderColor = '#696cff'; });
dropZone.addEventListener('dragleave', () => { dropZone.style.borderColor = '#ccc'; });
dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = '#ccc';
    if (e.dataTransfer.files.length) {
        imageInput.files = e.dataTransfer.files;
        previewImage(e.dataTransfer.files[0]);
    }
});

imageInput.addEventListener('change', function() {
    if (this.files[0]) previewImage(this.files[0]);
});

function previewImage(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.src = e.target.result;
        imagePreview.style.display = 'block';
        uploadPlaceholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function addOption() {
    const index = $('.option-row').length;
    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $('#optionsContainer').append(`
        <div class="input-group mb-2 option-row">
            <div class="input-group-text">
                <input type="radio" name="correct_option" value="${index}" class="form-check-input">
            </div>
            <input type="text" class="form-control" name="options[]" placeholder="Option ${letters[index]}" required>
            <button type="button" class="btn btn-outline-danger" onclick="$(this).closest('.option-row').remove()">
                <i class="bx bx-x"></i>
            </button>
        </div>
    `);
}

// Submit scenario
$('#scenarioForm').on('submit', function(e) {
    e.preventDefault();
    
    if (!scenarioId) {
        Swal.fire('Error', 'Please enable simulations first (toggle the switch above).', 'warning');
        return;
    }

    const fd = new FormData(this);
    const $btn = $('#submitScenarioBtn');
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

    $.ajax({
        url: `/admin/scenarios/${scenarioId}/items/store`,
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.fire('Success', response.message, 'success').then(() => location.reload());
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            let msg = xhr.responseJSON?.message || 'Failed to save.';
            if (errors) {
                msg = Object.values(errors).flat().join('\n');
            }
            Swal.fire('Error', msg, 'error');
            $btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Save Scenario');
        }
    });
});

function deleteItem(encItemId, rawId, title) {
    Swal.fire({
        title: 'Delete Scenario?',
        text: `Delete "${title}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/scenarios/items/destroy/${encItemId}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    $(`#item-${rawId}`).fadeOut();
                    Swal.fire('Deleted!', 'Scenario removed.', 'success');
                },
                error: function() {
                    Swal.fire('Error', 'Failed to delete.', 'error');
                }
            });
        }
    });
}

function toggleSimulation() {
    const enabled = document.getElementById('hasSimulation').checked;
    $.ajax({
        url: `/admin/lessons/simulation/${lessonEncId}/toggle`,
        method: 'POST',
        data: { _token: '{{ csrf_token() }}', has_simulation: enabled ? 1 : 0 },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: enabled ? 'Simulations Enabled' : 'Simulations Disabled',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            }
        }
    });
}
</script>
@endsection
