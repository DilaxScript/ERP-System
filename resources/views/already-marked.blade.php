@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h2>Attendance Scan Notice</h2>
    <p>{{ $message ?? ($user->full_name . ' has already completed both check-in and check-out for today.') }}</p>
</div>
@endsection
