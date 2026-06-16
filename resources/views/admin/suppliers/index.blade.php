@extends('admin.layouts.app')
@section('title', 'Suppliers')

@push('styles')
<style>
    .supplier-hero {
        position: relative;
        overflow: hidden;
        border: 0;
        border-radius: 24px;
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #38bdf8 100%);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.16);
    }

    .supplier-hero::after {
        content: '';
        position: absolute;
        right: -70px;
        bottom: -90px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
    }

    .supplier-metric-card,
    .supplier-table-card,
    .supplier-modal .modal-content {
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 24px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
    }

    .supplier-metric-icon {
        width: 52px;
        height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: rgba(29, 78, 216, 0.1);
        color: #1d4ed8;
        font-size: 1.3rem;
    }

    .supplier-table-card {
        overflow: hidden;
        background: #fff;
    }

    .supplier-toolbar-gap {
        gap: 1rem;
    }

    .supplier-search-chip {
        display: inline-flex;
        align-items: center;
        gap: .65rem;
        min-width: 260px;
        padding: .75rem 1rem;
        border: 1px solid rgba(148, 163, 184, 0.32);
        border-radius: 16px;
        background: #f8fafc;
    }

    .supplier-search-chip input {
        width: 100%;
        border: 0;
        outline: none;
        background: transparent;
        color: #0f172a;
    }

    .supplier-table-card .table {
        margin-bottom: 0;
    }

    .supplier-table-card .table thead th {
        border-top: 0;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem .85rem;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #64748b;
        background: #f8fafc;
    }

    .supplier-table-card .table tbody td {
        padding: 1rem .85rem;
        border-color: #eef2f7;
        vertical-align: middle;
        color: #1e293b;
    }

    .supplier-table-card .table tbody tr:hover {
        background: #f8fbff;
    }

    .supplier-table-card .dataTables_wrapper .dataTables_filter,
    .supplier-table-card .dataTables_wrapper .dataTables_length {
        display: none;
    }

    .supplier-status-badge {
        display: inline-flex;
        align-items: center;
        padding: .45rem .75rem;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        font-weight: 700;
        font-size: .78rem;
    }

    .supplier-status-badge.inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .supplier-actions {
        display: flex;
        gap: .5rem;
    }

    .supplier-action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 12px;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .supplier-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 20px rgba(15, 23, 42, 0.12);
    }

    .supplier-action-btn.edit {
        background: rgba(37, 99, 235, 0.12);
        color: #1d4ed8;
    }

    .supplier-action-btn.delete {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
    }

    .supplier-modal .modal-content {
        border: 0;
        overflow: hidden;
    }

    .supplier-modal .modal-header {
        border-bottom: 0;
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);
    }

    .supplier-modal .modal-body,
    .supplier-modal .modal-footer {
        padding: 1.5rem;
    }

    .supplier-modal .modal-footer {
        border-top: 1px solid #e2e8f0;
    }

    .supplier-modal .form-control {
        min-height: 48px;
        border-radius: 14px;
        border-color: #dbe4f0;
        box-shadow: none;
    }

    .supplier-modal textarea.form-control {
        min-height: 110px;
    }

    @media (max-width: 767.98px) {
        .supplier-search-chip {
            min-width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title"> 
            Suppliers Management
        </h3> 
    </div>

    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card supplier-hero text-white">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-4 mb-lg-0">
                            <span class="badge badge-primary rounded-4 px-3 py-2 mb-3">Supplier Directory</span>
                            <h2 class="mb-2 text-white">Manage supplier records with a cleaner, faster admin workspace.</h2>
                            <p class="mb-0 text-white-50">Track contacts, companies, and onboarding details from one polished table view.</p>
                        </div>
                        {{-- <div class="col-lg-4 text-lg-right">
                            <button type="button" class="btn btn-light" data-toggle="modal" data-target="#supplierModal">
                                <i class="mdi mdi-plus mr-1"></i> Add New Supplier
                            </button>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card supplier-metric-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Directory Status</p>
                        <h4 class="mb-1">Active Suppliers</h4>
                        <p class="mb-0 text-muted">Live data updates in the table below</p>
                    </div>
                    <span class="supplier-metric-icon">
                        <i class="mdi mdi-account-multiple-outline"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card supplier-metric-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Coverage</p>
                        <h4 class="mb-1">Company + Region</h4>
                        <p class="mb-0 text-muted">Scan organisation and county at a glance</p>
                    </div>
                    <span class="supplier-metric-icon">
                        <i class="mdi mdi-map-marker-outline"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card supplier-metric-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Productivity</p>
                        <h4 class="mb-1">Quick Actions</h4>
                        <p class="mb-0 text-muted">Search, review, and edit records faster</p>
                    </div>
                    <span class="supplier-metric-icon">
                        <i class="mdi mdi-lightning-bolt-outline"></i>
                    </span>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card supplier-table-card">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between supplier-toolbar-gap mb-4">
                        <div>
                            <h4 class="card-title mb-1">All Suppliers</h4>
                            {{-- <p class="text-muted mb-0">A searchable, server-side table for supplier operations.</p> --}}
                        </div>
                        <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center supplier-toolbar-gap">
                            <label class="supplier-search-chip mb-0">
                                <i class="mdi mdi-magnify text-muted"></i>
                                <input type="text" id="supplierSearch" placeholder="Search suppliers, company, county...">
                            </label>
                            <button type="button" class="btn btn-outline-primary rounded-4" id="refreshSupplierTable">
                                <i class="mdi mdi-refresh mr-1"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="supplierTable" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Company</th>
                                    <th>Categories</th>
                                    <th>Address</th>
                                    <th>Website</th>
                                    <th>Date Added</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade supplier-modal" id="supplierModal" tabindex="-1" role="dialog" aria-labelledby="supplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="supplierModalLabel">Add New Supplier</h5>
                <button type="button" class="close btn border text-white" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="supplierForm">
                    <input type="hidden" id="supplier_id" name="supplier_id">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_name">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="is_active">Status</label>
                                <select class="form-control text-dark" id="is_active" name="is_active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="supplier_organisation">Categories *</label>
                                <select name="supplier_organisation[]" id="supplier_organisation" class="form-select supplier-organisations" multiple required>
                                    @foreach ($organisation as $item)
                                        @if ($item->type === 'supplier')
                                            <option value="{{ $item->id }}">{{ strtoupper($item->name) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="website">Website</label>
                                <input type="url" class="form-control" id="website" name="website">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveBtnSupplier">Save Supplier</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        var table = $('#supplierTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            scrollX: true,
            ajax: {
                url: "{{ route('admin.suppliers') }}",
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'company_name', name: 'company_name' },
                { data: 'organisation_names', name: 'organisation_names', orderable: false },
                { data: 'address', name: 'address' },
                { data: 'website', name: 'website' },
                { data: 'created_at', name: 'created_at' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'asc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                processing: '<div class="spinner-border text-primary" role="status" style="height:80px;"><span class="sr-only">Loading...</span></div>',
                paginate: {
                    previous: 'Prev',
                    next: 'Next'
                }
            }
        });

        $('#supplierSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        $('#refreshSupplierTable').on('click', function() {
            table.ajax.reload(null, false);
        });

        $('[data-toggle="tooltip"]').tooltip();

        function resetSupplierForm() {
            $('#supplierForm')[0].reset();
            $('#supplier_id').val('');
            $('#is_active').val('1');
            $('#supplier_organisation').val(null).trigger('change');
            $('#supplierModalLabel').text('Add New Supplier');
            $('#saveBtnSupplier').text('Save Supplier');
        }

        $('#supplierModal').on('hidden.bs.modal', function() {
            resetSupplierForm();
            table.ajax.reload();
        });

        $('#supplierModal').on('shown.bs.modal', function() {
            $(this).find('.supplier-organisations').select2({
                placeholder: 'Select supplier categories',
                width: '100%',
                dropdownParent: $('#supplierModal')
            });
        });

        $('#supplierTable tbody').on('click', '.supplier-action-btn.edit', function() {
            var supplierId = $(this).data('id');

            $.ajax({
                url: "{{ route('admin.suppliers.edit', ':id') }}".replace(':id', supplierId),
                type: 'GET',
                success: function(data) {
                    $('#supplier_id').val(data.id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone);
                    $('#is_active').val(data.is_active ? '1' : '0');
                    $('#company_name').val(data.company_name);
                    $('#address').val(data.address);
                    $('#website').val(data.website);

                    if (data.categories && data.categories.length) {
                        $('#supplier_organisation').val(data.categories).trigger('change');
                    }

                    $('#supplierModalLabel').text('Edit Supplier');
                    $('#saveBtnSupplier').text('Update Supplier');
                    $('#supplierModal').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to fetch supplier data.',
                    });
                }
            });
        });

        $('#saveBtnSupplier').on('click', function() {
            var supplierId = $('#supplier_id').val();
            var formData = $('#supplierForm').serialize();
            var url, method;

            if (supplierId) {
                url = "{{ route('admin.suppliers.update', ':id') }}".replace(':id', supplierId);
                method = 'PUT';
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Coming Soon',
                    text: 'Add new supplier functionality will be implemented soon.',
                });
                return;
            }

            Swal.fire({
                title: 'Saving...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: url,
                type: 'POST',
                data: formData + '&_method=' + method + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: response.message || 'Supplier updated successfully.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    $('#supplierModal').modal('hide');
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    var message = 'Something went wrong.';
                    if (errors) {
                        message = Object.values(errors).flat().join('\n');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                    });
                }
            });
        });

            $('#supplierTable tbody').on('click', '.supplier-action-btn.delete', function () {
                let supplierId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('admin.suppliers.destroy', ':id') }}".replace(':id', supplierId),
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Supplier deleted successfully.',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null, false);
                            },
                            error: function (xhr) {

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Something went wrong.',
                                });
                            }
                        });

                    }
                });
            });

    });
</script>
@endpush
