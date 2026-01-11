<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'bool',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->latest();
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function syncTags(array $names): void
    {
        $tags = Tag::findOrCreateByNames($names);
        $this->tags()->sync(collect($tags)->pluck('id')->all());
    }

    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        return $query->when(! $user || ! $user->isAdmin(), function ($q) use ($user) {
            $q->where(function ($sub) use ($user) {
                $sub->where('is_private', false);

                if ($user) {
                    $sub->orWhere(function ($own) use ($user) {
                        $own->where('is_private', true)->where('user_id', $user->id);
                    });
                }
            });
        });
    }
}
