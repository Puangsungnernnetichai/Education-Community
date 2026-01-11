<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function view(?User $user, Post $post): bool
    {
        if (! $post->is_private) {
            return true;
        }

        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return (int) $post->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return (int) $post->user_id === (int) $user->id;
    }

    public function delete(User $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return (int) $post->user_id === (int) $user->id;
    }
}
