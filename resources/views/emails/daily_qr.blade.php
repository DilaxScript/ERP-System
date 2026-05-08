<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your {{ ucfirst($purpose ?? 'login') }} QR Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            padding: 20px;
        }
        .container {
            background-color: white;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        h2 {
            color: #4CAF50;
        }
        img {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hi {{ $user->first_name }} 👋</h2>

        @if (($purpose ?? 'login') === 'logout')
            <p>Your login was marked successfully. This QR is for logout only. Please scan this QR when you leave.</p>
        @else
            <p>This is your login QR code for today. Please scan it to mark attendance login.</p>
        @endif

        <p style="text-align: center;">
            <img src="data:image/png;base64,{{ $qrImageBase64 }}" alt="QR Code" width="200" height="200">
        </p>

        <p>
            @if (($purpose ?? 'login') === 'logout')
                Keep this QR safe until logout time.
            @else
                After login, a separate logout QR will be sent to your email automatically.
            @endif
            <br><strong>– Attendance System</strong>
        </p>
    </div>
</body>
</html>
