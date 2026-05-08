@extends('layouts.app')

@section('content')
<style>
    .employee-qr-shell {
        max-width: 1040px;
        margin: 0 auto;
    }

    .employee-qr-card,
    .employee-qr-side {
        border: 0;
        border-radius: 24px;
        box-shadow: 0 20px 45px rgba(17, 24, 39, 0.08);
    }

    .employee-qr-card {
        background:
            radial-gradient(circle at top right, rgba(34, 197, 94, 0.16), transparent 32%),
            linear-gradient(180deg, #ffffff 0%, #f8fff9 100%);
    }

    .employee-qr-code-box {
        display: inline-block;
        padding: 22px;
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
    }

    .employee-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border-radius: 999px;
        background: #eef6ff;
        color: #124076;
        font-weight: 600;
    }

    .employee-status-pill::before {
        content: "";
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #0d6efd;
        box-shadow: 0 0 0 6px rgba(13, 110, 253, 0.12);
    }

    .employee-reader-wrap {
        max-width: 340px;
        margin: 0 auto;
        padding: 12px;
        border-radius: 20px;
        background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        border: 1px solid #dbe9ff;
    }

    #reader {
        overflow: hidden;
        border-radius: 16px;
    }
</style>

<div class="py-4 employee-qr-shell">
    @if ($qr)
        <div class="row g-4 align-items-stretch">
            <div class="col-12 col-xl-7">
                <div class="card employee-qr-card h-100">
                    <div class="card-body p-4 p-lg-5 text-center">
                        <span class="badge bg-success mb-3">Today’s Attendance Pass</span>
                        <h2 class="h3 mb-2">My QR Code</h2>
                        <p class="text-muted mb-4">Use the same QR for every login and logout scan during the day.</p>

                        <div class="employee-qr-code-box mb-4">
                            {!! QrCode::size(250)->generate($qr->token) !!}
                        </div>

                        <div class="small text-muted">
                            Valid for {{ now()->toFormattedDateString() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card employee-qr-side h-100">
                    <div class="card-body p-4 p-lg-5 text-center">
                        <h3 class="h4 mb-2">Self Scan</h3>
                        <p class="text-muted mb-4">Every valid scan alternates between login and logout. Fast duplicate reads are ignored.</p>

                        <div class="employee-reader-wrap mb-4">
                            <div id="reader"></div>
                        </div>

                        <div id="status" class="employee-status-pill">Waiting for scan...</div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card employee-qr-side">
            <div class="card-body p-5 text-center">
                <span class="badge bg-warning text-dark mb-3">No QR Yet</span>
                <h2 class="h3 mb-2">No QR code generated for today</h2>
                <p class="text-muted mb-0">Please ask an administrator to generate today’s attendance QR set.</p>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
const statusElement = document.getElementById('status');
let scanLock = false;

function setStatus(message, tone = 'info') {
    if (!statusElement) return;

    const tones = {
        info: { background: '#eef6ff', color: '#124076' },
        success: { background: '#ecfdf3', color: '#166534' },
        warning: { background: '#fff8e8', color: '#92400e' },
        error: { background: '#fef2f2', color: '#991b1b' }
    };

    const theme = tones[tone] || tones.info;
    statusElement.innerText = message;
    statusElement.style.background = theme.background;
    statusElement.style.color = theme.color;
}

function releaseScanLock(delay = 3000) {
    window.setTimeout(() => {
        scanLock = false;
    }, delay);
}

function onScanSuccess(decodedText, decodedResult) {
    if (scanLock) {
        return;
    }

    scanLock = true;
    new Audio('https://www.soundjay.com/button/beep-07.wav').play();
    setStatus('Processing scanned QR...', 'info');

    fetch("{{ route('qr.scan') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
        },
        body: JSON.stringify({ token: decodedText })
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            setStatus(data.error, 'error');
            releaseScanLock();
            return;
        }

        const message = data.message || 'Scan complete.';
        const tone = /already|duplicate/i.test(message) ? 'warning' : 'success';
        setStatus(message, tone);
        releaseScanLock();
    })
    .catch(() => {
        setStatus('Error sending scan data.', 'error');
        releaseScanLock();
    });
}

const readerElement = document.getElementById('reader');
if (readerElement) {
    const scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    scanner.render(onScanSuccess);
}
</script>
@endpush
