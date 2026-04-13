@extends('admin.layout.base')

@section('title')
Scenario Management
@endsection

@section('nav_title')
Scenario Management
@endsection

@section('body')
<div class="container-fluid">
    <div class="content-container">
        <!-- Page Header -->
        <x-table.page-header title="" subtitle="Manage pre-assessments, post-assessments, and simulation scenarios">
            <button class="btn btn-primary" data-bs-toggle="offcanvas" id="btn-add" data-bs-target="#add-or-update-modal">
                <i class="fa-solid fa-plus fa-1x me-2"></i>
                New Scenario
            </button>
        </x-table.page-header>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Filter by Type</label>
                        <select class="form-select" id="filterType">
                            <option value="">All Types</option>
                            <option value="pre_assessment">Pre-Assessment</option>
                            <option value="post_assessment">Post-Assessment</option>
                            <option value="simulation">Simulation</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Filter by Lesson</label>
                        <select class="form-select" id="filterLesson">
                            <option value="">All Lessons</option>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <x-table.table id="scenariosTable">
            <th>Id</th>
            <th>Title</th>
            <th>Lesson</th>
            <th>Type</th>
            <th>Items</th>
            <th>Status</th>
            <th>Actions</th>
        </x-table.table>

        @include('admin.scenarios.form')
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/shared/generic-datatable.js') }}"></script>
<script src="{{ asset('js/shared/generic-crud.js') }}"></script>
<script src="{{ asset('js/admin_panel/utils.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    const scenarioTable = new GenericDataTable({
        tableId: 'scenariosTable',
        ajaxUrl: "{{ route('admin.scenarios.data') }}",
        ajaxData: function(d) {
            d.type = $('#filterType').val();
            d.lesson_id = $('#filterLesson').val();
        },
        columns: [
            { data: "id", visible: false },
            { data: "title" },
            { data: "lesson_title" },
            { data: "type_badge" },
            {
                data: "items_count",
                render: (data) => `<span class="badge bg-label-info">${data} items</span>`
            },
            {
                data: "is_active",
                render: (data, type, row) => {
                    const status = data ? 'Active' : 'Inactive';
                    const badge = data ? 'success' : 'danger';
                    return `<span class="badge bg-label-${badge} cursor-pointer" 
                                onclick="toggleScenario('${row.id}')"
                                title="Click to toggle">${status}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: (data, type, row) => {
                    return `
                        <a href="/admin/scenarios/${row.id}/items" class="btn btn-sm btn-outline-info" title="Manage items">
                            <i class="fa-solid fa-list"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-warning" title="Edit" onclick="scenarioCRUD.edit('${row.id}')">
                            <i class="fa-solid fa-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="scenarioCRUD.delete('${row.id}', '${row.title}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    }).init();

    // Filter handlers
    $('#filterType, #filterLesson').on('change', function() {
        scenarioTable.reload();
    });

    // Toggle active status
    window.toggleScenario = function(id) {
        $.ajax({
            url: `/admin/scenarios/toggle/${id}`,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    scenarioTable.reload();
                    Swal.fire({ icon: 'success', title: response.message, timer: 1500, showConfirmButton: false });
                }
            }
        });
    };

    window.scenarioCRUD = new GenericCRUD({
        baseUrl: '/admin/scenarios/',
        storeUrl: "{{ route('admin.scenarios.store') }}",
        editUrl: "{{ route('admin.scenarios.edit', ':id') }}",
        updateUrl: "{{ route('admin.scenarios.update', ':id') }}",
        destroyUrl: "{{ route('admin.scenarios.destroy', ':id') }}",
        entityName: 'Scenario',
        dataTable: scenarioTable,
        csrfToken: "{{ csrf_token() }}",
        form: '#add-or-update-form',
        modal: '#add-or-update-modal'
    });

    $('#add-or-update-form').on('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        const id = $(this).find('input[name="id"]').val();

        if (id) {
            fd.append('_method', 'PUT');
            scenarioCRUD.update(id, fd);
        } else {
            scenarioCRUD.create(fd);
        }
    });

    scenarioCRUD.onEditSuccess = (data) => {
        $('#add-or-update-form input[name="id"]').val(data.id);
        $('#add-or-update-form input[name="title"]').val(data.title);
        $('#add-or-update-form textarea[name="description"]').val(data.description);
        $('#add-or-update-form select[name="lesson_id"]').val(data.lesson_id);
        $('#add-or-update-form select[name="type"]').val(data.type);
    };
});
</script>
@endsection
