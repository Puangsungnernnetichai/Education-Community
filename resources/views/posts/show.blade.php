@extends('layouts.app')

@section('content')
    <div class="bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
            <div class="flex items-start justify-between gap-6">
                <div class="min-w-0">
                    <div class="text-xs font-medium text-slate-500 dark:text-slate-400">
                        Posted by {{ $post->user?->name ?? 'User' }} • {{ $post->created_at->diffForHumans() }}
                    </div>

                    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 sm:text-3xl">{{ $post->title }}</h1>

                    @if ($post->tags && $post->tags->count())
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($post->tags as $tag)
                                <a
                                    href="{{ route('tags.show', $tag->slug) }}"
                                    class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-200/70 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-200 dark:ring-indigo-500/20 dark:hover:bg-indigo-500/15"
                                >
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if ($post->is_private)
                        <div class="mt-3">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-900/60 dark:text-slate-200">Private</span>
                        </div>
                    @endif
                </div>

                <div class="shrink-0 flex items-center gap-2">
                    <a
                        href="{{ route('home') }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-950"
                    >
                        Back
                    </a>

                    @auth
                        @canany(['update', 'delete'], $post)
                            <div class="menu-target relative">
                                <button
                                    type="button"
                                    data-menu-button
                                    aria-label="Post actions"
                                    class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-950"
                                >
                                    ⋯
                                </button>

                                <div
                                    data-menu-panel
                                    class="absolute right-0 top-11 z-10 hidden w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950"
                                >
                                    @can('update', $post)
                                        <a
                                            href="{{ route('posts.show', $post) }}?edit=1"
                                            data-post-edit
                                            data-render="index"
                                            data-post-id="{{ $post->id }}"
                                            data-post-title="{{ e($post->title) }}"
                                            data-post-body="{{ e($post->body) }}"
                                            data-post-tags="{{ e(($post->tags?->pluck('name')->implode(', ')) ?? '') }}"
                                            data-post-private="{{ $post->is_private ? '1' : '0' }}"
                                            data-post-update-url="{{ route('posts.update', $post) }}"
                                            class="block w-full px-4 py-3 text-left text-sm font-semibold text-indigo-700 hover:bg-indigo-50 dark:text-indigo-200 dark:hover:bg-indigo-500/10"
                                        >
                                            Edit
                                        </a>
                                    @endcan

                                    @can('delete', $post)
                                        <form
                                            method="POST"
                                            action="{{ route('posts.destroy', $post) }}"
                                            data-ajax-delete-post
                                            data-redirect="{{ route('posts.index') }}"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="block w-full px-4 py-3 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-950/20"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        @endcanany
                    @endauth
                </div>
            </div>

            @if (session('status'))
                <div class="mt-6 rounded-2xl bg-white px-4 py-3 text-sm text-slate-700 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:text-slate-200 dark:ring-white/10">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mt-8 rounded-3xl bg-white p-6 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10">
                <div class="whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-slate-200">{{ $post->body }}</div>
            </div>

            @auth
                @can('update', $post)
                    @if (request()->boolean('edit'))
                        <div class="mt-6 rounded-3xl bg-white p-6 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Edit post</div>
                                    <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">This is the no-JS fallback edit form.</div>
                                </div>
                                <a
                                    href="{{ route('posts.show', $post) }}"
                                    class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-950"
                                >
                                    Close
                                </a>
                            </div>

                            <form method="POST" action="{{ route('posts.update', $post) }}" class="mt-5 grid gap-4">
                                @csrf
                                @method('PATCH')

                                <input type="hidden" name="_render" value="index" />
                                <input type="hidden" name="is_private" value="0" />

                                <div>
                                    <label for="post-edit-title-inline" class="text-sm font-medium text-slate-900 dark:text-slate-100">Title</label>
                                    <input
                                        id="post-edit-title-inline"
                                        name="title"
                                        value="{{ old('title', $post->title) }}"
                                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                        required
                                    />
                                    @if ($errors->post->has('title'))
                                        <p class="mt-2 text-sm text-rose-600">{{ $errors->post->first('title') }}</p>
                                    @endif
                                </div>

                                <div>
                                    <label for="post-edit-tags-inline" class="text-sm font-medium text-slate-900 dark:text-slate-100">Tags</label>
                                    <input
                                        id="post-edit-tags-inline"
                                        name="tags"
                                        value="{{ old('tags', ($post->tags?->pluck('name')->implode(', ')) ?? '') }}"
                                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                        placeholder="e.g. Laravel, Tailwind"
                                    />
                                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Comma-separated topics. We auto-trim and dedupe.</p>
                                </div>

                                <div>
                                    <label for="post-edit-body-inline" class="text-sm font-medium text-slate-900 dark:text-slate-100">Body</label>
                                    <textarea
                                        id="post-edit-body-inline"
                                        name="body"
                                        class="mt-2 min-h-[140px] w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                        required
                                    >{{ old('body', $post->body) }}</textarea>
                                    @if ($errors->post->has('body'))
                                        <p class="mt-2 text-sm text-rose-600">{{ $errors->post->first('body') }}</p>
                                    @endif
                                </div>

                                <label class="inline-flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:ring-white/10">
                                    <input type="checkbox" name="is_private" value="1" {{ old('is_private', $post->is_private) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-950" />
                                    <span class="text-sm text-slate-700 dark:text-slate-200">Private</span>
                                </label>

                                <div class="flex items-center justify-end gap-2">
                                    <a
                                        href="{{ route('posts.show', $post) }}"
                                        class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-950"
                                    >
                                        Cancel
                                    </a>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
                                    >
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                @endcan
            @endauth

            <div class="mt-10 rounded-3xl bg-white p-6 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10">
                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Add a comment</div>

                @auth
                    @can('view', $post)
                        <form method="POST" action="{{ route('comments.store', $post) }}" data-ajax-comment data-comments-root="comments-root" class="mt-4 grid gap-3">
                            @csrf
                            <input type="hidden" name="_depth" value="0" />

                            <div>
                                <textarea
                                    name="body"
                                    class="min-h-[110px] w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                    placeholder="Write a helpful comment..."
                                    required
                                >{{ old('body') }}</textarea>
                                <p class="js-form-error mt-2 hidden text-sm text-rose-600" aria-live="polite"></p>
                                @if ($errors->comment->has('body'))
                                    <p class="mt-2 text-sm text-rose-600">{{ $errors->comment->first('body') }}</p>
                                @endif
                            </div>

                            <div class="flex items-center justify-end">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
                                >
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    @endcan
                @endauth

                @guest
                    <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:text-slate-200 dark:ring-white/10">
                        Please <a href="{{ route('login') }}" class="font-semibold text-slate-900 underline dark:text-white">log in</a> to comment.
                    </div>
                @endguest
            </div>

            <div class="mt-10 grid gap-4">
                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Comments</div>

                <div id="comments-root" class="grid gap-4">
                    @forelse ($post->comments as $comment)
                        <x-comment :comment="$comment" :post="$post" />
                    @empty
                        <div class="rounded-3xl bg-white p-6 text-sm text-slate-600 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:text-slate-300 dark:ring-white/10">
                            No comments yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
