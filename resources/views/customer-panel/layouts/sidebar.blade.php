<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav pt-3">
        <li class="nav-item{{ request()->routeIs('customer-panel.dashboard') ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('customer-panel.dashboard') }}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item{{ request()->routeIs('customer-panel.jobs') ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('customer-panel.jobs') }}">
                <i class="mdi mdi-briefcase menu-icon"></i>
                <span class="menu-title">My Jobs</span>
            </a>
        </li>
        <li class="nav-item{{ request()->routeIs('customer-panel.quotes') ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('customer-panel.quotes') }}">
                <i class="mdi mdi-email-outline menu-icon"></i>
                <span class="menu-title">Supplier Quotes</span>
            </a>
        </li>
        <li class="nav-item{{ request()->routeIs('customer-panel.suppliers') ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('customer-panel.suppliers') }}">
                <i class="mdi mdi-account-multiple menu-icon"></i>
                <span class="menu-title">Suppliers</span>
            </a>
        </li>
        <li class="nav-item{{ request()->routeIs('customer-panel.notifications.*') ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('customer-panel.notifications.index') }}">
                <i class="mdi mdi-bell-outline menu-icon"></i>
                <span class="menu-title">Notifications</span>
                @php($customerUnreadCount = Auth::user()->userNotifications()->unread()->count())
                @if($customerUnreadCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $customerUnreadCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item{{ request()->routeIs('customer-panel.profile') ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('customer-panel.profile') }}">
                <i class="mdi mdi-account-circle menu-icon"></i>
                <span class="menu-title">Profile</span>
            </a>
        </li>
    </ul>
</nav>
