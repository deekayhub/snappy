<style>
    .step-icon{
        width:40px;
        height:40px;
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:700;
        font-size:16px;
        box-shadow:0 8px 20px rgba(0,0,0,0.08);
    }

    .shadow-lg{
    transition:0.3s;
    }
    .shadow-lg:hover{
    transform:translateY(-8px);
    box-shadow:0 25px 50px rgba(0,0,0,0.08)!important;
    }

    .py-6{
    padding:90px 0;
    }

    .preview-card{
    background:#fff;
    padding:22px;
    border-radius:18px;
    box-shadow:0 20px 50px rgba(0,0,0,0.06);
    transition:.35s;
    height:100%;
    }

    .preview-card:hover{
    transform:translateY(-12px);
    box-shadow:0 35px 80px rgba(0,0,0,0.1);
    }

    .preview-header{
    display:flex;
    justify-content:space-between;
    font-size:12px;
    color:#6c757d;
    }

    .info-box{
    background:#f7f9fc;
    border:1px solid #eef1f4;
    border-radius:10px;
    padding:10px;
    font-size:13px;
    line-height:1.8;
    }

    .highlight{
    transform:scale(1.05);
    border:1px solid #eef2ff;
    }

    .price-box{
    font-size:26px;
    font-weight:700;
    }
    .price-box span{
    font-size:13px;
    color:#6c757d;
    margin-left:8px;
    }



