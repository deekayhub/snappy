@extends('layouts.app')
@section('title', 'How It Works')
@section('section')

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
    <section class="actions-section">
        <div class="container">
            <div class="row">
                <div class="col-md-5 mx-auto">
                    <div class="text-center p-3 p-md-5 shadow rounded-3">
                        <h4 class="fw-bold text-white mb-4">Let’s Build Your Next Winning Partnership</h4>
                        <p class="text-white">Thousands of clubs and suppliers already use our platform — join them today.</p>
                         <div class="d-md-flex justify-content-center flex- wrap gap-3">
                            <button type="button" class="btn rounded-4 bg-white px-4 py-3 me-md-2 fw-bold w-100 mb-3">Post a Job</button>
                            <button type="button" class="btn rounded-4 border text-white py-3 px-4 w-100 mb-3">Join as a Supplier</button>
                        </div>
                </div>
            </div>
        </div>
    </section>

    <div class="py-5"></div>
    
   @include('components.contact-section')
   @include('components.faq') 
@endsection