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
.edit-mode-controls {
    position: sticky;
    top: 0;
    z-index: 100;
    background: #fff;
    padding: 1rem;
    border-bottom: 2px solid #e7e7ff;
    margin: -1.5rem -1.5rem 1.5rem -1.5rem;
}
.inline-edit-field {
    border: 2px dashed transparent;
    transition: all 0.3s ease;
    border-radius: 6px;
    padding: 8px;
    margin: -8px;
}
body.edit-mode-active .inline-edit-field:hover {
    background: #f8f9fa;
    border-color: #e0e0e0;
    cursor: pointer;
}
.inline-edit-field.editing {
    border-color: #696cff;
    background: #f8f9ff;
}
.edit-icon {
    opacity: 0;
    transition: opacity 0.2s;
    color: #696cff;
}
body:not(.edit-mode-active) .edit-icon {
    display: none;
}
body.edit-mode-active .inline-edit-field:hover .edit-icon {
    opacity: 1;
}
.image-upload-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
    cursor: pointer;
    pointer-events: none;
}
body.edit-mode-active .image-container:hover .image-upload-overlay {
    opacity: 1;
    pointer-events: auto;
}
/* Remove padding/margin adjustments in preview mode */
body:not(.edit-mode-active) .inline-edit-field {
    padding: 0;
    margin: 0;
}
</style>
@endsection

