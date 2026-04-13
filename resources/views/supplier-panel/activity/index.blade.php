@extends('supplier-panel.layouts.app')
@section('title', 'Supplier Activity')

@section('content')
<div class="content-wrapper p-3">
    <div class="page-header">
        <h3 class="page-title"> 
            Activity
        </h3> 
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h4 class="mb-3">Recent marketplace activity</h4>
                    <div class="timeline">
                        @forelse ($recentJobs as $job)
                            <div class="border-start ps-4 pb-4 position-relative">
                                <span class="position-absolute top-0 start-0 translate-middle p-2 bg-primary border border-light rounded-circle"></span>
                                <div class="small text-muted">{{ $job->created_at?->format('d M Y h:i A') }}</div>
                                <h5 class="mb-1">{{ $job->title }}</h5>
                                <p class="mb-1 text-muted">{{ \Illuminate\Support\Str::limit($job->description, 120) }}</p>
                                <div class="small text-muted">Posted by {{ $job->user?->name ?: 'Customer' }}</div>
                            </div>
                        @empty
                            <div class="text-muted">No recent marketplace activity yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h4 class="mb-3">Your quote history</h4>
                    @forelse ($recentQuotes as $quote)
                        <div class="border rounded-4 p-3 mb-3">
                            <div class="small text-muted">{{ $quote->created_at?->format('d M Y h:i A') }}</div>
                            <div class="fw-semibold">{{ $quote->job?->title ?: 'Job removed' }}</div>
                            <div class="small text-muted mb-1 text-uppercase">{{ $quote->status }}</div>
                            <div class="small text-muted">Total quoted: £ {{ number_format((float) $quote->total_price, 2) }}</div>
                        </div>
                    @empty
                        <div class="text-muted">You have not sent any quotes yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

