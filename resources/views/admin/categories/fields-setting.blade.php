@extends('admin.layouts.app')
@section('title', 'Dynamic Category Fields')

@push('styles')
<style>
    .badge-type-text { background: #dbeafe; color: #1d4ed8; }
    .badge-type-textarea { background: #ccfbf1; color: #0f766e; }
    .badge-type-number { background: #fed7aa; color: #c2410c; }
    .badge-type-select { background: #e9d5ff; color: #7c3aed; }
    .badge-type-radio { background: #fce7f3; color: #be185d; }
    .badge-type-checkbox { background: #f3f4f6; color: #4b5563; }
    .badge-type-file { background: #d1fae5; color: #059669; }
    .badge-type-date { background: #cffafe; color: #0891b2; }
    .badge-type-time { background: #ede9fe; color: #6d28d9; }
    .badge-type-url { background: #e0e7ff; color: #4338ca; }
    .badge-type-color { background: #fce7f3; color: #9d174d; }

    .badge-pill-active { background: #d1fae5; color: #059669; padding: 4px 14px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
    .badge-pill-inactive { background: #fee2e2; color: #dc2626; padding: 4px 14px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
    .badge-yes { background: #d1fae5; color: #059669; padding: 2px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
    .badge-no { background: #f3f4f6; color: #6b7280; padding: 2px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; display: inline-block; }

    .source-category-select + .select2-container .select2-selection--single {
        border: 1px solid #ced4da;
        height: 40px;
        border-radius: 8px;
        padding: 0;
    }
    .source-category-select + .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
        padding-right: 12px;
        color: #6c757d;
    }
    .source-category-select + .select2-container .select2-selection--single .select2-selection__rendered:not([title]) {
        padding-left: 12px;
    }
    .source-category-select + .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }
    .target-category-select + .select2-container {
        width: 100% !important;
    }
    .target-category-select + .select2-container .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 8px;
        min-height: 40px;
    }

    .fields-table-card .dataTables_wrapper .dataTables_filter {
        display: none;
    }
    .fields-table-card .dataTables_wrapper .dataTables_length {
        margin-bottom: 1rem;
    }
        height: 40px;
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 0.375rem 0.75rem;
    }
    .fields-table-card .dataTables_wrapper .dataTables_info {
        padding-top: 1rem;
    }
    .fields-table-card .dataTables_wrapper .dataTables_paginate {
        padding-top: 1rem;
    }
    .fields-table-card .table tbody td {
        border-color: #eef2f7;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper p-4">

    {{-- ─────── Header ─────── --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="font-size: 1.5rem;">Add Dynamic Category Field</h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Add a new field that will appear when creating or editing a category.</p>
        </div>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" style="border-radius: 8px; font-weight: 600; white-space: nowrap;">
            <i class="mdi mdi-plus" style="font-size: 1.2rem;"></i>
            Add New Field
        </button>
    </div>

    {{-- ─────── Add/Edit Form Card ─────── --}}
    <div class="collapse mb-4" id="collapseExample">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.category-fields.store') }}">
                    @csrf
                    <div class="row g-4">

                        {{-- Row 1: Category, Field Label, Field Type, Options --}}
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Category</label>
                            <select name="category_id" class="form-select" required style="height: 46px; border-radius: 8px;">
                                <option value="">Choose Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ ucfirst($category->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Field Label</label>
                            <input type="text" name="field_label" class="form-control" placeholder="Example: Material Type" required style="height: 46px; border-radius: 8px;">
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Field Type</label>
                            <select name="field_type" class="form-select" required style="height: 46px; border-radius: 8px;">
                                <option value="">Select Type</option>
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="number">Number</option>
                                <option value="select">Select</option>
                                <option value="radio">Radio</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="file">File Upload</option>
                                <option value="date">Date</option>
                                <option value="time">Time</option>
                                <option value="url">URL</option>
                                <option value="color">Color</option>
                            </select>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Options</label>
                            <input type="text" name="field_options" class="form-control" placeholder="Enter options separated by comma" style="height: 46px; border-radius: 8px;">
                            <div class="form-text" style="font-size: 0.75rem;">Example: Option1, Option2, Option3</div>
                        </div>

                        {{-- Row 2: Required + More Settings --}}
                        <div class="col-12">
                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-4 justify-content-between pt-2">
                                <div class="d-flex align-items-start gap-3">
                                    <input type="checkbox" name="is_required" value="1" class="form-check-input mt-1" id="requiredField" style="width: 18px; height: 18px; flex-shrink: 0;">
                                    <div>
                                        <label class="fw-semibold mb-0" for="requiredField">Required Field</label>
                                        <div class="text-muted" style="font-size: 0.75rem; line-height: 1.3;">Enable if this field is mandatory</div>
                                    </div>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="height: 40px; border-radius: 8px;">
                                        <i class="mdi mdi-cog"></i>
                                        More Settings
                                        <i class="mdi mdi-chevron-down"></i>
                                    </button>
                                    <div class="dropdown-menu p-3" style="min-width: 320px; border-radius: 10px;">
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold">Sort Order</label>
                                                <input type="number" name="sort_order" class="form-control" value="0" style="height: 38px; border-radius: 6px;">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold">Status</label>
                                                <select name="status" class="form-select" style="height: 38px; border-radius: 6px;">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold">Placeholder</label>
                                                <input type="text" name="placeholder" class="form-control" placeholder="Enter placeholder" style="height: 38px; border-radius: 6px;">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold">Default Value</label>
                                                <input type="text" name="default_value" class="form-control" placeholder="Default Value" style="height: 38px; border-radius: 6px;">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-semibold">Validation Rules</label>
                                                <input type="text" name="validation_rules" class="form-control" placeholder="required|max:255" style="height: 38px; border-radius: 6px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Help Text --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Help Text</label>
                            <textarea name="help_text" class="form-control" rows="2" placeholder="Helpful text for user" style="border-radius: 8px;"></textarea>
                        </div>

                        
                        <div class="col-12 d-flex gap-3 pt-2">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-toggle="collapse" data-bs-target="#collapseExample" style="height: 46px; border-radius: 8px; font-weight: 500;">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4" style="height: 46px; border-radius: 8px; font-weight: 600;">
                                <i class="mdi mdi-content-save me-1"></i>
                                <span id="saveBtnLabel">Save Field</span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

   

    {{-- {{─ ─────── Table Section ─────── ─}} --}}
    <div class="card border-0 shadow-sm fields-table-card" style="border-radius: 12px;">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="fw-bold mb-0">All Dynamic Fields</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#duplicateCollapse" aria-expanded="false" aria-controls="duplicateCollapse" style="height: 40px; border-radius: 8px; white-space: nowrap;">
                    <i class="mdi mdi-content-copy"></i>
                    Duplicate Fields
                </button>
                <div class="d-flex gap-2">
                    <div class="position-relative">
                        <i class="mdi mdi-magnify position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 1.1rem;"></i>
                        <input type="text" id="tableSearch" class="form-control bg-light border-0 ps-5" placeholder="Search fields..." style="height: 40px; border-radius: 8px; width: 240px;">
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px; border-radius: 8px;">
                            <i class="mdi mdi-filter-variant"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 280px; border-radius: 10px;">
                            <h6 class="fw-bold mb-3" style="font-size: 0.85rem;">Filter By</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Category</label>
                                <select id="filterCategory" class="form-select" style="height: 36px; border-radius: 6px; font-size: 0.85rem;">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ strtolower($cat->name) }}">{{ ucfirst($cat->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Field Type</label>
                                <select id="filterType" class="form-select" style="height: 36px; border-radius: 6px; font-size: 0.85rem;">
                                    <option value="">All Types</option>
                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="number">Number</option>
                                    <option value="select">Select</option>
                                    <option value="radio">Radio</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="file">File Upload</option>
                                    <option value="date">Date</option>
                                    <option value="time">Time</option>
                                    <option value="url">URL</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Status</label>
                                <select id="filterStatus" class="form-select" style="height: 36px; border-radius: 6px; font-size: 0.85rem;">
                                    <option value="">All</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label small fw-semibold text-muted">Required</label>
                                <select id="filterRequired" class="form-select" style="height: 36px; border-radius: 6px; font-size: 0.85rem;">
                                    <option value="">All</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <hr class="my-3">
                            <div class="d-flex gap-2">
                                <button id="clearFilters" class="btn btn-outline-secondary flex-fill" style="height: 36px; border-radius: 6px; font-size: 0.85rem;">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="collapse px-4" id="duplicateCollapse">
            <div class="border rounded-3 p-3 my-4 " style="background:#f8f9fa;">
                <form method="POST" action="{{ route('admin.category-fields.duplicate') }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-muted">Source Category</label>
                            <select name="source_category_id" class="form-select source-category-select" required>
                                <option value=""></option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ ucfirst($cat->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-muted">Target Categories</label>
                            <select name="target_category_ids[]" class="form-select target-category-select" multiple="multiple">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ ucfirst($cat->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" style="height: 40px; border-radius: 8px;">
                                <i class="mdi mdi-content-copy"></i> Duplicate
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="fieldsTable" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 ps-0 fw-semibold text-muted" style="width: 50px;">#</th>
                            <th class="py-3 fw-semibold text-muted">Category</th>
                            <th class="py-3 fw-semibold text-muted">Field Label</th>
                            <th class="py-3 fw-semibold text-muted">Type</th>
                            <th class="py-3 fw-semibold text-muted">Required</th>
                            <th class="py-3 fw-semibold text-muted">Options</th>
                            <th class="py-3 fw-semibold text-muted">Sort Order</th>
                            <th class="py-3 fw-semibold text-muted">Status</th>
                            <th class="py-3 pe-0 fw-semibold text-muted" style="width: 110px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fields as $field)
                            <tr>
                                <td class="ps-0 py-3 text-muted">{{ $loop->iteration }}</td>
                                <td class="py-3">{{ ucfirst($field->categoryId?->name) ?? 'N/A' }}</td>
                                <td class="py-3 fw-medium">{{ $field->field_label }}</td>
                                <td class="py-3">
                                    @php
                                        $typeStyles = [
                                            'file' => 'badge-type-file',
                                            'text' => 'badge-type-text',
                                            'textarea' => 'badge-type-textarea',
                                            'select' => 'badge-type-select',
                                            'number' => 'badge-type-number',
                                            'date' => 'badge-type-date',
                                            'radio' => 'badge-type-radio',
                                            'checkbox' => 'badge-type-checkbox',
                                            'time' => 'badge-type-time',
                                            'url' => 'badge-type-url',
                                            'color' => 'badge-type-color',
                                        ];
                                        $class = $typeStyles[$field->field_type] ?? 'badge bg-secondary';
                                    @endphp
                                    <span class="badge {{ $class }}" style="padding: 6px 14px; border-radius: 9999px; font-weight: 500; font-size: 0.78rem;">
                                        @switch($field->field_type)
                                            @case('file') File Upload @break
                                            @case('textarea') Textarea @break
                                            @case('select') Select @break
                                            @case('checkbox') Checkbox @break
                                            @default {{ ucfirst($field->field_type) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="py-3">{!! $field->is_required ? '<span class="badge-yes">Yes</span>' : '<span class="badge-no">No</span>' !!}</td>
                                <td class="py-3 text-muted" style="max-width: 200px;">
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;">{{ $field->field_options ?? '-' }}</span>
                                </td>
                                <td class="py-3">{{ $field->sort_order }}</td>
                                <td class="py-3">{!! $field->status ? '<span class="badge-pill-active">Active</span>' : '<span class="badge-pill-inactive">Inactive</span>' !!}</td>
                                <td class="py-3 pe-0">
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-outline-primary btn-sm edit-button d-inline-flex align-items-center justify-content-center" data-id="{{ $field->id }}" style="width: 32px; height: 32px; border-radius: 6px;" title="Edit">
                                            <i class="mdi mdi-pencil" style="font-size: 0.9rem;"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm delete-button d-inline-flex align-items-center justify-content-center" data-id="{{ $field->id }}" style="width: 32px; height: 32px; border-radius: 6px;" title="Delete">
                                            <i class="mdi mdi-trash-can-outline" style="font-size: 0.9rem;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        var table = $('#fieldsTable').DataTable({
            processing: false,
            serverSide: false,
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[0, 'asc']],
            language: {
                paginate: {
                    previous: 'Prev',
                    next: 'Next'
                }
            }
        });

        // ─── Custom search + column filters ───
        function applyFilters() {
            var query = $('#tableSearch').val().toLowerCase();
            var catFilter = $('#filterCategory').val().toLowerCase();
            var typeFilter = $('#filterType').val().toLowerCase();
            var statusFilter = $('#filterStatus').val().toLowerCase();
            var reqFilter = $('#filterRequired').val().toLowerCase();

            $.fn.dataTable.ext.search = [];

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var row = table.row(dataIndex).node();
                var text = $(row).text().toLowerCase();
                var cat = $(row).find('td:eq(1)').text().trim().toLowerCase();
                var type = $(row).find('td:eq(3) .badge').text().trim().toLowerCase();
                var statusEl = $(row).find('td:eq(7)');
                var isActive = statusEl.text().trim().toLowerCase() === 'active';
                var reqEl = $(row).find('td:eq(4)');
                var isRequired = reqEl.text().trim().toLowerCase() === 'yes';

                var match = true;
                if (query && text.indexOf(query) === -1) match = false;
                if (catFilter && cat.indexOf(catFilter) === -1) match = false;
                if (typeFilter && type.indexOf(typeFilter) === -1) match = false;
                if (statusFilter === 'active' && !isActive) match = false;
                if (statusFilter === 'inactive' && isActive) match = false;
                if (reqFilter === 'yes' && !isRequired) match = false;
                if (reqFilter === 'no' && isRequired) match = false;

                return match;
            });

            table.draw();
        }

        $('#tableSearch, #filterCategory, #filterType, #filterStatus, #filterRequired').on('change keyup', applyFilters);

        $('#clearFilters').on('click', function () {
            $('#filterCategory, #filterType, #filterStatus, #filterRequired').val('');
            $('#tableSearch').val('');
            applyFilters();
        });
    });

    // ─── Edit button ───
    $(document).on('click', '.edit-button', function () {
        let id = $(this).data('id');

        $.ajax({
            url: "{{ route('admin.categories.fields.edit', ':id') }}".replace(':id', id),
            type: "GET",
            beforeSend: function () {
                Swal.fire({
                    title: 'Loading...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
            },
            success: function (response) {
                Swal.close();

                let collapseElement = document.getElementById('collapseExample');
                if (!collapseElement.classList.contains('show')) {
                    new bootstrap.Collapse(collapseElement, { show: true });
                }

                $('select[name="category_id"]').val(response.data.category_id);
                $('input[name="field_label"]').val(response.data.field_label);
                $('select[name="field_type"]').val(response.data.field_type);
                $('input[name="field_options"]').val(response.data.field_options);
                $('input[name="placeholder"]').val(response.data.placeholder);
                $('input[name="validation_rules"]').val(response.data.validation_rules);
                $('input[name="default_value"]').val(response.data.default_value);
                $('input[name="sort_order"]').val(response.data.sort_order);
                $('textarea[name="help_text"]').val(response.data.help_text);
                $('input[name="is_required"]').prop('checked', response.data.is_required == 1);
                $('select[name="status"]').val(response.data.status != null ? response.data.status : 1);

                $('form').attr('action', "{{ route('admin.categories.fields.update', ':id') }}".replace(':id', id));

                if ($('input[name="_method"]').length === 0) {
                    $('form').append('<input type="hidden" name="_method" value="PUT">');
                } else {
                    $('input[name="_method"]').val('PUT');
                }

                $('#saveBtnLabel').text('Update Field');

                setTimeout(function () {
                    collapseElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to fetch field data.' });
            }
        });
    });

    // ─── Delete button ───
    $(document).on('click', '.delete-button', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This field will be deleted permanently.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.categories.fields.destroy', ':id') }}".replace(':id', id),
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", _method: "DELETE" },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Field deleted successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => { location.reload(); });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Failed', text: 'Failed to delete field.' });
                        }
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong while deleting.' });
                    }
                });
            }
        });
    });

    // ─── Reset form when collapse is hidden (new mode) ───
    $('#collapseExample').on('hidden.bs.collapse', function () {
        $('form')[0].reset();
        $('form').attr('action', "{{ route('admin.category-fields.store') }}");
        $('input[name="_method"]').remove();
        $('#saveBtnLabel').text('Save Field');
        $('select[name="status"]').val('1');
    });

    // ─── Duplicate fields Select2 ───
    $('#duplicateCollapse').on('shown.bs.collapse', function () {
        $('.source-category-select').select2({
            dropdownParent: $('#duplicateCollapse'),
            minimumResultsForSearch: -1,
            placeholder: 'Select source category',
        });
        $('.target-category-select').select2({
            dropdownParent: $('#duplicateCollapse'),
            placeholder: 'Select target categories',
            closeOnSelect: false,
        });
    });
    $('#duplicateCollapse').on('hidden.bs.collapse', function () {
        if ($.fn.select2) {
            $('.source-category-select, .target-category-select').select2('destroy');
        }
    });
</script>
@endpush
