<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prompt extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'active_prompt_version_id',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(PromptVersion::class);
    }

    public function activeVersion(): BelongsTo
    {
        return $this->belongsTo(PromptVersion::class, 'active_prompt_version_id');
    }

    public static function activeContent(string $key, ?string $fallback = null): ?string
    {
        $prompt = static::query()->where('key', $key)->with('activeVersion')->first();
        $content = $prompt?->activeVersion?->content;

        if (is_string($content) && trim($content) !== '') {
            return $content;
        }

        return $fallback;
    }
}
