@if(auth()->check())
@php
    $currentUser = auth()->user();
    $notificationCount = count($notifications);
    $workspaceTitle = $currentUser->is_admin ? 'Admin Dashboard' : 'Employee Workspace';
    $workspaceSubtitle = $currentUser->is_admin
        ? 'Manage attendance, leave, and employees.'
        : 'Check attendance, leave, and QR access.';
@endphp

<style>
    .app-topbar {
        border-radius: 22px;
        margin-bottom: 1rem;
        padding: 0.95rem 1.1rem;
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(148, 163, 184, 0.16);
        backdrop-filter: blur(12px);
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.06);
    }

    .app-topbar-kicker {
        display: inline-flex;
        align-items: center;
        padding: 0.34rem 0.68rem;
        border-radius: 999px;
        background: rgba(31, 117, 254, 0.08);
        color: #1f5ebf;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .app-topbar-title {
        margin: 0.5rem 0 0.15rem;
        color: #152033;
        font-size: 1.3rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .app-topbar-subtitle {
        margin: 0;
        color: #667085;
        font-size: 0.92rem;
    }

    .app-topbar-tools {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 0.7rem;
        flex-wrap: nowrap;
        position: relative;
    }

    .app-topbar-tools .dropdown {
        position: relative;
    }

    .app-topbar-icon-btn,
    .app-topbar-user {
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.16);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
    }

    .app-topbar-icon-btn {
        position: relative;
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #152033 !important;
    }

    .app-topbar-badge {
        position: absolute;
        top: 6px;
        right: 6px;
        min-width: 18px;
        height: 18px;
        padding: 0 0.25rem;
        border-radius: 999px;
        background: #ef4444;
        color: #fff;
        font-size: 0.65rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .app-topbar-user {
        min-height: 48px;
        padding: 0.3rem 0.45rem 0.3rem 0.3rem;
    }

    .app-topbar-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
    }

    .app-topbar-user-name {
        color: #152033;
        font-size: 0.92rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .app-topbar-user-role {
        color: #7b8aa5;
        font-size: 0.72rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .app-topbar-menu {
        width: min(360px, calc(100vw - 32px));
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.14);
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.12);
        margin-top: 0.6rem !important;
    }

    .app-topbar-menu-head {
        padding: 0.95rem 1rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        background: #f8fbff;
    }

    .app-topbar-menu-title {
        margin: 0;
        color: #152033;
        font-size: 0.98rem;
        font-weight: 800;
    }

    .app-topbar-menu-subtitle {
        margin: 0.2rem 0 0;
        color: #667085;
        font-size: 0.84rem;
    }

    .app-topbar-notification-item {
        padding: 0.9rem 1rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
    }

    .app-topbar-notification-item:last-child {
        border-bottom: 0;
    }

    .app-topbar-notification-name {
        color: #152033;
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }

    .app-topbar-notification-text {
        color: #667085;
        font-size: 0.85rem;
        margin: 0;
    }

    .app-topbar-notification-time {
        color: #ef4444;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .app-topbar-notification-actions {
        display: flex;
        gap: 0.6rem;
        justify-content: flex-end;
        margin-top: 0.55rem;
    }

    .app-topbar-empty {
        padding: 1.5rem 1rem;
        text-align: center;
        color: #667085;
    }

    .app-topbar-profile-summary {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .app-topbar-profile-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
    }

    @media (max-width: 767.98px) {
        .app-topbar {
            padding: 0.9rem;
        }

        .app-topbar-tools {
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .app-topbar-user {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>

<nav class="navbar app-topbar">
    <div class="container-fluid px-0">
        <div class="row g-3 align-items-center w-100">
            <div class="col-12 col-lg-7">
                <span class="app-topbar-kicker">{{ $currentUser->is_admin ? 'Control Center' : 'My Workspace' }}</span>
                <h1 class="app-topbar-title">{{ $workspaceTitle }}</h1>
                <p class="app-topbar-subtitle">{{ $workspaceSubtitle }}</p>
            </div>

            <div class="col-12 col-lg-5">
                <div class="app-topbar-tools">
                    <div class="nav-item dropdown">
                        <a class="nav-link app-topbar-icon-btn dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                            <svg class="icon icon-sm" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                            </svg>
                            @if ($notificationCount)
                                <span class="app-topbar-badge">{{ $notificationCount > 9 ? '9+' : $notificationCount }}</span>
                            @endif
                        </a>

                        <div class="dropdown-menu dropdown-menu-end mt-2 py-0 app-topbar-menu">
                            <div class="app-topbar-menu-head">
                                <h3 class="app-topbar-menu-title">Notifications</h3>
                                <p class="app-topbar-menu-subtitle">{{ $notificationCount ? 'Unread updates' : 'No new notifications' }}</p>
                            </div>

                            @forelse ($notifications as $notification)
                                @php
                                    $actionUrl = null;

                                    if (!empty($notification->data['action_url'])) {
                                        $actionUrl = $notification->data['action_url'];
                                    } elseif (!empty($notification->data['attendance_id']) && !empty($notification->data['is_admin'])) {
                                        $actionUrl = route('attendances.view-complain', $notification->id);
                                    }
                                @endphp
                                <div class="app-topbar-notification-item">
                                    <div class="d-flex justify-content-between gap-3">
                                        <div class="flex-grow-1">
                                            <div class="app-topbar-notification-name">{{ $notification->data['user_name'] ?? 'Notification' }}</div>
                                            <p class="app-topbar-notification-text">
                                                @if (($notification->data['type'] ?? null) === 'leave_request')
                                                    {{ $notification->data['message'] ?? 'New leave request submitted.' }}
                                                @elseif (!empty($notification->data['attendance_id']) && !empty($notification->data['is_admin']))
                                                    Complain about attendance status <strong>{{ $notification->data['current_status'] }}</strong>.
                                                @else
                                                    {{ $notification->data['message'] ?? 'Status updated.' }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="app-topbar-notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>

                                    <div class="app-topbar-notification-actions">
                                        @if ($actionUrl)
                                            <a href="{{ $actionUrl }}" class="text-info fw-bold small">View</a>
                                        @endif
                                        <a href="" class="text-muted fw-bold small" wire:click.prevent="markAsRead('{{ $notification->id }}')">Mark as read</a>
                                    </div>
                                </div>
                            @empty
                                <div class="app-topbar-empty">
                                    No unread notifications.
                                </div>
                            @endforelse

                            @if ($notificationCount)
                                <div class="p-3 border-top">
                                    <a href="" wire:click.prevent="markAsRead" class="btn btn-primary w-100">Mark all as read</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle app-topbar-user" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center gap-2">
                                <img class="app-topbar-avatar" src="{{ $currentUser->profile_image_url }}"
                                    alt="{{ $currentUser->full_name }}">
                                <div class="d-none d-md-block">
                                    <div class="app-topbar-user-name">{{ $currentUser->full_name }}</div>
                                    <div class="app-topbar-user-role">{{ $currentUser->is_admin ? 'Administrator' : 'Employee' }}</div>
                                </div>
                            </div>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end mt-2 py-0 app-topbar-menu">
                            <div class="app-topbar-menu-head">
                                <div class="app-topbar-profile-summary">
                                    <img src="{{ $currentUser->profile_image_url }}" alt="{{ $currentUser->full_name }}" class="app-topbar-profile-avatar">
                                    <div>
                                        <h3 class="app-topbar-menu-title">{{ $currentUser->full_name }}</h3>
                                        <p class="app-topbar-menu-subtitle">{{ $currentUser->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="py-2">
                                <a class="dropdown-item d-flex align-items-center" href="/profile">
                                    <svg class="dropdown-icon me-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                                    </svg>
                                    My Profile
                                </a>
                                <div class="dropdown-divider my-1"></div>
                                <a class="dropdown-item d-flex align-items-center">
                                    <livewire:logout />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
@endif
