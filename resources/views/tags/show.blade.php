@extends('layouts.app')

@section('content')
    <div class="bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <div class="text-xs font-medium text-slate-500 dark:text-slate-400">Topic</div>
                    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 sm:text-3xl">{{ $tag->name }}</h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Browse posts tagged with this topic.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        href="{{ route('home', ['tag' => $tag->slug]) }}#community"
                        class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-950"
                    >
                        View on Home
                    </a>
                    <a
                        href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-950"
                    >
                        Posts
                    </a>
                </div>
            </div>

            <div class="mt-10 grid gap-5">
                @forelse ($posts as $post)
                    <x-post-card :post="$post" />
                @empty
                    <div class="rounded-3xl bg-white p-6 text-sm text-slate-600 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:text-slate-300 dark:ring-white/10">
                        No posts yet for this topic.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
@endsection
