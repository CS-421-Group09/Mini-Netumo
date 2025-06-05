<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'url',
        'is_active',
        'check_frequency',
        'last_check_at',
        'ssl_checked_at',
        'ssl_expiry_days',
        'domain_checked_at',
        'domain_expiry_days',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function checks()
    {
        return $this->hasMany(\App\Models\Check::class);
    }
} 