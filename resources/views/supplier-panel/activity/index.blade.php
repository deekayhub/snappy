@extends('supplier-panel.layouts.app')
@section('title', 'Supplier Activity')

@section('content')
<div class="content-wrapper p-3">
    <div class="card border-0 shadow-sm rounded-4">
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
                    <div class="text-muted">No recent activity yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
