@extends('customer-panel.layouts.app')
@section('title', 'Supplier Quotes')

@push('styles')
<style>
    .rating-stars {
        display: inline-flex;
        flex-direction: row-reverse;
        gap: 0.35rem;
    }

    .rating-stars input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .rating-stars label {
        margin: 0;
        font-size: 1.4rem;
        color: #ced4da;
        cursor: pointer;
        line-height: 1;
        transition: color 0.15s ease-in-out;
    }

    .rating-stars label:hover,
    .rating-stars label:hover ~ label,
    .rating-stars input[type="radio"]:checked ~ label {
        color: #f59f00;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper p-3">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <span class="badge rounded-pill text-bg-light px-3 py-2">Quote Inbox</span>
            <h2 class="mt-2 mb-1">Supplier quotes for your jobs</h2>
            <p class="text-muted mb-0">Compare prices, update quote status, and email suppliers from one screen.</p>
        </div>
        {{-- <a href="{{ route('customer.jobs.create') }}" class="btn btn-primary rounded-4">Post New Quote Request</a> --}}
    </div> 

    @forelse ($jobs as $job)
        @php
            $supplierCount = $job->quotes->pluck('supplier_user_id')->filter()->unique()->count();
        @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
                    <div>
                        <div class="small text-muted mb-1">Job #{{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <h3 class="mb-1">{{ $job->title }}</h3>
                        <p class="mb-0 text-muted">{{ $job->category ?: 'General' }} | {{ $job->location ?: 'Location not set' }}</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ $job->quotes->count() }} quotes</span>
                        <span class="badge bg-success-subtle text-success px-3 py-2">{{ $supplierCount ?? '' }} suppliers quoted</span>
                    </div>
                </div>

                @forelse ($job->quotes as $quote)
                    @php
                        $supplierName = $quote->supplier?->supplierProfile?->company_name ?: $quote->supplier?->name;
                        $mailSubject = rawurlencode('Quote follow-up for Job #'.str_pad((string) $job->id, 4, '0', STR_PAD_LEFT).' - '.$job->title);
                        $mailBody = rawurlencode('Hello '.$supplierName.",\n\nI am contacting you regarding your quote for ".$job->title.".\n\nThank you.");
                    @endphp
                    <div class="border rounded-4 p-4 mb-3" style="background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);">
                        <div class="row g-3 align-items-center">
                            <div class="col-xl-5">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-dark">Supplier quote</span>
                                    <span class="badge bg-light text-dark text-uppercase">{{ $quote->status }}</span>
                                </div>
                                <h5 class="mb-1">{{ $supplierName }}</h5>
                                <div class="small text-muted mb-2">{{ $quote->supplier?->email ?: 'No email address' }}</div>
                                @php
                                    $avgRating = $quote->supplier?->supplier_average_rating ? round((float) $quote->supplier->supplier_average_rating, 1) : null;
                                    $ratingCount = (int) ($quote->supplier?->supplier_ratings_count ?? 0);
                                @endphp
                                <div class="small mb-2">
                                    @if ($avgRating)
                                        <span class="text-warning">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa {{ $i <= round($avgRating) ? 'fa-star' : 'fa-star-o' }}"></i>
                                            @endfor
                                        </span>
                                        <span class="text-muted">{{ $avgRating }}/5 ({{ $ratingCount }} ratings)</span>
                                    @else
                                        <span class="text-muted">No ratings yet</span>
                                    @endif
                                </div>
                                <p class="mb-0 text-muted">{{ $quote->notes ?: 'No extra notes were provided for this quote.' }}</p>
                            </div>
                            <div class="col-xl-4">
                                <div class="row g-2 text-center">
                                    <div class="col-6">
                                        <div class="border rounded-4 p-2 h-100">
                                            <div class="small text-muted">Job price</div>
                                            <div class="fw-semibold">£ {{ number_format((float) $quote->price_for_job, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded-4 p-2 h-100">
                                            <div class="small text-muted">Delivery</div>
                                            <div class="fw-semibold">£ {{ number_format((float) $quote->delivery_cost, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded-4 p-2 h-100">
                                            <div class="small text-muted">Discount</div>
                                            <div class="fw-semibold">£ {{ number_format((float) $quote->discount_offered, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded-4 p-2 h-100 bg-light">
                                            <div class="small text-muted">Total</div>
                                            <div class="fw-bold">£ {{ number_format((float) $quote->total_price, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="d-grid gap-2">
                                    <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="accepted">
                                        <button class="btn btn-success rounded-4 w-100">Accept Quote</button>
                                    </form>
                                    <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="btn btn-outline-danger rounded-4 w-100">Reject Quote</button>
                                    </form>
                                    <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="submitted">
                                        <button class="btn btn-outline-secondary rounded-4 w-100">Mark Pending</button>
                                    </form>
                                    @if (in_array($quote->status, ['accepted', 'completed']))
                                        <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button class="btn btn-outline-dark rounded-4 w-100">Mark Completed</button>
                                        </form>
                                    @endif
                                    @if ($quote->supplier?->email)
                                        <a class="btn btn-outline-primary rounded-4 w-100" href="mailto:{{ $quote->supplier->email }}?subject={{ $mailSubject }}&body={{ $mailBody }}">
                                            Email Supplier
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($quote->status === 'completed')
                            <div class="border rounded-4 p-3 mt-3">
                                <div class="fw-semibold mb-2">Rate this supplier</div>
                                <form method="POST" action="{{ route('customer.quotes.rating', $quote) }}" class="row g-2 align-items-end">
                                    @csrf
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">Stars</label>
                                        @php($selectedRating = (int) old('customer_rating', $quote->customer_rating))
                                        <div class="rating-stars mt-1">
                                            @for ($i = 5; $i >= 1; $i--)
                                                <input
                                                    type="radio"
                                                    name="customer_rating"
                                                    id="customer_rating_{{ $quote->id }}_{{ $i }}"
                                                    value="{{ $i }}"
                                                    @checked($selectedRating === $i)
                                                    required
                                                >
                                                <label for="customer_rating_{{ $quote->id }}_{{ $i }}" title="{{ $i }} Star{{ $i > 1 ? 's' : '' }}">
                                                    <i class="fa fa-star"></i>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label small text-muted">Review (optional)</label>
                                        <input
                                            type="text"
                                            name="customer_review"
                                            class="form-control rounded-4"
                                            maxlength="1000"
                                            value="{{ old('customer_review', $quote->customer_review) }}"
                                            placeholder="Share your experience with this supplier"
                                        >
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary rounded-4 w-100">{{ $quote->customer_rating ? 'Update' : 'Submit' }}</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="alert alert-light border rounded-4 mb-0">No supplier quotes have been submitted for this job yet.</div>
                @endforelse
            </div>
        </div>
    @empty
        <div class="alert alert-light border rounded-4">You have not posted any jobs yet, so there are no quotes to review.</div>
    @endforelse
</div>
@endsection
