<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * @return array<int, Tag>
     */
    public static function findOrCreateByNames(array $names): array
    {
        $normalized = [];
        foreach ($names as $name) {
            $name = is_string($name) ? trim($name) : '';
            $name = preg_replace('/\s+/', ' ', $name ?? '');
            if (! $name) {
                continue;
            }

            $key = mb_strtolower($name);
            $normalized[$key] = $name;
        }

        if (! $normalized) {
            return [];
        }

        $existing = static::query()
            ->whereIn('name', array_values($normalized))
            ->get()
            ->keyBy(function (Tag $tag) {
                return mb_strtolower($tag->name);
            });

        $result = [];

        foreach ($normalized as $key => $displayName) {
            /** @var Tag|null $tag */
            $tag = $existing->get($key);
            if ($tag) {
                $result[] = $tag;
                continue;
            }

            $baseSlug = Str::slug($displayName);
            $slug = $baseSlug ?: Str::slug('tag');

            $suffix = 2;
            while (static::query()->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix += 1;
            }

            $created = static::create([
                'name' => $displayName,
                'slug' => $slug,
            ]);

            $result[] = $created;
        }

        return $result;
    }

    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }
}
