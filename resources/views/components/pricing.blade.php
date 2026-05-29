<section class="features-section">
    <div class="container" id="featured-3">
        <div class="section-header mx-auto text-center mb-5">
            <h2 class="h1 fw-bold text-body-emphasis">Simple, <div class="text-primary d-inline">Transparent Pricing</div> for Suppliers</h2>
            <p class="fs-5 text-body-secondary">Start with the free plan and upgrade anytime as your business grows.</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($plans as $plan)
                <div class="col-md-4 col-lg-4">
                    <div class="card h-100 pricing-card shadow-sm @if($plan->slug === 'gold') border-primary border-2 @endif">
                        @if($plan->slug === 'gold')
                            <div class="card-header bg-primary text-white text-center py-2 small fw-bold">POPULAR</div>
                        @endif
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="card-title text-muted text-uppercase fw-bold">{{ $plan->name }}</h5>
                            <p class="text-muted small flex-grow-0">{{ $plan->description }}</p>
                            <h1 class="display-5 mb-2 fw-bold">
                                {{ $plan->price_formatted }}
                                @if(!$plan->is_free)
                                    <small class="text-muted fw-light fs-6">/ {{ $plan->duration_label }}</small>
                                @endif
                            </h1>
                            @if(!$plan->is_free && $plan->duration_months > 0 && $plan->duration_months != 12)
                                <p class="text-danger small mb-3">({{ $plan->yearly_price_formatted }} Per Year)</p>
                            @elseif(!$plan->is_free)
                                <p class="text-muted small mb-3">&nbsp;</p>
                            @else
                                <p class="text-muted small mb-3">Forever free</p>
                            @endif

                            @auth
                                @php $userSub = auth()->user()->subscription('default'); @endphp
                                @if($plan->is_free && !auth()->user()->subscribed('default'))
                                    <button class="btn btn-outline-secondary btn-lg w-100 mb-3" disabled>Current Plan</button>
                                @elseif($plan->is_free)
                                    <form method="POST" action="{{ route('subscription.checkout', $plan) }}" class="mt-auto">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-lg w-100 mb-3">Downgrade to Free</button>
                                    </form>
                                @elseif($userSub && optional(auth()->user()->currentPlan())->slug === 'bronze' && $userSub->stripe_price !== $plan->stripe_price_id)
                                    <form method="POST" action="{{ route('subscription.checkout', $plan) }}" class="mt-auto">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                            Cancel Bronze & Start New Payment
                                        </button>
                                    </form>
                                @elseif($userSub && $userSub->stripe_price === $plan->stripe_price_id)
                                    <button class="btn btn-outline-secondary btn-lg w-100 mb-3" disabled>Current Plan</button>
                                @else
                                    <form method="POST" action="{{ route('subscription.checkout', $plan) }}" class="mt-auto">
                                        @csrf
                                        <button type="submit" class="btn btn-{{ $plan->slug === 'gold' ? 'primary' : 'outline-primary' }} btn-lg w-100 mb-3">
                                            @if($userSub) Switch to {{ $plan->name }} @else Subscribe @endif
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('register.supplier') }}" class="btn btn-{{ $plan->slug === 'gold' ? 'primary' : 'outline-primary' }} btn-lg w-100 mb-3 mt-auto">
                                    Get Started
                                </a>
                            @endauth

                            <ul class="list-unstyled feature-list">
                                @foreach($plan->features as $feature)
                                    <li class="mb-2">
                                        <i class="bi bi-check2 text-primary me-2"></i>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
