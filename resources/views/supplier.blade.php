@extends('layouts.app')
@section('title', 'Supplier')
@section('section')
    <section class="page-header py-5 bg-primary">
        <div class="container">
            <h1 class="text-center text-white">Supplier</h1>
        </div>
    </section>
    @include('components.pricing')
     @include('components.testimonial')
    @include('components.cta-section')

    <div class="py-5"></div>

   @include('components.contact-section')
   @include('components.faq')
@endsection
