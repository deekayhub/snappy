

<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
    @php
        $customerHeaderAvatar = Auth::user()->profile_picture
            ? asset(Auth::user()->profile_picture)
            : asset('admin/assets/images/faces/face8.jpg');
    @endphp
    <div class="text-center navbar-brand-wrapper border-bottom d-flex align-items-center justify-content-start" style="height: 80px;">
        <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
        </div>
        <div>
            <a class="navbar-brand brand-logo" href="{{ route('customer-panel.dashboard') }}">
                <img src="{{ asset('assets/images/snappy-logo.png') }}" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="{{ route('customer-panel.dashboard') }}">
                <img src="{{ asset('assets/images/favicon.png') }}" alt="logo" />
            </a>
        </div>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-top border-bottom" style="height: 80px;">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown user-dropdown">
                <a class="nav-link dropdown-toggle border rounded-pill px-2 px-lg-3 py-2 d-flex align-items-center gap-2" id="CustomerUserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="rounded-circle" src="{{ $customerHeaderAvatar }}" alt="Profile image" style="width: 32px; height: 32px; object-fit: cover;">
                    <span class="profile-text d-none d-lg-inline">{{ ucfirst(Auth::user()->name) }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown" aria-labelledby="CustomerUserDropdown">
                    <div class="dropdown-header text-center">
                        <p class="mb-1 mt-3 fw-semibold">{{ ucfirst(Auth::user()->name) }}</p>
                        <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
                    </div>
                    <a class="dropdown-item" href="{{ route('customer-panel.profile') }}"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger border-0">
                            <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i> Log Out
                        </button>
                    </form>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
