@props([
    'comment',
    'post',
    'depth' => 0,
])

@php
    $depth = max(0, (int) $depth);
    $indent = $depth > 0 ? 'pl-4 sm:pl-6 border-l border-slate-200 dark:border-white/10' : '';

    $toggleId = 'reply-toggle-' . $comment->id;
    $replyWrapperId = 'reply-form-' . $comment->id;
    $repliesRootId = 'comment-replies-' . $comment->id;
@endphp

<div id="comment-{{ $comment->id }}" data-comment-id="{{ $comment->id }}" class="grid gap-3 {{ $indent }}">
    <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    {{ $comment->user?->name ?? 'User' }} • {{ $comment->created_at->diffForHumans() }}
                </div>
                <div data-comment-display class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-200">{{ $comment->body }}</div>

                @auth
                    @can('update', $comment)
                        <form
                            method="POST"
                            action="{{ route('comments.update', $comment) }}"
                            data-ajax-comment-update
                            class="mt-3 hidden grid gap-3"
                            data-comment-edit-form
                        >
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="_depth" value="{{ $depth }}" />

                            <div>
                                <textarea
                                    name="body"
                                    class="min-h-[90px] w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                    required
                                >{{ old('body', $comment->body) }}</textarea>
                                <p class="js-form-error mt-2 hidden text-sm text-rose-600" aria-live="polite"></p>
                            </div>

                            <div class="flex items-center justify-end gap-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
                                >
                                    Save
                                </button>
                                <button
                                    type="button"
                                    data-comment-edit-cancel
                                    class="rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-950"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    @endcan
                @endauth
            </div>

            <div class="flex shrink-0 items-center gap-2">
                @auth
                    @can('view', $post)
                        <a
                            id="{{ $toggleId }}"
                            href="#{{ $replyWrapperId }}"
                            data-reply-toggle
                            data-target="{{ $replyWrapperId }}"
                            data-label-closed="Reply"
                            data-label-open="Cancel"
                            class="rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-950"
                        >
                            Reply
                        </a>
                    @endcan

                    @canany(['update', 'delete'], $comment)
                        <div class="menu-target relative">
                            <button
                                type="button"
                                data-menu-button
                                aria-label="Comment actions"
                                class="rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-950"
                            >
                                ⋯
                            </button>

                            <div
                                data-menu-panel
                                class="absolute right-0 top-11 z-10 hidden w-40 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950"
                            >
                                @can('view', $post)
                                    <button
                                        type="button"
                                        data-reply-toggle
                                        data-target="{{ $replyWrapperId }}"
                                        class="block w-full px-4 py-3 text-left text-sm font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-white/5"
                                    >
                                        Reply
                                    </button>
                                @endcan

                                @can('update', $comment)
                                    <button
                                        type="button"
                                        data-comment-edit-toggle
                                        class="block w-full px-4 py-3 text-left text-sm font-semibold text-indigo-700 hover:bg-indigo-50 dark:text-indigo-200 dark:hover:bg-indigo-500/10"
                                    >
                                        Edit
                                    </button>
                                @endcan

                                @can('delete', $comment)
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" data-ajax-delete>
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

        <div id="{{ $replyWrapperId }}" class="reply-target mt-4 hidden">
            @auth
                @can('view', $post)
                    <form
                        method="POST"
                        action="{{ route('comments.store', $post) }}"
                        data-ajax-comment
                        data-replies-root="{{ $repliesRootId }}"
                        data-reply-wrapper="{{ $replyWrapperId }}"
                        data-reply-toggle-id="{{ $toggleId }}"
                        class="grid gap-3"
                    >
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}" />
                        <input type="hidden" name="_depth" value="{{ $depth + 1 }}" />

                        <div>
                            <textarea
                                name="body"
                                class="min-h-[90px] w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                placeholder="Write a reply..."
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
                                Post Reply
                            </button>
                        </div>
                    </form>
                @endcan
            @endauth

            @guest
                <div class="rounded-2xl bg-white px-4 py-3 text-sm text-slate-700 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:text-slate-200 dark:ring-white/10">
                    Please <a href="{{ route('login') }}" class="font-semibold text-slate-900 underline dark:text-white">log in</a> to reply.
                </div>
            @endguest
        </div>
    </div>

    <div id="{{ $repliesRootId }}" class="grid gap-3">
        @foreach ($comment->replies as $reply)
            <x-comment :comment="$reply" :post="$post" :depth="$depth + 1" />
        @endforeach
    </div>
</div>
