<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav pt-3">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer-panel.dashboard') ? 'active' : '' }}" href="{{ route('customer-panel.dashboard') }}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.jobs.create') ? 'active' : '' }}" href="{{ route('customer.jobs.create') }}">
                <i class="mdi mdi-note-plus-outline menu-icon"></i>
                <span class="menu-title">Post Quote</span>
            </a>
        </li> --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer-panel.jobs') ? 'active' : '' }}" href="{{ route('customer-panel.jobs') }}">
                <i class="mdi mdi-briefcase menu-icon"></i>
                <span class="menu-title">My Jobs</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer-panel.quotes') ? 'active' : '' }}" href="{{ route('customer-panel.quotes') }}">
                <i class="mdi mdi-email-outline menu-icon"></i>
                <span class="menu-title">Supplier Quotes</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer-panel.suppliers') ? 'active' : '' }}" href="{{ route('customer-panel.suppliers') }}">
                <i class="mdi mdi-account-multiple menu-icon"></i>
                <span class="menu-title">Suppliers</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer-panel.notifications.*') ? 'active' : '' }}" href="{{ route('customer-panel.notifications.index') }}">
                <i class="mdi mdi-bell-outline menu-icon"></i>
                <span class="menu-title">Notifications</span>
                @php($customerUnreadCount = Auth::user()->userNotifications()->unread()->count())
                @if($customerUnreadCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $customerUnreadCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer-panel.profile') ? 'active' : '' }}" href="{{ route('customer-panel.profile') }}">
                <i class="mdi mdi-account-circle menu-icon"></i>
                <span class="menu-title">Profile</span>
            </a>
        </li>
    </ul>
</nav>
