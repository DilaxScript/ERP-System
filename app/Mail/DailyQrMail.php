<?php

namespace App\Mail;

use App\Models\DailyQr;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DailyQrMail extends Mailable
{
    use Queueable, SerializesModels;

    public $qr;
    public $user;
    public $qrImageBase64;

    /**
     * Create a new message instance.
     */
    public function __construct(DailyQr $qr)
    {
        $this->qr = $qr;
        $this->user = $qr->user; // Ensure DailyQr has user() relationship

        // Generate QR as Base64 to use inline (optional)
        $this->qrImageBase64 = base64_encode(
            QrCode::format('png')->size(300)->generate($this->qr->token)
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $qrImageBinary = base64_decode($this->qrImageBase64);
        $purpose = $this->qr->purpose === DailyQr::PURPOSE_LOGOUT ? 'Logout' : 'Login';

        return $this->subject('Your ' . $purpose . ' QR Code')
            ->view('emails.daily_qr')
            ->with([
                'user' => $this->user,
                'qrImageBase64' => $this->qrImageBase64,
                'purpose' => strtolower($purpose),
            ])
            ->attachData($qrImageBinary, 'qrcode.png', [
                'mime' => 'image/png',
            ]);
    }
}
