<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminLeaveRequestNotification extends Notification
{
    use Queueable;

    public Leave $leave;

    public function __construct(Leave $leave)
    {
        $this->leave = $leave;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'leave_request',
            'user_name' => $this->leave->user->full_name,
            'message' => 'submitted a leave request from ' . $this->leave->from_date->format('d M Y') . ' to ' . $this->leave->to_date->format('d M Y') . '.',
            'leave_id' => $this->leave->id,
            'status' => $this->leave->status,
            'has_attachment' => (bool) $this->leave->attachment,
            'action_url' => route('leave.show', $this->leave->id),
        ];
    }
}
