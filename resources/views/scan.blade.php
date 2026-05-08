@extends('layouts.app')

@section('content')
<style>
    .qr-shell {
        max-width: 980px;
        margin: 0 auto;
    }

    .qr-hero-card,
    .qr-panel-card {
        border: 0;
        border-radius: 24px;
        box-shadow: 0 20px 45px rgba(17, 24, 39, 0.08);
    }

    .qr-hero-card {
        background:
            radial-gradient(circle at top left, rgba(14, 165, 233, 0.18), transparent 35%),
            linear-gradient(135deg, #ffffff 0%, #f5fbff 100%);
    }

    .qr-panel-card {
        background: #ffffff;
    }

    .qr-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border-radius: 999px;
        background: #eef6ff;
        color: #124076;
        font-weight: 600;
    }

    .qr-status-pill::before {
        content: "";
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #0d6efd;
        box-shadow: 0 0 0 6px rgba(13, 110, 253, 0.12);
    }

    .qr-reader-wrap {
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

    .qr-help-list {
        margin: 0;
        padding-left: 1rem;
        color: #5b6472;
    }
</style>

<div class="py-4 qr-shell">
    <div class="card qr-hero-card mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
                <div>
                    <span class="badge bg-info text-dark mb-3">QR Attendance</span>
                    <h2 class="h3 mb-2">Scan employee QR codes for login and logout</h2>
                    <p class="text-muted mb-0">Each valid scan alternates between login and logout. Duplicate reads within a few seconds are ignored automatically.</p>
                </div>
                @if (auth()->check() && auth()->user()->is_admin)
                    <form action="{{ route('qr.generate') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg px-4">
                            Generate Today’s QR
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-7">
            <div class="card qr-panel-card h-100">
                <div class="card-body p-4 p-lg-5 text-center">
                    <h3 class="h4 mb-3">Live Scanner</h3>
                    <p class="text-muted mb-4">Place the QR inside the frame and keep the camera steady for a moment.</p>
                    <div class="qr-reader-wrap mb-4">
                        <div id="reader"></div>
                    </div>
                    <div id="status" class="qr-status-pill">Waiting for scan...</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card qr-panel-card h-100">
                <div class="card-body p-4 p-lg-5">
                    <h3 class="h4 mb-3">How It Works</h3>
                    <ul class="qr-help-list mb-4">
                        <li>Generate today’s QR set once from this page.</li>
                        <li>First scan marks login attendance.</li>
                        <li>Next scan marks logout time.</li>
                        <li>After logout, the following scan starts login again.</li>
                        <li>Duplicate or invalid scans are handled automatically.</li>
                    </ul>

                    <div class="p-3 rounded-4 bg-light border">
                        <div class="small text-uppercase text-muted fw-bold mb-2">Current Session</div>
                        <div class="fw-semibold">Date: {{ now()->toFormattedDateString() }}</div>
                        <div class="text-muted small mt-1">Admin scanner is active for repeated check-in and check-out tracking.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    const statusElement = document.getElementById('status');
    let scanLock = false;

    function setStatus(message, tone = 'info') {
        const tones = {
            info: { background: '#eef6ff', color: '#124076', dot: '#0d6efd' },
            success: { background: '#ecfdf3', color: '#166534', dot: '#16a34a' },
            warning: { background: '#fff8e8', color: '#92400e', dot: '#f59e0b' },
            error: { background: '#fef2f2', color: '#991b1b', dot: '#ef4444' }
        };

        const theme = tones[tone] || tones.info;
        statusElement.innerText = message;
        statusElement.style.background = theme.background;
        statusElement.style.color = theme.color;
        statusElement.style.setProperty('--status-dot', theme.dot);
        statusElement.style.boxShadow = 'none';
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

            const message = data.message || 'Scan processed.';
            const tone = /already|duplicate/i.test(message) ? 'warning' : 'success';
            setStatus(message, tone);
            releaseScanLock();
        })
        .catch(() => {
            setStatus('Failed to send QR data.', 'error');
            releaseScanLock();
        });
    }

    const scanner = new Html5QrcodeScanner("reader", {
        fps: 10,
        qrbox: 250
    });

    scanner.render(onScanSuccess);
</script>
@endpush