@section('body')
<form id="lessonEditForm" action="{{ route('admin.lessons.update', Crypt::encryptString($lesson->id)) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Please check the form for errors.
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="row g-6">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <!-- Edit Mode Controls (Hidden by default) -->
                    <div class="edit-mode-controls d-none" id="editModeControls">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="ri-edit-line me-2 text-primary"></i>
                                <strong class="text-primary">Edit Mode Active</strong>
                                <small class="text-muted ms-2">Click on any field to edit</small>
                            </div>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-label-secondary" id="cancelEdit">
                                    <i class="ri-close-line me-1"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Title and Description Section -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-6 gap-2">
                        <div class="me-1">
                            <!-- Title -->
                            <div class="inline-edit-field position-relative" data-field="title">
                                <h5 class="mb-0 view-mode" id="titleView">
                                    {{ $lesson->title }}
                                    <i class="ri-pencil-line ri-sm edit-icon ms-2"></i>
                                </h5>
                                <input type="text" 
                                       class="form-control edit-mode d-none" 
                                       name="title" 
                                       id="titleEdit"
                                       value="{{ $lesson->title }}"
                                       required>
                            </div>
                            
                            <!-- Description -->
                            <div class="inline-edit-field position-relative" data-field="description">
                                <p class="mb-0 view-mode" id="descriptionView">
                                    {{ $lesson->description }}
                                    <i class="ri-pencil-line ri-sm edit-icon ms-2"></i>
                                </p>
                                <textarea class="form-control edit-mode d-none" 
                                          name="description" 
                                          id="descriptionEdit"
                                          rows="3"
                                          style="min-width: 500px; max-width: 100%;"
                                          required>{{ $lesson->description }}</textarea>
                            </div>
                        </div>
                        
                        <!-- Difficulty Badge -->
                        <div class="d-flex align-items-center">
                            <div class="inline-edit-field" data-field="difficulty">
                                <span class="badge view-mode {{ $lesson->difficulty === 'EASY' ? 'bg-label-primary' : ($lesson->difficulty === 'MEDIUM' ? 'bg-label-warning' : 'bg-label-danger') }}">
                                    {{ $lesson->difficulty }}
                                    <i class="ri-pencil-line ri-sm edit-icon ms-1"></i>
                                </span>
                                <select class="form-select form-select-sm edit-mode d-none" 
                                        name="difficulty" 
                                        id="difficultyEdit"
                                        style="width: auto;">
                                    <option value="EASY" {{ $lesson->difficulty === 'EASY' ? 'selected' : '' }}>EASY</option>
                                    <option value="MEDIUM" {{ $lesson->difficulty === 'MEDIUM' ? 'selected' : '' }}>MEDIUM</option>
                                    <option value="HARD" {{ $lesson->difficulty === 'HARD' ? 'selected' : '' }}>HARD</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Image and Content Section -->
                    <div class="card academy-content shadow-none border">
                        <!-- Image -->
                        <div class="p-2 position-relative image-container inline-edit-field" data-field="image">
                            @if ($lesson->image_path)
                                <img class="w-100" src="{{ asset('storage/' . $lesson->image_path) }}" id="lessonImage" style="height: 560px; object-fit: cover;" />
                            @else
                                <img class="w-100" src="{{ asset('img/lessons/default.png') }}" id="lessonImage" style="height: 560px; object-fit: cover;" />
                            @endif
                            <div class="image-upload-overlay">
                                <div class="text-center text-white">
                                    <i class="ri-upload-cloud-line ri-3x mb-2"></i>
                                    <p class="mb-0">Click to change image</p>
                                </div>
                            </div>
                            <input type="file" 
                                   name="image" 
                                   id="imageUpload" 
                                   class="d-none" 
                                   accept="image/*">
                        </div>

                        <!-- Content -->
                        <div class="card-body pt-4">
                            <div class="inline-edit-field" data-field="content">
                                <div class="view-mode" id="contentView">
                                    {!! $lesson->content !!}
                                    <div class="text-end mt-3">
                                        <i class="ri-pencil-line edit-icon"></i>
                                    </div>
                                </div>
                                <textarea class="form-control edit-mode d-none" 
                                          name="content" 
                                          id="contentEdit"
                                          rows="15">{{ $lesson->content }}</textarea>
                                <small class="text-muted edit-mode d-none">HTML content supported</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="accordion stick-top accordion-custom-button" id="courseContent">
                <div class="accordion-item active mb-0">
                    <div class="accordion-header" id="headingOne">
                        <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#chapterOne" aria-expanded="true" aria-controls="chapterOne">
                            <span class="d-flex flex-column">
                                <span class="h5 mb-0">Lesson Content</span>
                                <!-- Time Duration -->
                                <span class="text-body fw-normal" data-field="time">
                                    <span class="view-mode">
                                        {{ $lesson->time }} min
                                    </span>
                                    <span class="edit-mode d-none">
                                        <input type="number" 
                                               class="form-control form-control-sm" 
                                               name="time" 
                                               id="timeEdit"
                                               value="{{ $lesson->time }}"
                                               min="1"
                                               style="width: 80px; display: inline-block;">
                                        <span class="ms-1">min</span>
                                    </span>
                                </span>
                            </span>
                        </button>
                    </div>
                    <div id="chapterOne" class="accordion-collapse collapse show" data-bs-parent="#courseContent">
                        <div class="accordion-body py-4">
                            <div class="mb-4">
                                <label class="ms-4">
                                    <span class="mb-0 h6 text-primary">1. Lesson</span>
                                    <small class="text-body d-block text-primary">content</small>
                                </label>
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
                            
                            <!-- Active Status (Only show in edit mode) -->
                            <div id="activeStatusSection" class="d-none">
                                <hr>
                                <div class="form-check form-switch ms-4">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="is_active" 
                                           id="isActive"
                                           value="1"
                                           {{ $lesson->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">
                                        Lesson Active
                                    </label>
                                </div>
                            </div>
                            <div id="prerequisiteSection" class="d-none">
                                <hr>
                                <div class="ms-4">
                                    <label for="prerequisite_lesson_id" class="form-label">Prerequisite Lesson</label>
                                    <select class="form-select" name="prerequisite_lesson_id" id="prerequisite_lesson_id">
                                        <option value="">None - First Lesson</option>
                                        @foreach($availableLessons as $availableLesson)
                                            <option value="{{ $availableLesson->id }}" 
                                                {{ $lesson->prerequisite_lesson_id == $availableLesson->id ? 'selected' : '' }}>
                                                {{ $availableLesson->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-2">
                                        Users must complete the prerequisite lesson before accessing this one
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Edit Button -->
            <div class="card mt-4">
                <div class="card-body text-center">
                    <button type="button" class="btn btn-primary w-100" id="toggleEditMode">
                        <i class="ri-edit-box-line me-2"></i> Edit Lesson
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script src="{{ asset('themes/sneat/assets/js/app-academy-course.js') }}"></script>
<script>
$(document).ready(function() {
    let isEditMode = false;
    const $toggleBtn = $('#toggleEditMode');
    const $editControls = $('#editModeControls');
    const $cancelBtn = $('#cancelEdit');
    const $form = $('#lessonEditForm');
    const $imageUpload = $('#imageUpload');
    const $lessonImage = $('#lessonImage');
    const $activeStatusSection = $('#activeStatusSection');
    const $prerequisiteSection = $('#prerequisiteSection'); // Add this
    
    // Store original values
    const originalValues = {};
    const originalImageSrc = $lessonImage.attr('src');
    
    $('input, textarea, select').each(function() {
        const name = $(this).attr('name');
        if (name) {
            originalValues[name] = $(this).is(':checkbox') ? $(this).prop('checked') : $(this).val();
        }
    });

    // Toggle edit mode
    $toggleBtn.on('click', function() {
        isEditMode = !isEditMode;
        
        if (isEditMode) {
            $('body').addClass('edit-mode-active');
            $editControls.removeClass('d-none');
            $activeStatusSection.removeClass('d-none');
            $prerequisiteSection.removeClass('d-none'); // Add this
            $toggleBtn.html('<i class="ri-eye-line me-2"></i> Preview Mode');
            $toggleBtn.removeClass('btn-primary').addClass('btn-label-secondary');
        } else {
            exitEditMode();
        }
    });

    // Cancel edit
    $cancelBtn.on('click', function() {
        // Restore original values
        $.each(originalValues, function(name, value) {
            const $el = $('[name="' + name + '"]');
            if ($el.length) {
                if ($el.is(':checkbox')) {
                    $el.prop('checked', value);
                } else {
                    $el.val(value);
                }
            }
        });
        
        // Restore original image
        $lessonImage.attr('src', originalImageSrc);
        
        exitEditMode();
    });

    function exitEditMode() {
        isEditMode = false;
        $('body').removeClass('edit-mode-active');
        $editControls.addClass('d-none');
        $activeStatusSection.addClass('d-none');
        $prerequisiteSection.addClass('d-none'); // Add this
        $toggleBtn.html('<i class="ri-edit-box-line me-2"></i> Edit Lesson');
        $toggleBtn.addClass('btn-primary').removeClass('btn-label-secondary');
        
        // Hide all edit fields
        $('.edit-mode').addClass('d-none');
        $('.view-mode').removeClass('d-none');
        $('.inline-edit-field').removeClass('editing');
    }

    // Click to edit individual fields (only in edit mode)
    $('.inline-edit-field').on('click', function(e) {
        if (!isEditMode) return;
        
        const $viewMode = $(this).find('.view-mode');
        const $editMode = $(this).find('.edit-mode');
        
        if ($viewMode.length && $editMode.length) {
            $viewMode.addClass('d-none');
            $editMode.removeClass('d-none');
            $(this).addClass('editing');
            
            // Focus on input/textarea
            const $input = $editMode.find('input, textarea, select');
            if ($input.length) $input.focus();
        }
    });

    // Image upload handling (only in edit mode)
    $('.image-upload-overlay').on('click', function(e) {
        if (isEditMode) {
            e.stopPropagation();
            $imageUpload.click();
        }
    });

    $imageUpload.on('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $lessonImage.attr('src', e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Form submission
    $form.on('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        $submitBtn.prop('disabled', true);
        $submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
        
        // Submit form
        this.submit();
    });
});
</script>
@endsection