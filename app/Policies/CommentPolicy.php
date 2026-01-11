<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function delete(User $user, Comment $comment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return (int) $comment->user_id === (int) $user->id;
    }

    public function update(User $user, Comment $comment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return (int) $comment->user_id === (int) $user->id;
    }
}
