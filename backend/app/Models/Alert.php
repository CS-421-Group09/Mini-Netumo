<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Notifications\AlertNotification;
use Illuminate\Support\Facades\Http;

class Alert extends Model
{
    protected $fillable = [
        'target_id',
        'type',
        'message',
        'meta',
        'notified_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'notified_at' => 'datetime',
    ];

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    protected static function booted()
    {
        static::created(function ($alert) {
            $target = $alert->target;
            if ($target && $target->user) {
                $target->user->notify(new AlertNotification($alert));
                // Custom webhook (e.g., Discord)
                if (!empty($target->user->custom_webhook_url)) {
                    Http::post($target->user->custom_webhook_url, [
                        'content' => 'Netumo Alert: ' . $alert->message,
                    ]);
                }
            }
        });
    }
} 