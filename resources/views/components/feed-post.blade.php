@props([
    'post',
])

@php
    $commentsRootId = 'comments-root-' . $post->id;
    $panelId = 'comments-panel-' . $post->id;
    $toggleId = 'comments-toggle-' . $post->id;
@endphp

<article id="post-{{ $post->id }}" class="rounded-3xl bg-white p-5 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="text-xs font-medium text-slate-500 dark:text-slate-400">
                Posted by {{ $post->user?->name ?? 'User' }} • {{ $post->created_at->diffForHumans() }}
            </div>

            <div class="mt-2 flex items-start justify-between gap-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                    <a href="{{ route('posts.show', $post) }}" class="hover:underline">{{ $post->title }}</a>
                </h3>

                @if ($post->tags && $post->tags->count())
                    <div class="mt-2 flex flex-wrap gap-2">
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

                <div class="shrink-0 flex items-center gap-2">
                    <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:text-slate-300 dark:ring-white/10">
                        {{ $post->comments_count ?? $post->allComments()->count() }} comments
                    </span>

                    <a
                        id="{{ $toggleId }}"
                        href="{{ route('posts.show', $post) }}"
                        data-reply-toggle
                        data-target="{{ $panelId }}"
                        data-label-closed="View"
                        data-label-open="Hide"
                        class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-950"
                    >
                        View
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
                                            data-render="feed"
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
                                            data-remove-id="post-{{ $post->id }}"
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

            <div class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-200">{{ \Illuminate\Support\Str::limit($post->body, 280) }}</div>
        </div>
    </div>

    @if ($post->is_private)
        <div class="mt-3">
            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-950/60 dark:text-slate-200">Private</span>
        </div>
    @endif

    <div id="{{ $panelId }}" class="mt-6 hidden">
        <div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:ring-white/10">
            <div class="grid gap-6">
                <div>
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Add a comment</div>
                    @auth
                        <form method="POST" action="{{ route('comments.store', $post) }}" data-ajax-comment data-comments-root="{{ $commentsRootId }}" class="mt-3 grid gap-3">
                            @csrf
                            <input type="hidden" name="_depth" value="0" />

                            <div>
                                <input
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                    name="body"
                                    placeholder="Write a helpful comment..."
                                    required
                                />
                                @if ($errors->comment->has('body'))
                                    <p class="mt-2 text-sm text-rose-600">{{ $errors->comment->first('body') }}</p>
                                @endif

                                <p class="js-form-error mt-2 hidden text-sm text-rose-600" aria-live="polite"></p>
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
                    @endauth

                    @guest
                        <div class="mt-3 rounded-2xl bg-white px-4 py-3 text-sm text-slate-700 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:text-slate-200 dark:ring-white/10">
                            Please <a href="{{ route('login') }}" class="font-semibold text-slate-900 underline dark:text-white">log in</a> to comment.
                        </div>
                    @endguest
                </div>

                <div class="grid gap-4">
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Comments</div>

                    <div id="{{ $commentsRootId }}" class="grid gap-4">
                        @forelse ($post->comments as $comment)
                            <x-comment :comment="$comment" :post="$post" />
                        @empty
                            <div class="rounded-3xl bg-white p-6 text-sm text-slate-600 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:text-slate-300 dark:ring-white/10">
                                No comments yet. Be the first to help.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
