<div class="card mb-3">
  <div class="row g-0">
    <div class="col-md-4">
    @if($suppliers->supplierProfile?->company_logo)
    <img src="{{ asset($suppliers->supplierProfile?->company_logo) }}" class="img-fluid rounded-start" alt="...">
    @else
    <img src="https://placehold.net/default.png" class="img-fluid rounded-start" alt="...">

    @endif
    </div>
    <div class="col-md-8">
      <div class="card-body">
        <h2 class="fw-bold mb-2">
            {{ $suppliers->supplierProfile?->company_name ?? $suppliers->name }}
        </h2>
        @if($suppliers->hasFeature('recommended_badge') && $suppliers->isRecommended())
            <span class="badge bg-warning text-dark mb-2"><i class="mdi mdi-star me-1"></i>Recommended Supplier</span>
        @endif
        <p class="card-text"><span class="mdi mdi-map-marker"></span> {{ $suppliers->supplierProfile?->address ?? '' }}</p>
        <p class="card-text"><span class="mdi mdi-phone"></span> {{ $suppliers->phone ?? '' }}</p>
        <p class="card-text"><span class="mdi mdi-email"></span> {{ $suppliers->email ?? '' }}</p>
        @if($suppliers->hasFeature('enhanced_profile'))
        <div class="d-flex align-items-center gap-2 mb-2">
            @php
                $rating = round($suppliers->avg_rating ?? 0);
                $totalReviews = $suppliers->total_reviews ?? 0;
            @endphp
            <div class="text-warning fs-5">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $rating)
                        ★
                    @else
                        ☆
                    @endif
                @endfor
            </div>
            <div class="fw-semibold">
                {{ number_format($suppliers->avg_rating ?? 0, 1) }}
            </div>
            <div class="text-muted">
                ({{ $totalReviews }} Reviews)
            </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
<div class="card border-0 shadow-sm rounded-4  ">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Description</h5>
         <p class="text-muted mb-3">
            {{ $suppliers->supplierProfile?->company_description ?? 'No company description available.' }}
        </p> 
    </div>
</div>

@if($suppliers->hasFeature('enhanced_profile'))
<div class="row">
    <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Website</h5>

            @if($suppliers->supplierProfile?->website)
                <span class="mdi mdi-web"></span> <a
                    href="{{ $suppliers->supplierProfile?->website }}"
                    target="_blank"
                    class="text-decoration-none"
                >
                    {{ $suppliers->supplierProfile?->website }}
                </a>
            @else
                <p class="text-muted mb-0">Not Available</p>
            @endif
        </div>
    </div> 
 
    <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Review Link</h5>

            @if($suppliers->supplierProfile?->review_link)
                <span class="mdi mdi-link"></span> 
                <a
                    href="{{ $suppliers->supplierProfile?->review_link }}"
                    target="_blank"
                    class="text-decoration-none"
                >
                    View Reviews
                </a>
            @else
                <p class="text-muted mb-0">Not Available</p>
            @endif
        </div>
    </div> 

    <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Social Profiles</h5>

            @if(!empty($suppliers->supplierProfile?->social_links) && count($suppliers->supplierProfile->social_links) > 0)

                @foreach($suppliers->supplierProfile->social_links as $social)
                    <div class="mb-2">

                        <span class="fw-semibold text-capitalize">
                            {{ $social['platform'] ?? 'Social' }} :
                        </span>

                        @if(!empty($social['url']))
                            <a
                                href="{{ $social['url'] }}"
                                target="_blank"
                                class="text-decoration-none"
                            >
                                Visit Profile
                            </a>
                        @else
                            <span class="text-muted">Not Available</span>
                        @endif

                    </div>
                @endforeach

            @else
                <p class="text-muted mb-0">No Social Profiles Available</p>
            @endif
        </div>
    </div>

</div>
@endif