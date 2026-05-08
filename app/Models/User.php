<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Allow all attributes to be mass assignable
    protected $guarded = [];

    // Hidden fields from array or JSON output
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Automatically cast these fields
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Add computed full_name attribute automatically
    protected $appends = ['full_name', 'profile_image_url'];

    /**
     * Accessor: Get full name with title case.
     */
    public function getFullNameAttribute()
    {
        return Str::title(trim($this->first_name . ' ' . $this->last_name));
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return route('users.profile-image', $this->id);
        }

        return 'https://ui-avatars.com/api/?background=17355f&color=fff&size=256&name=' . urlencode($this->full_name ?: 'User Name');
    }

    /**
     * Relationship: User belongs to a job.
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Relationship: Latest single attendance.
     */
    public function attendance()
    {
        return $this->hasOne(Attendance::class, 'employee_id')->latestOfMany();
    }

    /**
     * Relationship: All attendances.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id')->orderByDesc('created_at');
    }

    /**
     * Relationship: All leave requests for the user.
     */
    public function leaves()
    {
        return $this->hasMany(Leave::class)->orderByDesc('from_date');
    }

    // Optional: Global scopes can be enabled here if needed in the future
    /*
    protected static function booted()
    {
        static::addGlobalScope('orderbyid', function (Builder $builder) {
            $builder->orderBy('id');
        });
    }
    */
}
