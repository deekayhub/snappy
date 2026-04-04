@extends('admin.layouts.app')
@section('title', 'Purchase Quotes')

@section('content')
<div class="content-wrapper p-3">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 p-lg-5">
            <span class="badge bg-light text-primary px-3 py-2 mb-3">Admin Quote Space</span>
            <h2 class="mb-2">Purchase quote management</h2>
            <p class="text-muted mb-0">This page is ready in the admin panel, but the real quote database and paid supplier quote flow still need to be added before quote records can be fully managed.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4"><div class="small text-muted">Jobs Open for Quotes</div><div class="display-6 fw-bold">{{ $stats['jobs_available_for_quotes'] }}</div></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4"><div class="small text-muted">Active Jobs</div><div class="display-6 fw-bold text-success">{{ $stats['active_jobs'] }}</div></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4"><div class="small text-muted">Ending Soon</div><div class="display-6 fw-bold text-warning">{{ $stats['ending_soon'] }}</div></div></div>
        </div>
    </div>
</div>
@endsection
