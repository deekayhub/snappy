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

    .job-action-btn.view {
        background: rgba(37, 99, 235, 0.12);
        color: #2563eb;
        margin-right: 6px;
    }

    .job-quote-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 24px;
        padding: 0 8px;
        border-radius: 999px;
        background: rgba(14, 165, 233, 0.12);
        color: #0284c7;
        font-size: .78rem;
        font-weight: 700;
    }

    .winner-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(34, 197, 94, 0.14);
        color: #15803d;
        font-size: .78rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .winner-badge .mdi {
        font-size: 1rem;
    }

    #jobDetailsModal .modal-content {
        border: 0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.2);
    }

    #jobDetailsModal .modal-header {
        border-bottom: 1px solid #eef2f7;
    }

    #jobDetailsModal .modal-body {
        max-height: 72vh;
        overflow-y: auto;
    }

    #jobDetailsModal .detail-stat {
        background: #f8fafc;
        border: 1px solid #eef2f7;
        border-radius: 14px;
        padding: .75rem 1rem;
    }

    #jobDetailsModal .detail-stat .label {
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748b;
    }

    #jobDetailsModal .detail-stat .value {
        font-size: .95rem;
        font-weight: 600;
        color: #1e293b;
        margin-top: 2px;
        word-break: break-word;
    }

    #jobDetailsModal .quote-row {
        border: 1px solid #eef2f7;
        border-radius: 14px;
        padding: .9rem 1rem;
        background: #fff;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    #jobDetailsModal .quote-row.is-winner {
        border-color: rgba(34, 197, 94, 0.5);
        background: rgba(34, 197, 94, 0.06);
        box-shadow: 0 8px 20px rgba(34, 197, 94, 0.1);
    }

    #jobDetailsModal .dynamic-item {
        background: #f8fafc;
        border: 1px solid #eef2f7;
        border-radius: 14px;
        padding: .9rem 1rem;
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
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                    <h4 class="card-title mb-0">All Jobs</h4>
                    <button type="button" class="btn btn-danger rounded-3" id="deleteSelectedJobs" disabled>
                        <i class="mdi mdi-trash-can-outline me-1"></i> Delete Selected
                        (<span id="selectedJobsCount">0</span>)
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 w-100" id="jobs-table">
                        <thead>
                            <tr>
                                <th class="text-center w-auto">
                                    <input type="checkbox" class="form-check-input" id="selectAllJobs" style="cursor:pointer;">
                                </th>
                                <th>#</th>
                                <th>Job</th>
                                <th>Customer</th>
                                <th>Organisation</th>
                                <th>Budget</th>
                                <th>Needed By</th>
                                <th>Status</th>
                                <th>Quotes</th>
                                <th>Winner</th>
                                <th>Posted On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="jobDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header px-4 py-3">
                    <h5 class="modal-title fw-bold" id="jobDetailsTitle">Job Details</h5>
                    <button type="button" class="close btn p-2" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div id="jobDetailsContent">
                        <div class="d-flex justify-content-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        var table = $('#jobs-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            scrollX: true,
            ajax: '{{ route('admin.jobs') }}',
            order: [[10, 'desc']],
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'title', name: 'title' },
                { data: 'customer_name', name: 'user.name', orderable: false },
                { data: 'organisation_name', name: 'organisation_name' },
                { data: 'budget', name: 'budget' },
                { data: 'needed_by', name: 'needed_by' },
                { data: 'status', name: 'status' },
                { data: 'quotes_count', name: 'quotes_count', orderable: false, searchable: false },
                { data: 'winner', name: 'winner', orderable: false, searchable: false },
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

        $('#jobs-table tbody').on('click', '.job-action-btn.view', function () {
            var jobId = $(this).data('id');

            function esc(value) {
                return $('<div>').text(value == null ? '' : String(value)).html();
            }

            $('#jobDetailsContent').html('<div class="d-flex justify-content-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
            $('#jobDetailsModal').modal('show');

            $.get("{{ route('admin.jobs.details', ':id') }}".replace(':id', jobId), function (job) {
                $('#jobDetailsTitle').text(job.title);

                var statusColors = {
                    submitted: 'rgba(14,165,233,0.12)',
                    accepted: 'rgba(34,197,94,0.14)',
                    completed: 'rgba(20,184,166,0.14)',
                    rejected: 'rgba(239,68,68,0.12)'
                };
                var statusTextColors = {
                    submitted: '#0284c7',
                    accepted: '#15803d',
                    completed: '#0f766e',
                    rejected: '#dc2626'
                };

                var statusBadge = function (status) {
                    var s = (status || '').toLowerCase();
                    var bg = statusColors[s] || 'rgba(100,116,139,0.12)';
                    var fg = statusTextColors[s] || '#475569';
                    return '<span class="badge rounded px-3 py-2 text-uppercase" style="background:' + bg + ';color:' + fg + ';font-size:.7rem;font-weight:700;">' + esc(status) + '</span>';
                };

                var stats = [
                    { label: 'Budget', value: job.budget },
                    { label: 'Needed By', value: job.needed_by },
                    { label: 'Location', value: job.location },
                    { label: 'Organisation', value: job.organisation_name },
                    { label: 'Posted On', value: job.posted_at },
                    { label: 'Quotes Received', value: job.quotes_count }
                ];

                var statsHtml = '';
                stats.forEach(function (stat) {
                    statsHtml += '<div class="col-6 col-md-4"><div class="detail-stat"><div class="label">' + esc(stat.label) + '</div><div class="value">' + esc(stat.value) + '</div></div></div>';
                });

                var dynamicHtml = '';
                if (job.dynamic_fields && job.dynamic_fields.length) {
                    dynamicHtml += '<div class="mb-4"><h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size:.72rem;letter-spacing:.04em;">Additional Details</h6>';
                    job.dynamic_fields.forEach(function (item) {
                        dynamicHtml += '<div class="dynamic-item mb-2">';
                        item.forEach(function (field) {
                            dynamicHtml += '<div class="small mb-1"><span class="text-muted fw-semibold">' + esc(field.label) + ':</span> <span class="text-body">' + esc(field.value) + '</span></div>';
                        });
                        dynamicHtml += '</div>';
                    });
                    dynamicHtml += '</div>';
                }

                var quotesHtml = '<div class="mb-2"><h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size:.72rem;letter-spacing:.04em;">Quotes Received (' + job.quotes_count + ')</h6>';
                if (!job.quotes || !job.quotes.length) {
                    quotesHtml += '<div class="text-muted py-3">No quotes have been submitted yet.</div>';
                } else {
                    job.quotes.forEach(function (quote) {
                        var supplierName = quote.company_name || quote.supplier_name;
                        var email = quote.email ? '<div class="small text-muted">' + esc(quote.email) + '</div>' : '';
                        var sentAt = '<div class="small text-muted">Sent ' + esc(quote.sent_at) + '</div>';
                        var winnerBadge = quote.is_winner
                            ? '<span class="winner-badge ms-2"><i class="mdi mdi-trophy-outline"></i>Winner</span>'
                            : '';
                        quotesHtml += '<div class="quote-row mb-2' + (quote.is_winner ? ' is-winner' : '') + '">';
                        quotesHtml += '<div class="d-flex flex-wrap align-items-center justify-content-between gap-2">';
                        quotesHtml += '<div class="min-w-0"><div class="fw-semibold">' + esc(supplierName) + '</div>' + email + sentAt + '</div>';
                        quotesHtml += '<div class="text-end"><div class="fw-bold mb-1">' + esc(quote.total_price) + '</div>' + statusBadge(quote.status) + winnerBadge + '</div>';
                        quotesHtml += '</div></div>';
                    });
                }
                quotesHtml += '</div>';

                $('#jobDetailsContent').html(
                    '<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">' +
                        '<h4 class="fw-bold mb-0">' + esc(job.title) + '</h4>' +
                        statusBadge(job.status) +
                    '</div>' +
                    '<div class="mb-2"><span class="badge bg-light rounded text-dark px-3 py-2">' + esc(job.category) + '</span></div>' +
                    '<div class="row g-3">' + statsHtml + '</div>' +
                    '<div class="mb-4"><h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size:.72rem;letter-spacing:.04em;">Description</h6><div class="text-muted" style="white-space:pre-wrap;">' + esc(job.description) + '</div></div>' +
                    dynamicHtml +
                    quotesHtml +
                    '<hr>' +
                    '<h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size:.72rem;letter-spacing:.04em;">Posted By</h6>' +
                    '<div class="d-flex flex-wrap gap-3 small">' +
                        '<div><span class="text-muted fw-semibold">Name:</span> ' + esc(job.posted_by.name) + '</div>' +
                        '<div><span class="text-muted fw-semibold">Email:</span> ' + esc(job.posted_by.email) + '</div>' +
                        (job.posted_by.school
                            ? '<div><span class="text-muted fw-semibold">Organization:</span> ' + esc(job.posted_by.school) + '</div>'
                            : '') +
                    '</div>'
                );
            }).fail(function () {
                $('#jobDetailsContent').html('<div class="alert alert-warning mb-0">Something went wrong while loading this job.</div>');
            });
        });

        function updateJobSelection() {
            var checked = $('#jobs-table tbody .job-row-check:checked').length;
            var total = $('#jobs-table tbody .job-row-check').length;
            $('#selectedJobsCount').text(checked);
            $('#deleteSelectedJobs').prop('disabled', checked === 0);
            $('#selectAllJobs').prop('checked', total > 0 && checked === total);
        }

        table.on('draw', function () {
            updateJobSelection();
        });

        $('#jobs-table tbody').on('change', '.job-row-check', function () {
            updateJobSelection();
        });

        $('#selectAllJobs').on('change', function () {
            $('#jobs-table tbody .job-row-check').prop('checked', this.checked);
            updateJobSelection();
        });

        $('#deleteSelectedJobs').on('click', function () {
            var ids = $('#jobs-table tbody .job-row-check:checked').map(function () {
                return parseInt($(this).val(), 10);
            }).get();

            if (!ids.length) {
                return;
            }

            var deleteSelected = function () {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('admin.jobs.bulkDelete') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ids: ids
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message || 'Selected jobs deleted successfully.',
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

            Swal.fire({
                title: 'Are you sure?',
                text: 'These jobs and their quotes will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete them!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteSelected();
                }
            });
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
