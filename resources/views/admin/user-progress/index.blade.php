<!-- resources/views/admin/user-progress/index.blade.php -->
@extends('admin.layout.base')

@section('title')
USER PROGRESS
@endsection

@section('nav_title')
USER PROGRESS TRACKING
@endsection

@section('style')
<style>
.progress-badge {
    font-size: 0.875rem;
    padding: 0.35rem 0.65rem;
}
.user-card {
    transition: all 0.3s;
}
.user-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endsection

@section('body')
<div class="container-fluid">
    <div class="content-container">
        <!-- Page Header -->
        <x-table.page-header title="" subtitle="Monitor student learning progress and performance">
        </x-table.page-header>
        
        <!-- DataTable -->
                <x-table.table id="userProgressTable">
                    {{-- Columns --}}
                    <th>Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Lessons Completed</th>
                    <th>Quiz Average</th>
                    <th>Simulation Average</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </x-table.table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/shared/generic-datatable.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    const progressTable = $('#userProgressTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.user-progress.data') }}",
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id', visible: false },
            { 
                data: null,
                name: 'name',
                orderable: true,
                searchable: true,
                render: function(data, type, row) {
                    return `${row.first_name} ${row.last_name}`;
                }
            },
            { 
                data: 'email', 
                name: 'email',
                orderable: true,
                searchable: true
            },
            { 
                data: null,
                name: 'lessons_completed',
                orderable: true,
                searchable: false,
                render: function(data, type, row) {
                    const completed = row.lessons_completed;
                    const total = row.total_lessons;
                    const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
                    
                    let badgeClass = 'bg-danger';
                    if (percentage >= 75) badgeClass = 'bg-success';
                    else if (percentage >= 50) badgeClass = 'bg-warning';
                    else if (percentage >= 25) badgeClass = 'bg-info';
                    
                    return `
                        <div class="d-flex align-items-center">
                            <span class="badge ${badgeClass} progress-badge me-2">${completed}/${total}</span>
                            <div class="progress flex-grow-1" style="height: 8px; max-width: 100px;">
                                <div class="progress-bar ${badgeClass}" style="width: ${percentage}%"></div>
                            </div>
                            <span class="ms-2 small">${percentage}%</span>
                        </div>
                    `;
                }
            },
            { 
                data: 'quiz_avg',
                name: 'quiz_avg',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    if (data === 'N/A') return '<span class="text-muted">N/A</span>';
                    
                    const score = parseFloat(data);
                    let badgeClass = 'bg-danger';
                    if (score >= 80) badgeClass = 'bg-success';
                    else if (score >= 70) badgeClass = 'bg-warning';
                    else if (score >= 60) badgeClass = 'bg-info';
                    
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            { 
                data: 'simulation_avg',
                name: 'simulation_avg',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    if (data === 'N/A') return '<span class="text-muted">N/A</span>';
                    
                    const score = parseFloat(data);
                    let badgeClass = 'bg-danger';
                    if (score >= 80) badgeClass = 'bg-success';
                    else if (score >= 70) badgeClass = 'bg-warning';
                    else if (score >= 60) badgeClass = 'bg-info';
                    
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            { 
                data: 'created_at',
                name: 'created_at',
                orderable: true,
                searchable: false,
                render: function(data, type, row) {
                    return new Date(data).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                }
            },
            { 
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false
            }
        ],
        order: [[7, 'desc']], // Order by created_at (joined date) descending
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        language: {
            search: "Search users:",
            lengthMenu: "Show _MENU_ users",
            info: "Showing _START_ to _END_ of _TOTAL_ users",
            infoEmpty: "No users found",
            infoFiltered: "(filtered from _MAX_ total users)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        responsive: true,
        autoWidth: false
    });
});
</script>
@endsection