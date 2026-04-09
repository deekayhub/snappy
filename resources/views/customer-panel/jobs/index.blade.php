@extends('customer-panel.layouts.app')
@section('title', 'My Jobs')

@section('content')
<div class="content-wrapper p-3">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h3 class="mb-1">My posted jobs</h3>
            <p class="text-muted mb-0">Track every request and see how many suppliers have replied.</p>
        </div>
        <a href="{{ route('customer.jobs.create') }}" class="btn btn-primary rounded-4">Post New Quote Request</a>
    </div>

    @forelse ($jobs as $job)
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div>
                        <div class="small text-muted mb-1">Job #{{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <h4 class="mb-1">{{ $job->title }}</h4>
                        <div class="text-muted mb-2">{{ $job->category ?: 'General' }} | {{ $job->location ?: 'No location set' }}</div>
                        <p class="mb-0 text-muted">{{ \Illuminate\Support\Str::limit($job->description, 200) }}</p>
                    </div>
                    <div class="text-lg-end">
                        <span class="badge bg-{{ $job->status === 'open' ? 'success' : 'secondary' }}">{{ ucfirst($job->status) }}</span>
                        <div class="small text-muted mt-2">Needed by: {{ $job->needed_by?->format('d M Y') ?? 'Not set' }}</div>
                        <div class="small text-muted">Budget: {{ $job->budget ? '€ '.number_format((float) $job->budget, 2) : 'Not shared' }}</div>
                        <div class="fw-semibold mt-2">{{ $job->quotes_count }} supplier quotes</div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-light border rounded-4">No jobs posted yet.</div>
    @endforelse

    <div class="mt-4">
        {{ $jobs->links() }}
    </div>
</div>
@endsection
