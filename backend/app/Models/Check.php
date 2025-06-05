<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    use HasFactory;

    // If your table is named 'checks', you don't need to specify $table.
    // protected $table = 'checks';

    protected $fillable = [
        'target_id',      // Foreign key to the monitored target
        'status_code',    // integer, nullable
        'latency_ms',     // integer, nullable
        'is_success',     // boolean, default false
        'error_message',  // text, nullable
    ];


    // Relationships
    public function target()
    {
        return $this->belongsTo(Target::class);
    }
}
