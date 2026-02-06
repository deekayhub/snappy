@extends('layouts.app')
@section('title', 'Supplier')
@section('section')

    <section class="page-header bg-primar y">
        <div class="container">
            <h1 class="text-center text-white display-4">Supplier</h1>
            <ul class="breadcrumb justify-content-center">
                <li class="breadcrumb-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-houses-fill" viewBox="0 0 16 16">
                    <path d="M7.207 1a1 1 0 0 0-1.414 0L.146 6.646a.5.5 0 0 0 .708.708L1 7.207V12.5A1.5 1.5 0 0 0 2.5 14h.55a2.5 2.5 0 0 1-.05-.5V9.415a1.5 1.5 0 0 1-.56-2.475l5.353-5.354z"/>
                    <path d="M8.793 2a1 1 0 0 1 1.414 0L12 3.793V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v3.293l1.854 1.853a.5.5 0 0 1-.708.708L15 8.207V13.5a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 4 13.5V8.207l-.146.147a.5.5 0 1 1-.708-.708z"/>
                    </svg>
                    <a href="{{ route('home') }}" class="breadcrumb-link text-white">Home</a>
                </li>
                <li class="breadcrumb-item text-white">Supplier</li>
            </ul>
        </div>
    </section>

    @include('components.pricing')
     {{-- @include('components.testimonial') --}}
    @include('components.cta-section')

    <div class="py-5"></div>

   @include('components.contact-section')
   @include('components.faq')
@endsection
