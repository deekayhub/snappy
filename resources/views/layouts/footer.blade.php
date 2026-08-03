<footer class="">
    <div class="container mb-5">
        <div class="row align-items-center">
            <div class="col-12 col-md-3 text-center text-md-start">
                <img class="mb-2" src="{{ asset(setting('footer_logo', 'assets/images/footer-logo.png')) }}" alt="snappy logo" >
            </div>
            <div class="col-md-6 col-12">
                 <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="{{ route('home') }}" class="nav-link px-2 {{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                    @role('supplier')
                    <li><a href="{{ route('supplier') }}" class="nav-link px-2 {{ request()->routeIs('supplier') ? 'active' : '' }}">Suppliers</a></li>
                    @endrole
                    <li><a href="{{ route('how-it-work') }}" class="nav-link px-2 {{ request()->routeIs('how-it-work') ? 'active' : '' }}">How it Works</a></li>
                    <li><a href="{{ route('contact-us') }}" class="nav-link px-2 {{ request()->routeIs('contact-us') ? 'active' : '' }}">Contact Us</a></li>
                    <li><a href="{{ route('faq') }}" class="nav-link px-2 {{ request()->routeIs('faq') ? 'active' : '' }}">FAQ's</a></li>
                </ul>
            </div>
            <div class="col-12 col-md-3 text-center text-md-end">
                 <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0 social-icons">
                    <li><a href="{{ setting('twitter_url', '#') }}" class="nav-link px-2"><i class="bi bi-twitter"></i></a></li>
                    <li><a href="{{ setting('facebook_url', '#') }}" class="nav-link px-2"><i class="bi bi-facebook"></i></a></li>
                    <li><a href="{{ setting('instagram_url', '#') }}" class="nav-link px-2"><i class="bi bi-instagram"></i></a></li>
                </ul>
            </div>

        </div>
    </div>
    <hr class="m-0">
    <div class="copyright py-3">
        <p class="text-center mb-0">{{ setting('copyright_text', '© ' . date('Y') . ' Snappy Quotes Hub. All rights reserved.') }}</p>
    </div>
</footer>
