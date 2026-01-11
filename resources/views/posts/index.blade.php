@extends('layouts.app')

@section('content')
    <div class="bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 sm:text-3xl">Posts</h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Public posts are visible to everyone. Private posts are visible only to their owner and admins.</p>
                </div>

                @if (session('status'))
                    <div class="rounded-2xl bg-white px-4 py-3 text-sm text-slate-700 ring-1 ring-slate-900/5">
                        {{ session('status') }}
                    </div>
                @endif
            </div>

            @auth
                @can('create', App\Models\Post::class)
                    <div class="mt-8 rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                        <div class="text-sm font-semibold text-slate-900">Create a post</div>

                        <form method="POST" action="{{ route('posts.store') }}" data-ajax-post data-stay data-render="index" data-posts-root="posts-root" class="mt-4 grid gap-4">
                            @csrf
                            <input type="hidden" name="_redirect" value="posts.index" />

                            <p class="js-form-error hidden rounded-2xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 ring-1 ring-rose-200" aria-live="polite"></p>

                            <div>
                                <label class="text-sm font-medium text-slate-900" for="title">Title</label>
                                <input
                                    id="title"
                                    name="title"
                                    value="{{ old('title') }}"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                    placeholder="What are you learning today?"
                                    required
                                />
                                @if ($errors->post->has('title'))
                                    <p class="mt-2 text-sm text-rose-600">{{ $errors->post->first('title') }}</p>
                                @endif
                            </div>

                            <div>
                                <label class="text-sm font-medium text-slate-900" for="tags">Tags</label>
                                <input
                                    id="tags"
                                    name="tags"
                                    value="{{ old('tags') }}"
                                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                    placeholder="e.g. Laravel, Tailwind, Machine Learning"
                                />
                                @if ($errors->post->has('tags'))
                                    <p class="mt-2 text-sm text-rose-600">{{ $errors->post->first('tags') }}</p>
                                @endif
                                <p class="mt-2 text-xs text-slate-500">Comma-separated topics. We auto-trim and dedupe.</p>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-slate-900" for="body">Body</label>
                                <textarea
                                    id="body"
                                    name="body"
                                    class="mt-2 min-h-[140px] w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Share context, what you tried, and where you're stuck..."
                                    required
                                >{{ old('body') }}</textarea>
                                @if ($errors->post->has('body'))
                                    <p class="mt-2 text-sm text-rose-600">{{ $errors->post->first('body') }}</p>
                                @endif
                            </div>

                            <label class="inline-flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-slate-900/5">
                                <input type="checkbox" name="is_private" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-sm text-slate-700">Make this post private (only you and admins can see it)</span>
                            </label>

                            <div class="flex items-center justify-end">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300"
                                >
                                    Submit Post
                                </button>
                            </div>
                        </form>
                    </div>
                @endcan
            @endauth

            @guest
                <div class="mt-8 rounded-3xl bg-white p-6 text-sm text-slate-700 ring-1 ring-slate-900/5">
                    Please <a href="{{ route('login') }}" class="font-semibold text-slate-900 underline">log in</a> (or <a href="{{ route('register') }}" class="font-semibold text-slate-900 underline">create an account</a>) to create a post.
                </div>
            @endguest

            <div id="posts-root" class="mt-10 grid gap-5">
                @forelse ($posts as $post)
                    <x-post-card :post="$post" />
                @empty
                    <div class="rounded-3xl bg-white p-6 text-sm text-slate-600 ring-1 ring-slate-900/5">
                        No posts yet.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
@endsection
