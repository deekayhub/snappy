@php
    $faqSettings = [
        'title' => 'Frequently Asked Questions',
        'highlight_text' => '(FAQs)',
        'description' => 'Find answers to the most common questions about posting jobs, receiving quotes and joining as a supplier.',
        'cta_title' => 'Still have questions?',
        'cta_description' => "Can't find the answer you're looking for? Our friendly team is here to help reach out anytime.",
        'cta_button_text' => 'Get in touch',
        'cta_button_link' => '/contact-us',
        'items' => [
            [
                'question' => 'How does the platform work?',
                'answer' => 'When a customer posts a job, verified suppliers receive the request based on their categories. Suppliers then send quotes, and the customer chooses the best option.',
            ],
            [
                'question' => 'Is it free for customers to use?',
                'answer' => 'Yes. Customers can post their requirements and compare quotes without paying to access the platform.',
            ],
            [
                'question' => 'How do suppliers receive leads?',
                'answer' => 'Suppliers receive relevant job opportunities based on the organisation categories they selected in their profile.',
            ],
            [
                'question' => 'Can I compare multiple quotes?',
                'answer' => 'Yes. You can review multiple supplier responses and choose the option that best matches your needs and budget.',
            ],
        ],
    ];
@endphp

<section class="faq-section bg-white">
    <div class="container">
        <div class="row">
            <div class="section-header mx-auto text-center mb-5">
                <h2 class="h1 fw-bold text-dark">
                    {{ $faqs['data']['heading'] }}
                    <div class="text-primary d-inline">(FAQs)</div>
                </h2>
                <p class="secondary-color">{{ $faqs['data']['description'] }}</p>
            </div>
            <div class="col-md-9 mb-3">
                <div class="accordion" id="faqAccordion">
                    @foreach ($faqs['data']['faqs'] as $index => $item)
                    
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-bold secondary-color {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="{{ $index }}">
                                    {{ $item['question'] }}
                                </button>
                            </h2>
                            <div id="{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body secondary-color">
                                    {{ $item['answer'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-md-3">
                <div class="card rounded-4 px-4 py-5 shadow-sm text-center">
                    <div class="avatar-box d-inline-flex align-items-center p-2 position-relative justify-content-center mb-4">
                        <img src="https://i.pravatar.cc/40?img=1" class="avatar position-absolute" style="left: 35%;">
                        <img src="https://i.pravatar.cc/40?img=2" class="avatar position-absolute ms-n2 z-1" style="top: -29px; width:56px; height:56px;">
                        <img src="https://i.pravatar.cc/40?img=3" class="avatar position-absolute ms-n2" style="right: 35%;">
                    </div>

                    <div class="card-content">
                        <h5 class="card-title secondary-color fw-bold">{{ $faqSettings['cta_title'] }}</h5>
                        <p class="card-description secondary-color">{{ $faqSettings['cta_description'] }}</p>
                        <a href="{{ $faqSettings['cta_button_link'] ?: route('contact-us') }}" class="btn btn-primary w-100 rounded-4 px-4 py-3">{{ $faqSettings['cta_button_text'] ?: 'Get in touch' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- <section class="faq-section bg-white">
    <div class="container">
        <div class="row">
            <div class="section-header mx-auto text-center mb-5">
                <h2 class="h1 fw-bold text-dark">
                    {{ $faqSettings['title'] }}
                    @if (! empty($faqSettings['highlight_text']))
                        <div class="text-primary d-inline">{{ $faqSettings['highlight_text'] }}</div>
                    @endif
                </h2>
                <p class="secondary-color">{{ $faqSettings['description'] }}</p>
            </div>
            <div class="col-md-9 mb-3">
                <div class="accordion" id="faqAccordion">
                    @foreach ($faqSettings['items'] as $index => $item)
                        @php($faqId = 'faq' . ($index + 1))
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-bold secondary-color {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $faqId }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="{{ $faqId }}">
                                    {{ $item['question'] }}
                                </button>
                            </h2>
                            <div id="{{ $faqId }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body secondary-color">
                                    {{ $item['answer'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-md-3">
                <div class="card rounded-4 px-4 py-5 shadow-sm text-center">
                    <div class="avatar-box d-inline-flex align-items-center p-2 position-relative justify-content-center mb-4">
                        <img src="https://i.pravatar.cc/40?img=1" class="avatar position-absolute" style="left: 35%;">
                        <img src="https://i.pravatar.cc/40?img=2" class="avatar position-absolute ms-n2 z-1" style="top: -29px; width:56px; height:56px;">
                        <img src="https://i.pravatar.cc/40?img=3" class="avatar position-absolute ms-n2" style="right: 35%;">
                    </div>

                    <div class="card-content">
                        <h5 class="card-title secondary-color fw-bold">{{ $faqSettings['cta_title'] }}</h5>
                        <p class="card-description secondary-color">{{ $faqSettings['cta_description'] }}</p>
                        <a href="{{ $faqSettings['cta_button_link'] ?: route('contact-us') }}" class="btn btn-primary w-100 rounded-4 px-4 py-3">{{ $faqSettings['cta_button_text'] ?: 'Get in touch' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> --}}
