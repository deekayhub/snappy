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
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
        </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('supplier') ? 'active' : '' }}" href="{{ route('supplier') }}">Suppliers</a>
        </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('how-it-work') ? 'active' : '' }}" href="{{ route('how-it-work') }}">How it Works</a>
        </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('contact-us') ? 'active' : '' }}" href="{{ route('contact-us') }}">Contact Us</a>
        </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('faq') ? 'active' : '' }}" href="{{ route('faq') }}">FAQ's</a>
        </li>
        </ul>

        <!-- Buttons -->
        <div class="d-lg-flex justify-content-center gap-lg-2">

            @auth
            <ul class="list-group">
                <li class="nav-item list-group-item rounded-4 dropdown">
                    <button type="button" class="nav-link dropdown-toggle d-flex align-items-center"
                        href="#"
                        id="userDropdown"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                            {{ ucfirst(Auth::user()->name) }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        {{-- <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                Profile
                            </a>
                        </li> --}}

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
            @else


                <!-- Login -->
                <div class="dropdown">
                    <a
                        href="#"
                        class="btn btn-outline-primary rounded-4 px-4 dropdown-toggle"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        >
                        Login
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end text-center">
                        <li>
                            <a class="dropdown-item" href="{{ route('login', ['type' => 'customer']) }}">
                            Customer Login
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('login', ['type' => 'supplier']) }}">
                            Supplier Login
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Sign-up -->
                <div class="dropdown">
                    <a
                    href="#"
                    class="btn btn-primary rounded-4 px-4 dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    >
                    Sign-up
                    </a>

                    <!-- 🔑 THIS FIX -->
                    <ul class="dropdown-menu dropdown-menu-end text-center">
                        <li>
                            <a class="dropdown-item" href="{{ route('register', ['type' => 'customer']) }}">
                            Customer Sign-up
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('register', ['type' => 'supplier']) }}">
                            Supplier Sign-up
                            </a>
                        </li>
                    </ul>
                </div>
            @endauth

            </div>


    </nav>
  </div>
</header>
