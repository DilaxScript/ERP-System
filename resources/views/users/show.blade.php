@extends('layouts.app')

@section('title', $title . ' Profile')

@section('content')
@php
    $avatarName = $user->first_name ? $user->full_name : 'User Name';
    $detailCards = [
        ['label' => 'Email Address', 'value' => $user->email ?: 'Not available'],
        ['label' => 'Phone Number', 'value' => $user->number ?: 'Not available'],
        ['label' => 'Address', 'value' => $user->address ?: 'Not available'],
        ['label' => 'Monthly Salary', 'value' => $user->sallary ?: 'Not available'],
        ['label' => 'Created On', 'value' => optional($user->created_at)->format('d M Y')],
        ['label' => 'Updated On', 'value' => optional($user->updated_at)->format('d M Y')],
    ];
@endphp

<style>
    .user-profile-page {
        display: grid;
        gap: 1.5rem;
    }

    .user-profile-hero,
    .user-profile-card,
    .user-profile-grid-card {
        border-radius: 28px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
    }

    .user-profile-hero {
        overflow: hidden;
        background:
            radial-gradient(circle at top left, rgba(31, 117, 254, 0.2), transparent 28%),
            radial-gradient(circle at right, rgba(244, 185, 66, 0.16), transparent 24%),
            linear-gradient(135deg, #ffffff 0%, #f6fbff 100%);
    }

    .user-profile-hero-inner {
        padding: 1.75rem;
    }

    .user-profile-kicker {
        display: inline-flex;
        align-items: center;
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        font-size: 0.78rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        font-weight: 700;
        color: #17355f;
        background: rgba(23, 53, 95, 0.08);
    }

    .user-profile-hero h1 {
        margin: 1rem 0 0.55rem;
        font-size: clamp(2rem, 3.2vw, 2.8rem);
        font-weight: 800;
        color: #152033;
    }

    .user-profile-hero p {
        margin: 0;
        max-width: 650px;
        color: #667085;
    }

    .user-profile-stats {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-top: 1.5rem;
    }

    .user-profile-stat {
        padding: 1rem 1.1rem;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid rgba(148, 163, 184, 0.18);
    }

    .user-profile-stat-label {
        font-size: 0.74rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #7b8aa5;
        font-weight: 700;
        margin-bottom: 0.45rem;
    }

    .user-profile-stat-value {
        color: #152033;
        font-size: 1.2rem;
        font-weight: 800;
    }

    .user-profile-card {
        padding: 1.5rem;
        height: 100%;
    }

    .user-profile-avatar-wrap {
        width: 122px;
        height: 122px;
        margin: 0 auto 1rem;
        padding: 6px;
        border-radius: 32px;
        background: linear-gradient(135deg, #17355f 0%, #1f75fe 55%, #f4b942 100%);
        box-shadow: 0 20px 40px rgba(23, 53, 95, 0.18);
    }

    .user-profile-avatar {
        width: 100%;
        height: 100%;
        border-radius: 28px;
        object-fit: cover;
        background: #fff;
    }

    .user-role-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        background: rgba(31, 117, 254, 0.1);
        color: #1f5ebf;
        font-weight: 700;
        font-size: 0.82rem;
    }

    .user-profile-meta {
        display: grid;
        gap: 0.85rem;
        margin-top: 1.5rem;
    }

    .user-profile-meta-item {
        padding: 0.95rem 1rem;
        border-radius: 18px;
        background: #f8fbff;
        border: 1px solid rgba(148, 163, 184, 0.16);
    }

    .user-profile-meta-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #7b8aa5;
        margin-bottom: 0.3rem;
    }

    .user-profile-meta-value {
        color: #152033;
        font-weight: 700;
    }

    .user-profile-grid-card {
        padding: 1.5rem;
    }

    .user-profile-grid-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .user-profile-grid-head h2 {
        margin: 0;
        color: #152033;
        font-size: 1.3rem;
        font-weight: 800;
    }

    .user-profile-grid-head p {
        margin: 0.35rem 0 0;
        color: #667085;
    }

    .user-profile-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .user-info-card {
        padding: 1rem 1.1rem;
        border-radius: 22px;
        background: #fbfdff;
        border: 1px solid rgba(148, 163, 184, 0.16);
    }

    .user-info-label {
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #7b8aa5;
        margin-bottom: 0.4rem;
    }

    .user-info-value {
        color: #152033;
        font-size: 1rem;
        font-weight: 700;
        word-break: break-word;
    }

    .user-action-stack {
        display: flex;
        flex-wrap: wrap;
        gap: 0.85rem;
        margin-top: 1.5rem;
    }

    @media (max-width: 991.98px) {
        .user-profile-stats,
        .user-profile-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .user-profile-hero-inner,
        .user-profile-card,
        .user-profile-grid-card {
            padding: 1.15rem;
        }

        .user-profile-grid-head {
            flex-direction: column;
        }
    }
