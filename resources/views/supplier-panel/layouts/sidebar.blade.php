<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav pt-3">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('supplier-panel.dashboard') }}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('supplier-panel.jobs') }}">
                <i class="mdi mdi-briefcase-search menu-icon"></i>
                <span class="menu-title">Job Board</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('supplier-panel.reports') }}">
                <i class="mdi mdi-chart-areaspline menu-icon"></i>
                <span class="menu-title">Reports</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('supplier-panel.activity') }}">
                <i class="mdi mdi-history menu-icon"></i>
                <span class="menu-title">Activity</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('supplier-panel.profile') }}">
                <i class="mdi mdi-account-circle menu-icon"></i>
                <span class="menu-title">Profile</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('subscription.index') }}">
                <i class="mdi mdi-currency-gbp menu-icon"></i>
                <span class="menu-title">Subscription</span>
            </a>
        </li>
    </ul>
</nav>
