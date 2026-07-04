@extends('supplier-panel.layouts.app')
@section('title', 'Confirm Plan Change')

@section('content')
    <div class="content-wrapper p-3">
        <div class="mb-4">
            <h1 class="fw-bold mb-1">Confirm Plan Change</h1>
            <p class="text-muted mb-0">Review the details before changing your subscription.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <span class="badge bg-light text-dark mb-3">Current Plan</span>
                        @if($currentPlan)
                            <h4 class="fw-bold mb-2">{{ $currentPlan->name }}</h4>
                            <p class="mb-1">{{ $currentPlan->price_formatted }} / {{ $currentPlan->duration_label }}</p>
                            @if($subscription && $subscription->valid())
                                <p class="text-muted small mb-0">
                                    @if($subscription->onGracePeriod())
                                        Cancelled - access until {{ $subscription->ends_at->format('d M Y') }}
                                    @else
                                        Active
                                    @endif
                                </p>
                            @endif
                        @else
                            <h4 class="fw-bold mb-2">Free</h4>
                            <p class="text-muted mb-0">£0.00</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-1 d-flex align-items-center justify-content-center">
                <span class="fs-1 text-muted">&rarr;</span>
            </div>

            <div class="col-md-5">
                <div class="card border-0 shadow-sm rounded-4 h-100 border border-primary">
                    <div class="card-body p-4">
                        <span class="badge bg-primary mb-3">New Plan</span>
                        <h4 class="fw-bold mb-2">{{ $plan->name }}</h4>
                        <p class="mb-0">{{ $plan->price_formatted }} / {{ $plan->duration_label }}</p>
                        @if(!$plan->is_free && $plan->duration_months > 0 && $plan->duration_months != 12)
                            <p class="text-danger small mt-1">({{ $plan->yearly_price_formatted }} Per Year)</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($prorationPreview)
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Proration Details</h5>
                    <p class="text-muted small mb-3">
                        Since you're changing plans mid-cycle, Stripe will prorate the remaining time on your current plan
                        and charge or credit the difference.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                @foreach($prorationPreview['lines'] as $line)
                                    <tr>
                                        <td class="ps-0">{{ $line['description'] }}</td>
                                        <td class="text-end pe-0">
                                            @php
                                                $amount = $line['amount'];
                                                $formatted = '£' . number_format(abs($amount) / 100, 2);
                                            @endphp
                                            @if($amount < 0)
                                                <span class="text-success">-{{ $formatted }}</span>
                                            @elseif($amount > 0)
                                                <span class="text-danger">{{ $formatted }}</span>
                                            @else
                                                <span class="text-muted">£0.00</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-top">
                                    <td class="ps-0 fw-bold">Total</td>
                                    <td class="text-end pe-0 fw-bold">
                                        @php
                                            $total = $prorationPreview['total'];
                                            $formatted = '£' . number_format(abs($total) / 100, 2);
                                        @endphp
                                        @if($total < 0)
                                            <span class="text-success">Credit: -{{ $formatted }}</span>
                                        @elseif($total > 0)
                                            <span class="text-danger">{{ $formatted }}</span>
                                        @else
                                            <span class="text-muted">£0.00</span>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @if($prorationPreview['total'] > 0)
                        <p class="text-muted small mt-3 mb-0">
                            This amount will be charged to your saved payment method.
                        </p>
                    @elseif($prorationPreview['total'] < 0)
                        <p class="text-muted small mt-3 mb-0">
                            The credit will be applied to future invoices.
                        </p>
                    @else
                        <p class="text-muted small mt-3 mb-0">
                            No immediate charge. Your billing cycle will adjust.
                        </p>
                    @endif
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">{{ $plan->name }} Features</h5>
                <ul class="list-unstyled mb-0">
                    @foreach($plan->display_features as $feature)
                        <li class="mb-2"><i class="mdi mdi-check text-primary me-1"></i>{{ $feature }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-4 text-center">
            @if($prorationPreview)
                <p class="fw-bold fs-5 mb-3">
                    @if($prorationPreview['total'] > 0)
                        <span class="text-danger">Total to charge now: &pound;{{ number_format($prorationPreview['total'] / 100, 2) }}</span>
                    @elseif($prorationPreview['total'] < 0)
                        <span class="text-success">Credit of &pound;{{ number_format(abs($prorationPreview['total']) / 100, 2) }} — no charge now</span>
                    @else
                        <span class="text-muted">No immediate charge</span>
                    @endif
                </p>
            @endif
            <form method="POST" action="{{ route('supplier-panel.subscription.checkout', $plan) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary rounded-4 btn-lg px-5 me-3">
                    @if($plan->is_free)
                        Confirm Downgrade to Free
                    @elseif($prorationPreview && $prorationPreview['total'] > 0)
                        Pay &pound;{{ number_format($prorationPreview['total'] / 100, 2) }} &amp; Switch
                    @elseif($prorationPreview && $prorationPreview['total'] < 0)
                        Switch &amp; Get Credit
                    @else
                        Confirm Switch
                    @endif
                </button>
            </form>
            <a href="{{ route('supplier-panel.subscription.index') }}" class="btn btn-outline-secondary rounded-4 btn-lg px-5">Cancel</a>
        </div>
    </div>
@endsection
