<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $tag = Tag::query()->where('slug', $slug)->firstOrFail();

        $user = $request->user();

        $posts = Post::query()
            ->with(['user', 'tags'])
            ->withCount(['allComments as comments_count'])
            ->visibleTo($user)
            ->whereHas('tags', function ($q) use ($tag) {
                $q->where('tags.id', $tag->id);
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('tags.show', [
            'tag' => $tag,
            'posts' => $posts,
        ]);
    }
}
