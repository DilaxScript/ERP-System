@extends('layouts.app')

@php
    use Carbon\Carbon;

    $today = Carbon::today();
    $allLeaves = $userLeaves->getCollection();
    $pendingCount = $allLeaves->where('status', 'Pending')->count();
    $approvedDays = $allLeaves->where('status', 'Approved')->sum('number_of_days');
    $latestRequest = $allLeaves->first();
@endphp

@section('title', 'My Leaves')

@section('content')
<style>
    .leave-page {
        display: grid;
        gap: 1.5rem;
    }

    .leave-hero,
    .leave-form-card,
    .leave-history-card {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
    }

    .leave-hero {
        overflow: hidden;
        background:
            radial-gradient(circle at top left, rgba(31, 117, 254, 0.18), transparent 28%),
            radial-gradient(circle at right, rgba(244, 185, 66, 0.18), transparent 24%),
            linear-gradient(135deg, #ffffff 0%, #f6fbff 100%);
    }

    .leave-hero-panel {
        padding: 1.75rem;
    }

    .leave-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        background: rgba(23, 53, 95, 0.08);
        color: #17355f;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .leave-hero h1 {
        font-size: clamp(1.9rem, 3vw, 2.7rem);
        line-height: 1.1;
        margin: 1rem 0 0.75rem;
        color: #152033;
    }

    .leave-hero p {
        margin: 0;
        color: #667085;
        max-width: 640px;
    }

    .leave-quick-stats {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-top: 1.5rem;
    }

    .leave-stat {
        padding: 1rem 1.1rem;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid rgba(148, 163, 184, 0.18);
    }

    .leave-stat-label {
        font-size: 0.74rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #7b8aa5;
        margin-bottom: 0.45rem;
        font-weight: 700;
    }

    .leave-stat-value {
        font-size: 1.7rem;
        font-weight: 800;
        color: #152033;
        line-height: 1;
    }

    .leave-stat-note {
        margin-top: 0.4rem;
        color: #667085;
        font-size: 0.92rem;
    }

    .leave-form-card,
    .leave-history-card {
        padding: 1.5rem;
    }

    .leave-section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .leave-section-head h2 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 800;
        color: #152033;
    }

    .leave-section-head p {
        margin: 0.35rem 0 0;
        color: #667085;
    }

    .leave-date-chip {
        padding: 0.55rem 0.9rem;
        border-radius: 999px;
        background: #f8fbff;
        border: 1px solid rgba(31, 117, 254, 0.14);
        color: #17355f;
        font-weight: 700;
        white-space: nowrap;
    }

    .leave-alert {
        border-radius: 18px;
        padding: 1rem 1.1rem;
        border: 1px solid transparent;
        margin-bottom: 1.25rem;
    }

    .leave-alert ul {
        margin: 0.65rem 0 0;
        padding-left: 1.2rem;
    }

    .leave-alert.is-danger {
        background: rgba(220, 53, 69, 0.08);
        border-color: rgba(220, 53, 69, 0.16);
        color: #a61e2f;
    }

    .leave-alert.is-success {
        background: rgba(25, 135, 84, 0.08);
        border-color: rgba(25, 135, 84, 0.16);
        color: #146c43;
    }

    .leave-form-label {
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #667085;
        margin-bottom: 0.55rem;
    }

    .leave-form-control,
    .leave-form-card .form-select {
        min-height: 52px;
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.3);
        background: #fbfdff;
        box-shadow: none;
    }

    .leave-form-control:focus,
    .leave-form-card .form-select:focus {
        border-color: rgba(31, 117, 254, 0.55);
        box-shadow: 0 0 0 0.2rem rgba(31, 117, 254, 0.12);
        background: #fff;
    }

    .leave-form-card textarea.leave-form-control {
        min-height: 156px;
        resize: vertical;
    }

    .leave-form-note {
        color: #667085;
        font-size: 0.9rem;
        margin-top: 0.45rem;
    }

    .leave-form-error {
        margin-top: 0.5rem;
        color: #d63384;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .leave-submit-btn {
        min-height: 54px;
        border: 0;
        border-radius: 18px;
        font-weight: 800;
        padding: 0.95rem 1.25rem;
        background: linear-gradient(135deg, #17355f 0%, #1f75fe 100%);
        box-shadow: 0 18px 30px rgba(23, 53, 95, 0.2);
    }

    .leave-submit-btn:hover {
        filter: brightness(1.03);
        transform: translateY(-1px);
    }

    .leave-table-wrap {
        overflow-x: auto;
    }

    .leave-table {
        margin-bottom: 0;
    }

    .leave-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .leave-status-badge {
        border-radius: 999px;
        padding: 0.45rem 0.8rem;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .leave-status-badge.is-pending {
        background: rgba(255, 193, 7, 0.18);
        color: #9a6700;
    }

    .leave-status-badge.is-approved {
        background: rgba(25, 135, 84, 0.14);
        color: #146c43;
    }

    .leave-status-badge.is-rejected {
        background: rgba(220, 53, 69, 0.12);
        color: #b42318;
    }

    .leave-empty-state {
        padding: 2.5rem 1rem;
        text-align: center;
        color: #667085;
    }

    @media (max-width: 991.98px) {
        .leave-quick-stats {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .leave-hero-panel,
        .leave-form-card,
        .leave-history-card {
            padding: 1.15rem;
        }

        .leave-section-head {
            flex-direction: column;
        }

        .leave-date-chip {
            white-space: normal;
        }
    }
</style>

<div class="leave-page py-2">
    <section class="leave-hero">
        <div class="leave-hero-panel">
            <span class="leave-kicker">Leave Management</span>
            <h1>Apply for leave without losing track of your requests.</h1>
            <p>Submit a clean request, attach supporting files when needed, and review every status update from one page.</p>

            <div class="leave-quick-stats">
                <div class="leave-stat">
                    <div class="leave-stat-label">Pending Requests</div>
                    <div class="leave-stat-value">{{ $pendingCount }}</div>
                    <div class="leave-stat-note">Requests currently waiting for review.</div>
                </div>
                <div class="leave-stat">
                    <div class="leave-stat-label">Approved Days</div>
                    <div class="leave-stat-value">{{ $approvedDays }}</div>
                    <div class="leave-stat-note">Approved leave days shown on this page.</div>
                </div>
                <div class="leave-stat">
                    <div class="leave-stat-label">Latest Request</div>
                    <div class="leave-stat-value">{{ $latestRequest ? Carbon::parse($latestRequest->from_date)->format('d M') : '--' }}</div>
                    <div class="leave-stat-note">{{ $latestRequest ? $latestRequest->status . ' request most recently submitted.' : 'No requests submitted yet.' }}</div>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-4 align-items-start">
        <div class="col-12 col-xl-5">
            <section class="leave-form-card h-100">
                <div class="leave-section-head">
                    <div>
                        <h2>New Leave Request</h2>
                        <p>Choose a date range, add a short reason, and upload proof if your request needs it.</p>
                    </div>
                    <div class="leave-date-chip">{{ $today->format('l, d M Y') }}</div>
                </div>

                @if ($errors->any())
                    <div class="leave-alert is-danger">
                        <strong>Submission failed.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('message'))
                    <div class="leave-alert is-success">
                        <strong>{{ session('title', 'Success') }}.</strong> {{ session('message') }}
                    </div>
                @endif

                <form action="{{ route('leave.store') }}" method="POST" enctype="multipart/form-data" class="row g-4">
                    @csrf

                    <div class="col-12 col-md-6 col-xl-12">
                        <label for="from_date" class="form-label leave-form-label">From Date</label>
                        <input
                            type="date"
                            name="from_date"
                            id="from_date"
                            value="{{ old('from_date') }}"
                            min="{{ $today->toDateString() }}"
                            class="form-control leave-form-control @error('from_date') is-invalid @enderror"
                            required
                        >
                        @error('from_date')
                            <div class="leave-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 col-xl-12">
                        <label for="to_date" class="form-label leave-form-label">To Date</label>
                        <input
                            type="date"
                            name="to_date"
                            id="to_date"
                            value="{{ old('to_date') }}"
                            min="{{ old('from_date', $today->toDateString()) }}"
                            class="form-control leave-form-control @error('to_date') is-invalid @enderror"
                            required
                        >
                        @error('to_date')
                            <div class="leave-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="reason" class="form-label leave-form-label">Reason</label>
                        <textarea
                            name="reason"
                            id="reason"
                            class="form-control leave-form-control @error('reason') is-invalid @enderror"
                            placeholder="Briefly explain why you need the leave."
                            required
                        >{{ old('reason') }}</textarea>
                        <div class="leave-form-note">Keep it short and clear so approval is faster.</div>
                        @error('reason')
                            <div class="leave-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="attachment" class="form-label leave-form-label">Attachment</label>
                        <input
                            type="file"
                            name="attachment"
                            id="attachment"
                            accept="image/*,application/pdf"
                            class="form-control leave-form-control @error('attachment') is-invalid @enderror"
                        >
                        <div class="leave-form-note">Optional. Accepted: JPG, PNG, PDF up to 2 MB.</div>
                        @error('attachment')
                            <div class="leave-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100 leave-submit-btn">Submit Leave Request</button>
                    </div>
                </form>
            </section>
        </div>

        <div class="col-12 col-xl-7">
            <section class="leave-history-card">
                <div class="leave-section-head">
                    <div>
                        <h2>Your Leave History</h2>
                        <p>Review your recent requests, duration, status, and attachments.</p>
                    </div>
                    <div class="leave-date-chip">{{ $userLeaves->total() }} total request{{ $userLeaves->total() === 1 ? '' : 's' }}</div>
                </div>

                @if ($userLeaves->count())
                    <div class="leave-table-wrap">
                        <table class="table align-items-center leave-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Period</th>
                                    <th>Days</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Attachment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userLeaves as $leave)
                                    @php
                                        $statusClass = match ($leave->status) {
                                            'Approved' => 'is-approved',
                                            'Rejected' => 'is-rejected',
                                            default => 'is-pending',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">{{ $userLeaves->firstItem() + $loop->index }}</td>
                                        <td>
                                            <div class="fw-bold">{{ Carbon::parse($leave->from_date)->format('d M Y') }}</div>
                                            <div class="text-muted small">to {{ Carbon::parse($leave->to_date)->format('d M Y') }}</div>
                                        </td>
                                        <td>{{ $leave->number_of_days }}</td>
                                        <td>{{ $leave->reason }}</td>
                                        <td>
                                            <span class="leave-status-badge {{ $statusClass }}">{{ $leave->status }}</span>
                                        </td>
                                        <td>
                                            @if ($leave->attachment)
                                                <a href="{{ $leave->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    View File
                                                </a>
                                            @else
                                                <span class="text-muted">No file</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-4">
                        {{ $userLeaves->links() }}
                    </div>
                @else
                    <div class="leave-empty-state">
                        <h3 class="h5 mb-2">No leave requests yet</h3>
                        <p class="mb-0">Once you submit a request, it will appear here with status and attachment details.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
