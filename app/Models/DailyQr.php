<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyQr extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'date', 'token', 'purpose', 'consumed_at'];

    protected $casts = [
        'date' => 'date',
        'consumed_at' => 'datetime',
    ];

    const PURPOSE_LOGIN = 'login';
    const PURPOSE_LOGOUT = 'logout';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
