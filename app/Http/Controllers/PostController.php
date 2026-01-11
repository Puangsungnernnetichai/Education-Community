<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    private function renderPost(Post $post, string $render): string
    {
        if ($render === 'feed') {
            return view('components.feed-post', ['post' => $post])->render();
        }

        return view('components.post-card', ['post' => $post])->render();
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $tagSlug = (string) $request->query('tag', '');
        $tagSlug = trim($tagSlug);
        $tag = null;
        if ($tagSlug !== '') {
            $tag = Tag::query()->where('slug', $tagSlug)->first();
        }

        $posts = Post::query()
            ->with(['user', 'tags'])
            ->withCount(['allComments as comments_count'])
            ->visibleTo($user)
            ->when($tag, function ($query) use ($tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('tags.id', $tag->id);
                });
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('posts.index', [
            'posts' => $posts,
            'tag' => $tag,
        ]);
    }

    public function show(Request $request, Post $post): View
    {
        $this->authorize('view', $post);

        $post->load([
            'user',
            'tags',
            'comments.user',
            'comments.replies.user',
            'comments.replies.replies.user',
            'comments.replies.replies.replies.user',
        ]);

        return view('posts.show', [
            'post' => $post,
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $post = Post::create([
            'user_id' => $request->user()->id,
            'is_private' => (bool) ($validated['is_private'] ?? false),
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        $post->syncTags($validated['tags'] ?? []);

        $post->loadMissing('user', 'tags');
        $post->setAttribute('comments_count', 0);

        if ($request->expectsJson()) {
            $render = (string) ($validated['_render'] ?? 'index');
            return response()->json([
                'postId' => $post->id,
                'render' => $render,
                'html' => $this->renderPost($post, $render),
                'redirect' => route('posts.show', $post),
                'toast' => [
                    'type' => 'success',
                    'message' => 'Post created successfully.',
                ],
            ]);
        }

        $redirectRoute = (string) ($validated['_redirect'] ?? 'home');
        if (! in_array($redirectRoute, ['home', 'posts.index'], true)) {
            $redirectRoute = 'home';
        }

        return redirect()
            ->route($redirectRoute)
            ->with('status', 'Post created!')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Post created successfully.',
            ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $post->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'is_private' => (bool) ($validated['is_private'] ?? $post->is_private),
        ]);

        $post->syncTags($validated['tags'] ?? []);

        $post->loadMissing('user', 'tags');
        $post->setAttribute('comments_count', $post->allComments()->count());

        if ($request->expectsJson()) {
            $render = (string) ($validated['_render'] ?? 'index');
            return response()->json([
                'postId' => $post->id,
                'render' => $render,
                'html' => $this->renderPost($post, $render),
                'toast' => [
                    'type' => 'success',
                    'message' => 'Post updated successfully.',
                ],
            ]);
        }

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Post updated successfully.',
        ]);
    }

    public function destroy(Request $request, Post $post): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'deleted' => true,
                'toast' => [
                    'type' => 'success',
                    'message' => 'Post deleted successfully.',
                ],
            ]);
        }

        return redirect()
            ->route('posts.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Post deleted successfully.',
            ]);
    }
}
