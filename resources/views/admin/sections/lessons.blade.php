@extends('admin.layout.base')

@section('title')
{{ $section->name }} - Lessons
@endsection

@section('head')
@endsection

@section('nav_title')
{{ $section->name }} - Lesson Management
@endsection

@section('body')
<div class="container-fluid">
    <div class="content-container">
        <!-- Page Header -->
        <x-table.page-header title="" subtitle="Manage lessons in {{ $section->name }}">
            <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Sections
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                <i class="fa-solid fa-plus fa-1x me-2"></i>
                Add Lesson
            </button>
        </x-table.page-header>

        <!-- Section Info Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">{{ $section->name }}</h5>
                                <p class="text-muted mb-0">{{ $section->description ?? 'No description' }}</p>
                            </div>
                            <div class="d-flex gap-3">
                                <span class="badge bg-label-primary fs-6">
                                    <i class="fa-solid fa-hashtag me-1"></i>
                                    {{ $section->section_code }}
                                </span>
                                <span class="badge bg-label-info fs-6">
                                    <i class="fa-solid fa-book me-1"></i>
                                    {{ $section->lessons->count() }} Lessons
                                </span>
                                <span class="badge bg-label-warning fs-6">
                                    <i class="fa-solid fa-users me-1"></i>
                                    {{ $section->students->count() }} Students
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lessons Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="lessonsTable">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Lesson</th>
                                <th>Difficulty</th>
                                <th>Duration</th>
                                <th>Quiz</th>
                                <th>Simulation</th>
                                <th style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($section->lessons as $index => $lesson)
                            <tr id="lesson-row-{{ $lesson->id }}">
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($lesson->image_path)
                                        <img src="{{ asset('storage/' . $lesson->image_path) }}" 
                                             class="rounded me-3" 
                                             style="width: 48px; height: 48px; object-fit: cover;" 
                                             alt="{{ $lesson->title }}">
                                        @else
                                        <div class="rounded me-3 d-flex align-items-center justify-content-center bg-label-primary" 
                                             style="width: 48px; height: 48px;">
                                            <i class="fa-solid fa-book-open"></i>
                                        </div>
                                        @endif
                                        <div>
                                            <strong>{{ $lesson->title }}</strong>
                                            <small class="d-block text-muted">{{ Str::limit($lesson->description, 60) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-{{ $lesson->difficulty === 'EASY' ? 'success' : ($lesson->difficulty === 'MEDIUM' ? 'warning' : 'danger') }}">
                                        {{ $lesson->difficulty }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fa-regular fa-clock me-1 text-muted"></i>
                                    {{ $lesson->time }} min
                                </td>
                                <td>
                                    @if($lesson->quiz)
                                        <span class="badge bg-label-success">
                                            <i class="fa-solid fa-check me-1"></i> {{ $lesson->quiz->questions->count() }} Qs
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lesson->has_simulation)
                                        <span class="badge bg-label-success">
                                            <i class="fa-solid fa-check me-1"></i> Enabled
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            title="Remove from section" 
                                            onclick="removeLesson({{ $lesson->id }}, '{{ addslashes($lesson->title) }}')">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa-solid fa-book-open fa-3x mb-3 d-block"></i>
                                        <h6>No lessons assigned yet</h6>
                                        <p class="mb-3">Add lessons to this section for your students</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                                            <i class="fa-solid fa-plus me-1"></i> Add Lesson
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Add Lesson Modal -->
<div class="modal fade" id="addLessonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i>Add Lesson to Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="lessonSearch" class="form-label">Search Lessons</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" class="form-control" id="lessonSearch" placeholder="Type to search available lessons...">
                    </div>
                    <small class="text-muted">Only lessons not yet in this section are shown</small>
                </div>
                
                <div id="lessonResults" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center py-4 text-muted">
                        <span class="spinner-border spinner-border-sm me-1"></span> Loading available lessons...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const sectionId = "{{ Crypt::encryptString($section->id) }}";
    let searchTimer;

    // Load lessons when modal opens
    $('#addLessonModal').on('shown.bs.modal', function() {
        loadAvailableLessons('');
        $('#lessonSearch').focus();
    });

    // Debounced search
    $('#lessonSearch').on('keyup', function() {
        clearTimeout(searchTimer);
        const val = $(this).val();
        searchTimer = setTimeout(() => loadAvailableLessons(val), 300);
    });

    function loadAvailableLessons(search) {
        $('#lessonResults').html('<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Searching...</div>');
        
        $.get(`/admin/sections/${sectionId}/lessons/available`, { search: search }, function(lessons) {
            if (lessons.length === 0) {
                $('#lessonResults').html(`
                    <div class="text-center py-4 text-muted">
                        <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                        <p class="mb-0">No available lessons found</p>
                        <small>All lessons may already be assigned to this section</small>
                    </div>
                `);
                return;
            }

            const difficultyColors = { 'EASY': 'success', 'MEDIUM': 'warning', 'HARD': 'danger' };
            
            let html = '<div class="list-group list-group-flush">';
            lessons.forEach(l => {
                const color = difficultyColors[l.difficulty] || 'secondary';
                html += `
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded me-3 d-flex align-items-center justify-content-center bg-label-primary" 
                                 style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fa-solid fa-book-open"></i>
                            </div>
                            <div>
                                <strong>${l.title}</strong>
                                <div class="mt-1">
                                    <span class="badge bg-label-${color}">${l.difficulty}</span>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="addLesson(${l.id}, this)">
                            <i class="fa-solid fa-plus me-1"></i> Add
                        </button>
                    </div>
                `;
            });
            html += '</div>';
            $('#lessonResults').html(html);
        });
    }

    window.addLesson = function(lessonId, btn) {
        const $btn = $(btn);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: `/admin/sections/${sectionId}/lessons/add`,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', lesson_id: lessonId },
            success: function(response) {
                $btn.closest('.list-group-item').fadeOut(300, function() { $(this).remove(); });
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: response.message, showConfirmButton: false, timer: 2000
                });
                // Reload page after brief delay to update table
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-plus me-1"></i> Add');
                Swal.fire('Error', xhr.responseJSON?.message || 'Failed to add lesson.', 'error');
            }
        });
    };

    window.removeLesson = function(lessonId, title) {
        Swal.fire({
            title: 'Remove Lesson?',
            text: `Remove "${title}" from this section?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, remove'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/sections/${sectionId}/lessons/${lessonId}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        $(`#lesson-row-${lessonId}`).fadeOut(300, function() { $(this).remove(); });
                        Swal.fire({
                            toast: true, position: 'top-end', icon: 'success',
                            title: response.message, showConfirmButton: false, timer: 2000
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to remove lesson.', 'error');
                    }
                });
            }
        });
    };
});
</script>
@endsection
