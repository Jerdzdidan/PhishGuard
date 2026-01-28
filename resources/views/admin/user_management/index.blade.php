@extends('admin.layout.base')

@section('title')
Admin Accounts Management
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/app/admin_panel/user_management/custom_profile.css') }}">
@endsection

@section('nav_title')
Admin Accounts Management
@endsection

@section('body')
<div class="container-fluid">
    <div class="content-container">
        <!-- Page Header -->
        <x-table.page-header title="" subtitle="Manage system accounts" showBackButton="">
            <button class="btn btn-primary" data-bs-toggle="offcanvas" id="btn-add" data-bs-target="#add-or-update-modal">
                <i class="fa-solid fa-plus fa-1x me-2"></i>
                Add New Account
            </button>
        </x-table.page-header>
        
        <!-- Statistics Cards (Optional) -->
        <div class="row mb-4">
            
            {{-- TOTAL ADMINS --}}
            <x-table.stats-card 
                id="totalAdmins" 
                title="Total Admins" 
                icon="fa-solid fa-user fa-2x" 
                bgColor="bg-primary" 
                class="col-md-4"/>

            {{-- ACTIVE ACCOUNTS --}}
            <x-table.stats-card 
                id="activeAdmins" 
                title="Active" 
                icon="fa-solid fa-user-check fa-2x" 
                bgColor="bg-success" 
                class="col-md-4"/>

            {{-- INACTIVE ACCOUNTS --}}
            <x-table.stats-card 
                id="inactiveAdmins" 
                title="Inactive" 
                icon="fa-solid fa-user-xmark fa-2x" 
                bgColor="bg-danger" 
                class="col-md-4"/>

        </div>
        
        <!-- Status Filter -->
        <div class="row">
            <div class="col-md-2">
                <x-input.select-field
                    id="filter-status"
                    label="Filter by Status:"
                    icon="fa-solid fa-tags"
                    :options="[
                        ['value' => 'All', 'text' => 'All Status'],
                        ['value' => 'Active', 'text' => 'Active'],
                        ['value' => 'Inactive', 'text' => 'Inactive'],
                    ]"
                    placeholder="Select Status"
                />
            </div>
        </div>

        <!-- DataTable -->
        <x-table.table id="adminAccountsTable">
            {{-- Columns --}}
            <th>Id</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>User Type</th>
            <th>Status</th>
            <th>Actions</th>
        </x-table.table>

        @include('admin.user_management.form')

    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/shared/generic-datatable.js') }}"></script>
<script src="{{ asset('js/shared/generic-crud.js') }}"></script>
<script src="{{ asset('js/admin_panel/utils.js') }}"></script>
<script>
$(document).ready(function() {
    $('#filter-status').select2({
        minimumResultsForSearch: -1,
        placeholder: 'All Status'
    });

    $('#user_type').select2({
        placeholder: 'Select User Type',
    });

    // Initialize DataTable
    const adminTable = new GenericDataTable({
        tableId: 'adminAccountsTable',
        ajaxUrl: "{{ route('admin.users.data') }}",
        ajaxData: function(d) {
            d.status = $('#filter-status').val();
        },
        columns: [
            { data: "id", visible: false },
            { data: "first_name" },
            { data: "last_name" },
            { data: "email" },
            { data: "user_type" },
            { 
                data: "status",
                render: (data, type, row) => {
                    const status = row.status ? 'Active' : 'Inactive';
                    const badge = row.status ? 'success' : 'danger';
                    return `<span class="badge bg-label-${badge}">${status}</span>`;
                }
            },
            { 
                data: null,
                orderable: false,
                render: (data, type, row) => {
                    const toggleIcon = row.status
                        ? '<i class="fa-solid fa-toggle-on"></i>'
                        : '<i class="fa-solid fa-toggle-off"></i>';

                    return `
                        <button class="btn btn-sm btn-outline-primary" title="Toggle user status" onclick="adminCRUD.toggleStatus('${row.id}', '${row.name}')">
                            ${toggleIcon}
                        </button>

                        <button class="btn btn-sm btn-outline-warning" title="Edit user: ${row.name}" onclick="adminCRUD.edit('${row.id}')">
                            <i class="fa-solid fa-pencil"></i>
                        </button>

                        <button class="btn btn-sm btn-outline-danger" title="Delete user: ${row.name}" onclick="adminCRUD.delete('${row.id}', '${row.name}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        statsCards: {
            callback: (table) => {
                $.get("{{ route('admin.users.stats') }}", (data) => {
                    $('#totalAdmins').text(data.total);
                    $('#activeAdmins').text(data.active);
                    $('#inactiveAdmins').text(data.inactive);
                });
            }
        }
    }).init();
    
    window.adminCRUD = new GenericCRUD({
        baseUrl: '/admin/users/',
        storeUrl: "{{ route('admin.users.store') }}",
        editUrl: "{{ route('admin.users.edit', ':id') }}",
        updateUrl: "{{ route('admin.users.update', ':id') }}",
        destroyUrl: "{{ route('admin.users.destroy', ':id') }}",
        toggleUrl: "{{ route('admin.users.toggle', ':id') }}",

        entityName: 'User',
        dataTable: adminTable,
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
            adminCRUD.update(id, fd);
        } else {
            adminCRUD.create(fd);
        }
    });

    adminCRUD.onEditSuccess = (data) => {
        $('#add-or-update-form input[name="id"]').val(data.id);
        $('#add-or-update-form input[name="first_name"]').val(data.first_name);
        $('#add-or-update-form input[name="last_name"]').val(data.last_name);
        $('#add-or-update-form input[name="email"]').val(data.email);

        $('#add-or-update-form select[name="user_type"]').val(data.user_type).trigger('change');
    };

    $('#filter-status').on('change', function() {
        adminTable.reload();
    });

});
</script>

@endsection