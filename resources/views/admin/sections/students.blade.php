@extends('admin.layout.base')

@section('title')
{{ $section->name }} - Students
@endsection

@section('head')
@endsection

@section('nav_title')
{{ $section->name }} - Student Management
@endsection

@section('body')
<div class="container-fluid">
    <div class="content-container">
        <!-- Page Header -->
        <x-table.page-header title="" subtitle="Manage students in {{ $section->name }}">
            <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Sections
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                <i class="fa-solid fa-plus fa-1x me-2"></i>
                Add Student
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
                                <span class="badge bg-label-primary fs-6" style="cursor:pointer;" onclick="navigator.clipboard.writeText('{{ $section->section_code }}'); Swal.fire({toast:true,position:'top-end',icon:'success',title:'Code copied!',showConfirmButton:false,timer:1000})" title="Click to copy">
                                    <i class="fa-solid fa-hashtag me-1"></i>
                                    {{ $section->section_code }}
                                    <i class="fa-regular fa-copy ms-1"></i>
                                </span>
                                <span class="badge bg-label-info fs-6">
                                    <i class="fa-solid fa-users me-1"></i>
                                    {{ $section->students->count() }} Students
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <x-table.table id="studentsTable">
            <th>Id</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Actions</th>
        </x-table.table>

    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Student to Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="studentEmail" class="form-label">Student Email</label>
                    <input type="email" class="form-control" id="studentEmail" placeholder="Enter student email address">
                    <small class="text-muted">Enter the email address of a registered student to add them to this section.</small>
                </div>
                <div id="studentSearchResults" class="list-group mt-2" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAddStudent">
                    <i class="fa-solid fa-plus me-1"></i> Add Student
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/shared/generic-datatable.js') }}"></script>
<script src="{{ asset('js/admin_panel/utils.js') }}"></script>
<script>
$(document).ready(function() {
    const sectionId = "{{ Crypt::encryptString($section->id) }}";

    // Initialize DataTable
    const studentsTable = new GenericDataTable({
        tableId: 'studentsTable',
        ajaxUrl: `/admin/sections/${sectionId}/students/data`,
        columns: [
            { data: "id", visible: false },
            { data: "first_name" },
            { data: "last_name" },
            { data: "email" },
            { 
                data: null,
                orderable: false,
                render: (data, type, row) => {
                    return `
                        <button class="btn btn-sm btn-outline-danger" title="Remove student" onclick="removeStudent('${row.id}', '${row.first_name} ${row.last_name}')">
                            <i class="fa-solid fa-user-minus"></i> Remove
                        </button>
                    `;
                }
            }
        ]
    }).init();

    // Add student
    $('#confirmAddStudent').on('click', function() {
        const email = $('#studentEmail').val().trim();
        if (!email) {
            Swal.fire('Error', 'Please enter a student email.', 'error');
            return;
        }

        $.ajax({
            url: `/admin/sections/${sectionId}/students/add`,
            method: 'POST',
            data: { email: email, _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success', response.message, 'success');
                    $('#addStudentModal').modal('hide');
                    $('#studentEmail').val('');
                    studentsTable.reload();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Failed to add student.';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // Remove student
    window.removeStudent = function(studentId, studentName) {
        Swal.fire({
            title: 'Remove Student?',
            text: `Are you sure you want to remove ${studentName} from this section?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, remove'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/sections/${sectionId}/students/${studentId}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Removed!', response.message, 'success');
                            studentsTable.reload();
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to remove student.', 'error');
                    }
                });
            }
        });
    };
});
</script>
@endsection
