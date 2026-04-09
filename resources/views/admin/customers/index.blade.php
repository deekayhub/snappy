@extends('admin.layouts.app')
@section('title', 'Customers')
@section('content')

@push('styles')
<style>
    .customer-hero {
        position: relative;
        overflow: hidden;
        border: 0;
        border-radius: 24px;
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #38bdf8 100%);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.16);
    }

    .customer-hero::after {
        content: '';
        position: absolute;
        right: -70px;
        bottom: -90px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
    }

    .customer-metric-card,
    .customer-table-card,
    .customer-modal .modal-content {
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 24px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
    }

    .customer-metric-icon {
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

    .customer-table-card {
        overflow: hidden;
        background: #fff;
    }

    .customer-toolbar-gap {
        gap: 1rem;
    }

    .customer-search-chip {
        display: inline-flex;
        align-items: center;
        gap: .65rem;
        min-width: 260px;
        padding: .75rem 1rem;
        border: 1px solid rgba(148, 163, 184, 0.32);
        border-radius: 16px;
        background: #f8fafc;
    }

    .customer-search-chip input {
        width: 100%;
        border: 0;
        outline: none;
        background: transparent;
        color: #0f172a;
    }

    .customer-table-card .table {
        margin-bottom: 0;
    }

    .customer-table-card .table thead th {
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

    .customer-table-card .table tbody td {
        padding: 1rem .85rem;
        border-color: #eef2f7;
        vertical-align: middle;
        color: #1e293b;
    }

    .customer-table-card .table tbody tr:hover {
        background: #f8fbff;
    }

    .customer-table-card .dataTables_wrapper .dataTables_filter,
    .customer-table-card .dataTables_wrapper .dataTables_length {
        display: none;
    }

    .customer-status-badge {
        display: inline-flex;
        align-items: center;
        padding: .45rem .75rem;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        font-weight: 700;
        font-size: .78rem;
    }

    .customer-actions {
        display: flex;
        gap: .5rem;
    }

    .customer-action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 12px;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .customer-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 20px rgba(15, 23, 42, 0.12);
    }

    .customer-action-btn.edit {
        background: rgba(37, 99, 235, 0.12);
        color: #1d4ed8;
    }

    .customer-action-btn.delete {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
    }

    .customer-modal .modal-content {
        border: 0;
        overflow: hidden;
    }

    .customer-modal .modal-header {
        border-bottom: 0;
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);
    }

    .customer-modal .modal-body,
    .customer-modal .modal-footer {
        padding: 1.5rem;
    }

    .customer-modal .modal-footer {
        border-top: 1px solid #e2e8f0;
    }

    .customer-modal .form-control {
        min-height: 48px;
        border-radius: 14px;
        border-color: #dbe4f0;
        box-shadow: none;
    }

    .customer-modal textarea.form-control {
        min-height: 110px;
    }

    @media (max-width: 767.98px) {
        .customer-search-chip {
            min-width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            {{-- <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-factory"></i>
            </span> --}}
            Customer Management
        </h3> 
    </div>

    {{-- <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card customer-hero text-white">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-4 mb-lg-0">
                            <span class="badge badge-light text-primary px-3 py-2 mb-3">customer Directory</span>
                            <h2 class="mb-2 text-white">Manage customer records with a cleaner, faster admin workspace.</h2>
                            <p class="mb-0 text-white-50">Track contacts, companies, and onboarding details from one polished table view.</p>
                        </div>
                        <div class="col-lg-4 text-lg-right">
                            <button type="button" class="btn btn-light" data-toggle="modal" data-target="#customerModal">
                                <i class="mdi mdi-plus mr-1"></i> Add New customer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card customer-metric-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Directory Status</p>
                        <h4 class="mb-1">Active customers</h4>
                        <p class="mb-0 text-muted">Live data updates in the table below</p>
                    </div>
                    <span class="customer-metric-icon">
                        <i class="mdi mdi-account-multiple-outline"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card customer-metric-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Coverage</p>
                        <h4 class="mb-1">Company + Region</h4>
                        <p class="mb-0 text-muted">Scan organisation and county at a glance</p>
                    </div>
                    <span class="customer-metric-icon">
                        <i class="mdi mdi-map-marker-outline"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card customer-metric-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Productivity</p>
                        <h4 class="mb-1">Quick Actions</h4>
                        <p class="mb-0 text-muted">Search, review, and edit records faster</p>
                    </div>
                    <span class="customer-metric-icon">
                        <i class="mdi mdi-lightning-bolt-outline"></i>
                    </span>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card customer-table-card">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between customer-toolbar-gap mb-4">
                        <div>
                            <h4 class="card-title mb-1">All Customers</h4>
                            <p class="text-muted mb-0">A searchable, server-side table for customer operations.</p>
                        </div>
                        <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center customer-toolbar-gap">
                            <label class="customer-search-chip mb-0">
                                <i class="mdi mdi-magnify text-muted"></i>
                                <input type="text" id="customerSearch" placeholder="Search customers, company, county...">
                            </label>
                            <button type="button" class="btn btn-outline-primary rounded-3" id="refreshcustomerTable">
                                <i class="mdi mdi-refresh mr-1"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="customerTable" class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Categories</th>
                                    <th>School</th>
                                    <th>County</th>
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

<div class="modal fade customer-modal" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="customerModalLabel">Add New customer</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
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
                    <div class="form-group">
                        <label for="school_name">School / Club Name</label>
                        <input type="text" class="form-control" id="school_name" name="school_name">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="organisation">Categories</label>
                                <input type="text" class="form-control" id="organisation" name="organisation">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="county">County</label>
                                <input type="text" class="form-control" id="county" name="county">
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
                <button type="button" class="btn btn-light border" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-gradient-primary" id="saveBtncustomer">Save customer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        var table = $('#customerTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            scrollX: true,
            ajax: {
                url: "{{ route('admin.customers') }}",
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'organisation_names', name: 'organisation_names', orderable: false },
                { data: 'school_name', name: 'school_name' },
                { data: 'county', name: 'county' },
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

        $('#customerSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        $('#refreshcustomerTable').on('click', function() {
            table.ajax.reload(null, false);
        });

        $('#customerModal').on('hidden.bs.modal', function() {
            table.ajax.reload();
        });

        $('#saveBtncustomer').on('click', function() {
            alert('Save functionality will be implemented soon');
            $('#customerModal').modal('hide');
        });

        $('[data-toggle="tooltip"]').tooltip();
        $('#customerTable tbody').on('click', '.customer-action-btn.delete', function () {
            let customerId = $(this).data('id');

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
                        url: "{{ route('admin.customers.destroy', ':id') }}".replace(':id', customerId),
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Customer deleted successfully.',
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
