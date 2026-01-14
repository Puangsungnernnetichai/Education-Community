<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRequest extends Model
{
    protected $fillable = [
        'user_id',
        'feature',
        'provider',
        'model',
        'prompt_version_id',
        'status',
        'http_status',
        'error_code',
        'error_message',
        'latency_ms',
        'input_chars',
        'output_chars',
    ];

    protected $casts = [
        'http_status' => 'integer',
        'latency_ms' => 'integer',
        'input_chars' => 'integer',
        'output_chars' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promptVersion(): BelongsTo
    {
        return $this->belongsTo(PromptVersion::class, 'prompt_version_id');
    }
}
