@extends('supplier-panel.layouts.app')
@section('title', 'Job Board')

@php
    $jobMeta = function ($job) {
        if ($job->status !== 'open' || ($job->needed_by && $job->needed_by->isPast())) {
            return ['Ended', 'danger', 'Job listing ended'];
        }
        if ($job->needed_by && $job->needed_by->isBefore(now()->addDays(4))) {
            return ['Ending Soon', 'warning', 'Job listing ending soon'];
        }
        return ['Active', 'success', 'Job active'];
    };
@endphp

@section('content')
<div class="content-wrapper p-3">
    @if (session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Search jobs</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-4" placeholder="Search title, category, organisation, location">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sort</label>
                    <select name="sort" class="form-select rounded-4">
                        <option value="newest" @selected($sort === 'newest')>Newest to oldest</option>
                        <option value="oldest" @selected($sort === 'oldest')>Oldest to newest</option>
                        <option value="ending_soon" @selected($sort === 'ending_soon')>Ending soon</option>
                        <option value="budget_high" @selected($sort === 'budget_high')>Highest budget</option>
                        <option value="budget_low" @selected($sort === 'budget_low')>Lowest budget</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary rounded-4 w-100">Go</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @forelse ($jobs as $job)
            @php($meta = $jobMeta($job))
            @php($existingQuote = $job->quotes->first())
            <div class="col-lg-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="border-top: 5px solid var(--bs-{{ $meta[1] }}) !important;">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <span class="badge bg-{{ $meta[1] }}">{{ $meta[0] }}</span>
                            <span class="small text-muted">Job No. {{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h4 class="mb-2">{{ $job->title }}</h4>
                        <p class="text-muted mb-3">{{ \Illuminate\Support\Str::limit($job->description, 140) }}</p>
                        <div class="small text-muted mb-2">Category: {{ $job->category ?: 'General' }}</div>
                        <div class="small text-muted mb-2">Organisation: {{ $job->organisation_name ?: 'Not provided' }}</div>
                        <div class="small text-muted mb-2">Location: {{ $job->location ?: 'Not provided' }}</div>
                        <div class="small text-muted mb-3">Needed by: {{ $job->needed_by?->format('d M Y') ?? 'TBC' }}</div>
                        <div class="fw-semibold mb-3">{{ $job->budget ? '$ '.number_format((float) $job->budget, 2) : 'Budget not shared' }}</div>
                        <div class="alert alert-light border rounded-4 small mb-3">{{ $meta[2] }}</div>
                        @if ($existingQuote)
                            <div class="small text-success mb-3">Your quote is already submitted for this job.</div>
                        @endif
                        <div class="mt-auto d-flex gap-2">
                            <button class="btn btn-outline-dark rounded-4 flex-fill" type="button" data-bs-toggle="modal" data-bs-target="#jobModal{{ $job->id }}">View details</button>
                            <button class="btn btn-primary rounded-4 flex-fill" type="button" data-bs-toggle="modal" data-bs-target="#quoteModal{{ $job->id }}">{{ $existingQuote ? 'Update Quote' : 'Send Quote' }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="jobModal{{ $job->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0">
                        <div class="modal-header border-0">
                            <div>
                                <div class="small text-muted">Job No. {{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</div>
                                <h5 class="modal-title">{{ $job->title }}</h5>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">Category</div><div class="fw-semibold">{{ $job->category ?: 'General' }}</div></div></div>
                                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">Location</div><div class="fw-semibold">{{ $job->location ?: 'Not provided' }}</div></div></div>
                                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">Budget</div><div class="fw-semibold">{{ $job->budget ? '$ '.number_format((float) $job->budget, 2) : 'Not shared' }}</div></div></div>
                            </div>
                            <p class="mb-0">{{ $job->description }}</p>
                        </div>
                        <div class="modal-footer border-0">
                            <button class="btn btn-secondary rounded-4" disabled>Upgrade to quote</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="quoteModal{{ $job->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0">
                        <div class="modal-header border-0">
                            <div>
                                <div class="small text-muted">Quote for Job No. {{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</div>
                                <h5 class="modal-title">{{ $job->title }}</h5>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('supplier-panel.quotes.store', $job) }}">
                            @csrf
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Delivery cost</label>
                                        <input type="number" name="delivery_cost" step="0.01" min="0" class="form-control rounded-4" value="{{ old('delivery_cost', optional($existingQuote)->delivery_cost) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Discount offered</label>
                                        <input type="number" name="discount_offered" step="0.01" min="0" class="form-control rounded-4" value="{{ old('discount_offered', optional($existingQuote)->discount_offered) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Price for job</label>
                                        <input type="number" name="price_for_job" step="0.01" min="0" class="form-control rounded-4" value="{{ old('price_for_job', optional($existingQuote)->price_for_job) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Notes</label>
                                        <textarea name="notes" rows="4" class="form-control rounded-4" placeholder="Add delivery terms, timing, extras, or any helpful context for the customer.">{{ old('notes', optional($existingQuote)->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light rounded-4" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-primary rounded-4">{{ $existingQuote ? 'Update quote' : 'Submit quote' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border rounded-4">No jobs matched your filters.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $jobs->links() }}
    </div>
</div>
@endsection
