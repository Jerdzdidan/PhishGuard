@extends('admin.layout.base')

@section('title')
{{ $scenario->title }} - Items
@endsection

@section('nav_title')
Scenario Items
@endsection

@section('body')
<div class="container-fluid">
    <div class="content-container">
        <!-- Page Header -->
        <x-table.page-header title="" subtitle="Manage items for: {{ $scenario->title }}">
            <a href="{{ route('admin.scenarios.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
            <button class="btn btn-primary" data-bs-toggle="offcanvas" id="btn-add" data-bs-target="#add-or-update-modal">
                <i class="fa-solid fa-plus fa-1x me-2"></i>
                New Item
            </button>
        </x-table.page-header>

        <!-- Scenario Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">{{ $scenario->title }}</h5>
                                <p class="text-muted mb-0">{{ $scenario->description ?? 'No description' }}</p>
                                <div class="mt-2">
                                    <span class="badge bg-label-primary me-2">{{ $scenario->lesson->title }}</span>
                                    @php
                                        $typeBadge = match($scenario->type) {
                                            'pre_assessment' => 'info',
                                            'post_assessment' => 'warning',
                                            'simulation' => 'primary',
                                        };
                                    @endphp
                                    <span class="badge bg-label-{{ $typeBadge }}">{{ ucwords(str_replace('_', ' ', $scenario->type)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <x-table.table id="itemsTable">
            <th>Id</th>
            <th>Order</th>
            <th>Title</th>
            <th>Correct Action</th>
            <th>Options</th>
            <th>Status</th>
            <th>Actions</th>
        </x-table.table>

        @include('admin.scenarios.item_form')
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/shared/generic-datatable.js') }}"></script>
<script src="{{ asset('js/shared/generic-crud.js') }}"></script>
<script src="{{ asset('js/admin_panel/utils.js') }}"></script>
<script>
$(document).ready(function() {
    const scenarioId = "{{ Crypt::encryptString($scenario->id) }}";

    // Initialize DataTable
    const itemTable = new GenericDataTable({
        tableId: 'itemsTable',
        ajaxUrl: `/admin/scenarios/${scenarioId}/items/data`,
        columns: [
            { data: "id", visible: false },
            { data: "order", width: "50px" },
            { data: "title" },
            { 
                data: "correct_action",
                render: (data) => data ? `<code>${data}</code>` : '<span class="text-muted">N/A</span>'
            },
            {
                data: "options_count",
                render: (data) => `<span class="badge bg-label-info">${data} options</span>`
            },
            {
                data: "is_active",
                render: (data) => {
                    const status = data ? 'Active' : 'Inactive';
                    const badge = data ? 'success' : 'danger';
                    return `<span class="badge bg-label-${badge}">${status}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: (data, type, row) => {
                    return `
                        <button class="btn btn-sm btn-outline-warning" title="Edit" onclick="editItem('${row.id}')">
                            <i class="fa-solid fa-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteItem('${row.id}', '${row.title}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    }).init();

    // Dynamic options fields
    let optionIndex = 0;
    window.addOption = function() {
        optionIndex++;
        $('#optionsContainer').append(`
            <div class="input-group mb-2 option-row">
                <input type="text" class="form-control" name="options[]" placeholder="Option ${optionIndex}">
                <button type="button" class="btn btn-outline-danger" onclick="$(this).parent().remove()">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        `);
    };

    // Form submission
    $('#add-or-update-form').on('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        const id = $(this).find('input[name="id"]').val();
        const $btn = $(this).find('button[type="submit"]');
        
        $btn.prop('disabled', true);

        if (id) {
            fd.append('_method', 'PUT');
            $.ajax({
                url: `/admin/scenarios/items/update/${id}`,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', response.message, 'success');
                        itemTable.reload();
                        const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('add-or-update-modal'));
                        if (offcanvas) offcanvas.hide();
                        $('#add-or-update-form')[0].reset();
                        $('input[name="id"]').val('');
                    }
                },
                error: function(xhr) { Swal.fire('Error', xhr.responseJSON?.message || 'Failed.', 'error'); },
                complete: function() { $btn.prop('disabled', false); }
            });
        } else {
            $.ajax({
                url: `/admin/scenarios/${scenarioId}/items/store`,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', response.message, 'success');
                        itemTable.reload();
                        const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('add-or-update-modal'));
                        if (offcanvas) offcanvas.hide();
                        $('#add-or-update-form')[0].reset();
                    }
                },
                error: function(xhr) { Swal.fire('Error', xhr.responseJSON?.message || 'Failed.', 'error'); },
                complete: function() { $btn.prop('disabled', false); }
            });
        }
    });

    // Edit item
    window.editItem = function(id) {
        $.get(`/admin/scenarios/items/edit/${id}`, function(data) {
            $('input[name="id"]').val(data.id);
            $('input[name="title"]').val(data.title);
            $('textarea[name="description"]').val(data.description);
            $('textarea[name="content"]').val(data.content);
            $('input[name="correct_action"]').val(data.correct_action);
            
            // Load options
            $('#optionsContainer').empty();
            if (data.options && data.options.length) {
                data.options.forEach(function(opt, i) {
                    optionIndex = i + 1;
                    $('#optionsContainer').append(`
                        <div class="input-group mb-2 option-row">
                            <input type="text" class="form-control" name="options[]" value="${opt}" placeholder="Option ${optionIndex}">
                            <button type="button" class="btn btn-outline-danger" onclick="$(this).parent().remove()">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                    `);
                });
            }

            const offcanvas = new bootstrap.Offcanvas(document.getElementById('add-or-update-modal'));
            offcanvas.show();
        });
    };

    // Delete item
    window.deleteItem = function(id, title) {
        Swal.fire({
            title: 'Delete Item?',
            text: `Are you sure you want to delete "${title}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/scenarios/items/destroy/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success');
                            itemTable.reload();
                        }
                    }
                });
            }
        });
    };
});
</script>
@endsection
