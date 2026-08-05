@extends('admin.layouts.app')
@section('title', 'Purchase Quotes')

@push('styles')
<style>
    .quote-summary-card,
    .quote-table-card {
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 24px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
    }

    .quote-summary-card {
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #38bdf8 100%);
        color: #fff;
        overflow: hidden;
        position: relative;
    }

    .quote-summary-card::after {
        content: '';
        position: absolute;
        right: -60px;
        bottom: -80px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
    }

    .quote-table-card {
        background: #fff;
        overflow: hidden;
    }

    .quote-toolbar-gap {
        gap: 1rem;
    }

    .quote-search-chip {
        display: inline-flex;
        align-items: center;
        gap: .65rem;
        min-width: 260px;
        padding: .75rem 1rem;
        border: 1px solid rgba(148, 163, 184, 0.32);
        border-radius: 16px;
        background: #f8fafc;
    }

    .quote-search-chip input {
        width: 100%;
        border: 0;
        outline: none;
        background: transparent;
        color: #0f172a;
    }

    .quote-table-card .table {
        margin-bottom: 0;
    }

    .quote-table-card .table thead th {
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

    .quote-table-card .table tbody td {
        padding: 1rem .85rem;
        border-color: #eef2f7;
        vertical-align: middle;
        color: #1e293b;
    }

    .quote-table-card .table tbody tr:hover {
        background: #f8fbff;
    }

    .quote-table-card .dataTables_wrapper .dataTables_filter,
    .quote-table-card .dataTables_wrapper .dataTables_length {
        display: none;
    }

    .quote-status-badge {
        display: inline-flex;
        align-items: center;
        padding: .4rem .7rem;
        border-radius: 999px;
        font-weight: 700;
        font-size: .75rem;
        text-transform: uppercase;
        background: rgba(100, 116, 139, 0.12);
        color: #475569;
    }

    .quote-status-badge.submitted {
        background: rgba(14, 165, 233, 0.12);
        color: #0284c7;
    }

    .quote-status-badge.accepted {
        background: rgba(34, 197, 94, 0.14);
        color: #15803d;
    }

    .quote-status-badge.completed {
        background: rgba(20, 184, 166, 0.14);
        color: #0f766e;
    }

    .quote-status-badge.rejected {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
    }

    .quote-winner-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(34, 197, 94, 0.14);
        color: #15803d;
        font-size: .72rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .quote-winner-badge .mdi {
        font-size: 1rem;
    }

    .quote-job-badge {
        display: inline-flex;
        align-items: center;
        padding: .4rem .7rem;
        border-radius: 999px;
        font-weight: 700;
        font-size: .75rem;
        text-transform: uppercase;
        background: rgba(100, 116, 139, 0.12);
        color: #475569;
    }

    .quote-job-badge.open {
        background: rgba(34, 197, 94, 0.14);
        color: #15803d;
    }

    #quotePageLength,
    #filterQuoteStatus,
    #filterJobStatus {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%2364748b' d='M6 8L0 0h12z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.9rem center;
        background-size: 12px 8px;
        padding-right: 2.4rem;
    }

    .quote-metric-card {
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 20px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        position: relative;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .quote-metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 22px 50px rgba(15, 23, 42, 0.12);
    }

    .quote-metric-card::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -40px;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.03);
    }

    .quote-metric-icon {
        width: 52px;
        height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        font-size: 1.4rem;
    }

    .quote-metric-icon.total {
        background: rgba(29, 78, 216, 0.1);
        color: #1d4ed8;
    }

    .quote-metric-icon.submitted {
        background: rgba(14, 165, 233, 0.1);
        color: #0284c7;
    }

    .quote-metric-icon.accepted {
        background: rgba(34, 197, 94, 0.12);
        color: #16a34a;
    }

    .quote-metric-icon.jobs {
        background: rgba(245, 158, 11, 0.12);
        color: #d97706;
    }

    .quote-metric-icon.completed {
        background: rgba(20, 184, 166, 0.12);
        color: #0f766e;
    }

    .quote-metric-icon.rejected {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
    }

    @media (max-width: 767.98px) {
        .quote-search-chip {
            min-width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="content-wrapper p-3">
    <div class="card quote-summary-card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 p-lg-5">
            <span class="badge bg-primary px-3 py-2 mb-3 rounded-4">Admin Quote Space</span>
            <h2 class="mb-2">Purchase quote management</h2>
            <p class="text-white mb-0">Review supplier quotes, linked jobs, and the latest customer-facing status in one place.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-sm-6 col-xl-2">
            <div class="card quote-metric-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="text-muted mb-1">Total Quotes</p>
                        <h3 class="mb-0 fw-bold">{{ $stats['total_quotes'] }}</h3>
                    </div>
                    <span class="quote-metric-icon total">
                        <i class="mdi mdi-file-document-multiple-outline"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card quote-metric-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="text-muted mb-1">Submitted</p>
                        <h3 class="mb-0 fw-bold text-success">{{ $stats['submitted_quotes'] }}</h3>
                    </div>
                    <span class="quote-metric-icon submitted">
                        <i class="mdi mdi-send-outline"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card quote-metric-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="text-muted mb-1">Accepted</p>
                        <h3 class="mb-0 fw-bold text-warning">{{ $stats['accepted_quotes'] }}</h3>
                    </div>
                    <span class="quote-metric-icon accepted">
                        <i class="mdi mdi-check-circle-outline"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card quote-metric-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="text-muted mb-1">Completed</p>
                        <h3 class="mb-0 fw-bold text-info">{{ $stats['completed_quotes'] }}</h3>
                    </div>
                    <span class="quote-metric-icon completed">
                        <i class="mdi mdi-clipboard-check-outline"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card quote-metric-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="text-muted mb-1">Rejected</p>
                        <h3 class="mb-0 fw-bold text-danger">{{ $stats['rejected_quotes'] }}</h3>
                    </div>
                    <span class="quote-metric-icon rejected">
                        <i class="mdi mdi-close-circle-outline"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card quote-metric-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="text-muted mb-1">Jobs With Quotes</p>
                        <h3 class="mb-0 fw-bold text-primary">{{ $stats['jobs_with_quotes'] }}</h3>
                    </div>
                    <span class="quote-metric-icon jobs">
                        <i class="mdi mdi-briefcase-outline"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card quote-table-card mt-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between quote-toolbar-gap mb-4">
                <div>
                    <h4 class="card-title mb-1">All Quotes</h4>
                </div>
                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center quote-toolbar-gap">
                    <select class="form-select rounded-3" id="quotePageLength" style="min-width:120px;">
                        <option value="10">10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>
                    <select class="form-select rounded-3" id="filterQuoteStatus" style="min-width:170px;">
                        <option value="">All Quote Statuses</option>
                        <option value="submitted">Submitted</option>
                        <option value="accepted">Accepted</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <select class="form-select rounded-3" id="filterJobStatus" style="min-width:150px;">
                        <option value="">All Job Statuses</option>
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                    </select>
                    <label class="quote-search-chip mb-0">
                        <i class="mdi mdi-magnify text-muted"></i>
                        <input class="rounded-3" type="text" id="quoteSearch" placeholder="Search quotes, jobs...">
                    </label>
                    <button type="button" class="btn btn-outline-primary rounded-3" id="refreshQuoteTable">
                        <i class="mdi mdi-refresh mr-1"></i> 
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="quoteTable" class="table align-middle mb-0 w-100">
                    <thead>
                        <tr>
                            <th>Job</th>
                            <th>Supplier</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Job Status</th>
                            <th>Status</th>
                            <th>Sent</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        var table = $('#quoteTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            scrollX: true,
            ajax: {
                url: "{{ route('admin.quotes') }}",
                data: function (d) {
                    d.status = $('#filterQuoteStatus').val();
                    d.job_status = $('#filterJobStatus').val();
                }
            },
            pageLength: 10,
            order: [[6, 'desc']],
            columns: [
                { data: 'job_title', name: 'job_title', searchable: true },
                { data: 'supplier', name: 'supplier', orderable: false, searchable: false },
                { data: 'customer', name: 'customer', orderable: false, searchable: false },
                { data: 'total_price', name: 'total_price' },
                { data: 'job_status', name: 'job_status', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'sent_at', name: 'sent_at' }
            ],
            language: {
                processing: '<div class="spinner-border text-primary" role="status" style="height:80px;"><span class="sr-only">Loading...</span></div>',
                paginate: {
                    previous: 'Prev',
                    next: 'Next'
                }
            }
        });

        $('#quoteSearch').on('keyup', function () {
            table.search(this.value).draw();
        });

        $('#filterQuoteStatus, #filterJobStatus').on('change', function () {
            table.ajax.reload();
        });

        $('#quotePageLength').on('change', function () {
            table.page.len(parseInt(this.value, 10)).draw();
        });

        $('#refreshQuoteTable').on('click', function () {
            table.ajax.reload(null, false);
        });
    });
</script>
@endpush
