@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h2>✅ {{ ($mode ?? 'login') === 'logout' ? 'Logout Marked Successfully' : 'Login Marked Successfully' }}</h2>
    <p>{{ $user->full_name }} {{ ($mode ?? 'login') === 'logout' ? 'has been checked out for today.' : 'has been checked in for today.' }}</p>
</div>
@endsection
