<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
    <div class="text-center navbar-brand-wrapper border-bottom d-flex align-items-center justify-content-start">
        <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
        </div>
        <div>
            <a class="navbar-brand brand-logo" href="{{ route('supplier-panel.dashboard') }}">
                <img src="{{ asset('assets/images/snappy-logo.png') }}" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="{{ route('supplier-panel.dashboard') }}">
                <img src="{{ asset('assets/images/favicon.png') }}" alt="logo" />
            </a>
        </div>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-top border-bottom">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                <a class="nav-link dropdown-toggle border rounded-pill px-3" id="SupplierUserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="profile-text">{{ ucfirst(Auth::user()->name) }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="SupplierUserDropdown">
                    <div class="dropdown-header text-center">
                        <img class="img-md rounded-circle" src="{{ asset('admin/assets/images/faces/face8.jpg') }}" alt="Profile image">
                        <p class="mb-1 mt-3 fw-semibold">{{ ucfirst(Auth::user()->name) }}</p>
                        <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
                    </div>
                    <a class="dropdown-item" href="{{ route('supplier-panel.profile') }}"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile</a>
                    <a class="dropdown-item" href="{{ route('supplier-panel.activity') }}"><i class="dropdown-item-icon mdi mdi-history text-primary me-2"></i> Activity</a>
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
