@extends('layouts.app')
@section('title', 'How It Works')
@section('section')
    <section class="page-header bg-primar y">
        <div class="container">
            <h1 class="text-center text-white display-4">How it Works</h1>
            <ul class="breadcrumb justify-content-center">
                <li class="breadcrumb-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-houses-fill" viewBox="0 0 16 16">
                    <path d="M7.207 1a1 1 0 0 0-1.414 0L.146 6.646a.5.5 0 0 0 .708.708L1 7.207V12.5A1.5 1.5 0 0 0 2.5 14h.55a2.5 2.5 0 0 1-.05-.5V9.415a1.5 1.5 0 0 1-.56-2.475l5.353-5.354z"/>
                    <path d="M8.793 2a1 1 0 0 1 1.414 0L12 3.793V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v3.293l1.854 1.853a.5.5 0 0 1-.708.708L15 8.207V13.5a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 4 13.5V8.207l-.146.147a.5.5 0 1 1-.708-.708z"/>
                    </svg>
                    <a href="{{ route('home') }}" class="breadcrumb-link text-white">Home</a>
                </li>
                <li class="breadcrumb-item text-white">How it Works</li>
            </ul>
        </div>
    </section>
    <section class="py-5 bg-light">
        <div class="container">

            <!-- Heading -->
            <div class="text-center mb-5">
                <h2 class="fw-bold">How It Works</h2>
            </div>

            <div class="row g-4">

                <!-- Step 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-building fs-1 text-primary"></i>
                            </div>
                            <p class="mb-0">
                                Suppliers can register and create a company file. There are different
                                levels of membership for suppliers to choose from depending on how
                                active they wish to be in the market.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-person-plus fs-1 text-primary"></i>
                            </div>
                            <p class="mb-0">
                                Customers can register for free and post a description of what they
                                are looking to buy. The customer can add as little or as much detail
                                as possible along with delivery time scales and budgets.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-file-text fs-1 text-primary"></i>
                            </div>
                            <p class="mb-0">
                                Suppliers can browse the job platform and quote for any jobs that
                                appeal to them. Quoting is made simple with our online quoting system.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-bell fs-1 text-primary"></i>
                            </div>
                            <p class="mb-0">
                                Customers will receive notifications of all quotes and decide who
                                they wish to work with.
                            </p>
                        </div>
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
