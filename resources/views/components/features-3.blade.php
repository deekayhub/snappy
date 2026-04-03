@php($categorySettings = \App\Support\PageSettings::all()['home_category_section'])

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
                @foreach ($categorySettings['items'] as $item)
                    <div class="col-md-4 mb-3">
                        <div class="card position-relative border-0 shadow-sm">
                            <img src="{{ \App\Support\PageSettings::imageUrl($item['image'] ?? null) }}" class="card-img-top" alt="{{ $item['title'] }}">
                            <h4 class="fw-bolder fst-italic text-white position-absolute bottom-0 start-0 end-0 text-center mb-2 text-shadow">{{ $item['title'] }}</h4>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
