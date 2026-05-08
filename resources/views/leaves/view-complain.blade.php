@extends("layouts.app")
@php
$title = 'Leave Request';
@endphp
@section('title', $title)

@section('content')
<div class="d-flex justify-content-between flex-md-nowrap align-items-center flex-wrap py-4">
    <div class="d-block mb-md-0 mb-4">
        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
            <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">
                        <svg class="icon icon-xxs" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                    </a>
                </li>
                <li class="breadcrumb-item"><a href="{{ route('leave.index') }}">Leaves</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
            </ol>
        </nav>
        <h2 class="h4">{{ $title }}</h2>
    </div>
</div>

<div class="card card-body table-wrapper table-responsive border-0 shadow">
    <h3 class="text-muted mb-3 text-center">
        <span class="text-info">{{ $leave->user->name }}</span> Leave Request
    </h3>
    <h4 class="text-muted mb-3 text-center">
        From {{ \Carbon\Carbon::parse($leave->from_date)->format('l, F d, Y') }} 
        to {{ \Carbon\Carbon::parse($leave->to_date)->format('l, F d, Y') }}
    </h4>
    
    <div class="mt-4">
        <div class="row mb-2">
            <div class="col-3"><strong>Current Status:</strong></div>
            <div class="col-3 text-info">{{ $leave->status }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-3"><strong>Reason:</strong></div>
            <div class="col-6">{{ $leave->reason }}</div>
        </div>

        @if ($leave->attachment)
        <div class="row mb-2">
            <div class="col-3"><strong>Attachment:</strong></div>
            <div class="col-6">
                <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank">View Attachment</a>
            </div>
        </div>
        @endif

        <div class="mt-3 d-flex">
            <form action="{{ route('leave.updateStatus', $leave->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="Approved">
                <button type="submit" class="btn btn-sm btn-success me-2">Approve</button>
            </form>

            <form action="{{ route('leave.updateStatus', $leave->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="Rejected">
                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
            </form>
        </div>
    </div>
</div>
@endsection
