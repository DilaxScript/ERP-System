<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Attendance Report - {{ $date }}</title>
    <style>
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Attendance Report - {{ $date }}</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Email</th>
                <th>Status</th>
                <th>Login Time</th>
                <th>Logout Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $i => $attendance)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $attendance->user->full_name }}</td>
                <td>{{ $attendance->user->email }}</td>
                <td>{{ $attendance->status_text }}</td>
                <td>{{ $attendance->login_time_display ? $attendance->login_time_display->format('H:i:s') : '-' }}</td>
                <td>{{ $attendance->logout_time ? $attendance->logout_time->format('H:i:s') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
