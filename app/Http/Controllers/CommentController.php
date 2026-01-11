<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post): RedirectResponse|JsonResponse
    {
        $this->authorize('view', $post);

        $validated = $request->validated();

        $parentId = $request->input('parent_id');
        $parent = null;
        if ($parentId) {
            $parent = Comment::query()
                ->whereKey($parentId)
                ->where('post_id', $post->id)
                ->firstOrFail();
        }

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'parent_id' => $parent?->id,
            'body' => $validated['body'],
        ]);

        $comment->loadMissing('user', 'replies.user');

        if ($request->expectsJson()) {
            $depth = (int) ($validated['_depth'] ?? ($parent ? 1 : 0));
            $html = view('components.comment', [
                'comment' => $comment,
                'post' => $post,
                'depth' => $depth,
            ])->render();

            return response()->json([
                'commentId' => $comment->id,
                'parentId' => $comment->parent_id,
                'html' => $html,
                'toast' => [
                    'type' => 'success',
                    'message' => $comment->parent_id ? 'Reply posted successfully.' : 'Comment posted successfully.',
                ],
            ]);
        }

        return back()
            ->with('status', $comment->parent_id ? 'Reply posted!' : 'Comment posted!')
            ->with('toast', [
                'type' => 'success',
                'message' => $comment->parent_id ? 'Reply posted successfully.' : 'Comment posted successfully.',
            ]);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): RedirectResponse|JsonResponse
    {
        $comment->loadMissing('post');
        $this->authorize('update', $comment);

        $post = $comment->post;
        $this->authorize('view', $post);

        $validated = $request->validated();

        $comment->update([
            'body' => $validated['body'],
        ]);

        $comment->loadMissing('user', 'replies.user');

        if ($request->expectsJson()) {
            $depth = (int) ($validated['_depth'] ?? 0);
            $html = view('components.comment', [
                'comment' => $comment,
                'post' => $post,
                'depth' => $depth,
            ])->render();

            return response()->json([
                'commentId' => $comment->id,
                'html' => $html,
                'toast' => [
                    'type' => 'success',
                    'message' => 'Comment updated successfully.',
                ],
            ]);
        }

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Comment updated successfully.',
        ]);
    }

    public function destroy(Request $request, Comment $comment): RedirectResponse|JsonResponse
    {
        $comment->loadMissing('post');
        $this->authorize('delete', $comment);

        $post = $comment->post;
        $this->authorize('view', $post);

        $comment->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'deleted' => true,
                'commentId' => $comment->id,
                'toast' => [
                    'type' => 'success',
                    'message' => 'Comment deleted successfully.',
                ],
            ]);
        }

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Comment deleted successfully.',
        ]);
    }
}
