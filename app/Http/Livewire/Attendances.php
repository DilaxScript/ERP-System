<?php

namespace App\Http\Livewire;

use App\Mail\DailyQrMail;
use App\Models\Attendance;
use App\Models\DailyQr;
use App\Models\User;
use App\Notifications\EmployeeAttendanceNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Attendances extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $disableActions = false;

    public function mount()
    {

        // $this->users = $users;
    }

    public function render()
    {
        $users = User::query()->with([
            "attendance" => function ($q) {
                $q->whereDate("created_at", Carbon::today())->get();
            }
        ])->whereIsAdmin(0)->paginate(15);
        return view('livewire.attendances', compact("users"));
    }

    public function attendance($userId, $action)
    {
        //NOTE
        // 0 => AtWork
        // 1 => Absent
        // 2 => Late

        // find user
        $user = User::find($userId);

        //if admin click twice attendace for a user delete the old one;
        Attendance::where('employee_id', $userId)
            ->whereDate('date', Carbon::today())
            ->delete();

        Attendance::create([
            "employee_id" => $userId,
            "date" => Carbon::today()->toDateString(),
            "login_time" => $action === 0 ? now() : null,
            "login_method" => $action === 0 ? Attendance::METHOD_MANUAL : null,
            "status" => $action,
        ]);

        //if user get "Absent" or "Late" status send notfifaction to him
        if ($action === 1 || $action === 2) {

            //only delete todays notification when admin change its mind about the user attendance
            $user->notifications()->whereDate('created_at', Carbon::today())->delete();

            $message = null;
            if ($action === 1) {
                $message = "You Are Assigned As Absent";
            } else {
                $message = "You Are Assigned As Late";
            }

            Notification::send($user, new EmployeeAttendanceNotification($user, $action, $message));
        }
    }

    public function manualLogin($userId)
    {
        $user = User::findOrFail($userId);
        $today = Carbon::today()->toDateString();
        $attendance = Attendance::firstOrNew([
            'employee_id' => $userId,
            'date' => $today,
        ]);

        if ($attendance->logout_time) {
            session()->flash('error', 'Today logout is already marked for this employee.');
            return;
        }

        if (!$attendance->exists) {
            $attendance->date = $today;
        }

        if (!$attendance->login_time) {
            $attendance->login_time = now();
        }

        $attendance->status = Attendance::STATUS_AT_WORK;
        $attendance->login_method = Attendance::METHOD_MANUAL;
        $attendance->save();

        $this->sendLogoutQr($user, $today);
        session()->flash('message', 'Emergency login marked and logout QR sent.');
    }

    public function emergencyLogout($userId)
    {
        $attendance = Attendance::where('employee_id', $userId)
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$attendance || !$attendance->login_time) {
            session()->flash('error', 'Login must be marked before emergency logout.');
            return;
        }

        if ($attendance->logout_time) {
            session()->flash('error', 'Logout is already marked for today.');
            return;
        }

        $attendance->logout_time = now();
        $attendance->logout_method = Attendance::METHOD_MANUAL;
        $attendance->status = Attendance::STATUS_LOGGED_OUT;
        $attendance->save();

        session()->flash('message', 'Emergency logout marked successfully.');
    }

    protected function sendLogoutQr(User $user, string $today): void
    {
        $logoutQr = DailyQr::updateOrCreate([
            'user_id' => $user->id,
            'date' => $today,
            'purpose' => DailyQr::PURPOSE_LOGOUT,
        ], [
            'token' => Str::uuid()->toString(),
            'consumed_at' => null,
        ]);

        Mail::to($user->email)->send(new DailyQrMail($logoutQr));
    }
}
