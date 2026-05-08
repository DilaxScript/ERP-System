<?php

namespace App\Http\Controllers;

use App\Mail\DailyQrMail;
use App\Models\DailyQr;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class QrController extends Controller
{
    public function generate(Request $request)
    {
        $users = User::where('is_admin', 0)->get();
        $date = now()->toDateString();

        foreach ($users as $user) {
            DailyQr::where('user_id', $user->id)
                ->whereDate('date', $date)
                ->where('purpose', DailyQr::PURPOSE_LOGOUT)
                ->delete();

            $qr = DailyQr::updateOrCreate([
                'user_id' => $user->id,
                'date' => $date,
                'purpose' => DailyQr::PURPOSE_LOGIN,
            ], [
                'token' => Str::uuid()->toString(),
                'consumed_at' => null,
            ]);

            Mail::to($user->email)->send(new DailyQrMail($qr));
        }

        return back()->with([
            'icon' => 'success',
            'title' => 'QR Generated!',
            'message' => 'QR code generated and emailed to all employees.'
        ]);
    }
}
