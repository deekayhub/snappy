@extends('layouts.app')
@section('title', 'FAQ')
@section('section')

@include('components.faq')
 @include('components.testimonial')
    @include('components.cta-section')

    <div class="py-5"></div>

   @include('components.contact-section')
@endsection
