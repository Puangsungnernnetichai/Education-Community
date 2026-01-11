<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
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
            ->with('comments.user')
            ->with('comments.replies.user')
            ->with('comments.replies.replies.user')
            ->with('comments.replies.replies.replies.user')
            ->withCount(['allComments as comments_count'])
            ->visibleTo($user)
            ->when($tag, function ($query) use ($tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('tags.id', $tag->id);
                });
            })
            ->latest()
            ->take(20)
            ->get();

        $sponsors = [
            ['name' => 'OpenAI Study Club'],
            ['name' => 'Laravel Learners'],
            ['name' => 'Tailwind Labs'],
            ['name' => 'GitHub Education'],
            ['name' => 'CodeMentor'],
            ['name' => 'Dev Campus'],
        ];

        $blogCards = [
            [
                'tag' => 'Learning',
                'title' => 'How to Think Like a Developer',
                'excerpt' => 'A practical approach to problem-solving, debugging, and building confidence.',
            ],
            [
                'tag' => 'Laravel',
                'title' => 'Routes, Controllers, Views: The Flow',
                'excerpt' => 'Understand the request lifecycle and how to structure features cleanly.',
            ],
            [
                'tag' => 'Community',
                'title' => 'Asking Better Questions',
                'excerpt' => 'Get faster answers by sharing context, constraints, and what you tried.',
            ],
            [
                'tag' => 'Frontend',
                'title' => 'Tailwind UI Patterns for Study Apps',
                'excerpt' => 'Spacing, hierarchy, and components that feel calm and modern.',
            ],
        ];

        return view('home', [
            'posts' => $posts,
            'tag' => $tag,
            'sponsors' => $sponsors,
            'blogCards' => $blogCards,
        ]);
    }
}
