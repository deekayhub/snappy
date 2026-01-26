@extends('layouts.app')
@section('title', 'FAQ')
@section('section')

@include('components.faq') 
 @include('components.testimonial')
    <section class="actions-section">
        <div class="container">
            <div class="row">
                <div class="col-md-5 mx-auto">
                    <div class="text-center p-3 p-md-5 shadow rounded-3" style="background:#99bfdb38;">
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
@endsection