<style>
    .border-divider:not(:last-child) {
      border-right: 1px solid #dee2e6;
    }
    @media (max-width: 767px) {
      .border-divider {
        border-right: 0;
      }
    }


    .outer-ring {
      width: 95px;
      height: 95px;
      border: 2px solid #0d6efd;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .inner-ring {
      width: 80px;
      height: 80px;
      border: 2px solid #0d6efd;
      border-radius: 50%;
      padding: 6px;
    }

    .inner-ring img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }
  </style>
<section class="testimonial-section section-padding py-5" style="background-color: #0284C70D;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title">
                        <div class="p-3 pb-md-4 mx-auto text-center ">
                        <h1 class="fw-bold text-body-emphasis">What <span class="text-primary">Clubs</span> and <span class="text-primary">Suppliers</span> Say</h1>
                    </div>

                    <div class="section-borders">
                        <span></span>
                        <span class="black-border"></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="owl-carousel client-testimonial-carousel">
                    <div class="single-testimonial-item border rounded-4 p-4 bg-white text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="outer-ring">
                                <div class="inner-ring">
                                    <img src="{{ asset('assets/images/testimonial-1.png') }}" width="80" alt="Profile">
                                </div>
                            </div>
                            </div>
                            <div class="fs-5 text-primary mb-3">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p>“This platform gives us consistent, high-quality enquiries from real buyers. Clubs and schools reach out with clear requirements, which makes quoting fast and accurate. Since joining, we’ve seen a noticeable increase in closed orders every month.”</p>
                        <h4 class="fw-bold fst-italic">ProActive Sportswear</h4>
                    </div>
                    <div class="single-testimonial-item border rounded-4 p-4 bg-white text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="outer-ring">
                                <div class="inner-ring">
                                    <img src="{{ asset('assets/images/testimonial-2.png') }}" width="80" alt="Profile">
                                </div>
                            </div>
                            </div>
                            <div class="fs-5 text-primary mb-3">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p>“Posting a job was incredibly simple, and we received multiple quotes within just a few hours. The suppliers were responsive, professional and easy to compare. We saved time, stayed within budget, and ended up choosing a much better option than expected.”</p>
                        <h4 class="fw-bold fst-italic">Cambridge Youth FC</h4>
                    </div>
                    <div class="single-testimonial-item border rounded-4 p-4 bg-white text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="outer-ring">
                                <div class="inner-ring">
                                    <img src="{{ asset('assets/images/testimonial-3.png') }}" width="80" alt="Profile">
                                </div>
                            </div>
                            </div>
                            <div class="fs-5 text-primary mb-3">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p>“Every lead we receive is genuine and detailed, which means no time wasted on cold outreach. Buyers on this platform are serious and ready to order, making the sales process smooth. It has quickly become one of our most reliable sources of new business.”</p>
                        <h4 class="fw-bold fst-italic">Elite Trophies UK</h4>
                    </div>


                </div>
            </div>
        </div>
    </div>
</section>
