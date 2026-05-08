@extends('layouts.app')

@section('title', 'Edit ' . $title)

@section('content')
@php
    $avatarName = $user->first_name ? $user->full_name : 'User Name';
    $genderOptions = [
        0 => 'Male',
        1 => 'Female',
    ];
@endphp

<style>
    .user-edit-page {
        display: grid;
        gap: 1.5rem;
    }

    .user-edit-hero,
    .user-edit-card,
    .user-edit-form-card {
        border-radius: 28px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
    }

    .user-edit-hero {
        overflow: hidden;
        background:
            radial-gradient(circle at top left, rgba(31, 117, 254, 0.2), transparent 28%),
            radial-gradient(circle at right, rgba(244, 185, 66, 0.16), transparent 24%),
            linear-gradient(135deg, #ffffff 0%, #f6fbff 100%);
    }

    .user-edit-hero-inner,
    .user-edit-card,
    .user-edit-form-card {
        padding: 1.5rem;
    }

    .user-edit-kicker {
        display: inline-flex;
        align-items: center;
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        background: rgba(23, 53, 95, 0.08);
        color: #17355f;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .user-edit-hero h1 {
        margin: 1rem 0 0.55rem;
        font-size: clamp(2rem, 3vw, 2.6rem);
        color: #152033;
        font-weight: 800;
    }

    .user-edit-hero p {
        margin: 0;
        max-width: 680px;
        color: #667085;
    }

    .user-edit-avatar-wrap {
        width: 118px;
        height: 118px;
        margin: 0 auto 1rem;
        padding: 6px;
        border-radius: 30px;
        background: linear-gradient(135deg, #17355f 0%, #1f75fe 60%, #f4b942 100%);
        box-shadow: 0 18px 34px rgba(23, 53, 95, 0.16);
    }

    .user-edit-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 26px;
        background: #fff;
    }

    .user-edit-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        background: rgba(31, 117, 254, 0.1);
        color: #1f5ebf;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .user-edit-side-list {
        display: grid;
        gap: 0.85rem;
        margin-top: 1.25rem;
    }

    .user-edit-side-item {
        padding: 0.95rem 1rem;
        border-radius: 18px;
        background: #f8fbff;
        border: 1px solid rgba(148, 163, 184, 0.16);
    }

    .user-edit-side-label {
        display: block;
        font-size: 0.75rem;
        color: #7b8aa5;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }

    .user-edit-side-value {
        color: #152033;
        font-weight: 700;
    }

    .user-edit-form-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .user-edit-form-head h2 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 800;
        color: #152033;
    }

    .user-edit-form-head p {
        margin: 0.35rem 0 0;
        color: #667085;
    }

    .user-edit-group-title {
        margin: 0 0 0.9rem;
        font-size: 0.88rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #17355f;
        font-weight: 800;
    }

    .user-edit-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #667085;
        margin-bottom: 0.55rem;
    }

    .user-edit-input,
    .user-edit-form-card .form-select {
        min-height: 52px;
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.3);
        background: #fbfdff;
        box-shadow: none;
    }

    .user-edit-input:focus,
    .user-edit-form-card .form-select:focus {
        border-color: rgba(31, 117, 254, 0.55);
        box-shadow: 0 0 0 0.2rem rgba(31, 117, 254, 0.12);
        background: #fff;
    }

    .user-edit-note {
        color: #667085;
        font-size: 0.9rem;
        margin-top: 0.45rem;
    }

    .user-edit-error {
        margin-top: 0.5rem;
        color: #d63384;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .user-edit-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.85rem;
        margin-top: 1.5rem;
    }

    .user-edit-save {
        min-height: 54px;
        border: 0;
        border-radius: 18px;
        font-weight: 800;
        padding: 0.95rem 1.35rem;
        background: linear-gradient(135deg, #17355f 0%, #1f75fe 100%);
        box-shadow: 0 18px 30px rgba(23, 53, 95, 0.2);
    }

    @media (max-width: 767.98px) {
        .user-edit-hero-inner,
        .user-edit-card,
        .user-edit-form-card {
            padding: 1.15rem;
        }

        .user-edit-form-head {
            flex-direction: column;
        }
    }
</style>

<div class="user-edit-page py-2">
    <div class="d-flex justify-content-between flex-wrap align-items-center gap-3">
        <div class="d-block">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ $title }}s</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.show', $user->id) }}">{{ $user->full_name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
            <h2 class="h4 mb-0">Edit Employee Profile</h2>
        </div>
        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-gray-300 d-inline-flex align-items-center">
            <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Profile
        </a>
    </div>

    <section class="user-edit-hero">
        <div class="user-edit-hero-inner">
            <span class="user-edit-kicker">Admin Editor</span>
            <h1>Update {{ $user->full_name }}</h1>
            <p>Edit personal details, role assignment, and account access information from a cleaner admin form.</p>
        </div>
    </section>

    <div class="row g-4 align-items-start">
        <div class="col-12 col-xl-4">
            <section class="user-edit-card text-center">
                <div class="user-edit-avatar-wrap">
                    <img src="{{ $user->profile_image_url }}" alt="{{ $avatarName }}" class="user-edit-avatar">
                </div>
                <h3 class="h4 mb-2">{{ $avatarName }}</h3>
                <div class="user-edit-chip">{{ $user->is_admin ? 'System Admin' : 'Employee Record' }}</div>

                <div class="user-edit-side-list text-start">
                    <div class="user-edit-side-item">
                        <span class="user-edit-side-label">Current Job</span>
                        <div class="user-edit-side-value">{{ $user->job->title ?? 'Not assigned' }}</div>
                    </div>
                    <div class="user-edit-side-item">
                        <span class="user-edit-side-label">Department</span>
                        <div class="user-edit-side-value">{{ $user->job->department->name ?? 'Not assigned' }}</div>
                    </div>
                    <div class="user-edit-side-item">
                        <span class="user-edit-side-label">Email</span>
                        <div class="user-edit-side-value">{{ $user->email }}</div>
                    </div>
                    <div class="user-edit-side-item">
                        <span class="user-edit-side-label">Profile Image</span>
                        <div class="user-edit-side-value">{{ $user->profile_image ? 'Custom image uploaded' : 'Avatar is generated from the employee name.' }}</div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-8">
            <section class="user-edit-form-card">
                <div class="user-edit-form-head">
                    <div>
                        <h2>Employee Information</h2>
                        <p>Leave password blank if you do not want to change it.</p>
                    </div>
                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-primary">Preview Profile</a>
                </div>

                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="row g-4" data-profile-image-form>
                    @csrf
                    @method('PUT')

                    <div class="col-12">
                        <div class="user-edit-group-title">Basic Details</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="first_name" class="form-label user-edit-label">First Name</label>
                        <input type="text" class="form-control user-edit-input @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}">
                        @error('first_name')
                            <div class="user-edit-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="last_name" class="form-label user-edit-label">Last Name</label>
                        <input type="text" class="form-control user-edit-input @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                        @error('last_name')
                            <div class="user-edit-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label user-edit-label">Email</label>
                        <input type="email" class="form-control user-edit-input @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}">
                        @error('email')
                            <div class="user-edit-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label user-edit-label">Password</label>
                        <input type="password" class="form-control user-edit-input @error('password') is-invalid @enderror" id="password" name="password" placeholder="Leave blank to keep current password">
                        <div class="user-edit-note">Minimum 6 characters only when changing password.</div>
                        @error('password')
                            <div class="user-edit-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="profile_image" class="form-label user-edit-label">Profile Image</label>
                        <input type="file" class="form-control user-edit-input @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <div class="user-edit-note">Upload JPG, PNG, or WEBP up to 2 MB. If not uploaded, avatar will be used.</div>
                        @error('profile_image')
                            <div class="user-edit-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="user-edit-group-title">Work Details</div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="job" class="form-label user-edit-label">Job</label>
                        <select class="form-select @error('job') is-invalid @enderror" name="job" id="job">
                            <option value="">Select Job</option>
                            @foreach ($jobs as $key => $value)
                                <option value="{{ $key }}" @selected(old('job', $user->job_id) == $key)>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('job')
                            <div class="user-edit-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="gender" class="form-label user-edit-label">Gender</label>
                        <select class="form-select @error('gender') is-invalid @enderror" name="gender" id="gender">
                            <option value="">Select Gender</option>
                            @foreach ($genderOptions as $key => $value)
                                <option value="{{ $key }}" @selected((string) old('gender', $user->gender) === (string) $key)>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('gender')
                            <div class="user-edit-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="sallary" class="form-label user-edit-label">Salary</label>
                        <input type="number" class="form-control user-edit-input @error('sallary') is-invalid @enderror" id="sallary" name="sallary" value="{{ old('sallary', $user->sallary) }}">
                        @error('sallary')
                            <div class="user-edit-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="user-edit-group-title">Contact Details</div>
                    </div>

                    <div class="col-12 col-md-7">
                        <label for="address" class="form-label user-edit-label">Address</label>
                        <input type="text" class="form-control user-edit-input @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address) }}">
                        @error('address')
                            <div class="user-edit-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-5">
                        <label for="number" class="form-label user-edit-label">Phone Number</label>
                        <input type="text" class="form-control user-edit-input @error('number') is-invalid @enderror" id="number" name="number" value="{{ old('number', $user->number) }}">
                        @error('number')
                            <div class="user-edit-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="user-edit-actions">
                            <button class="btn btn-primary user-edit-save" type="submit">Save Changes</button>
                            <button class="btn btn-gray-300" type="reset">Reset Form</button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('[data-profile-image-form]');
        const input = document.getElementById('profile_image');
        const maxFileSize = 2 * 1024 * 1024;

        if (!form || !input) {
            return;
        }

        form.addEventListener('submit', function (event) {
            const file = input.files && input.files[0];

            if (file && file.size > maxFileSize) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Image too large',
                    text: 'Please upload a profile image smaller than 2 MB.',
                });
            }
        });
    });
</script>
@endsection
