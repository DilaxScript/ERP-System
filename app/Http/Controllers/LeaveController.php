<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AdminLeaveRequestNotification;
use Illuminate\Http\Request;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function create()
    {
        return $this->takeLeave();
    }

    /**
     * Display the current authenticated user's leave requests with pagination.
     */
    public function takeLeave()
    {
        $userLeaves = Leave::where('user_id', Auth::id())
            ->orderByDesc('from_date')
            ->paginate(10);

        return view('leaves.take', compact('userLeaves'));
    }

    /**
     * Admin: Display all leave requests with pagination.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $date = $request->get('date');

        $leaves = Leave::with('user')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reason', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('from_date', '<=', $date)
                    ->whereDate('to_date', '>=', $date);
            })
            ->orderByDesc('from_date')
            ->paginate(10)
            ->withQueryString();

        return view('leaves.index', compact('leaves'));
    }

    public function show($id)
    {
        $leave = Leave::with('user')->findOrFail($id);

        return view('leaves.show', compact('leave'));
    }

    public function attachment($id)
    {
        $leave = Leave::findOrFail($id);

        abort_unless($leave->attachment && Storage::disk('public')->exists('leave_attachments/' . $leave->attachment), 404);

        return Storage::disk('public')->response('leave_attachments/' . $leave->attachment);
    }

    /**
     * Store a new leave request after validating input and checking leave limits.
     */
    public function store(Request $request)
    {
        // Validate the form inputs
        $validated = $request->validate([
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $userId = Auth::id();

        $fromDate = Carbon::parse($validated['from_date']);
        $toDate = Carbon::parse($validated['to_date']);

        // Calculate the number of days requested (inclusive)
        $requestedDays = $fromDate->diffInDays($toDate) + 1;

        // Define leave limits
        $maxMonthlyLeave = 3;
        $maxAnnualLeave = 30;

        // Calculate total approved leaves used in the current month
        $monthlyLeavesUsed = Leave::where('user_id', $userId)
            ->where('status', Leave::STATUS_APPROVED)
            ->whereYear('from_date', $fromDate->year)
            ->whereMonth('from_date', $fromDate->month)
            ->sum('number_of_days');

        // Calculate total approved leaves used in the current year
        $annualLeavesUsed = Leave::where('user_id', $userId)
            ->where('status', Leave::STATUS_APPROVED)
            ->whereYear('from_date', $fromDate->year)
            ->sum('number_of_days');

        // Check if monthly leave limit will be exceeded
        if (($monthlyLeavesUsed + $requestedDays) > $maxMonthlyLeave) {
            return back()->withErrors([
                'from_date' => "Monthly leave limit exceeded. Maximum allowed is $maxMonthlyLeave days."
            ])->withInput();
        }

        // Check if annual leave limit will be exceeded
        if (($annualLeavesUsed + $requestedDays) > $maxAnnualLeave) {
            return back()->withErrors([
                'from_date' => "Annual leave limit exceeded. Maximum allowed is $maxAnnualLeave days."
            ])->withInput();
        }

        // Handle file upload if present
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $storedPath = $request->file('attachment')->store('leave_attachments', 'public');
            $attachmentPath = basename($storedPath); // Only filename.ext is saved in DB
        }

        // Create the leave request
        $leave = Leave::create([
            'user_id' => $userId,
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'number_of_days' => $requestedDays,
            'reason' => $validated['reason'],
            'status' => Leave::STATUS_PENDING,
            'attachment' => $attachmentPath, // Save only filename.ext
        ]);

        $admins = User::where('is_admin', 1)->get();
        Notification::send($admins, new AdminLeaveRequestNotification($leave->load('user')));

        return redirect()->route('leave.take-leave')->with([
            'message' => 'Leave request submitted successfully.',
            'title' => 'Success',
            'icon' => 'success',
        ]);
    }

    /**
     * Admin: Update the status of a leave request (Approve/Reject/Pending).
     */
    public function updateStatus(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);

        // Validate new status input
        $request->validate([
            'status' => 'required|string|in:Approved,Rejected,Pending',
        ]);

        // Update the status and save
        $leave->status = $request->input('status');
        $leave->save();

        return redirect()->route('leave.index')->with([
            'message' => 'Leave status updated successfully.',
            'title' => 'Success',
            'icon' => 'success',
        ]);
    }
}
