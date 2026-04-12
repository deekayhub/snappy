<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
    <div class="text-center navbar-brand-wrapper border-bottom  d-flex align-items-center justify-content-start" style="height: 80px;">
        <div class="me-3">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
            <span class="icon-menu"></span>
        </button>
        </div>
        <div>
        <a class="navbar-brand brand-logo" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets/images/snappy-logo.png') }}" alt="logo" />
        </a>
        <a class="navbar-brand brand-logo-mini" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets/images/favicon.png') }}" alt="logo" />
        </a>
        </div>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-top border-bottom" style="height: 80px;"> 
        <ul class="navbar-nav ms-auto"> 
        {{-- <li class="nav-item">
            <form class="search-form" action="#">
            <i class="icon-search"></i>
            <input type="search" class="form-control" placeholder="Search Here" title="Search here">
            </form>
        </li> --}}
        <li class="nav-item dropdown">
            <a class="nav-link count-indicator" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
            <i class="icon-bell"></i>
            <span class="count"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="notificationDropdown">
            <a class="dropdown-item py-3 border-bottom">
                <p class="mb-0 fw-medium float-start">You have 4 new notifications </p>
                <span class="badge badge-pill badge-primary float-end">View all</span>
            </a>
            <a class="dropdown-item preview-item py-3">
                <div class="preview-thumbnail">
                <i class="mdi mdi-alert m-auto text-primary"></i>
                </div>
                <div class="preview-item-content">
                <h6 class="preview-subject fw-normal text-dark mb-1">Application Error</h6>
                <p class="fw-light small-text mb-0"> Just now </p>
                </div>
            </a>
            <a class="dropdown-item preview-item py-3">
                <div class="preview-thumbnail">
                <i class="mdi mdi-lock-outline m-auto text-primary"></i>
                </div>
                <div class="preview-item-content">
                <h6 class="preview-subject fw-normal text-dark mb-1">Settings</h6>
                <p class="fw-light small-text mb-0"> Private message </p>
                </div>
            </a>
            <a class="dropdown-item preview-item py-3">
                <div class="preview-thumbnail">
                <i class="mdi mdi-airballoon m-auto text-primary"></i>
                </div>
                <div class="preview-item-content">
                <h6 class="preview-subject fw-normal text-dark mb-1">New user registration</h6>
                <p class="fw-light small-text mb-0"> 2 days ago </p>
                </div>
            </a>
            </div>
        </li> 
        <li class="nav-item dropdown d-none d-lg-block user-dropdown">
            <a class="nav-link dropdown-toggle border rounded-pill px-3" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                {{-- <img class="img-xs rounded-circle" src="{{ asset('admin/assets/images/faces/face8.jpg') }}" alt="Profile image">  --}}
                <span class="profile-text">{{ ucfirst(Auth::user()->name) }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                    <img class="img-md rounded-circle" src="{{ asset('admin/assets/images/faces/face8.jpg') }}" alt="Profile image">
                    <p class="mb-1 mt-3 fw-semibold">{{ ucfirst(Auth::user()->name) }}</p>
                    <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
                </div>
                <a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile</a>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i> Activity</a>
                {{-- <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-help-circle-outline text-primary me-2"></i> FAQ</a> --}}
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger border-0">
                            Log Out
                        </button>
                    </form>
                </a>
            </div>
        </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
        <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
