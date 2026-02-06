@extends('layouts.app')
@section('title', 'Contact Us')
@section('section')
<section class="page-header ">
    <div class="container">
        <h2 class="h1 text-center text-white display-4">Contact Us</h2>
        <ul class="breadcrumb justify-content-center">
            <li class="breadcrumb-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-houses-fill" viewBox="0 0 16 16">
                <path d="M7.207 1a1 1 0 0 0-1.414 0L.146 6.646a.5.5 0 0 0 .708.708L1 7.207V12.5A1.5 1.5 0 0 0 2.5 14h.55a2.5 2.5 0 0 1-.05-.5V9.415a1.5 1.5 0 0 1-.56-2.475l5.353-5.354z"/>
                <path d="M8.793 2a1 1 0 0 1 1.414 0L12 3.793V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v3.293l1.854 1.853a.5.5 0 0 1-.708.708L15 8.207V13.5a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 4 13.5V8.207l-.146.147a.5.5 0 1 1-.708-.708z"/>
                </svg>
                <a href="{{ route('home') }}" class="breadcrumb-link text-white">Home</a>
            </li>
            <li class="breadcrumb-item text-white">Contact Us</li>
        </ul>
    </div>
</section>
<section class="contact-form-section">
    <div class="container">

        <!-- Heading -->
        <div class="row mb-5">
            <div class="col text-center">
                <h2 class="h1 fw-bold">Contact Us</h2>
                <p class="text-muted">
                    Have a question or need support? We’re here to help.
                </p>
            </div>
        </div>

        <div class="row g-5">

            <!-- Contact Information -->
            <div class="col-lg-5">
                <div class="p-4 bg-white rounded shadow-sm h-100">
                    <h5 class="fw-semibold mb-3">Get in Touch</h5>
                    <p>
                        If you have any questions about using the platform, memberships,
                        or need help getting started, feel free to contact us using the
                        details below.
                    </p>

                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="bi bi-envelope text-primary me-2"></i>
                            support@snappyquotes.com
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-telephone text-primary me-2"></i>
                            +1 234 567 890
                        </li>
                        <li>
                            <i class="bi bi-geo-alt text-primary me-2"></i>
                            123 Business Street, City, Country
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="p-4 border rounded shadow-sm h-100">
                    <h5 class="fw-semibold mb-3">Send Us a Message</h5>

                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Your Name">
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control" placeholder="Your Email">
                            </div>
                            <div class="col-12">
                                <input type="text" class="form-control" placeholder="Subject">
                            </div>
                            <div class="col-12">
                                <textarea class="form-control" rows="4" placeholder="Your Message"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>

    </div>
</section>

    {{-- @include('components.testimonial') --}}
    @include('components.cta-section')

    <div class="py-5"></div>

   @include('components.contact-section')
   @include('components.faq')
@endsection
