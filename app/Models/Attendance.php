<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // 🔐 Allow mass assignment for all fields
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
        'status' => 'integer',
    ];

    // 🏷️ Status codes
    const STATUS_AT_WORK = 0;
    const STATUS_ABSENT = 1;
    const STATUS_LATE = 2;
    const STATUS_LOGGED_OUT = 3;
    const METHOD_QR = 'qr';
    const METHOD_MANUAL = 'manual';

    // 🔗 Relationship: Each attendance belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function logs()
    {
        return $this->hasMany(AttendanceLog::class)->orderBy('scanned_at');
    }

    // 🏷️ Accessor: Get readable status without overriding the stored integer field
    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status'] ?? null) {
            case self::STATUS_AT_WORK:
                return "At Work";
            case self::STATUS_ABSENT:
                return "Absent";
            case self::STATUS_LATE:
                return "Late";
            case self::STATUS_LOGGED_OUT:
                return "Logged Out";
            default:
                return "Unknown";
        }
    }

    public function getLoginTimeDisplayAttribute()
    {
        return $this->login_time;
    }

    public function getLoginMethodTextAttribute()
    {
        return $this->login_method === self::METHOD_MANUAL ? 'Manual' : ($this->login_method === self::METHOD_QR ? 'QR' : '-');
    }

    public function getLogoutMethodTextAttribute()
    {
        return $this->logout_method === self::METHOD_MANUAL ? 'Manual' : ($this->logout_method === self::METHOD_QR ? 'QR' : '-');
    }
}
