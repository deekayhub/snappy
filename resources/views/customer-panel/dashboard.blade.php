@extends('customer-panel.layouts.app')
@section('title', 'Customer Dashboard')

@section('content')
<div class="content-wrapper p-3">
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 24px; background: linear-gradient(135deg, #1e293b, #0284c7);">
        <div class="card-body text-white p-4 p-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge bg-white text-primary px-3 py-2 mb-3">Customer Hub</span>
                    <h2 class="text-white mb-2">Manage jobs, compare supplier quotes, and contact suppliers quickly.</h2>
                    <p class="mb-0 text-white-50">Everything you need in one panel: post jobs, track quote activity, and follow up with suppliers from a single screen.</p>
                </div>
                <div class="col-lg-4">
                    <a href="{{ route('customer.jobs.create') }}" class="btn btn-light rounded-4 w-100 py-3">Post New Quote Request</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Jobs Posted</div><div class="display-6 fw-bold">{{ $stats['jobs_posted'] }}</div></div></div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Open Jobs</div><div class="display-6 fw-bold text-success">{{ $stats['open_jobs'] }}</div></div></div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Closed Jobs</div><div class="display-6 fw-bold text-danger">{{ $stats['closed_jobs'] }}</div></div></div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Total Quotes Received</div><div class="display-6 fw-bold text-primary">{{ $stats['quotes_received'] }}</div></div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Recent jobs</h4>
                        <a href="{{ route('customer-panel.jobs') }}" class="btn btn-outline-primary rounded-4">View all</a>
                    </div>
                    @forelse ($recentJobs as $job)
                        <div class="border rounded-4 p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="small text-muted mb-1">Job #{{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    <h5 class="mb-1">{{ $job->title }}</h5>
                                    <div class="small text-muted">{{ $job->category ?: 'General' }} | {{ $job->location ?: 'No location set' }}</div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark">{{ $job->quotes_count }} quotes</span>
                                    <div class="small text-muted mt-1">{{ $job->created_at?->format('d M Y') }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No jobs posted yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Latest supplier quotes</h4>
                        <a href="{{ route('customer-panel.quotes') }}" class="btn btn-outline-dark rounded-4">Open inbox</a>
                    </div>
                    @forelse ($recentQuotes as $quote)
                        <div class="border rounded-4 p-3 mb-3">
                            <div class="small text-muted">{{ $quote->created_at?->format('d M Y h:i A') }}</div>
                            <div class="fw-semibold">{{ $quote->job?->title ?: 'Job removed' }}</div>
                            <div class="small text-muted mb-2">{{ $quote->supplier?->supplierProfile?->company_name ?: $quote->supplier?->name }}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark text-uppercase">{{ $quote->status }}</span>
                                <span class="fw-bold">€ {{ number_format((float) $quote->total_price, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No supplier quotes yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
