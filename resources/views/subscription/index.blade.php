@extends('layouts.app')
@section('title', 'Subscription')
@section('section')
    <div class="container py-5">
        <div class="mb-4">
            <h1 class="fw-bold">Subscription</h1>
            <p class="text-muted">Manage your subscription plan.</p>
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

        @if($subscription)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <h5 class="fw-bold mb-1">Current Plan</h5>
                            <p class="mb-0 text-muted">
                                @php
                                    $currentPlan = $plans->firstWhere('stripe_price_id', $subscription->stripe_price);
                                @endphp
                                @if($currentPlan)
                                    <span class="badge bg-primary fs-6 me-2">{{ $currentPlan->name }}</span>
                                    {{ $currentPlan->price_formatted }} / {{ $currentPlan->duration_label }}
                                @else
                                    <span class="badge bg-secondary fs-6">{{ $subscription->stripe_price }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="text-end">
                            @if($subscription->onGracePeriod())
                                <p class="mb-1 text-warning fw-semibold">Cancelled - Access until {{ $subscription->ends_at->format('d M Y') }}</p>
                                <form method="POST" action="{{ route('subscription.resume') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Resume Subscription</button>
                                </form>
                            @elseif($subscription->canceled())
                                <p class="mb-1 text-muted">Subscription ended {{ $subscription->ends_at->format('d M Y') }}</p>
                            @else
                                <p class="mb-1 text-success fw-semibold">Active</p>
                                <form method="POST" action="{{ route('subscription.cancel') }}" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel your subscription?');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger">Cancel Subscription</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <a href="{{ route('subscription.invoices') }}" class="btn btn-outline-primary">
                    <i class="bi bi-receipt me-2"></i>View Invoices
                </a>
            </div>
        @else
            <div class="alert alert-info">
                You are on the <strong>Basic (Free)</strong> plan. Upgrade to unlock more features.
            </div>
        @endif

        <h4 class="fw-bold mb-4">Available Plans</h4>
        <div class="row g-4 justify-content-center">
            @foreach($plans as $plan)
                <div class="col-md-4">
                    <div class="card h-100 pricing-card shadow-sm @if($plan->slug === 'gold') border-primary border-2 @endif">
                        @if($plan->slug === 'gold')
                            <div class="card-header bg-primary text-white text-center py-2 small fw-bold">POPULAR</div>
                        @endif
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="card-title text-muted text-uppercase fw-bold">{{ $plan->name }}</h5>
                            <p class="text-muted small">{{ $plan->description }}</p>
                            <h2 class="fw-bold mb-0">
                                {{ $plan->price_formatted }}
                                @if(!$plan->is_free)
                                    <small class="text-muted fw-light fs-6">/ {{ $plan->duration_label }}</small>
                                @endif
                            </h2>
                            @if(!$plan->is_free && $plan->duration_months > 0 && $plan->duration_months != 12)
                                <p class="text-danger small">({{ $plan->yearly_price_formatted }} Per Year)</p>
                            @endif

                            <div class="mt- auto">
                                @php
                                    $isCurrentPlan = $subscription && $subscription->stripe_price === $plan->stripe_price_id;
                                    $isFreeCurrent = $plan->is_free && !$subscription;
                                @endphp

                                @if($isCurrentPlan || $isFreeCurrent)
                                    <button class="btn btn-outline-secondary w-100 mb-3" disabled>
                                        @if($plan->is_free) Current Plan (Free) @else Current Plan @endif
                                    </button>
                                @elseif($plan->is_free)
                                    <form method="POST" action="{{ route('subscription.checkout', $plan) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary w-100 mb-3">Downgrade to Free</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('subscription.checkout', $plan) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-{{ $plan->slug === 'gold' ? 'primary' : 'outline-primary' }} w-100 mb-3">
                                            @if($subscription) Switch Plan @else Subscribe @endif
                                        </button>
                                    </form>
                                @endif

                                <ul class="list-unstyled feature-list small">
                                    @foreach($plan->features as $feature)
                                        <li class="mb-1"><i class="bi bi-check2 text-primary me-1"></i>{{ $feature }}</li>
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