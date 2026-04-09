<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer-panel.dashboard') ? 'active' : '' }}" href="{{ route('customer-panel.dashboard') }}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.jobs.create') ? 'active' : '' }}" href="{{ route('customer.jobs.create') }}">
                <i class="mdi mdi-note-plus-outline menu-icon"></i>
                <span class="menu-title">Post Quote</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer-panel.jobs') ? 'active' : '' }}" href="{{ route('customer-panel.jobs') }}">
                <i class="mdi mdi-briefcase menu-icon"></i>
                <span class="menu-title">My Jobs</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer-panel.quotes') || request()->routeIs('customer.quotes.index') ? 'active' : '' }}" href="{{ route('customer-panel.quotes') }}">
                <i class="mdi mdi-email-outline menu-icon"></i>
                <span class="menu-title">Supplier Quotes</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                <i class="mdi mdi-account-circle menu-icon"></i>
                <span class="menu-title">Profile</span>
            </a>
        </li>
    </ul>
</nav>
