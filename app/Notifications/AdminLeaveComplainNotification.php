<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
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
            "is_admin" => true,
            "user_name" => $this->leave->user->first_name . " " . $this->leave->user->last_name,
            "from_date" => $this->leave->from_date,
            "to_date" => $this->leave->to_date,
            "reason" => $this->leave->reason,
            "status" => $this->leave->status,
            "leave_id" => $this->leave->id,
            "created_at" => $this->leave->created_at,
        ];
    }
}
