@extends('supplier-panel.layouts.app')
@section('title', 'Supplier Dashboard')

@php
    $statusMeta = function ($job) {
        if ($job->status !== 'open' || ($job->needed_by && $job->needed_by->isPast())) {
            return ['Ended', 'danger'];
        }

        if ($job->needed_by && $job->needed_by->diffInSeconds(now(), false) <= 7200)  {
            return ['Ending Soon', 'warning'];
        }

        return ['Active', 'success'];
    };
@endphp

@section('content')
    <div class="content-wrapper p-3">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 24px; background: linear-gradient(135deg, #0f172a, #0f766e);">
            <div class="card-body text-white p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-primary px-3 py-2 mb-3">Supplier Hub</span>
                        <h2 class="text-white mb-2">View jobs, track activity, and manage your supplier presence.</h2>
                        <p class="mb-0 text-white-50">Built from your supplier requirements document: profile editing, job board visibility, past-quote space, reporting, and account activity.</p>
                    </div>
                    <div class="col-lg-4">
                        <div class="bg-white bg-opacity-10 rounded-4 p-3">
                            <div class="small text-white-50 mb-2">Quote activity</div>
                            <div class="h4 mb-1">{{ $stats['submitted_quotes'] }} quotes sent</div>
                            <div class="text-white-50 small">Your supplier account can now submit and update quotes directly from the job board.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Available Jobs</div><div class="display-6 fw-bold">{{ $stats['available_jobs'] }}</div></div></div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Active Jobs</div><div class="display-6 fw-bold text-success">{{ $stats['active_jobs'] }}</div></div></div>
            </div>
            {{-- <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Ending Soon</div><div class="display-6 fw-bold text-warning">{{ $stats['ending_soon'] }}</div></div></div>
            </div> --}}
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Ended</div><div class="display-6 fw-bold text-danger">{{ $stats['ended_jobs'] }}</div></div></div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Your Quotes</div><div class="display-6 fw-bold text-primary">{{ $stats['submitted_quotes'] }}</div></div></div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="mb-1">Latest job board items</h4>
                                <p class="text-muted mb-0">Each job gets a running reference based on its job number.</p>
                            </div>
                            <a href="{{ route('supplier-panel.jobs') }}" class="btn btn-outline-primary rounded-4">View full board</a>
                        </div>

                        <div class="row g-3">
                            @forelse ($jobs as $job)
                                @php($meta = $statusMeta($job))
                                <div class="col-12">
                                    <div class="border rounded-4 p-3">
                                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="badge bg-{{ $meta[1] }}">{{ $meta[0] }}</span>
                                                    <span class="badge bg-light text-dark">Job No. {{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</span>
                                                </div>
                                                <h5 class="mb-1">{{ $job->title }}</h5>
                                                <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit($job->description, 120) }}</p>
                                                <div class="small text-muted">{{ $job->category ?: 'General' }} • {{ $job->location ?: 'Location not specified' }}</div>
                                            </div>
                                            <div class="text-lg-end">
                                                <div class="fw-semibold">{{ $job->budget ? '£ '.number_format((float) $job->budget, 2) : 'Budget on request' }}</div>
                                                <div class="small text-muted mb-3">Needed by {{ $job->needed_by?->format('d M Y') ?? 'TBC' }}</div>
                                                <a href="{{ route('supplier-panel.jobs') }}" class="btn btn-primary rounded-4">Open quote form</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-light border rounded-4 mb-0">No jobs have been posted yet.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-2">Profile completion</h4>
                        <p class="text-muted">Supplier profile fields requested in your document.</p>
                        <ul class="list-unstyled mb-0">
                            <li class="py-2 border-bottom">Company name: {{ $user->supplierProfile?->company_name ?: 'Missing' }}</li>
                        <li class="py-2 border-bottom">Website: {{ $user->supplierProfile?->website ?: 'Missing' }}</li>
                        <li class="py-2 border-bottom">Review link: {{ $user->supplierProfile?->review_link ?: 'Missing' }}</li>
                        <li class="py-2 border-bottom">Social link: {{ $user->supplierProfile?->social_link ?: 'Missing' }}</li>
                        <li class="py-2">
                            Supplier rating:
                            @if ($supplierAverageRating)
                                <span class="text-warning">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa {{ $i <= round($supplierAverageRating) ? 'fa-star' : 'fa-star-o' }}"></i>
                                    @endfor
                                </span>
                                <span class="text-muted">{{ $supplierAverageRating }}/5 ({{ $supplierRatingsCount }} ratings)</span>
                            @else
                                <span class="text-muted">No ratings yet</span>
                            @endif
                        </li>
                        </ul>
                    </div>
                </div>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Recent quotes</h4>
                        @forelse ($recentQuotes as $quote)
                            <div class="border rounded-4 p-3 mb-3">
                                <div class="small text-muted">{{ $quote->created_at?->format('d M Y h:i A') }}</div>
                                <div class="fw-semibold">{{ $quote->job?->title ?: 'Job removed' }}</div>
                                <div class="small text-muted text-uppercase">{{ $quote->status }}</div>
                                <div class="fw-bold mt-1">£ {{ number_format((float) $quote->total_price, 2) }}</div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">You have not submitted any quotes yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
