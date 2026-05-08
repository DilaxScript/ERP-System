@extends('layouts.app')

@section('content')
    <h2>Attendance Report for {{ $date }}</h2>

    <form method="GET" action="{{ route('attendance.report') }}">
        <input type="date" name="date" value="{{ $date }}">
        <button type="submit">View</button>

        {{-- ✅ PDF Download Button --}}
        <a href="{{ route('attendance.report.pdf', ['date' => $date]) }}" target="_blank" style="margin-left: 10px;">
            <button type="button">Download PDF</button>
        </a>
    </form>

    <table border="1" cellpadding="10" style="margin-top: 20px; width: 100%;">
        <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Email</th>
                <th>Date</th>
                <th>Status</th>
                <th>Login Time</th>
                <th>Logout Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $i => $attendance)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $attendance->user->full_name }}</td>
                    <td>{{ $attendance->user->email }}</td>
                    <td>{{ $attendance->date }}</td>
                    <td>{{ $attendance->status_text }}</td>
                    <td>{{ $attendance->login_time_display ? $attendance->login_time_display->format('H:i:s') : '-' }}</td>
                    <td>{{ $attendance->logout_time ? $attendance->logout_time->format('H:i:s') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No attendance found for {{ $date }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
