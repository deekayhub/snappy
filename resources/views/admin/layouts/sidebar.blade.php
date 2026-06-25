<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        {{-- <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.profile') }}">
                <i class="mdi mdi-account-circle menu-icon"></i>
                <span class="menu-title">My Profile</span>
            </a>
        </li> --}}
        @if(auth()->user()->hasAnyRole(['superadmin', 'admin']))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.jobs') }}">
                <i class="mdi mdi-briefcase menu-icon"></i>
                <span class="menu-title">Active Jobs</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.quotes') }}">
                <i class="mdi mdi-cash-multiple menu-icon"></i>
                <span class="menu-title">Purchase Quotes</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.suppliers') }}">
                <i class="mdi mdi-account-multiple menu-icon"></i>
                <span class="menu-title">Supplier</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.customers') }}    ">
                <i class="mdi mdi-account-multiple menu-icon"></i>
                <span class="menu-title">Customer</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.invoices') }}">
                <i class="mdi mdi-invoice menu-icon"></i>
                <span class="menu-title">Invoice</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.reports') }}">
                <i class="mdi mdi-chart-box-outline menu-icon"></i>
                <span class="menu-title">Reports</span>
            </a>
        </li>
        <li class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" href="#settings-menu"
       aria-expanded="false" aria-controls="settings-menu">
        <i class="mdi mdi-cog-outline menu-icon"></i>
        <span class="menu-title">Settings</span>
        <i class="menu-arrow"></i>
    </a>

    <div class="collapse" id="settings-menu">
        <ul class="nav flex-column sub-menu">

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.categories') }}">
                    Categories
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.categories.fields') }}">
                    Categories Fields
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.page-sections') }}">
                    Page Sections
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.subscription.settings') }}">
                    Subscription Settings
                </a>
            </li>

            <li class="nav-item d-none">
                <a class="nav-link" href="{{ route('admin.features.index') }}">
                    Features
                </a>
            </li>

        </ul>
    </div>
</li>
        @endif
    </ul>
</nav>
