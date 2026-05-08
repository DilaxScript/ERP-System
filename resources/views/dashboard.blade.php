@extends("layouts.app")
@section('title', 'Dashboard')
@section('content')
@php
    $usersCount = $usersCount ?? 0;
    $deptsCount = $deptsCount ?? 0;
    $jobsCount = $jobsCount ?? 0;
    $users = $users ?? collect();
@endphp
<style>
    .dashboard-shell {
        display: grid;
        gap: 1.25rem;
    }

    .dashboard-hero {
        padding: 1.75rem;
        border-radius: 28px;
        background:
            radial-gradient(circle at top left, rgba(31, 117, 254, 0.18), transparent 30%),
            radial-gradient(circle at right, rgba(244, 185, 66, 0.16), transparent 26%),
            linear-gradient(135deg, #ffffff 0%, #f6fbff 100%);
        border: 1px solid rgba(148, 163, 184, 0.18);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
    }

    .dashboard-stat-card {
        height: 100%;
        border-radius: 24px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: rgba(255, 255, 255, 0.88);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
    }

    .dashboard-stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        box-shadow: 0 16px 26px rgba(15, 23, 42, 0.14);
    }

    .dashboard-stat-icon.is-blue { background: linear-gradient(135deg, #1f75fe 0%, #4ba3ff 100%); }
    .dashboard-stat-icon.is-gold { background: linear-gradient(135deg, #f4b942 0%, #ffcf77 100%); }
    .dashboard-stat-icon.is-navy { background: linear-gradient(135deg, #17355f 0%, #29548f 100%); }

    .dashboard-table-card {
        border-radius: 28px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        overflow: hidden;
        background: rgba(255, 255, 255, 0.88);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
    }

</style>

<div class="dashboard-shell py-2">
    <div class="dashboard-hero">
        <div class="row align-items-center g-4">
            <div class="col-12 col-xl-8">
                <span class="badge bg-info text-dark mb-3">Today Overview</span>
                <h1 class="h2 mb-2">Attendance operations at a glance</h1>
                <p class="text-muted mb-0">Monitor employees, keep the organization structure current, and jump directly into attendance or QR workflows without digging through the menu.</p>
            </div>
            <div class="col-12 col-xl-4">
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('qr.scan.ui') }}" class="btn btn-primary btn-lg">Open QR Scanner</a>
                    <a href="{{ route('attendances.index') }}" class="btn btn-outline-dark">View Attendance Records</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card dashboard-stat-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-2">Employees</div>
                            <div class="display-6 fw-bold mb-1">{{ $usersCount }}</div>
                            <div class="text-muted">Active employee records</div>
                        </div>
                        <div class="dashboard-stat-icon is-blue">
                            <svg class="icon" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" /></svg>
                        </div>
                    </div>
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary">Manage Employees</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card dashboard-stat-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-2">Departments</div>
                            <div class="display-6 fw-bold mb-1">{{ $deptsCount }}</div>
                            <div class="text-muted">Organizational units</div>
                        </div>
                        <div class="dashboard-stat-icon is-gold">
                            <svg class="icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" /></svg>
                        </div>
                    </div>
                    <a href="{{ route('departments.index') }}" class="btn btn-sm btn-outline-dark">View Departments</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card dashboard-stat-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <div class="text-uppercase small fw-bold text-muted mb-2">Jobs</div>
                            <div class="display-6 fw-bold mb-1">{{ $jobsCount }}</div>
                            <div class="text-muted">Roles currently defined</div>
                        </div>
                        <div class="dashboard-stat-icon is-navy">
                            <svg class="icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        </div>
                    </div>
                    <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-outline-dark">Review Job Titles</a>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-table-card">
        <div class="card-header bg-transparent border-0 p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fs-4 fw-bold mb-1">Recent Employees</h2>
                    <div class="text-muted">Newest employee records in the system.</div>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-primary">See All Employees</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Job</th>
                        <th>Department</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td class="fw-bold">{{ $loop->iteration }}</td>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td class="text-success">{{ $user->job->title ?? 'Not assigned' }}</td>
                        <td class="text-info">{{ $user->job->department->name ?? 'Not assigned' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">No employees found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
