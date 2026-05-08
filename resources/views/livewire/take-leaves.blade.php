@extends('layouts.app') <!-- உங்கள் layout -->

@section('content')
    <h2>My Leave Requests</h2>

    <a href="{{ route('leave.create') }}" class="btn btn-primary mb-3">Apply for Leave</a>

    @if ($userLeaves->count() > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Number of Days</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($userLeaves as $leave)
                    <tr>
                        <td>{{ $leave->from_date }}</td>
                        <td>{{ $leave->to_date }}</td>
                        <td>{{ $leave->number_of_days }}</td>
                        <td>{{ $leave->reason }}</td>
                        <td>
                            @if ($leave->status == 'Pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif ($leave->status == 'Approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif ($leave->status == 'Rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination links -->
        {{ $userLeaves->links() }}
    @else
        <p>No leave requests found.</p>
    @endif
@endsection
