@extends('layouts.app')
@section('title', 'Home')

@section('section')
    @include('components.hero-3')
    @if(isset($features))
        @include('components.features-3', [
            'features' => $features
        ])
    @endif 
    @if(isset($howItWork))
        @include('components.how-it-work', [
            'howItWork' => $howItWork
        ])
    @endif
    @include('components.how-it-work-snappy')

    {{-- <div class="py-5"></div> --}}
    {{-- @include('components.testimonial') --}}
    @include('components.cta-section')


    <div class="py-5"></div>

    
    
        @include('components.contact-section', [
            'homeContactSection' => $homeContactSection ?? ''
        ])

    @if(isset($faqs))
        @include('components.faq', [
            'faqs' => $faqs
        ])
    @endif
    
@endsection
