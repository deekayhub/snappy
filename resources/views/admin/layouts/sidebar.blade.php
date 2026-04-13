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
            <a class="nav-link" href="{{ route('admin.categories') }}">
                <i class="mdi mdi-shape-outline menu-icon"></i>
                <span class="menu-title">Categories</span>
            </a>
        </li>
        @endif
    </ul>
</nav>
