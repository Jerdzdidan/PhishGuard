@extends('user.layout.base')

@section('title')
Join a Section
@endsection

@section('nav_title')
JOIN SECTION
@endsection

@section('style')
<style>
    .join-section-card {
        max-width: 500px;
        margin: 0 auto;
    }
    .section-code-input {
        text-transform: uppercase;
        letter-spacing: 4px;
        font-size: 1.5rem;
        text-align: center;
        font-weight: 600;
    }
    .enrolled-section {
        transition: all 0.3s ease;
    }
    .enrolled-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        {{-- Join Section Card --}}
        <div class="card mb-4 join-section-card">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="bx bxs-graduation" style="font-size: 4rem; color: #696cff;"></i>
                </div>
                <h3 class="mb-2">Join a Section</h3>
                <p class="text-muted mb-4">Enter the section code provided by your teacher to get started.</p>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="input-group mb-3">
                            <input type="text" 
                                   class="form-control section-code-input" 
                                   id="sectionCode" 
                                   placeholder="ABC123" 
                                   maxlength="10"
                                   autocomplete="off">
                            <button class="btn btn-primary px-4" type="button" id="joinBtn">
                                <i class="bx bx-log-in me-1"></i> Join
                            </button>
                        </div>
                        <small class="text-muted">Ask your teacher for the section code</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- enrolled Sections --}}
        @if($enrolledSections->count() > 0)
        <h5 class="mb-3">Your Sections</h5>
        <div class="row g-4">
            @foreach($enrolledSections as $section)
            <div class="col-md-6">
                <div class="card enrolled-section h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $section->name }}</h5>
                                <small class="text-muted">
                                    <i class="bx bx-user me-1"></i>
                                    Teacher: {{ $section->teacher->first_name }} {{ $section->teacher->last_name }}
                                </small>
                            </div>
                            <span class="badge bg-label-primary">{{ $section->section_code }}</span>
                        </div>
                        @if($section->description)
                            <p class="text-muted small mb-3">{{ $section->description }}</p>
                        @endif
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bx bx-book me-1"></i>
                                {{ $section->lessons->count() }} lessons
                            </small>
                            <a href="{{ route('home') }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-right-arrow-alt"></i> Go to Lessons
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#joinBtn').on('click', function() {
        const code = $('#sectionCode').val().trim();
        if (!code) {
            Swal.fire('Error', 'Please enter a section code.', 'warning');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '{{ route("sections.join") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                section_code: code
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Joined!',
                    text: response.message,
                    confirmButtonColor: '#696cff'
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Failed to join section.', 'error');
                $btn.prop('disabled', false).html('<i class="bx bx-log-in me-1"></i> Join');
            }
        });
    });

    // Enter key support
    $('#sectionCode').on('keypress', function(e) {
        if (e.which === 13) {
            $('#joinBtn').click();
        }
    });
});
</script>
@endsection
