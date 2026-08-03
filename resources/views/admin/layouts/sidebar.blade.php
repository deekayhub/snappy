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
            <a class="nav-link{{ request()->routeIs('admin.dashboard') ? ' active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('admin.jobs') ? ' active' : '' }}" href="{{ route('admin.jobs') }}">
                <i class="mdi mdi-briefcase menu-icon"></i>
                <span class="menu-title">Active Jobs</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('admin.quotes') ? ' active' : '' }}" href="{{ route('admin.quotes') }}">
                <i class="mdi mdi-cash-multiple menu-icon"></i>
                <span class="menu-title">Purchase Quotes</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('admin.suppliers*') ? ' active' : '' }}" href="{{ route('admin.suppliers') }}">
                <i class="mdi mdi-account-multiple menu-icon"></i>
                <span class="menu-title">Supplier</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('admin.customers*') ? ' active' : '' }}" href="{{ route('admin.customers') }}">
                <i class="mdi mdi-account-multiple menu-icon"></i>
                <span class="menu-title">Customer</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('admin.invoices') ? ' active' : '' }}" href="{{ route('admin.invoices') }}">
                <i class="mdi mdi-invoice menu-icon"></i>
                <span class="menu-title">Invoice</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('admin.reports') ? ' active' : '' }}" href="{{ route('admin.reports') }}">
                <i class="mdi mdi-chart-box-outline menu-icon"></i>
                <span class="menu-title">Reports</span>
            </a>
        </li>
        <li class="nav-item{{ request()->routeIs('admin.categories*') || request()->routeIs('admin.categories.fields*') || request()->routeIs('admin.page-sections*') || request()->routeIs('admin.subscription.settings*') || request()->routeIs('admin.features*') || request()->routeIs('admin.settings*') ? ' active' : '' }}">
    <a class="nav-link{{ request()->routeIs('admin.categories*') || request()->routeIs('admin.categories.fields*') || request()->routeIs('admin.page-sections*') || request()->routeIs('admin.subscription.settings*') || request()->routeIs('admin.features*') || request()->routeIs('admin.settings*') ? '' : ' collapsed' }}" data-bs-toggle="collapse" href="#settings-menu"
       aria-expanded="{{ request()->routeIs('admin.categories*') || request()->routeIs('admin.categories.fields*') || request()->routeIs('admin.page-sections*') || request()->routeIs('admin.subscription.settings*') || request()->routeIs('admin.features*') || request()->routeIs('admin.settings*') ? 'true' : 'false' }}" aria-controls="settings-menu">
        <i class="mdi mdi-cog-outline menu-icon"></i>
        <span class="menu-title">Settings</span>
        <i class="menu-arrow"></i>
    </a>

    <div class="collapse{{ request()->routeIs('admin.categories*') || request()->routeIs('admin.categories.fields*') || request()->routeIs('admin.page-sections*') || request()->routeIs('admin.subscription.settings*') || request()->routeIs('admin.features*') || request()->routeIs('admin.settings*') ? ' show' : '' }}" id="settings-menu">
        <ul class="nav flex-column sub-menu">

            <li class="nav-item">
                <a class="nav-link{{ request()->routeIs('admin.categories*') ? ' active' : '' }}" href="{{ route('admin.categories') }}">
                    Categories
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link{{ request()->routeIs('admin.categories.fields*') ? ' active' : '' }}" href="{{ route('admin.categories.fields') }}">
                    Categories Fields
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link{{ request()->routeIs('admin.page-sections*') ? ' active' : '' }}" href="{{ route('admin.page-sections') }}">
                    Page Sections
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link{{ request()->routeIs('admin.subscription.settings*') ? ' active' : '' }}" href="{{ route('admin.subscription.settings') }}">
                    Subscription Settings
                </a>
            </li>

            <li class="nav-item d-none">
                <a class="nav-link{{ request()->routeIs('admin.features*') ? ' active' : '' }}" href="{{ route('admin.features.index') }}">
                    Features
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link{{ request()->routeIs('admin.settings*') ? ' active' : '' }}" href="{{ route('admin.settings') }}">
                    System Settings
                </a>
            </li>

        </ul>
    </div>
</li>
        @endif
    </ul>
</nav>
