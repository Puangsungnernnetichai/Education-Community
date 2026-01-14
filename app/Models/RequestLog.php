<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestLog extends Model
{
    protected $fillable = [
        'user_id',
        'method',
        'path',
        'route_name',
        'status_code',
        'duration_ms',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'duration_ms' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
