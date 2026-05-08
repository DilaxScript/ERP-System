<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\DailyQr;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyQrMail;
use Illuminate\Support\Str;

class GenerateDailyQRCodes extends Command
{
    // Artisan command signature
    protected $signature = 'generate:daily-qr';

    // Command description
    protected $description = 'Generate daily QR codes for all users and email them';

    public function handle()
    {
        $today = now()->toDateString();
        $users = User::where('is_admin', 0)->get();

        foreach ($users as $user) {
            DailyQr::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->where('purpose', DailyQr::PURPOSE_LOGOUT)
                ->delete();

            $qr = DailyQr::updateOrCreate([
                'user_id' => $user->id,
                'date' => $today,
                'purpose' => DailyQr::PURPOSE_LOGIN,
            ], [
                'token' => Str::uuid()->toString(),
                'consumed_at' => null,
            ]);

            Mail::to($user->email)->send(new DailyQrMail($qr));
        }

        $this->info('✅ Daily QR codes generated and emailed to all users.');

        return self::SUCCESS;
    }
}
