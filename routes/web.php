<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\UserController;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Auth\Register;
use App\Http\Livewire\ForgotPassword;
use App\Http\Livewire\ResetPassword;
use App\Models\DailyQr;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()->is_admin
        ? redirect()->route('dashboard')
        : redirect()->route('profile');
});

Route::get('/qr/{token}/image', function ($token) {
    $image = QrCode::format('png')->size(300)->generate($token);

    return response($image, 200)->header('Content-Type', 'image/png');
})->name('qr.image');

Route::get('/register', Register::class)->name('register');
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');
Route::get('/reset-password/{token}', ResetPassword::class)
    ->middleware('signed')
    ->name('reset-password');
Route::get('/getRandomEmp', [AuthController::class, 'getRandomEmp']);

Route::middleware('auth')->group(function () {
    Route::view('/profile', 'profile')->name('profile');

    Route::get('/user-attendance/{user}', [AttendanceController::class, 'userAttendance'])
        ->name('attendances.user-attendance');
    Route::post('/attendance-complain/{attendance}', [AttendanceController::class, 'attendanceComplain'])
        ->name('attendances.attendanceComplain');

    Route::get('/my-leaves', [LeaveController::class, 'takeLeave'])->name('leave.take-leave');
    Route::get('/leaves/apply', [LeaveController::class, 'create'])->name('leave.create');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leave.store');
    Route::get('/leaves/{id}/attachment', [LeaveController::class, 'attachment'])->name('leave.attachment');
    Route::get('/leaves/{id}', [LeaveController::class, 'show'])->name('leave.show');

    Route::get('/my-qr', function () {
        $qr = DailyQr::where('user_id', auth()->id())
            ->whereDate('date', today())
            ->whereNull('consumed_at')
            ->orderByRaw("CASE WHEN purpose = 'logout' THEN 0 ELSE 1 END")
            ->first();

        return view('employees.my-qr', compact('qr'));
    })->name('employee.qr');

    Route::get('/users/{user}/profile-image', [UserController::class, 'profileImage'])
        ->name('users.profile-image');

    Route::post('/scan', [AttendanceController::class, 'processTokenScan'])->name('qr.scan');

    Route::middleware('is_admin')->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
        Route::resource('departments', DepartmentController::class)->except('show');
        Route::resource('jobs', JobController::class)->except('show');
        Route::resource('users', UserController::class);

        Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
        Route::get('/take-attendance', [AttendanceController::class, 'takeAttendance'])->name('attendances.take-attendance');
        Route::get('/view-complain/{id}', [AttendanceController::class, 'viewComplain'])->name('attendances.view-complain');
        Route::post('/fix-complain/{id}', [AttendanceController::class, 'fixComplain'])->name('attendances.fix-complain');
        Route::get('/attendance-report', [AttendanceController::class, 'report'])->name('attendance.report');
        Route::get('/attendance/report/pdf', [AttendanceController::class, 'downloadPdf'])->name('attendance.report.pdf');

        Route::get('/leaves', [LeaveController::class, 'index'])->name('leave.index');
        Route::post('/leaves/{id}/status', [LeaveController::class, 'updateStatus'])->name('leave.updateStatus');

        Route::get('/scan-qr', function () {
            return view('scan');
        })->name('qr.scan.ui');

        Route::post('/generate-qr', [QrController::class, 'generate'])->name('qr.generate');
    });
});
