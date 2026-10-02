    <nav class="navbar app-navbar navbar-expand-lg fixed-top shadow-sm">
    <div class="container-fluid px-3 px-md-4">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ auth()->check() ? route(auth()->user()->isAdmin() ? 'admin.dashboard' : (auth()->user()->isGuidanceAssociate() ? 'guidance.dashboard' : 'student.dashboard')) : route('home') }}">
            <img src="{{ asset('images/navbar-calendar.png') }}" alt="G-SCHED" class="avatar-sm navbar-brand-logo me-2">
            @if(auth()->check() && auth()->user()->school && file_exists(public_path('images/school-' . auth()->user()->school . '.png')))
                <img src="{{ asset('images/school-' . auth()->user()->school . '.png') }}" alt="{{ auth()->user()->school }}" class="avatar-sm navbar-brand-logo navbar-brand-school-logo me-2">
            @endif
            <span class="app-navbar-brand-text">G-SCHED</span>
        </a>

        @if(auth()->check())
            <button id="sidebarToggle" class="navbar-toggler app-navbar-control app-navbar-sidebar-toggle border-0 me-2" type="button" onclick="toggleSidebar()" aria-controls="sidebarWrapper" aria-expanded="false" aria-label="Open navigation">
                <i class="bi bi-sidebar app-navbar-sidebar-icon"></i>
            </button>
        @endif

        @if(auth()->check())
            <ul class="navbar-nav app-navbar-actions align-items-center">
                    <li class="nav-item dropdown">
                         <a class="nav-link app-navbar-control dropdown-toggle position-relative p-2" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                             <div class="position-relative d-inline-block">
                                 <i class="bi bi-bell fs-4 app-navbar-notification-icon"></i>
                                 @if(auth()->user()->unreadNotificationsCount() > 0)
                                     <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill app-navbar-badge">
                                        {{ auth()->user()->unreadNotificationsCount() }}
                                    </span>
                                 @endif
                             </div>
                         </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow app-navbar-notifications-menu" aria-labelledby="notificationsDropdown">
                            <li><h6 class="dropdown-header px-3 py-2 app-navbar-dropdown-header">Notifications</h6></li>
                            @foreach(auth()->user()->notifications()->latest()->take(10)->get() as $notification)
                                <li>
                                    <a class="dropdown-item px-4 py-3 app-navbar-notification-link {{ !$notification->is_read ? 'fw-bold' : '' }}" href="{{ route('notifications.show', $notification) }}">
                                        <div class="d-flex gap-3 align-items-start">
                                            <div class="flex-shrink-0">
                                                <div class="kpi-icon notifications app-navbar-notification-icon-wrap">
                                                    <i class="bi {{ $notification->icon }} fs-5 app-navbar-notification-icon"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="small fw-medium app-navbar-notification-title">{{ $notification->title }}</div>
                                                <div class="small text-muted">{{ $notification->message }}</div>
                                                <div class="small app-navbar-notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                            <li><hr class="dropdown-divider mx-3"></li>
                            <li><a class="dropdown-item px-4 py-2 text-center app-navbar-view-all" href="{{ auth()->user()->isAdmin() ? route('admin.notifications') : (auth()->user()->isGuidanceAssociate() ? route('guidance.notifications') : route('student.notifications')) }}">View All Notifications</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link app-navbar-control app-navbar-theme-toggle" id="themeToggle" aria-label="Dark mode" aria-pressed="false" title="Switch to dark mode">
                            <i class="bi bi-moon-stars-fill" aria-hidden="true"></i>
                        </button>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link app-navbar-control dropdown-toggle d-flex align-items-center gap-2 p-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu for {{ auth()->user()->first_name }}">
                            @if(auth()->user()->profile_photo)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile" class="avatar-sm">
                            @else
                                <div class="avatar-sm app-navbar-avatar-initial">{{ auth()->user()->first_name[0] }}</div>
                            @endif
                            <span class="fw-medium app-navbar-user-name">{{ auth()->user()->first_name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow app-navbar-account-menu" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item px-3 py-2" href="{{ route('profile') }}"><i class="bi bi-person me-2 app-navbar-menu-icon"></i>Profile</a></li>
                            <hr class="dropdown-divider mx-2">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item px-3 py-2 app-navbar-logout"><i class="bi bi-box-arrow-right me-2 app-navbar-menu-icon"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
            </ul>
        @else
            <button class="navbar-toggler app-navbar-control" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle main navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav"></div>
        @endif
    </div>
</nav>