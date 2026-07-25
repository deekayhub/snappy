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
        {{-- <a href="{{ route('customer.jobs.create') }}" class="btn btn-primary rounded-3">Post New Quote Request</a> --}}
    </div> 
    {{-- @dump($jobs->toArray()) --}}

    @forelse ($jobs as $job)
        @php
            $supplierCount = $job->quotes->pluck('supplier_user_id')->filter()->unique()->count();
        @endphp
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
                    <div>
                        <div class="small text-muted mb-1">Job #{{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <h3 class="mb-1">{{ $job->title }}</h3>
                        <p class="mb-0 text-muted fw-semibold text-capitalize">{{ $job->categoryId?->name ?: 'General' }} | {{ $job->location ?: 'Location not set' }}</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge rounded bg-primary-subtle text-primary px-3 py-2">{{ $job->quotes->count() }} quotes</span>
                        <span class="badge rounded bg-success-subtle text-success px-3 py-2">{{ $supplierCount ?? '' }} suppliers quoted</span>
                    </div>
                </div>

                <div class="quote-list row ">
                    @forelse ($job->quotes as $quote)
                        @php
                            $supplierName = $quote->supplier?->supplierProfile?->company_name ?: $quote->supplier?->name;
                            $mailSubject = rawurlencode('Quote follow-up for Job #'.str_pad((string) $job->id, 4, '0', STR_PAD_LEFT).' - '.$job->title);
                            $mailBody = rawurlencode('Hello '.$supplierName.",\n\nI am contacting you regarding your quote for ".$job->title.".\n\nThank you.");
                        @endphp
                        <div class=" col-md-4">
                            <div class="border rounded-3 p-4 mb-3" style="background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);">
                                <div class="mb-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary rounded">Supplier quote</span>
                                        <span class="badge bg-success rounded text-white text-uppercase">{{ $quote->status }}</span>
                                    </div>
                                    <h5 class="mb-1">
                                        <a href="javascript:void(0)" class="text-decoration-none text-dark quote-supplier-link" data-id="{{ $quote->supplier?->id }}">
                                            {{ $supplierName }}
                                        </a>
                                    </h5>
                                    <div class="small text-muted mb-2">{{ $quote->supplier?->email ?: 'No email address' }}</div>
                                    @php
                                        $avgRating = $quote->supplier?->supplier_average_rating ? round((float) $quote->supplier->supplier_average_rating, 1) : null;
                                        $ratingCount = (int) ($quote->supplier?->supplier_ratings_count ?? 0);
                                    @endphp
                                    <div class="small mb-2">
                                        <span class="{{ $avgRating ? 'text-warning' : 'text-light' }}">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa {{ $avgRating && $i <= round($avgRating) ? 'fa-star' : 'fa fa-star' }}"></i>
                                            @endfor
                                        </span>

                                        @if ($avgRating)
                                            <span class="text-muted">
                                                {{ $avgRating }}/5 ({{ $ratingCount }} ratings)
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                0
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mb-0 text-muted">{{ $quote->notes ?: 'No extra notes were provided for this quote.' }}</p>
                                </div>
                                <div class="mb-3 p-2 rounded" style="background: #f7f9fc;">
                                    <ul class="list-unstyled">
                                        <li class="d-flex justify-content-between py-2 border-bottom">
                                            <span>Job price</span>
                                            <strong>£ {{ number_format((float) $quote->price_for_job, 2) }}</strong>
                                        </li>

                                        <li class="d-flex justify-content-between py-2 border-bottom">
                                            <span>Delivery</span>
                                            <strong>£ {{ number_format((float) $quote->delivery_cost, 2) }}</strong>
                                        </li>

                                        <li class="d-flex justify-content-between py-2 border-bottom">
                                            <span>Discount</span>
                                            <strong>£ {{ number_format((float) $quote->discount_offered, 2) }}</strong>
                                        </li>

                                        <li class="d-flex justify-content-between py-2 fw-bold">
                                            <span>Total</span>
                                            <span>£ {{ number_format((float) $quote->total_price, 2) }}</span>
                                        </li>
                                    </ul>
                                </div>
                                
                                @php $status = $quote->status; @endphp

                                @if ($status === 'completed')
                                    <div class="border rounded-3 p-3 mt-3">
                                        <div class="fw-semibold mb-2">Rate this supplier</div>
                                        <form method="POST" action="{{ route('customer.quotes.rating', $quote) }}" class=" ">
                                            @csrf
                                            <div class="mb-3 d-flex gap-3 align-items-center">
                                                <label class="form-label small mb-0 fw-semibold text-muted">Stars: </label>
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
                                            <div class="mb-3">
                                                <label class="form-label small text-muted">Review (optional)</label>
                                                <input
                                                    type="text"
                                                    name="customer_review"
                                                    class="form-control rounded-3"
                                                    maxlength="1000"
                                                    value="{{ old('customer_review', $quote->customer_review) }}"
                                                    placeholder="Share your experience with this supplier"
                                                >
                                            </div>
                                            <div class="co l-md-2">
                                                <button class="btn btn-primary rounded-3 w-100">{{ $quote->customer_rating ? 'Update' : 'Submit' }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                <div class="mt-3 d-flex flex-column gap-2">
                                    <div class="d-flex gap-2 flex-wrap">
                                        @if ($status === 'submitted')
                                            <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="accepted">
                                                <button class="btn btn-success btn-sm rounded-3" title="Accept"><i class="mdi mdi-check-circle"></i> Accept</button>
                                            </form>
                                            <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button class="btn btn-outline-danger btn-sm rounded-3" title="Reject"><i class="mdi mdi-close-circle"></i> Reject</button>
                                            </form>
                                        @elseif ($status === 'accepted')
                                            @unless(in_array($status, ['rejected', 'completed']))
                                            <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button class="btn btn-success btn-sm rounded-3" title="Completed"><i class="mdi mdi-check-all"></i> Completed</button>
                                            </form>
                                            @endunless
                                            <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="submitted">
                                                <button class="btn btn-outline-secondary btn-sm rounded-3" title="Pending"><i class="mdi mdi-clock-outline"></i> Pending</button>
                                            </form>
                                            <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button class="btn btn-outline-danger btn-sm rounded-3" title="Reject"><i class="mdi mdi-close-circle"></i> Reject</button>
                                            </form>
                                        @elseif ($status === 'rejected')
                                            <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="accepted">
                                                <button class="btn btn-success btn-sm rounded-3" title="Accept"><i class="mdi mdi-check-circle"></i> Accept</button>
                                            </form>
                                            <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="submitted">
                                                <button class="btn btn-outline-secondary btn-sm rounded-3" title="Pending"><i class="mdi mdi-clock-outline"></i> Pending</button>
                                            </form>
                                        @endif

                                        @unless($status === 'completed')
                                            @if ($quote->supplier?->email)
                                                <a class="btn btn-outline-primary btn-sm rounded-3" href="mailto:{{ $quote->supplier->email }}?subject={{ $mailSubject }}&body={{ $mailBody }}" title="Email Supplier">
                                                    <i class="mdi mdi-email"></i> Email
                                                </a>
                                            @endif
                                        @endunless
                                    </div>
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="alert alert-light border rounded-3 mb-0">No supplier quotes have been submitted for this job yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-light border rounded-3">You have not posted any jobs yet, so there are no quotes to review.</div>
    @endforelse
</div>
@endsection

@push('scripts')
<div class="offcanvas offcanvas-end" tabindex="-1" id="quoteSupplierOffcanvas" aria-labelledby="quoteSupplierOffcanvasLabel" style="width: 600px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="quoteSupplierOffcanvasLabel">Supplier Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">

    </div>
</div>
<script>
    $(document).on('click', '.quote-supplier-link', function () {
        const supplierId = $(this).data('id');
        if (!supplierId) return;
        const offcanvasElement = document.getElementById('quoteSupplierOffcanvas');
        const offCanvas = new bootstrap.Offcanvas(offcanvasElement);
        offCanvas.show();
        $('#quoteSupplierOffcanvas .offcanvas-body').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 mb-0">Loading supplier details...</p>
            </div>
        `);
        $.ajax({
            url: "{{ route('customer-panel.suppliers.details', ':id') }}".replace(':id', supplierId),
            type: 'GET',
            success: function (response) {
                $('#quoteSupplierOffcanvas .offcanvas-body').html(response);
            },
            error: function () {
                $('#quoteSupplierOffcanvas .offcanvas-body').html(`
                    <div class="alert alert-danger">Failed to load supplier details.</div>
                `);
            }
        });
    });
</script>
@endpush
