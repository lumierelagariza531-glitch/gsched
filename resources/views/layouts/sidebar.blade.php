<aside id="sidebar" class="sidebar">
    <nav class="px-3 py-3">
        <ul class="nav flex-column gap-1">
            @if(auth()->user()->isStudent())
                <li class="nav-item">
                    <span class="sidebar-label text-uppercase small px-3 py-2" style="font-size: 0.7rem;">{{ auth()->user()->school ? auth()->user()->school . ' Student' : 'STUDENT' }}</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.schedules*') ? 'active' : '' }}" href="{{ route('student.schedules') }}">
                        <i class="bi bi-calendar-week me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Available Schedules</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.appointments*') ? 'active' : '' }}" href="{{ route('student.appointments.index') }}">
                        <i class="bi bi-calendar-check me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">My Appointments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.notifications*') ? 'active' : '' }}" href="{{ route('student.notifications') }}">
                        <i class="bi bi-bell me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Notifications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.feedback*') ? 'active' : '' }}" href="{{ route('student.feedback.index') }}">
                        <i class="bi bi-chat-text me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Feedback</span>
                    </a>
                </li>
            @elseif(auth()->user()->isGuidanceAssociate())
                <li class="nav-item">
                    <span class="sidebar-label small px-3 py-2" style="font-size: 0.7rem;">{{ auth()->user()->display_role_name }}</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guidance.dashboard') ? 'active' : '' }}" href="{{ route('guidance.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guidance.requests*') ? 'active' : '' }}" href="{{ route('guidance.requests') }}">
                        <i class="bi bi-inbox me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Appointment Requests</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guidance.appointments*') ? 'active' : '' }}" href="{{ route('guidance.appointments') }}">
                        <i class="bi bi-calendar-check me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Appointments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guidance.availability*') ? 'active' : '' }}" href="{{ route('guidance.availability') }}">
                        <i class="bi bi-calendar-plus me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Manage Availability</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guidance.calendar*') ? 'active' : '' }}" href="{{ route('guidance.calendar') }}">
                        <i class="bi bi-calendar3 me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Calendar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guidance.history*') ? 'active' : '' }}" href="{{ route('guidance.history') }}">
                        <i class="bi bi-clock-history me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Appointment History</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guidance.notifications*') ? 'active' : '' }}" href="{{ route('guidance.notifications') }}">
                        <i class="bi bi-bell me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Notifications</span>
                    </a>
                </li>
            @elseif(auth()->user()->isAdmin())
                <li class="nav-item">
                    <span class="sidebar-label text-uppercase small px-3 py-2" style="font-size: 0.7rem;">ADMINISTRATOR</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">User Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.appointments*') ? 'active' : '' }}" href="{{ route('admin.appointments') }}">
                        <i class="bi bi-calendar-check me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">All Appointments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.schedules*') ? 'active' : '' }}" href="{{ route('admin.schedules') }}">
                        <i class="bi bi-calendar-week me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Schedules</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.availability*') ? 'active' : '' }}" href="{{ route('admin.availability') }}">
                        <i class="bi bi-calendar-plus me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Availability</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                        <i class="bi bi-graph-up me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Reports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.logs*') ? 'active' : '' }}" href="{{ route('admin.logs') }}">
                        <i class="bi bi-journal-text me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Activity Logs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}" href="{{ route('admin.notifications') }}">
                        <i class="bi bi-bell me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">Notifications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                        <i class="bi bi-gear me-2 fs-5 sidebar-icon"></i><span class="sidebar-text">System Settings</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
    <footer class="sidebar-iso-footer">
        <div class="sidebar-iso-badge" aria-label="ISO Certified, Certification">
            <span class="sidebar-iso-mark" aria-hidden="true">ISO</span>
            <span class="sidebar-iso-copy">
                <span class="sidebar-iso-title">ISO Certified</span>
                <span class="sidebar-iso-caption">Certification</span>
            </span>
        </div>
    </footer>
</aside>