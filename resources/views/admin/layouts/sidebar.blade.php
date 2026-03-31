<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="">
                <i class="mdi mdi-briefcase menu-icon"></i>
                <span class="menu-title">Active Jobs</span>
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
     
    </ul>
</nav>