</style>

<div class="user-profile-page py-2">
    <div class="d-flex justify-content-between flex-md-nowrap align-items-center flex-wrap gap-3">
        <div class="d-block">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ $title }}s</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $user->full_name }}</li>
                </ol>
            </nav>
            <h2 class="h4 mb-0">Employee Profile</h2>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-gray-300 d-inline-flex align-items-center">
            <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Employees
        </a>
    </div>

    <section class="user-profile-hero">
        <div class="user-profile-hero-inner">
            <span class="user-profile-kicker">Admin View</span>
            <h1>{{ $user->full_name }}</h1>
            <p>Review the employee profile, contact details, assigned role, and keep the record updated from one place.</p>

            <div class="user-profile-stats">
                <div class="user-profile-stat">
                    <div class="user-profile-stat-label">Employee ID</div>
                    <div class="user-profile-stat-value">#{{ $user->id }}</div>
                </div>
                <div class="user-profile-stat">
                    <div class="user-profile-stat-label">Department</div>
                    <div class="user-profile-stat-value">{{ $user->job->department->name ?? 'Not assigned' }}</div>
                </div>
                    <div class="user-profile-stat">
                        <div class="user-profile-stat-label">Job Title</div>
                        <div class="user-profile-stat-value">{{ $user->is_admin ? 'System Admin' : ($user->job->title ?? 'Not assigned') }}</div>
                    </div>
                    <div class="user-profile-stat">
                        <div class="user-profile-stat-label">Profile Image</div>
                        <div class="user-profile-stat-value">{{ $user->profile_image ? 'Custom Upload' : 'Avatar Fallback' }}</div>
                    </div>
                </div>
            </div>
        </section>

    <div class="row g-4 align-items-start">
        <div class="col-12 col-xl-4">
            <section class="user-profile-card text-center">
                <div class="user-profile-avatar-wrap">
                    <img src="{{ $user->profile_image_url }}" alt="{{ $avatarName }}" class="user-profile-avatar">
                </div>

                <h3 class="h4 mb-2">{{ $avatarName }}</h3>
                <div class="user-role-pill">
                    {{ $user->is_admin ? 'System Admin' : 'Employee Record' }}
                </div>

                <div class="user-profile-meta text-start">
                    <div class="user-profile-meta-item">
                        <span class="user-profile-meta-label">Gender</span>
                        <div class="user-profile-meta-value">{{ (int) $user->gender === 1 ? 'Female' : 'Male' }}</div>
                    </div>
                    <div class="user-profile-meta-item">
                        <span class="user-profile-meta-label">Photo Style</span>
                        <div class="user-profile-meta-value">{{ $user->profile_image ? 'Uploaded profile image' : 'Auto-generated profile avatar' }}</div>
                    </div>
                    <div class="user-profile-meta-item">
                        <span class="user-profile-meta-label">Status</span>
                        <div class="user-profile-meta-value">{{ $user->is_admin ? 'Administrator Access' : 'Employee Account' }}</div>
                    </div>
                </div>

                <div class="user-action-stack justify-content-center">
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">Edit Information</a>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" id="delete-user-{{ $user->id }}">
                        @csrf
                        @method('DELETE')
                    </form>
                    <a href="javascript:void(0)" class="btn btn-outline-danger" onclick="Swal.fire({
                        title: 'Delete this employee?',
                        text: 'This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('delete-user-{{ $user->id }}').submit();
                        }
                    })">Delete</a>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-8">
            <section class="user-profile-grid-card">
                <div class="user-profile-grid-head">
                    <div>
                        <h2>Personal Information</h2>
                        <p>All key employee details are grouped here for faster admin review and editing.</p>
                    </div>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-primary">Open Edit Form</a>
                </div>

                <div class="user-profile-grid">
                    @foreach ($detailCards as $detail)
                        <div class="user-info-card">
                            <div class="user-info-label">{{ $detail['label'] }}</div>
                            <div class="user-info-value">{{ $detail['value'] ?: 'Not available' }}</div>
                        </div>
                    @endforeach

                    <div class="user-info-card">
                        <div class="user-info-label">First Name</div>
                        <div class="user-info-value">{{ $user->first_name ?: 'Not available' }}</div>
                    </div>

                    <div class="user-info-card">
                        <div class="user-info-label">Last Name</div>
                        <div class="user-info-value">{{ $user->last_name ?: 'Not available' }}</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
