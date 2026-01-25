<header class="shadow border-bottom">
  <div class="container">
    <nav class="navbar navbar-expand-lg py-3">

      <!-- Logo -->
      <a href="/" class="navbar-brand">
        <img src="{{ asset('assets/images/snappy-logo.png') }}" alt="snappy logo" width="150">
      </a>

      <!-- Toggle Button (mobile only) -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menu -->
      <div class="collapse navbar-collapse" id="mainMenu">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('supplier') }}">Suppliers</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('how-it-work') }}">How it Works</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('contact-us') }}">Contact Us</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('faq') }}">FAQ's</a></li>
        </ul>

        <!-- Buttons -->
        <div class="d-lg-flex text-center">
          <a type="button" href="#" class="btn btn-outline-primary me-lg-2 rounded-4 px-4 mb-2 mb-lg-0 dropdown-toggle">
            Login
          </a>
          <a type="button" href="#" class="btn btn-primary rounded-4 px-4 dropdown-toggle">
            Sign-up
          </a>
        </div>
      </div>

    </nav>
  </div>
</header>
