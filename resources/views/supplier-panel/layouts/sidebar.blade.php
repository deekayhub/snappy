<nav class="sidebar sidebar-offcanvas d-flex flex-column" id="sidebar">
    <ul class="nav pt-3 flex-grow-1">
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
        @hasFeature('instant_job_alerts')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('supplier-panel.notifications.index') }}">
                <i class="mdi mdi-bell-outline menu-icon"></i>
                <span class="menu-title">Notifications</span>
                @php($unreadCount = Auth::user()->userNotifications()->unread()->count())
                @if($unreadCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $unreadCount }}</span>
                @endif
            </a>
        </li>
        @endhasFeature
        @hasFeature('early_access_jobs')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('supplier-panel.early.jobs') }}">
                <i class="mdi mdi-clock-fast menu-icon"></i>
                <span class="menu-title">Early Access Jobs</span>
            </a>
        </li>
        @endhasFeature
        <li class="nav-item">
            <a class="nav-link" href="{{ route('supplier-panel.reports') }}">
                <i class="mdi mdi-chart-areaspline menu-icon"></i>
                <span class="menu-title">Reports</span>
            </a>
        </li>
        @hasFeature('analytics_dashboard')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('supplier-panel.analytics') }}">
                <i class="mdi mdi-chart-bar menu-icon"></i>
                <span class="menu-title">Analytics</span>
            </a>
        </li>
        @endhasFeature
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
            <a class="nav-link {{ request()->routeIs('supplier-panel.subscription.*') ? 'active' : '' }}" href="{{ route('supplier-panel.subscription.index') }}">
                <i class="mdi mdi-currency-gbp menu-icon"></i>
                <span class="menu-title">Subscription</span>
            </a>
        </li>
    </ul>
    @if(!Auth::user()->hasActiveSubscription())
    <div class="px-3 pb-3 mt-auto">
        <a href="{{ route('supplier-panel.subscription.index') }}" class="btn btn-warning w-100 rounded-4 d-flex align-items-center justify-content-center gap-2">
            <i class="mdi mdi-crown"></i>
            <span>Upgrade Plan</span>
        </a>
        <p class="small text-muted text-center mt-2 mb-0">Unlock more features</p>
    </div>
    @endif
</nav>
