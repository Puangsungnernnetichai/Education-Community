<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GameSession;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function (Game $game) {
            if (! $game->slug) {
                $game->slug = static::generateUniqueSlug($game->name);
            }
        });

        static::updating(function (Game $game) {
            if (! $game->slug) {
                $game->slug = static::generateUniqueSlug($game->name, $game->id);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    private static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'game';
        }

        $slug = $base;
        $i = 2;

        while (static::query()
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function sessions()
    {
        return $this->hasMany(GameSession::class);
    }
}
