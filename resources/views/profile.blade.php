@extends('layouts.app')

@section("content")

@php
    $user = auth()->user();
@endphp

<style>
    .profile-page-avatar {
        width: 132px;
        height: 132px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12);
    }
</style>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 p-0 text-center shadow">

            
                {{-- Profile Cover --}}
                <div class="profile-cover rounded-top" style="background-image: url('../assets/img/profile-cover.jpg'); height: 180px;"></div>

                {{-- Card Body --}}
                <div class="card-body pb-5">

                    {{-- Avatar --}}
                    <img class="profile-page-avatar mt-n7 mx-auto mb-4"
                        src="{{ $user->profile_image_url }}"
                        alt="{{ $user->first_name ? $user->full_name : 'User Name' }}">

                    {{-- Name --}}
                    <h4 class="h3">
                        {{ $user->first_name ? $user->full_name : 'User Name' }}
                    </h4>

                    {{-- Role and Address --}}
                    @if ($user->is_admin)
                        <h5 class="fw-normal">System Admin</h5>
                        <p class="text-gray mb-4">{{ $user->profile_image ? 'Custom profile image active' : 'Avatar fallback active' }}</p>
                    @elseif ($user->job)
                        <h5 class="fw-normal">{{ $user->job->title }}</h5>
                        <p class="text-gray mb-4">{{ $user->address ?? 'No address added' }}</p>
                    @else
                        <h5 class="fw-normal text-muted">No Job Assigned</h5>
                        <p class="text-gray mb-4">{{ $user->address ?? 'No address added' }}</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
