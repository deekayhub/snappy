<div class="text-center mb-4">
    @if($suppliers->supplierProfile?->company_logo)
        <img src="{{ asset($suppliers->supplierProfile->company_logo) }}"
             style="width:80px;height:80px;object-fit:cover;border-radius:12px;"
             alt="Logo">
    @else
        <div style="width:80px;height:80px;border-radius:12px;background:#e9ecef;display:inline-flex;align-items:center;justify-content:center;font-size:28px;color:#adb5bd;">
            <i class="fa fa-building"></i>
        </div>
    @endif

    <h5 class="fw-bold mt-2 mb-1">{{ $suppliers->supplierProfile?->company_name ?? $suppliers->name }}</h5>

    @if($suppliers->hasFeature('recommended_badge') && $suppliers->isRecommended())
        <span class="badge bg-warning text-dark mb-2">
            <i class="fa fa-star me-1"></i>Recommended Supplier
        </span>
    @endif

    @php $rating = round($suppliers->avg_rating ?? 0); @endphp
    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
        <div class="text-warning" style="font-size:0.9rem;">
            @for($i = 1; $i <= 5; $i++)
                {{ $i <= $rating ? '★' : '☆' }}
            @endfor
        </div>
        <span class="fw-semibold small">{{ number_format($suppliers->avg_rating ?? 0, 1) }}</span>
        <span class="text-muted small">({{ $suppliers->total_reviews ?? 0 }} reviews)</span>
    </div>

    @php $cats = $suppliers->organisationCategories ?? collect(); @endphp
    @if($cats->isNotEmpty())
        <div>
            @foreach($cats as $cat)
                <span class="badge bg-secondary rounded-pill me-1" style="font-size:10px;">{{ ucfirst($cat->name) }}</span>
            @endforeach
        </div>
    @endif
</div>

<hr class="my-3">

<div>
    <div class="d-flex align-items-center mb-2">
        <div style="width:32px;height:32px;border-radius:8px;background:#e8f4fd;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="mdi mdi-map-marker text-primary" style="font-size:16px;"></i>
        </div>
        <div class="ms-3">
            <div class="text-muted small">Address</div>
            <div class="fw-semibold small">{{ $suppliers->supplierProfile?->address ?? 'Not provided' }}</div>
        </div>
    </div>

    <div class="d-flex align-items-center mb-2">
        <div style="width:32px;height:32px;border-radius:8px;background:#e8f4fd;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="mdi mdi-phone text-primary" style="font-size:16px;"></i>
        </div>
        <div class="ms-3">
            <div class="text-muted small">Phone</div>
            <div class="fw-semibold small">{{ $suppliers->phone ?? 'Not provided' }}</div>
        </div>
    </div>

    <div class="d-flex align-items-center mb-2">
        <div style="width:32px;height:32px;border-radius:8px;background:#e8f4fd;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="mdi mdi-email text-primary" style="font-size:16px;"></i>
        </div>
        <div class="ms-3">
            <div class="text-muted small">Email</div>
            <div class="fw-semibold small">{{ $suppliers->email ?? 'Not provided' }}</div>
        </div>
    </div>
</div>

@if($suppliers->supplierProfile?->company_description)
    <hr class="my-3">
    <div>
        <h6 class="fw-bold mb-2"><i class="mdi mdi-text-box-outline text-primary me-2"></i>About</h6>
        <p class="text-muted small mb-0" style="line-height:1.6;">
            {{ $suppliers->supplierProfile->company_description }}
        </p>
    </div>
@endif

<hr class="my-3">

<div>
    @if($suppliers->supplierProfile?->website)
        <div class="d-flex align-items-center mb-3">
            <div style="width:32px;height:32px;border-radius:8px;background:#e8f4fd;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="mdi mdi-web text-primary" style="font-size:16px;"></i>
            </div>
            <div class="ms-3">
                <div class="text-muted small">Website</div>
                <a href="{{ $suppliers->supplierProfile->website }}" target="_blank" class="fw-semibold small text-decoration-none">
                    {{ $suppliers->supplierProfile->website }}
                </a>
            </div>
        </div>
    @endif

    @if($suppliers->supplierProfile?->review_link)
        <div class="d-flex align-items-center mb-3">
            <div style="width:32px;height:32px;border-radius:8px;background:#e8f4fd;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="mdi mdi-star-circle text-primary" style="font-size:16px;"></i>
            </div>
            <div class="ms-3">
                <div class="text-muted small">Review Link</div>
                <a href="{{ $suppliers->supplierProfile->review_link }}" target="_blank" class="fw-semibold small text-decoration-none">
                    <i class="mdi mdi-open-in-new me-1"></i>View Reviews
                </a>
            </div>
        </div>
    @endif

    @if(!empty($suppliers->supplierProfile?->social_links) && count($suppliers->supplierProfile->social_links) > 0)
        <div class="d-flex align-items-start mb-2">
            <div style="width:32px;height:32px;border-radius:8px;background:#e8f4fd;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">
                <i class="mdi mdi-share-variant text-primary" style="font-size:16px;"></i>
            </div>
            <div class="ms-3 flex-grow-1">
                <div class="text-muted small mb-1">Social Profiles</div>
                @foreach($suppliers->supplierProfile->social_links as $social)
                    <div class="d-flex align-items-center mb-1">
                        <i class="mdi mdi-{{ strtolower($social['platform'] ?? 'web') }} text-muted me-2" style="font-size:14px;"></i>
                        <span class="text-capitalize small fw-semibold me-2">{{ $social['platform'] ?? 'Social' }}:</span>
                        @if(!empty($social['url']))
                            <a href="{{ $social['url'] }}" target="_blank" class="small text-decoration-none text-truncate" style="max-width:300px;">
                                {{ $social['url'] }}
                            </a>
                        @else
                            <span class="text-muted small">Not available</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
