@php
    $categorySettings = [
        'title' => 'What do you need a quote for?',
        'highlight_text' => 'quote',
        'description' => 'Quickly connect with suppliers who specialise in your exact requirement.',
        'items' => [
            [
                'title' => 'Sportswear',
                'image' => 'assets/images/Sportswear-1.png',
            ],
            [
                'title' => 'Sports Equipment',
                'image' => 'assets/images/sports-and-equipment.png',
            ],
            [
                'title' => 'Trophies & Awards',
                'image' => 'assets/images/trophies-awards.png',
            ],
            [
                'title' => 'Signage',
                'image' => 'assets/images/signage.png',
            ],
            [
                'title' => 'Gifts & Promotional Items',
                'image' => 'assets/images/gifts-promotions.png',
            ],
            [
                'title' => 'School Uniforms & Supplies',
                'image' => 'assets/images/uniforms-supplies.png',
            ],
        ],
    ];
@endphp

{{-- <section class="features-section">
    <div class="container" id="featured-3">
        <div class="section-header mx-auto text-center mb-5">
            <h2 class="h1 fw-bold text-body-emphasis">
                {{ $categorySettings['title'] }}
                @if (! empty($categorySettings['highlight_text']))
                    <div class="text-primary d-inline">{{ $categorySettings['highlight_text'] }}</div>
                @endif
            </h2>
            <p class="fs-5 secondary-color">{{ $categorySettings['description'] }}</p>
        </div>
        <div class="row">
            @foreach ($categorySettings['items'] as $item)
                <div class="col-md-4 mb-3">
                    <div class="card position-relative border-0 shadow-sm">
                        <img src="{{ asset($item['image']) }}" class="card-img-top" alt="{{ $item['title'] }}">
                        <h4 class="fw-bolder fst-italic text-white position-absolute bottom-0 start-0 end-0 text-center mb-2 text-shadow">{{ $item['title'] }}</h4>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section> --}}
@if ($features->isNotEmpty())
    <section class="features-section">
        <div class="container" id="featured-3">
            <div class="section-header mx-auto text-center mb-5">
                <h2 class="h1 fw-bold text-body-emphasis">
                    {{ $categorySettings['title'] }}
                    @if (! empty($categorySettings['highlight_text']))
                        <div class="text-primary d-inline">{{ $categorySettings['highlight_text'] }}</div>
                    @endif
                </h2>
                <p class="fs-5 secondary-color">{{ $categorySettings['description'] }}</p>
            </div>
            <div class="row">
                    @foreach ($features as $item)
                        <div class="col-md-4 mb-3{{ $loop->index >= 6 ? ' d-none category-extra' : '' }}">
                            <div class="card position-relative border-0 shadow-sm">
                                <img  src="{{ $item->image ? asset($item->image) : asset('assets/images/category-placeholder-img.png') }}" class="card-img-top" height="315" alt="{{ $item->organisationCategory?->name }}">
                                @if($item->coming_soon)
                                    <div class="position-absolute" style="top: 12px; right: 12px; background: #f59e0b; color: #fff; padding: 4px 14px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; z-index: 2; letter-spacing: 0.5px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">Coming Soon</div>
                                @endif
                                <h4 class="fw-bolder fst-italic text-white position-absolute bottom-0 start-0 end-0 text-center mb-0 pb-3 pt-4 text-capitalize" style="background: linear-gradient(transparent, rgba(0,0,0,9));">{{ $item->organisationCategory?->name }}</h4>
                            </div>
                        </div>
                    @endforeach
            </div>

            @if($features->count() > 6)
                <div class="text-center mt-4">
                    <button type="button" id="showMoreCategories" class="btn btn-primary px-5 py-2 rounded-pill fw-semibold">
                        Show More
                    </button>
                </div>
            @endif
        </div>
    </section>
    <script>
        $('#showMoreCategories').click(function () {
            $('.category-extra').toggleClass('d-none');
            $(this).text($('.category-extra.d-none').length ? 'Show More' : 'Show Less');
        });
    </script>
@endif
