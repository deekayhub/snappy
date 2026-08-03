@extends('customer-panel.layouts.app')
@section('title', 'Job Details')

@section('content')
<div class="content-wrapper p-3">
    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <h3 class="mt-2 mb-1">Job #{{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }} - {{ $job->title }}</h3>
                <p class="text-muted mb-0">
                    {{ ucfirst($job->categoryId?->name ?? 'General') }} | {{ $job->location ?: 'Location not set' }}
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <div class=""><span class="badge rounded bg-{{ $job->status === 'open' ? 'success' : 'secondary' }} text-white px-3 py-2">{{ ucfirst($job->status) }}</span></div>
                <a href="{{ route('customer-panel.jobs.edit', $job) }}" class="btn btn-primary rounded-4">
                    <i class="mdi mdi-pencil me-1"></i> Edit Job
                </a>
                <a href="{{ route('customer-panel.jobs') }}" class="btn btn-light border rounded-4">
                    <i class="mdi mdi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            @include('customer-panel.jobs.job-details', [
                'job' => $job,
                'groupedDynamicFieldValues' => $groupedDynamicFieldValues,
                'fields' => $fields,
            ])
        </div>
    </div>
</div>
@endsection
