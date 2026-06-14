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
                        <div class="col-md-4 mb-3">
                            <div class="card position-relative border-0 shadow-sm">
                                <img  src="{{ $item->image ? asset($item->image) : asset('assets/images/category-placeholder-img.png') }}" class="card-img-top" height="315" alt="{{ $item->organisationCategory?->name }}">
                                <h4 class="fw-bolder fst-italic text-white position-absolute bottom-0 start-0 end-0 text-center mb-2 text-shadow">{{ $item->organisationCategory?->name }}</h4>
                            </div>
                        </div>
                    @endforeach
            </div>
        </div>
    </section>    
@endif
