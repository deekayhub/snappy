@extends('supplier-panel.layouts.app')
@section('title', 'Job Board')

@php
    $jobMeta = function ($job) {
        $neededBy = $job->needed_by;

        if (!$neededBy || $job->status !== 'open') {
            return ['Ended Jobs', 'danger', 'Ended jobs'];
        }

        if ($neededBy->isPast()) {
            return ['Ended Jobs', 'danger', 'Ended jobs'];
        }

        if ($neededBy->diffInSeconds(now(), false) <= 7200) {  
            return ['Ending Soon', 'warning', 'Job ending time left'];
        }

        return ['Active Jobs', 'success', 'Active jobs'];
    };
@endphp

@section('content')
<div class="content-wrapper p-3">
    @dump($jobs->toArray())

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search jobs</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-4" placeholder="Search title, category, organisation, location">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select rounded-4 text-dark">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->name }}" @selected($category->name === request('category'))>
                                {{ ucfirst($category->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                 
                <div class="col-md-3">
                    <label class="form-label">Sort</label>
                    <select name="sort" class="form-select rounded-4 text-dark">
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
                <div class="col-md-1">
                    <button type="button" class="btn btn-secondary rounded-4 w-100" onclick="removeParams()"><i class="fa fa-undo"></i></button>
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
                            <span id="status-{{ $job->id }}" class="badge bg-{{ $meta[1] }}">
                                {{ $meta[0] }}
                            </span>
                            <span class="small text-muted">Job No. {{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h4 class="mb-2">{{ $job->title }}</h4>
                        <p class="text-muted mb-3">{{ \Illuminate\Support\Str::limit($job->description, 140) }}</p>
                        <div class="alert alert-light rounded p-2 mb-3">
                            <div class="small text-muted mb-2">Category: <strong class="text-capitalize">{{ $job->categoryId?->name ?? 'General' }}</strong></div>
                            <div class="small text-muted mb-2">Organisation: {{ $job->organisation_name ?: 'Not provided' }}</div>
                            <div class="small text-muted mb-2">Location: {{ $job->location ?: 'Not provided' }}</div>
                            <div class="small text-muted mb-3">Needed by: {{ $job->needed_by?->format('d M Y h:i A') ?? 'N/A' }}</div>
                        </div>
                        <div class="fw-semibold mb-3">{{ $job->budget ? '£ '.number_format((float) $job->budget, 2) : 'Budget not shared' }}</div>
                        <div class="alert alert-light border rounded-4 small mb-3">
                            <div class="d-flex justify-content-between flex-wrap align-items-center">
                                <span id="status-text-{{ $job->id }}" class="js-job-status-text">{{ $meta[2] }}</span>
                                @if ($job->needed_by && $job->status === 'open' && $job->needed_by->isFuture())
                                    <div 
                                        class="fw-semibold mt-1 js-job-countdown {{ $meta[0] === 'Ending Soon' ? 'text-danger' : 'text-muted d-none' }}"
                                        data-end-at="{{ $job->needed_by->toIso8601String() }}"
                                        data-status-badge-id="status-{{ $job->id }}"
                                        data-status-text-id="status-text-{{ $job->id }}"
                                    >
                                        Time left: --
                                    </div>
                                @endif

                            </div>
                        </div>
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
                        <div class="modal-header border0">
                            <div>
                                <div class="small text-muted">Job No. {{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</div>
                                <h5 class="modal-title">{{ $job->title }}</h5>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="small text-muted">Category</div><div class="fw-semibold">{{ $job->categoryId?->name ?? 'General' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="small text-muted">Location</div><div class="fw-semibold">{{ $job->location ?: 'Not provided' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="small text-muted">Budget</div><div class="fw-semibold">{{ $job->budget ? '£ '.number_format((float) $job->budget, 2) : 'Not shared' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="small text-muted">Description</div><div class="fw-semibold">{{ $job->description ?? ''}}</div>
                                    </div>
                                </div>
                                @if($job->dynamicFieldValues->isNotEmpty())
                                    <div class="col-md-12">
                                        <strong>More Details</strong>
                                    </div>
                                    @foreach ($job->dynamicFieldValues as $fieldsValue)

                                        @if($fieldsValue->categoryFields?->field_type == 'file')
                                            
                                            <div class="col-md-4">
                                                <div class="border rounded-4 p-3 h-100">
                                                    <div class="small text-muted">{{ optional($fieldsValue->categoryFields)->field_label ?? '' }}</div>
                                                    <img src="{{ asset($fieldsValue->field_value) }}" alt="" style="max-height: 200px; object-fit: cover;">
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-4">
                                                <div class="border rounded-4 p-3 h-100">
                                                    <div class="small text-muted">{{ optional($fieldsValue->categoryFields)->field_label ?? '' }}</div>
                                                    <div class="fw-semibold">{{ $fieldsValue->field_value ?? ''}}</div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                    @endforeach

                                @endif
                            </div>

                        </div>
                        {{-- <div class="modal-footer border-0">
                            <button class="btn btn-secondary rounded-4" disabled>Upgrade to quote</button>
                        </div> --}}
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
                                    <div class="col-md-3">
                                        <label class="form-label">Price for job</label>
                                        <input type="number" name="price_for_job" step="0.01" min="0" class="form-control rounded-4" value="{{ old('price_for_job', optional($existingQuote)->price_for_job) }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Discount offered</label>
                                        <input type="number" name="discount_offered" step="0.01" min="0" class="form-control rounded-4" value="{{ old('discount_offered', optional($existingQuote)->discount_offered) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Delivery cost</label>
                                        <input type="number" name="delivery_cost" step="0.01" min="0" class="form-control rounded-4" value="{{ old('delivery_cost', optional($existingQuote)->delivery_cost) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total</label>
                                        <input type="number" name="total" step="0.01" min="0" class="form-control rounded-4" value="{{ old('total', optional($existingQuote)->total_price) }}" readonly>
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

@push('scripts')
<script>
    function removeParams() {
        const url = new URL(window.location.href);
        url.searchParams.delete('search');
        url.searchParams.delete('category');
        url.searchParams.delete('sort');
        // window.history.replaceState({}, document.title, url.pathname + url.search);
        window.location.href = url.pathname + url.search;
    }

    document.addEventListener("DOMContentLoaded", function () {

        const priceInput = document.querySelector('input[name="price_for_job"]');
        const discountInput = document.querySelector('input[name="discount_offered"]');
        const deliveryInput = document.querySelector('input[name="delivery_cost"]');
        const totalInput = document.querySelector('input[name="total"]');

        function calculateTotal() {
            let price = parseFloat(priceInput.value) || 0;
            let discount = parseFloat(discountInput.value) || 0;
            let delivery = parseFloat(deliveryInput.value) || 0;

            let total = price - discount + delivery;

            if (total < 0) total = 0;

            totalInput.value = total.toFixed(2);
        }
        calculateTotal();

        totalInput.value = "0.00";

        priceInput.addEventListener("input", calculateTotal);
        discountInput.addEventListener("input", calculateTotal);
        deliveryInput.addEventListener("input", calculateTotal);
    });
   (function () {
        function formatTimeLeft(diffMs) {
            if (diffMs <= 0) return 'Ended';

            var totalSeconds = Math.floor(diffMs / 1000);
            var days = Math.floor(totalSeconds / 86400);
            var hours = Math.floor((totalSeconds % 86400) / 3600);
            var minutes = Math.floor((totalSeconds % 3600) / 60);
            var seconds = totalSeconds % 60;

            return days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's';
        }

        var timers = Array.from(document.querySelectorAll('.js-job-countdown'));
        if (!timers.length) return;

        function tick() {
            var now = new Date().getTime();

            timers.forEach(function (el) {
                var endAt = new Date(el.dataset.endAt).getTime();
                var badge = document.getElementById(el.dataset.statusBadgeId);
                var statusText = document.getElementById(el.dataset.statusTextId);

                if (isNaN(endAt)) {
                    el.textContent = 'Time left: --';
                    return;
                }

                var diff = endAt - now;

                if (diff <= 0) {
                    el.classList.add('d-none');
                    el.classList.remove('text-danger');
                    el.classList.add('text-muted');
                    el.textContent = 'Time left: --';
                    if (statusText) statusText.textContent = 'Ended jobs';

                    if (badge) {
                        badge.textContent = 'Ended Jobs';
                        badge.classList.remove('bg-success', 'bg-warning', 'bg-secondary');
                        badge.classList.add('bg-danger');
                    }

                    return;
                }

                if (diff > 86400000) { // more than 24 hours
                    el.classList.add('d-none');
                    el.classList.remove('text-danger');
                    el.classList.add('text-muted');
                    el.textContent = 'Time left: --';
                    if (statusText) statusText.textContent = 'Job active';

                    if (badge) {
                        badge.textContent = 'Active';
                        badge.classList.remove('bg-warning', 'bg-danger', 'bg-secondary');
                        badge.classList.add('bg-success');
                    }

                    return;
                }

                el.classList.remove('d-none');
                el.classList.remove('text-muted');
                el.classList.add('text-danger');
                if (statusText) statusText.textContent = 'Job ending soon';

                if (badge) {
                    badge.textContent = 'Ending Soon';
                    badge.classList.remove('bg-success', 'bg-danger', 'bg-secondary');
                    badge.classList.add('bg-warning');
                }

                el.textContent = 'Time left: ' + formatTimeLeft(diff);
            });
        }

        tick();
        setInterval(tick, 1000);
    })();
</script>
@endpush
