@props([
    'post',
])

<article id="post-{{ $post->id }}" class="rounded-3xl bg-white p-5 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <div class="text-xs font-medium text-slate-500 dark:text-slate-400">
                Posted by {{ $post->user?->name ?? 'User' }} • {{ $post->created_at->diffForHumans() }}
            </div>

            <h2 class="mt-2 text-lg font-semibold text-slate-900 dark:text-slate-100">
                <a href="{{ route('posts.show', $post) }}" class="hover:underline">{{ $post->title }}</a>
            </h2>

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

            @if ($post->is_private)
                <div class="mt-2">
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-950/60 dark:text-slate-200">Private</span>
                </div>
            @endif

            <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                {{ \Illuminate\Support\Str::limit($post->body, 220) }}
            </p>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:text-slate-300 dark:ring-white/10">
                {{ $post->comments_count ?? $post->allComments()->count() }} comments
            </span>

            <a
                href="{{ route('posts.show', $post) }}"
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
</article>
