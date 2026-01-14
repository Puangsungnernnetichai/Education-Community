<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class ModerationController extends Controller
{
    public function posts(Request $request)
    {
        $this->authorize('accessAdmin');

        $onlyHidden = $request->boolean('hidden');

        $posts = Post::query()
            ->with('user')
            ->when($onlyHidden, fn ($q) => $q->where('is_hidden', true))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.moderation.posts', [
            'posts' => $posts,
            'onlyHidden' => $onlyHidden,
        ]);
    }

    public function hidePost(Request $request, Post $post)
    {
        $this->authorize('accessAdmin');

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:200'],
        ]);

        $post->update([
            'is_hidden' => true,
            'hidden_at' => now(),
            'hidden_by' => $request->user()?->id,
            'hidden_reason' => $data['reason'] ?? null,
        ]);

        AdminAuditLog::record($request->user(), 'moderation.post_hidden', Post::class, $post->id, [
            'reason' => $data['reason'] ?? null,
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Post hidden.',
        ]);
    }

    public function unhidePost(Request $request, Post $post)
    {
        $this->authorize('accessAdmin');

        $post->update([
            'is_hidden' => false,
            'hidden_at' => null,
            'hidden_by' => null,
            'hidden_reason' => null,
        ]);

        AdminAuditLog::record($request->user(), 'moderation.post_unhidden', Post::class, $post->id);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Post unhidden.',
        ]);
    }

    public function comments(Request $request)
    {
        $this->authorize('accessAdmin');

        $onlyHidden = $request->boolean('hidden');

        $comments = Comment::query()
            ->with(['user', 'post'])
            ->when($onlyHidden, fn ($q) => $q->where('is_hidden', true))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.moderation.comments', [
            'comments' => $comments,
            'onlyHidden' => $onlyHidden,
        ]);
    }

    public function hideComment(Request $request, Comment $comment)
    {
        $this->authorize('accessAdmin');

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:200'],
        ]);

        $comment->update([
            'is_hidden' => true,
            'hidden_at' => now(),
            'hidden_by' => $request->user()?->id,
            'hidden_reason' => $data['reason'] ?? null,
        ]);

        AdminAuditLog::record($request->user(), 'moderation.comment_hidden', Comment::class, $comment->id, [
            'reason' => $data['reason'] ?? null,
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Comment hidden.',
        ]);
    }

    public function unhideComment(Request $request, Comment $comment)
    {
        $this->authorize('accessAdmin');

        $comment->update([
            'is_hidden' => false,
            'hidden_at' => null,
            'hidden_by' => null,
            'hidden_reason' => null,
        ]);

        AdminAuditLog::record($request->user(), 'moderation.comment_unhidden', Comment::class, $comment->id);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Comment unhidden.',
        ]);
    }
}
