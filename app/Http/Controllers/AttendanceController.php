<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\DailyQr;
use App\Models\User;
use App\Mail\DailyQrMail;
use App\Notifications\AdminAttendanceComplainNotification;
use App\Notifications\EmployeeAttendanceNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $date = $request->get('date');

        $users = User::query()
            ->where('is_admin', 0)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('job', function ($q2) use ($search) {
                          $q2->where('title', 'like', "%{$search}%");
                      })
                      ->orWhereHas('job.department', function ($q3) use ($search) {
                          $q3->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->whereHas('attendance', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereHas('attendance', function ($q) use ($date) {
                    $q->whereDate('date', Carbon::parse($date));
                });
            })
            ->with([
                'attendance' => function ($q) use ($date) {
                    $q->whereDate('date', $date ? Carbon::parse($date) : Carbon::today());
                }
            ])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('attendances.index', compact("users"));
    }

    public function takeAttendance()
    {
        return view('attendances.take-attendance');
    }

    public function userAttendance(User $user)
    {
        $statuses = ["At Work", "Absent", "Late", "Logged Out"];
        $user = User::query()->where('id', $user->id)->with("attendances")->first();
        return view('attendances.user-attendance', compact("user", "statuses"));
    }

    public function attendanceComplain(Request $request, Attendance $attendance)
    {
        $admins = User::whereIsAdmin(1)->get();
        Notification::send($admins, new AdminAttendanceComplainNotification($attendance, $request));

        return redirect()->back()->with([
            "message" => "Complain Successfully Sent",
            "title" => "Sent",
            "icon" => "success",
        ]);
    }

    public function report(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $attendances = Attendance::with('user')
                        ->where('date', $date)
                        ->orderBy('login_time')
                        ->get();

        return view('admin.attendance_report', compact('attendances', 'date'));
    }

    public function downloadPdf(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $attendances = Attendance::with('user')
                        ->where('date', $date)
                        ->orderBy('login_time')
                        ->get();

        $pdf = Pdf::loadView('admin.attendance_pdf', [
            'attendances' => $attendances,
            'date' => $date
        ]);

        return $pdf->download("attendance_report_$date.pdf");
    }

    public function viewComplain($id)
    {
        $statuses = ["At Work", "Absent", "Late", "Logged Out"];

        $notification = auth()->user()
            ->notifications
            ->when($id, function ($query) use ($id) {
                return $query->where('id', $id);
            })[0];

        return view('attendances.view-complain', compact("notification", 'statuses'));
    }

    public function fixComplain(Request $request, $id)
    {
        try {
            $notification = auth()->user()
                ->unreadNotifications
                ->when($id, function ($query) use ($id) {
                    return $query->where('id', $id);
                });

            $status = null;
            if ($notification[0]->data['status'] === "At Work") {
                $status = 0;
            } elseif ($notification[0]->data['status'] === "Absent") {
                $status = 1;
            } elseif ($notification[0]->data['status'] === "Late") {
                $status = 2;
            } else {
                $status = 3;
            }

            $attendance = Attendance::where('id', $notification[0]->data['attendance_id'])->first();

            if ($request->result === "accept") {
                $attendance->status = $status;
                $attendance->save();

                $action = "Accept";
                $message = "Your request to change attendance status from " . $notification[0]->data['current_status'] . " to " . $notification[0]->data['status'] . " is accepted";
            } else {
                $action = "Reject";
                $message = "Your request to change attendance status from " . $notification[0]->data['current_status'] . " to " . $notification[0]->data['status'] . " is rejected";
            }

            Notification::send($attendance->user, new EmployeeAttendanceNotification($attendance->user, $action, $message));
            $notification->markAsRead();

            return redirect()->route('attendances.index')->with([
                "message" => "Attendance Updated Successfully",
                "title" => "Updated",
                "icon" => "success",
            ]);
        } catch (\Exception $e) {
            return redirect()->route('attendances.index')->with([
                "message" => $e->getMessage(),
                "title" => "Code Error",
                "icon" => "error",
            ]);
        }
    }

    public function qrScanAttendance($userId)
    {
        $user = User::findOrFail($userId);
        $result = $this->recordAttendanceScan($user);

        return response()->json([
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function processTokenScan(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $qr = DailyQr::where('token', $request->token)
            ->whereDate('date', today())
            ->first();

        if (!$qr) {
            return response()->json(['error' => 'Invalid QR or expired'], 404);
        }

        if ($qr->consumed_at) {
            return response()->json(['message' => 'This QR has already been used.', 'status' => 'warning']);
        }

        $user = User::findOrFail($qr->user_id);
        $result = $this->recordAttendanceScan($user, $qr);

        return response()->json([
            'message' => $result['message'],
            'status' => $result['status'],
        ]);
    }

    public function webQrScan($userId)
    {
        $user = User::findOrFail($userId);
        $result = $this->recordAttendanceScan($user);

        if ($result['status'] === 'duplicate') {
            return view('already-marked', ['user' => $user, 'message' => $result['message']]);
        }

        return view('marked-success', [
            'user' => $user,
            'mode' => $result['mode'],
        ]);
    }

    protected function recordAttendanceScan(User $user, ?DailyQr $qr = null): array
    {
        $now = now();
        $today = $now->toDateString();

        $attendance = Attendance::firstOrCreate([
            'employee_id' => $user->id,
            'date' => $today,
        ], [
            'status' => Attendance::STATUS_AT_WORK,
            'login_time' => $now,
        ]);

        $lastLog = AttendanceLog::where('employee_id', $user->id)->whereDate('date', $today)->latest('scanned_at')->first();
        if ($lastLog && $lastLog->scanned_at->diffInSeconds($now) <= 8) {
            return [
                'message' => 'Duplicate scan ignored. Please wait a few seconds before scanning again.',
                'status' => 'duplicate',
                'mode' => $lastLog->type,
            ];
        }

        $mode = $qr?->purpose === DailyQr::PURPOSE_LOGOUT
            ? AttendanceLog::TYPE_LOGOUT
            : AttendanceLog::TYPE_LOGIN;

        if ($qr && $qr->purpose === DailyQr::PURPOSE_LOGIN && $attendance->logout_time && !is_null($attendance->logout_time)) {
            return [
                'message' => 'Today login is already marked for this employee.',
                'status' => 'warning',
                'mode' => 'login',
            ];
        }

        if ($qr && $qr->purpose === DailyQr::PURPOSE_LOGOUT && is_null($attendance->login_time)) {
            return [
                'message' => 'Login must be marked before logout QR can be used.',
                'status' => 'warning',
                'mode' => 'logout',
            ];
        }

        if ($mode === AttendanceLog::TYPE_LOGOUT && !is_null($attendance->logout_time)) {
            return [
                'message' => 'Logout is already marked for today.',
                'status' => 'warning',
                'mode' => 'logout',
            ];
        }

        AttendanceLog::create([
            'attendance_id' => $attendance->id,
            'employee_id' => $user->id,
            'date' => $today,
            'type' => $mode,
            'scanned_at' => $now,
        ]);

        if ($qr) {
            $qr->consumed_at = $now;
            $qr->save();
        }

        if ($mode === AttendanceLog::TYPE_LOGIN) {
            if (is_null($attendance->login_time) || $now->lt($attendance->login_time)) {
                $attendance->login_time = $now;
            }

            $attendance->status = Attendance::STATUS_AT_WORK;
            $attendance->login_method = Attendance::METHOD_QR;
            $attendance->save();

            $logoutQr = DailyQr::updateOrCreate([
                'user_id' => $user->id,
                'date' => $today,
                'purpose' => DailyQr::PURPOSE_LOGOUT,
            ], [
                'token' => Str::uuid()->toString(),
                'consumed_at' => null,
            ]);

            Mail::to($user->email)->send(new DailyQrMail($logoutQr));

            return [
                'message' => 'Login marked successfully for ' . $user->full_name . '. Logout QR has been sent to email.',
                'status' => 'success',
                'mode' => 'login',
            ];
        }

        $attendance->logout_time = $now;
        $attendance->status = Attendance::STATUS_LOGGED_OUT;
        $attendance->logout_method = Attendance::METHOD_QR;
        $attendance->save();

        return [
            'message' => 'Logout marked successfully for ' . $user->full_name,
            'status' => 'success',
            'mode' => 'logout',
        ];
    }
}