</style>
 {{-- <section class="py-5 position-relative" style="background:#f8fbff;">
        <div class="container">

            <!-- Section Heading -->
            <div class="text-center mb-5">
            <h2 class="fw-bold display-6">How SnappyQuotes Works</h2>
            <p class="text-muted fs-5">Simple. Fast. Transparent. Connect customers with the right suppliers instantly.</p>
            </div>

            <div class="row g-5">

            <!-- ================= CUSTOMER SIDE ================= -->
            <div class="col-lg-6">
                <div class="p-4 rounded-4 shadow-lg bg-white h-100 border-0">

                <div class="mb-4">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill mb-3">
                    FOR CUSTOMERS
                    </span>
                    <h4 class="fw-bold">Post a job & get best quotes</h4>
                </div>

                <!-- Step -->
                <div class="d-flex mb-4 modern-step">
                    <div class="step-icon bg-primary text-white">1</div>
                    <div class="ms-3">
                    <h6 class="fw-bold mb-1">Post Your Requirement</h6>
                    <p class="text-muted mb-0">Add product details, quantity, budget and delivery timeline.</p>
                    </div>
                </div>

                <div class="d-flex mb-4 modern-step">
                    <div class="step-icon bg-primary text-white">2</div>
                    <div class="ms-3">
                    <h6 class="fw-bold mb-1">Receive Multiple Quotes</h6>
                    <p class="text-muted mb-0">Suppliers send competitive pricing for your request.</p>
                    </div>
                </div>

                <div class="d-flex mb-4 modern-step">
                    <div class="step-icon bg-primary text-white">3</div>
                    <div class="ms-3">
                    <h6 class="fw-bold mb-1">Compare & Chat</h6>
                    <p class="text-muted mb-0">Compare price, reviews and delivery time. Chat instantly.</p>
                    </div>
                </div>

                <div class="d-flex modern-step">
                    <div class="step-icon bg-primary text-white">4</div>
                    <div class="ms-3">
                    <h6 class="fw-bold mb-1">Accept Best Quote</h6>
                    <p class="text-muted mb-0">Choose supplier and confirm order in seconds.</p>
                    </div>
                </div>

                <a href="#" class="btn btn-primary mt-4 px-4 rounded-pill">
                    Post a Job
                </a>

                </div>
            </div>

            <!-- ================= SUPPLIER SIDE ================= -->
            <div class="col-lg-6">
                <div class="p-4 rounded-4 shadow-lg bg-white h-100 border-0">

                <div class="mb-4">
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill mb-3">
                    FOR SUPPLIERS
                    </span>
                    <h4 class="fw-bold">Send quotes & win orders</h4>
                </div>

                <div class="d-flex mb-4 modern-step">
                    <div class="step-icon bg-success text-white">1</div>
                    <div class="ms-3">
                    <h6 class="fw-bold mb-1">Browse Job Leads</h6>
                    <p class="text-muted mb-0">See new customer requirements from dashboard.</p>
                    </div>
                </div>

                <div class="d-flex mb-4 modern-step">
                    <div class="step-icon bg-success text-white">2</div>
                    <div class="ms-3">
                    <h6 class="fw-bold mb-1">Submit Your Quote</h6>
                    <p class="text-muted mb-0">Send price, delivery time and product details.</p>
                    </div>
                </div>

                <div class="d-flex mb-4 modern-step">
                    <div class="step-icon bg-success text-white">3</div>
                    <div class="ms-3">
                    <h6 class="fw-bold mb-1">Get Selected</h6>
                    <p class="text-muted mb-0">Customer compares quotes and selects you.</p>
                    </div>
                </div>

                <div class="d-flex modern-step">
                    <div class="step-icon bg-success text-white">4</div>
                    <div class="ms-3">
                    <h6 class="fw-bold mb-1">Win More Orders</h6>
                    <p class="text-muted mb-0">Receive confirmed orders & grow business.</p>
                    </div>
                </div>

                <a href="#" class="btn btn-success mt-4 px-4 rounded-pill">
                    Become a Supplier
                </a>

                </div>
            </div>

            </div>
        </div>
    </section> --}}
    <section class="py-6 bg-light">
        <div class="container">

            <!-- heading -->
             <div class="text-center mb-5">
            <h2 class="h1 fw-bold">How <div class="text-primary d-inline">SnappyQuotes</div> Works</h2>
            <p class="fs-5 secondary-color">Simple. Fast. Transparent. Connect customers with the right suppliers instantly.</p>
            </div>

            <div class="row g-4">

            <!-- CARD 1 : SUPPLIER VIEW -->
            <div class="col-lg-4">
                <div class="preview-card">
                <div class="preview-header">
                    {{-- <span>Live Preview</span> --}}
                    <span class="badge bg-success-subtle text-success">Supplier Dashboard</span>
                </div>

                <h6 class="fw-bold mt-3">Trophies & Medals + Engraving</h6>
                <small class="text-muted">Posted 16 hours ago</small>

                <div class="info-box mt-3">
                    <div>Category: <strong>Trophies</strong></div>
                    <div>Qty: <strong>120</strong></div>
                    <div>Budget: <strong>£500–£1,000</strong></div>
                    <div>Location: <strong>Cambridge</strong></div>
                </div>

                <ul class="small mt-3 text-muted">
                    <li>✓ 60 trophies + 60 medals</li>
                    <li>✓ Engraving included</li>
                    <li>✓ Delivery by Nov 20</li>
                </ul>

                <button class="btn btn-primary w-100 rounded-pill mt-3">Send Quote</button>
                </div>
            </div>

            <!-- CARD 2 : JOB DETAIL -->
            <div class="col-lg-4">
                <div class="preview-card highlight">
                <div class="preview-header">
                    {{-- <span>Live Preview</span> --}}
                    <span class="badge bg-info text-white">Job Details</span>
                </div>

                <h6 class="fw-bold mt-3">Football Kits – U16 Team</h6>
                <small class="text-muted">Closes in 5 days</small>

                <div class="info-box mt-3">
                    <div>Category: <strong>Sportswear</strong></div>
                    <div>Qty: <strong>60 kits</strong></div>
                    <div>Budget: <strong>£2,000–£3,000</strong></div>
                    <div>Location: <strong>Leeds</strong></div>
                </div>

                <ul class="small mt-3 text-muted">
                    <li>✓ Custom crest & printing</li>
                    <li>✓ Player names & numbers</li>
                    <li>✓ Delivery Sept 15</li>
                </ul>

                <div class="mt-3 small text-muted">
                    <strong>3 suppliers competing</strong> • Avg £2,380
                </div>
                </div>
            </div>

            <!-- CARD 3 : CUSTOMER VIEW -->
            <div class="col-lg-4">
                <div class="preview-card">
                <div class="preview-header">
                    {{-- <span>Live Preview</span> --}}
                    <span class="badge bg-primary-subtle text-primary">Customer View</span>
                </div>

                <h6 class="fw-bold mt-3">Swift Sports Ltd</h6>
                <small class="text-muted">Top rated supplier</small>

                <div class="rating mt-2">⭐⭐⭐⭐⭐ 4.6 (127 reviews)</div>

                <div class="price-box mt-3">
                    £2,450 <span>12 days delivery</span>
                </div>

                <ul class="small mt-3 text-muted">
                    <li>✓ Custom badges included</li>
                    <li>✓ Free delivery</li>
                    <li>✓ Size exchange</li>
                </ul>

                <button class="btn btn-success w-100 rounded-pill mt-3">
                    Accept Quote
                </button>
                </div>
            </div>

            </div>

        </div>
    </section>
