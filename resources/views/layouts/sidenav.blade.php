@php
    $user = auth()->user();
@endphp

<style>
    #sidebarMenu {
        background: linear-gradient(180deg, #10233d 0%, #17355f 100%);
        border: 0;
        box-shadow: 0 24px 60px rgba(12, 24, 44, 0.26);
    }

    .sidebar-inner {
        min-height: 100vh;
        padding: 1rem;
    }

    .sidebar-brand,
    .sidebar-user-card,
    .sidebar-footer {
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .sidebar-brand {
        padding: 1rem 1.1rem;
        margin-bottom: 1rem;
    }

    .sidebar-brand-mark {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1f75fe 0%, #f4b942 100%);
        color: #fff;
        font-weight: 800;
        box-shadow: 0 12px 24px rgba(31, 117, 254, 0.25);
    }

    .sidebar-user-card {
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .sidebar-avatar {
        width: 52px;
        height: 52px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.16);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.05rem;
    }

    .sidebar-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 18px;
    }

    .sidebar-section-label {
        font-size: 0.73rem;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: rgba(255, 255, 255, 0.5);
        margin: 1.25rem 0 0.75rem;
        padding: 0 0.9rem;
    }

    #sidebarMenu .nav-link {
        border-radius: 16px;
        padding: 0.8rem 0.95rem;
        color: rgba(255, 255, 255, 0.78);
        display: flex;
        align-items: center;
        gap: 0.9rem;
        transition: 0.2s ease;
    }

    #sidebarMenu .nav-link:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        transform: translateX(2px);
    }

    #sidebarMenu .nav-item.active > .nav-link {
        background: linear-gradient(135deg, rgba(31, 117, 254, 0.24), rgba(244, 185, 66, 0.18));
        color: #fff;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
    }

    .sidebar-icon-badge {
        width: 38px;
        height: 38px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.08);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sidebar-footer {
        padding: 0.85rem 1rem;
        margin-top: 1.5rem;
    }

    .sidebar-logout {
        color: #fff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        font-weight: 600;
    }
</style>

<nav id="sidebarMenu" class="collapse sidebar d-lg-block text-white">
    <div class="sidebar-inner">
        <div class="sidebar-brand d-flex align-items-center gap-3">
            <div class="sidebar-brand-mark">EA</div>
            <div>
                <div class="fw-bold text-white">Employee Attendance</div>
                <div class="small text-white-50">Operations Console</div>
            </div>
        </div>

        @if ($user)
            <div class="sidebar-user-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="sidebar-avatar">
                        <img src="{{ $user->profile_image_url }}" alt="{{ $user->full_name }}">
                    </div>
                    <div>
                        <div class="fw-bold text-white">{{ $user->full_name }}</div>
                        <div class="small text-white-50">{{ $user->is_admin ? 'Administrator' : 'Employee' }}</div>
                    </div>
                </div>
            </div>
        @endif

        <ul class="nav flex-column pt-1">
            @if ($user && !$user->is_admin)
                <div class="sidebar-section-label">My Workspace</div>

                <li class="nav-item {{ Request::routeIs('profile') ? 'active' : '' }}">
                    <a href="{{ route('profile') }}" class="nav-link">
                        <span class="sidebar-icon-badge">
                            <svg class="icon icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        <span class="sidebar-text">Profile</span>
                    </a>
                </li>

                <li class="nav-item {{ Request::routeIs('attendances.user-attendance') ? 'active' : '' }}">
                    <a href="{{ route('attendances.user-attendance', auth()->id()) }}" class="nav-link">
                        <span class="sidebar-icon-badge">
                            <svg class="icon icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </span>
                        <span class="sidebar-text">My Attendance</span>
                    </a>
                </li>

                <li class="nav-item {{ Request::routeIs('leave.take-leave') || Request::routeIs('leave.create') ? 'active' : '' }}">
                    <a href="{{ route('leave.take-leave') }}" class="nav-link">
                        <span class="sidebar-icon-badge">
                            <svg class="icon icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m5 5H7a2 2 0 01-2-2V7a2 2 0 012-2h3l2-2h4l2 2h3a2 2 0 012 2v10a2 2 0 01-2 2z"></path></svg>
                        </span>
                        <span class="sidebar-text">My Leaves</span>
                    </a>
                </li>

                <li class="nav-item {{ Request::routeIs('employee.qr') ? 'active' : '' }}">
                    <a href="{{ route('employee.qr') }}" class="nav-link">
                        <span class="sidebar-icon-badge">
                            <svg class="icon icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h4v4H3V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zM3 9h4v4H3V9zm6 0h4v4h-4V9zm6 0h4v4h-4V9zM3 15h4v4H3v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4z" /></svg>
                        </span>
                        <span class="sidebar-text">My QR</span>
                    </a>
                </li>
            @endif

            @if ($user && $user->is_admin)
                <div class="sidebar-section-label">Overview</div>

                <li class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <span class="sidebar-icon-badge">
                            <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path></svg>
                        </span>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>

                <div class="sidebar-section-label">Management</div>

                <li class="nav-item {{ Request::routeIs('users.*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <span class="sidebar-icon-badge">
                            <svg class="icon icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </span>
                        <span class="sidebar-text">Employees</span>
                    </a>
                </li>

                <li class="nav-item {{ Request::routeIs('departments.*') ? 'active' : '' }}">
                    <a href="{{ route('departments.index') }}" class="nav-link">
                        <span class="sidebar-icon-badge">
                            <svg class="icon icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </span>
                        <span class="sidebar-text">Departments</span>
                    </a>
                </li>

                <li class="nav-item {{ Request::routeIs('jobs.*') ? 'active' : '' }}">
                    <a href="{{ route('jobs.index') }}" class="nav-link">
                        <span class="sidebar-icon-badge">
                            <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path></svg>
                        </span>
                        <span class="sidebar-text">Jobs</span>
                    </a>
                </li>

                <div class="sidebar-section-label">Operations</div>

                <li class="nav-item {{ Request::routeIs('attendances.index') || Request::routeIs('attendances.take-attendance') ? 'active' : '' }}">
                    <a href="{{ route('attendances.index') }}" class="nav-link">
                        <span class="sidebar-icon-badge">
                            <svg class="icon icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </span>
                        <span class="sidebar-text">Attendance Records</span>
                    </a>
                </li>

                <li class="nav-item {{ Request::routeIs('qr.scan.ui') ? 'active' : '' }}">
                    <a href="{{ route('qr.scan.ui') }}" class="nav-link">
                        <span class="sidebar-icon-badge">
                            <svg class="icon icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h4v4H3V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zM3 9h4v4H3V9zm6 0h4v4h-4V9zm6 0h4v4h-4V9zM3 15h4v4H3v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4z" /></svg>
                        </span>
                        <span class="sidebar-text">Scan QR</span>
                    </a>
                </li>

                <li class="nav-item {{ Request::routeIs('leave.*') ? 'active' : '' }}">
                    <a href="{{ route('leave.index') }}" class="nav-link">
                        <span class="sidebar-icon-badge">
                            <svg class="icon icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </span>
                        <span class="sidebar-text">Leave Requests</span>
                    </a>
                </li>
            @endif
        </ul>

        @if ($user)
            <div class="sidebar-footer">
                <div class="small text-white-50 mb-2">Signed in as {{ $user->email }}</div>
                <div class="sidebar-logout">
                    <livewire:logout />
                </div>
            </div>
        @endif
    </div>
</nav>
