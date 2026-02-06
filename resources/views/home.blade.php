@extends('layouts.app')
@section('title', 'Home')
@section('section')
    @include('components.hero-3')
    @include('components.features-3')
    @include('components.how-it-work')

    <div class="py-5"></div>
    {{-- @include('components.testimonial') --}}
    @include('components.cta-section')


    <div class="py-5"></div>

   @include('components.contact-section')
   @include('components.faq')
@endsection
