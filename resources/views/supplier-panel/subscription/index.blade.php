@extends('supplier-panel.layouts.app')
@section('title', 'Subscription')

@section('content')
    <div class="content-wrapper p-3">
        <div class="mb-4">
            <h1 class="fw-bold mb-1">Subscription</h1>
            <p class="text-muted mb-0">Manage your supplier plan without leaving the supplier panel.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {!! session('warning') !!}
                @if(session('portal_url'))
                    <a href="{{ session('portal_url') }}" class="alert-link ms-2">Update Payment Method</a>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($subscription)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <span class="badge bg-light text-dark mb-2">Current Plan</span>
                            <h4 class="mb-1">
                                @if($currentPlan)
                                    {{ $currentPlan->name }}
                                @elseif($stripePriceInfo)
                                    Active Subscription
                                @else
                                    Active Subscription
                                @endif
                            </h4>
                            <p class="text-muted mb-0">
                                @if($currentPlan)
                                    {{ $currentPlan->price_formatted }} / {{ $currentPlan->duration_label }}
                                @elseif($stripePriceInfo)
                                    &pound;{{ number_format($stripePriceInfo['amount'] / 100, 2) }}
                                    /
                                    @if($stripePriceInfo['interval_count'] > 1)
                                        {{ $stripePriceInfo['interval_count'] }} {{ $stripePriceInfo['interval'] }}s
                                    @else
                                        {{ $stripePriceInfo['interval'] }}
                                    @endif
                                @else
                                    Active Subscription
                                @endif
                            </p>
                        </div>
                        <div class="text-lg-end">
                            @if($subscription->onGracePeriod())
                                <p class="mb-2 text-warning fw-semibold">Cancelled - access until {{ $subscription->ends_at?->format('d M Y') }}</p>
                                <form method="POST" action="{{ route('supplier-panel.subscription.resume') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success rounded-4">Resume Subscription</button>
                                </form>
                            @elseif($subscription->canceled())
                                <p class="mb-0 text-muted">Subscription ended {{ $subscription->ends_at?->format('d M Y') }}</p>
                            @else
                                <p class="mb-2 text-success fw-semibold">Active</p>
                                <form method="POST" action="{{ route('supplier-panel.subscription.cancel') }}" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel your subscription?');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger rounded-4">Cancel Subscription</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4 d-flex gap-2 flex-wrap">
                <a href="{{ route('supplier-panel.subscription.invoices') }}" class="btn btn-outline-primary rounded-4">
                    <i class="mdi mdi-receipt me-1"></i> View Invoices
                </a>
                @if($portalUrl)
                    <a href="{{ $portalUrl }}" class="btn btn-outline-secondary rounded-4">
                        <i class="mdi mdi-credit-card me-1"></i> Billing Portal
                    </a>
                @endif
            </div>
        @else
            <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4">
                You are on the <strong>Basic (Free)</strong> plan. Upgrade to unlock more supplier features.
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
            <div>
                <h4 class="mb-1">Available Plans</h4>
                <p class="text-muted mb-0">Choose the plan that fits your supplier workflow.</p>
            </div>
        </div>

        <div class="row g-4">
            @foreach($plans as $plan)
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 {{ $plan->slug === 'gold' ? 'border border-primary' : '' }}">
                        <div class="card-body p-4 d-flex flex-column">
                            @if($plan->slug === 'gold')
                                <span class="badge bg-primary align-self-start mb-3">POPULAR</span>
                            @endif

                            <h5 class="text-uppercase text-muted fw-bold">{{ $plan->name }}</h5>
                            <p class="text-muted small">{{ $plan->description }}</p>
                            <h2 class="fw-bold mb-0">{{ $plan->price_formatted }}</h2>
                            @if(! $plan->is_free)
                                <p class="text-muted small mb-3">/ {{ $plan->duration_label }}</p>
                            @endif

                            @if(! $plan->is_free && $plan->duration_months > 0 && $plan->duration_months != 12)
                                <p class="text-danger small">({{ $plan->yearly_price_formatted }} Per Year)</p>
                            @endif

                            @php
                                $isCurrentPlan = $currentPlan && $currentPlan->id === $plan->id;
                                $isFreeCurrent = $plan->is_free && ! $subscription;
                            @endphp

                            <div class="mt-auto">
                                @if($isCurrentPlan || $isFreeCurrent)
                                    <button class="btn btn-outline-secondary w-100 mb-3 rounded-4" disabled>
                                        {{ $plan->is_free ? 'Current Plan (Free)' : 'Current Plan' }}
                                    </button>
                                @elseif($plan->is_free)
                                    <form method="POST" action="{{ route('supplier-panel.subscription.checkout', $plan) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary w-100 mb-3 rounded-4">Downgrade to Free</button>
                                    </form>
                                @else
                                    <a href="{{ route('supplier-panel.subscription.preview', $plan) }}" class="btn btn-{{ $plan->slug === 'gold' ? 'primary' : 'outline-primary' }} w-100 mb-3 rounded-4 d-block">
                                        {{ $subscription ? 'Switch Plan' : 'Subscribe' }}
                                    </a>
                                @endif

                                <ul class="list-unstyled small mb-0">
                                    @foreach($plan->display_features as $feature)
                                        <li class="mb-2"><i class="mdi mdi-check text-primary me-1"></i>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
