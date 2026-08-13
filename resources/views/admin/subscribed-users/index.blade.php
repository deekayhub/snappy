@extends('admin.layouts.app')

@section('title', 'Subscribed Users')

@push('styles')
<style>
    .subs-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #38bdf8 100%);
        color: #fff;
        border: 0;
        border-radius: 24px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.16);
    }

    .subs-hero::after {
        content: '';
        position: absolute;
        right: -60px;
        bottom: -80px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
    }

    .subs-table-card {
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
    }

    .subs-table-card .table {
        margin-bottom: 0;
    }

    .subs-table-card .table thead th {
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

    .subs-table-card .table tbody td {
        padding: 1rem .85rem;
        border-color: #eef2f7;
        vertical-align: middle;
        color: #1e293b;
    }

    .subs-table-card .table tbody tr:hover {
        background: #f8fbff;
    }

    .subs-table-card .dataTables_wrapper .dataTables_filter,
    .subs-table-card .dataTables_wrapper .dataTables_length {
        display: none;
    }

    .subs-search-chip {
        display: inline-flex;
        align-items: center;
        gap: .65rem;
        min-width: 260px;
        padding: .75rem 1rem;
        border: 1px solid rgba(148, 163, 184, 0.32);
        border-radius: 16px;
        background: #f8fafc;
    }

    .subs-search-chip input {
        width: 100%;
        border: 0;
        outline: none;
        background: transparent;
        color: #0f172a;
    }

    .subs-user-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        margin-right: 8px;
        vertical-align: middle;
    }

    .subs-user-dot.inactive {
        background: #ef4444;
    }

    .subs-role-badge {
        display: inline-flex;
        align-items: center;
        padding: .3rem .65rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .subs-status-badge {
        display: inline-flex;
        align-items: center;
        padding: .45rem .75rem;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        font-weight: 700;
        font-size: .76rem;
    }

    .subs-status-badge.trialing {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .subs-status-badge.past-due {
        background: #fef9c3;
        color: #a16207;
    }

    .subs-status-badge.canceled {
        background: #fee2e2;
        color: #991b1b;
    }

    .subs-status-badge.incomplete {
        background: #f1f5f9;
        color: #475569;
    }
</style>
@endpush

@section('content')
    <div class="content-wrapper p-3">
        <div class="card subs-hero mb-4">
            <div class="card-body p-4 p-lg-5 position-relative">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-7">
                        <span class="badge bg-white text-primary rounded fw-semibold mb-3">Subscriptions Overview</span>
                        <h2 class="fw-bold mb-2">Subscribed users</h2>
                        <p class="mb-0 text-white-50">Every paid subscription across customers and suppliers appears here.</p>
                    </div>
                    <div class="col-lg-5">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="bg-white bg-opacity-10 rounded-4 p-3 text-center">
                                    <div class="small text-white-50">Total</div>
                                    <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-white bg-opacity-10 rounded-4 p-3 text-center">
                                    <div class="small text-white-50">Active</div>
                                    <div class="fs-3 fw-bold">{{ $stats['active'] }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-white bg-opacity-10 rounded-4 p-3 text-center">
                                    <div class="small text-white-50">Past Due</div>
                                    <div class="fs-3 fw-bold">{{ $stats['past_due'] }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-white bg-opacity-10 rounded-4 p-3 text-center">
                                    <div class="small text-white-50">Canceled</div>
                                    <div class="fs-3 fw-bold">{{ $stats['canceled'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card subs-table-card">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
                    <div>
                        <h4 class="card-title mb-1">All Subscribed Users</h4>
                        <p class="text-muted mb-0">A server-side table of active, past due, and canceled subscriptions.</p>
                    </div>
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                        <label class="subs-search-chip mb-0">
                            <i class="mdi mdi-magnify text-muted"></i>
                            <input type="text" id="subsSearch" placeholder="Search name, email, plan...">
                        </label>
                        <button type="button" class="btn btn-outline-primary rounded-3" id="refreshSubsTable">
                            <i class="mdi mdi-refresh me-1"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 w-100" id="subscribedUsersTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Plan</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Subscribed On</th>
                                <th>Renews / Ends</th>
                                <th>Status</th>
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
        var table = $('#subscribedUsersTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            scrollX: true,
            ajax: '{{ route('admin.subscribed-users') }}',
            order: [[7, 'desc']],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role', orderable: false, searchable: false },
                { data: 'plan', name: 'plan' },
                { data: 'price', name: 'price' },
                { data: 'duration', name: 'duration' },
                { data: 'created_at', name: 'created_at' },
                { data: 'renews', name: 'renews', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false }
            ],
            language: {
                processing: '<div class="spinner-border text-primary" role="status" style="height:80px;"><span class="sr-only">Loading...</span></div>',
                paginate: {
                    previous: 'Prev',
                    next: 'Next'
                }
            }
        });

        $('#subsSearch').on('keyup', function () {
            table.search(this.value).draw();
        });

        $('#refreshSubsTable').on('click', function () {
            table.ajax.reload(null, false);
        });
    });
</script>
@endpush
