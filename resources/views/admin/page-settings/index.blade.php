@extends('admin.layouts.app')
@section('title', 'Page Settings')

@php
    $faqPage = old('faq_page', $settings['faq_page']);
    $faqSection = old('faq_section', $settings['faq_section']);
    $categorySection = old('home_category_section', $settings['home_category_section']);
@endphp

@section('content')
    <div class="content-wrapper p-3">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="mb-1">Page Settings</h3>
                        <p class="text-muted mb-0">Manage FAQ content and the category section shown after the homepage banner.</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('admin.page-settings.update') }}" method="POST">
                    @csrf

                    <div class="card card-rounded mb-4">
                        <div class="card-body">
                            <h4 class="card-title mb-4">FAQ Banner</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Banner Title</label>
                                    <input
                                        type="text"
                                        name="faq_page[banner_title]"
                                        value="{{ $faqPage['banner_title'] ?? '' }}"
                                        class="form-control @error('faq_page.banner_title') is-invalid @enderror"
                                    >
                                    @error('faq_page.banner_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Banner Description</label>
                                    <textarea
                                        name="faq_page[banner_description]"
                                        rows="3"
                                        class="form-control @error('faq_page.banner_description') is-invalid @enderror"
                                    >{{ $faqPage['banner_description'] ?? '' }}</textarea>
                                    @error('faq_page.banner_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-rounded mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">FAQ Section</h4>
                                <span class="badge badge-opacity-primary">FAQ Section</span>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Section Title</label>
                                    <input
                                        type="text"
                                        name="faq_section[title]"
                                        value="{{ $faqSection['title'] ?? '' }}"
                                        class="form-control @error('faq_section.title') is-invalid @enderror"
                                    >
                                    @error('faq_section.title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Highlight Text</label>
                                    <input
                                        type="text"
                                        name="faq_section[highlight_text]"
                                        value="{{ $faqSection['highlight_text'] ?? '' }}"
                                        class="form-control @error('faq_section.highlight_text') is-invalid @enderror"
                                    >
                                    @error('faq_section.highlight_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Section Description</label>
                                    <textarea
                                        name="faq_section[description]"
                                        rows="3"
                                        class="form-control @error('faq_section.description') is-invalid @enderror"
                                    >{{ $faqSection['description'] ?? '' }}</textarea>
                                    @error('faq_section.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">CTA Title</label>
                                    <input
                                        type="text"
                                        name="faq_section[cta_title]"
                                        value="{{ $faqSection['cta_title'] ?? '' }}"
                                        class="form-control @error('faq_section.cta_title') is-invalid @enderror"
                                    >
                                    @error('faq_section.cta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">CTA Description</label>
                                    <textarea
                                        name="faq_section[cta_description]"
                                        rows="3"
                                        class="form-control @error('faq_section.cta_description') is-invalid @enderror"
                                    >{{ $faqSection['cta_description'] ?? '' }}</textarea>
                                    @error('faq_section.cta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">CTA Button Text</label>
                                    <input
                                        type="text"
                                        name="faq_section[cta_button_text]"
                                        value="{{ $faqSection['cta_button_text'] ?? '' }}"
                                        class="form-control @error('faq_section.cta_button_text') is-invalid @enderror"
                                    >
                                    @error('faq_section.cta_button_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">CTA Link</label>
                                    <input
                                        type="text"
                                        name="faq_section[cta_button_link]"
                                        value="{{ $faqSection['cta_button_link'] ?? '' }}"
                                        class="form-control @error('faq_section.cta_button_link') is-invalid @enderror"
                                    >
                                    @error('faq_section.cta_button_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @error('faq_section.items')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <div class="row">
                                @foreach ($faqSection['items'] as $index => $item)
                                    <div class="col-12 mb-3">
                                        <div class="border rounded p-3">
                                            <h6 class="mb-3">FAQ Item {{ $index + 1 }}</h6>
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Question</label>
                                                    <input
                                                        type="text"
                                                        name="faq_section[items][{{ $index }}][question]"
                                                        value="{{ $item['question'] ?? '' }}"
                                                        class="form-control @error('faq_section.items.' . $index . '.question') is-invalid @enderror"
                                                    >
                                                    @error('faq_section.items.' . $index . '.question')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-8 mb-3">
                                                    <label class="form-label">Answer</label>
                                                    <textarea
                                                        name="faq_section[items][{{ $index }}][answer]"
                                                        rows="3"
                                                        class="form-control @error('faq_section.items.' . $index . '.answer') is-invalid @enderror"
                                                    >{{ $item['answer'] ?? '' }}</textarea>
                                                    @error('faq_section.items.' . $index . '.answer')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card card-rounded mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">Homepage Category Section</h4>
                                <span class="badge badge-opacity-success">Category Section</span>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Section Title</label>
                                    <input
                                        type="text"
                                        name="home_category_section[title]"
                                        value="{{ $categorySection['title'] ?? '' }}"
                                        class="form-control @error('home_category_section.title') is-invalid @enderror"
                                    >
                                    @error('home_category_section.title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Highlight Text</label>
                                    <input
                                        type="text"
                                        name="home_category_section[highlight_text]"
                                        value="{{ $categorySection['highlight_text'] ?? '' }}"
                                        class="form-control @error('home_category_section.highlight_text') is-invalid @enderror"
                                    >
                                    @error('home_category_section.highlight_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea
                                        name="home_category_section[description]"
                                        rows="3"
                                        class="form-control @error('home_category_section.description') is-invalid @enderror"
                                    >{{ $categorySection['description'] ?? '' }}</textarea>
                                    @error('home_category_section.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @error('home_category_section.items')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <div class="row">
                                @foreach ($categorySection['items'] as $index => $item)
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3 h-100">
                                            <h6 class="mb-3">Category Card {{ $index + 1 }}</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Card Title</label>
                                                <input
                                                    type="text"
                                                    name="home_category_section[items][{{ $index }}][title]"
                                                    value="{{ $item['title'] ?? '' }}"
                                                    class="form-control @error('home_category_section.items.' . $index . '.title') is-invalid @enderror"
                                                >
                                                @error('home_category_section.items.' . $index . '.title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="form-label">Image Path or URL</label>
                                                <input
                                                    type="text"
                                                    name="home_category_section[items][{{ $index }}][image]"
                                                    value="{{ $item['image'] ?? '' }}"
                                                    class="form-control @error('home_category_section.items.' . $index . '.image') is-invalid @enderror"
                                                >
                                                @error('home_category_section.items.' . $index . '.image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Example: `assets/images/signage.png` or a full `https://...` URL.</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4">Save Page Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
