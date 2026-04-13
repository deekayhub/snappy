@extends('admin.layouts.app')
@section('title', 'Purchase Quotes')

@section('content')
@push('styles')
<style>
    .quote-summary-card,
    .job-table-card {
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
</style>
@endpush
<div class="content-wrapper p-3">
    <div class="card quote-summary-card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 p-lg-5">
            <span class="badge bg-primary px-3 py-2 mb-3 rounded-4">Admin Quote Space</span>
            <h2 class="mb-2">Purchase quote management</h2>
            <p class="text-white mb-0">Review supplier quotes, linked jobs, and the latest customer-facing status in one place.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4"><div class="small text-muted">Total Quotes</div><div class="display-6 fw-bold">{{ $stats['total_quotes'] }}</div></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4"><div class="small text-muted">Submitted Quotes</div><div class="display-6 fw-bold text-success">{{ $stats['submitted_quotes'] }}</div></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4"><div class="small text-muted">Accepted Quotes</div><div class="display-6 fw-bold text-warning">{{ $stats['accepted_quotes'] }}</div></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4"><div class="small text-muted">Jobs With Quotes</div><div class="display-6 fw-bold text-primary">{{ $stats['jobs_with_quotes'] }}</div></div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Job</th>
                            <th>Supplier</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Sent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quotes as $quote)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $quote->job?->title ?: 'Job removed' }}</div>
                                    <div class="small text-muted">Job No. {{ str_pad((string) $quote->customer_job_id, 4, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $quote->supplier?->supplierProfile?->company_name ?: $quote->supplier?->name }}</div>
                                    <div class="small text-muted">{{ $quote->supplier?->email }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $quote->job?->user?->name ?: '-' }}</div>
                                    <div class="small text-muted">{{ $quote->job?->user?->email ?: '-' }}</div>
                                </td>
                                <td class="fw-bold">£ {{ number_format((float) $quote->total_price, 2) }}</td>
                                <td><span class="badge bg-light text-dark text-uppercase">{{ $quote->status }}</span></td>
                                <td>{{ $quote->sent_at?->format('d M Y h:i A') ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted">No quotes have been submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

