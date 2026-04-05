@extends('layouts.app')
@section('title', 'My Quotes')

@section('section')
<div class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <span class="badge rounded-pill text-bg-light px-3 py-2">Customer quote inbox</span>
            <h1 class="mt-3 mb-2">Quotes from suppliers</h1>
            <p class="text-secondary mb-0">Review every quote submitted against your posted jobs and update the status when you make a decision.</p>
        </div>
        <a href="{{ route('customer.jobs.create') }}" class="btn btn-primary rounded-4 px-4 py-3">Post another job</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    @forelse ($jobs as $job)
        <div class="card border-0 shadow-sm rounded-5 mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <div class="small text-muted mb-2">Job No. {{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <h3 class="mb-1">{{ $job->title }}</h3>
                        <p class="text-secondary mb-0">{{ $job->category ?: 'General' }} • {{ $job->location ?: 'Location not set' }}</p>
                    </div>
                    <div class="text-lg-end">
                        <div class="small text-muted">Quotes received</div>
                        <div class="display-6 fw-bold">{{ $job->quotes->count() }}</div>
                    </div>
                </div>

                @forelse ($job->quotes as $quote)
                    <div class="border rounded-4 p-4 mb-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <span class="badge bg-dark">Supplier quote</span>
                                    <span class="badge bg-light text-dark text-uppercase">{{ $quote->status }}</span>
                                </div>
                                <h5 class="mb-1">{{ $quote->supplier?->supplierProfile?->company_name ?: $quote->supplier?->name }}</h5>
                                <div class="small text-muted mb-2">{{ $quote->supplier?->email }}</div>
                                <p class="mb-0 text-secondary">{{ $quote->notes ?: 'No extra notes were provided with this quote.' }}</p>
                            </div>
                            <div class="col-lg-4">
                                <div class="row g-2 text-center">
                                    <div class="col-6">
                                        <div class="border rounded-4 p-3 h-100">
                                            <div class="small text-muted">Job price</div>
                                            <div class="fw-semibold">GBP {{ number_format((float) $quote->price_for_job, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded-4 p-3 h-100">
                                            <div class="small text-muted">Delivery</div>
                                            <div class="fw-semibold">GBP {{ number_format((float) $quote->delivery_cost, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded-4 p-3 h-100">
                                            <div class="small text-muted">Discount</div>
                                            <div class="fw-semibold">GBP {{ number_format((float) $quote->discount_offered, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded-4 p-3 h-100 bg-light">
                                            <div class="small text-muted">Total</div>
                                            <div class="fw-bold">GBP {{ number_format((float) $quote->total_price, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="accepted">
                                <button class="btn btn-success rounded-4">Accept quote</button>
                            </form>
                            <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button class="btn btn-outline-danger rounded-4">Reject quote</button>
                            </form>
                            <form method="POST" action="{{ route('customer.quotes.status', $quote) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="submitted">
                                <button class="btn btn-outline-secondary rounded-4">Mark pending</button>
                            </form>
                        </div>
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
