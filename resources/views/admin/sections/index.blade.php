@extends('admin.layout.base')

@section('title')
Section Management
@endsection

@section('head')
@endsection

@section('nav_title')
Section Management
@endsection

@section('body')
<div class="container-fluid">
    <div class="content-container">
        <!-- Page Header -->
        <x-table.page-header title="" subtitle="Manage your class sections" showBackButton="">
            <button class="btn btn-primary" data-bs-toggle="offcanvas" id="btn-add" data-bs-target="#add-or-update-modal">
                <i class="fa-solid fa-plus fa-1x me-2"></i>
                New Section
            </button>
        </x-table.page-header>

        <!-- DataTable -->
        <x-table.table id="sectionsTable">
            <th>Id</th>
            <th>Name</th>
            <th>Section Code</th>
            <th>Description</th>
            <th>Teacher</th>
            <th>Students</th>
            <th>Status</th>
            <th>Actions</th>
        </x-table.table>

        @include('admin.sections.form')

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
    const sectionTable = new GenericDataTable({
        tableId: 'sectionsTable',
        ajaxUrl: "{{ route('admin.sections.data') }}",
        columns: [
            { data: "id", visible: false },
            { data: "name" },
            { 
                data: "section_code",
                render: (data) => data 
                    ? `<code class="bg-light px-2 py-1 rounded" style="cursor:pointer; font-size: 0.9rem;" onclick="navigator.clipboard.writeText('${data}'); Swal.fire({toast:true,position:'top-end',icon:'success',title:'Code copied!',showConfirmButton:false,timer:1000})" title="Click to copy">${data} <i class='fa-regular fa-copy ms-1 text-muted' style='font-size:0.75rem'></i></code>` 
                    : '<span class="text-muted">N/A</span>'
            },
            { 
                data: "description",
                render: (data) => data ? (data.length > 50 ? data.substring(0, 50) + '...' : data) : '<span class="text-muted">N/A</span>'
            },
            { data: "teacher_name" },
            { 
                data: "students_count",
                render: (data) => `<span class="badge bg-label-info">${data} students</span>`
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
                        <a href="/admin/sections/${row.id}/lessons" class="btn btn-sm btn-outline-primary" title="Manage lessons">
                            <i class="fa-solid fa-book"></i>
                        </a>

                        <a href="/admin/sections/${row.id}/students" class="btn btn-sm btn-outline-info" title="Manage students">
                            <i class="fa-solid fa-users"></i>
                        </a>

                        <button class="btn btn-sm btn-outline-warning" title="Edit section" onclick="sectionCRUD.edit('${row.id}')">
                            <i class="fa-solid fa-pencil"></i>
                        </button>

                        <button class="btn btn-sm btn-outline-danger" title="Delete section" onclick="sectionCRUD.delete('${row.id}', '${row.name}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    }).init();
    
    window.sectionCRUD = new GenericCRUD({
        baseUrl: '/admin/sections/',
        storeUrl: "{{ route('admin.sections.store') }}",
        editUrl: "{{ route('admin.sections.edit', ':id') }}",
        updateUrl: "{{ route('admin.sections.update', ':id') }}",
        destroyUrl: "{{ route('admin.sections.destroy', ':id') }}",

        entityName: 'Section',
        dataTable: sectionTable,
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
            sectionCRUD.update(id, fd);
        } else {
            sectionCRUD.create(fd);
        }
    });

    sectionCRUD.onEditSuccess = (data) => {
        $('#add-or-update-form input[name="id"]').val(data.id);
        $('#add-or-update-form input[name="name"]').val(data.name);
        $('#add-or-update-form textarea[name="description"]').val(data.description);
    };
});
</script>
@endsection
