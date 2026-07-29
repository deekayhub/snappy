<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row" >
    @php
        $supplierHeaderAvatar = Auth::user()->profile_picture
            ? asset(Auth::user()->profile_picture)
            : asset('admin/assets/images/faces/face8.jpg');
    @endphp
    <div class="text-center navbar-brand-wrapper border-bottom d-flex align-items-center justify-content-start" style="height: 80px;">
        <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
        </div>
        <div>
            <a class="navbar-brand brand-logo" href="{{ route('supplier-panel.dashboard') }}">
                <img src="{{ asset('assets/images/snappy-logo.png') }}" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="{{ route('supplier-panel.dashboard') }}">
                <img src="{{ asset('assets/images/favicon.png') }}" alt="logo" />
            </a>
        </div>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-top border-bottom" style="height: 80px;">
        <ul class="navbar-nav ms-auto">
            @hasFeature('instant_job_alerts')
            @php
                $supplierUnreadCount = Auth::user()->userNotifications()->unread()->count();
                $supplierLatestNotifications = Auth::user()->userNotifications()->latest()->take(5)->get();
            @endphp
            <li class="nav-item dropdown">
                <a class="nav-link position-relative" href="#" id="SupplierNotificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                    <i class="mdi mdi-bell-outline" style="font-size: 1.3rem;"></i>
                    @if($supplierUnreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            {{ $supplierUnreadCount > 99 ? '99+' : $supplierUnreadCount }}
                        </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown p-0" aria-labelledby="SupplierNotificationDropdown" style="width: 420px;">
                    <div class="dropdown-header border-bottom px-3 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold"><i class="mdi mdi-bell-outline me-1"></i>Notifications</span>
                            @if($supplierUnreadCount > 0)
                            <form method="POST" action="{{ route('supplier-panel.notifications.mark-all-read') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-link text-decoration-none p-0">Mark all read</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    <div style="max-height: 360px; overflow-y: auto;">
                        @forelse($supplierLatestNotifications as $notif)
                            <a href="{{ route('supplier-panel.notifications.read', $notif) }}"
                               class="dropdown-item border-bottom px-3 py-3 {{ $notif->is_read ? '' : 'bg-light' }} text-decoration-none"
                               onclick="event.preventDefault(); document.getElementById('supplier-read-{{ $notif->id }}').submit();">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="flex-shrink-0">
                                        <div style="width: 36px; height: 36px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                                            <i class="mdi mdi-bell-outline" style="font-size: 16px;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="small fw-semibold text-dark text-truncate" style="max-width: 320px;">{{ $notif->message }}</div>
                                        <div class="small text-muted mt-1">
                                            <i class="mdi mdi-clock-outline me-1" style="font-size: 11px;"></i>{{ $notif->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    @if(!$notif->is_read)
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #2563eb; flex-shrink: 0; margin-top: 6px;"></div>
                                    @endif
                                </div>
                            </a>
                            <form id="supplier-read-{{ $notif->id }}" method="POST" action="{{ route('supplier-panel.notifications.read', $notif) }}" style="display:none;">@csrf</form>
                        @empty
                            <div class="dropdown-item text-center text-muted py-4">
                                <i class="mdi mdi-bell-off-outline" style="font-size: 24px; color: #d1d5db;"></i>
                                <div class="mt-2">No notifications</div>
                            </div>
                        @endforelse
                    </div>
                    <div class="dropdown-footer text-center border-top p-2">
                        <a href="{{ route('supplier-panel.notifications.index') }}" class="small text-decoration-none fw-semibold"><i class="mdi mdi-eye-outline me-1"></i>View all notifications</a>
                    </div>
                </div>
            </li>
            @endhasFeature
            <li class="nav-item dropdown user-dropdown">
                <a class="nav-link dropdown-toggle border rounded-pill px-2 px-lg-3 py-2 d-flex align-items-center gap-2" id="SupplierUserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="rounded-circle" src="{{ $supplierHeaderAvatar }}" alt="Profile image" style="width: 32px; height: 32px; object-fit: cover;">
                    <span class="profile-text d-none d-lg-inline">{{ ucfirst(Auth::user()->name) }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown" aria-labelledby="SupplierUserDropdown">
                    <div class="dropdown-header text-center">
                        <p class="mb-1 mt-3 fw-semibold">{{ ucfirst(Auth::user()->name) }}</p>
                        <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
                    </div>
                    <a class="dropdown-item" href="{{ route('supplier-panel.profile') }}"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile</a>
                    <a class="dropdown-item" href="{{ route('supplier-panel.activity') }}"><i class="dropdown-item-icon mdi mdi-history text-primary me-2"></i> Activity</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger border-0">
                            <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i> Log Out
                        </button>
                    </form>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
