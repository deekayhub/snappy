@extends('layouts.app')
@section('title', 'Contact Us')
@section('section')
<section class="py-5">
    <div class="container">

        <!-- Heading -->
        <div class="row mb-5">
            <div class="col text-center">
                <h2 class="fw-bold">Contact Us</h2>
                <p class="text-muted">
                    Have a question or need support? We’re here to help.
                </p>
            </div>
        </div>

        <div class="row g-5">

            <!-- Contact Information -->
            <div class="col-lg-5">
                <div class="p-4 bg-light rounded shadow-sm h-100">
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

    @include('components.testimonial')
    @include('components.cta-section')

    <div class="py-5"></div>

   @include('components.contact-section')
   @include('components.faq')
@endsection
