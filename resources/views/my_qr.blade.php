@extends('layouts.app')

@section('content')
    <h2>My QR Code ({{ now()->toDateString() }})</h2>

    @if ($qr)
        <div style="margin: 30px 0;">
            {!! QrCode::size(250)->generate($qr->token) !!}
        </div>
    @else
        <p>No QR code generated for today.</p>
    @endif
@endsection
