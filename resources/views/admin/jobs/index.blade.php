@extends('admin.layouts.app')

@section('title', 'Posted Jobs')

@push('styles')
<style>
    .job-summary-card,
    .job-table-card {
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 24px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
    }

    .job-summary-card {
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #38bdf8 100%);
        color: #fff;
        overflow: hidden;
        position: relative;
    }

    .job-summary-card::after {
        content: '';
        position: absolute;
        right: -60px;
        bottom: -80px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
    }

    .job-table-card {
        background: #fff;
        overflow: hidden;
    }

    .job-table-card .table {
        margin-bottom: 0;
    }

    .job-table-card .table thead th {
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

    .job-table-card .table tbody td {
        padding: 1rem .85rem;
        border-color: #eef2f7;
        vertical-align: middle;
        color: #1e293b;
    }

    .job-table-card .table tbody tr:hover {
        background: #f8fbff;
    }

    .job-action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 12px;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .job-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 20px rgba(15, 23, 42, 0.12);
    }

    .job-action-btn.delete {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
    }
</style>
@endpush

@section('content')
    <div class="content-wrapper p-3">
        <div class="card job-summary-card mb-4">
            <div class="card-body p-4 p-lg-5 position-relative">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-7">
                        <span class="badge bg-white text-primary rounded fw-semibold mb-3">Admin Overview</span>
                        <h2 class="fw-bold mb-2">Customer posted jobs</h2>
                        <p class="mb-0 text-white-50">Every job submitted from the landing page customer flow appears here.</p>
                    </div>
                    <div class="col-lg-5">
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="bg-white bg-opacity-10 rounded-4 p-3 text-center">
                                    <div class="small text-white-50">Total</div>
                                    <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-white bg-opacity-10 rounded-4 p-3 text-center">
                                    <div class="small text-white-50">Open</div>
                                    <div class="fs-3 fw-bold">{{ $stats['open'] }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-white bg-opacity-10 rounded-4 p-3 text-center">
                                    <div class="small text-white-50">Today</div>
                                    <div class="fs-3 fw-bold">{{ $stats['recent'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card job-table-card">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 w-100" id="jobs-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Job</th>
                                <th>Customer</th>
                                <th>Organisation</th>
                                <th>Budget</th>
                                <th>Needed By</th>
                                <th>Status</th>
                                <th>Posted On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#jobs-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            scrollX: true,
            ajax: '{{ route('admin.jobs') }}',
            order: [[7, 'desc']],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'title', name: 'title' },
                { data: 'customer_name', name: 'user.name', orderable: false },
                { data: 'organisation_name', name: 'organisation_name' },
                { data: 'budget', name: 'budget' },
                { data: 'needed_by', name: 'needed_by' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                processing: '<div class="spinner-border text-primary" role="status" style="height:80px;"><span class="sr-only">Loading...</span></div>',
                paginate: {
                    previous: 'Prev',
                    next: 'Next'
                }
            }
        });

        $('#jobs-table tbody').on('click', '.job-action-btn.delete', function () {
            var jobId = $(this).data('id');

            var deleteJob = function () {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('admin.jobs.destroy', ':id') }}".replace(':id', jobId),
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message || 'Job deleted successfully.',
                            timer: 3000,
                            showConfirmButton: false
                        });
                        table.ajax.reload(null, false);
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong.',
                        });
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This job and its quotes will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteJob();
                    }
                });
            } else if (confirm('This job and its quotes will be permanently deleted. Continue?')) {
                deleteJob();
            }
        });
    });
</script>
@endpush
