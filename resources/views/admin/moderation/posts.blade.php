<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Moderation · Posts</h1>
                <p class="mt-1 text-sm text-slate-600">Hide/unhide posts and keep the community clean.</p>
            </div>
            @include('admin.partials.nav')
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <div class="mb-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.moderation.posts', ['hidden' => 0]) }}" class="inline-flex items-center rounded-2xl px-3 py-2 text-sm font-semibold transition {{ !$onlyHidden ? 'bg-slate-900 text-white' : 'bg-white text-slate-900 ring-1 ring-slate-900/10 hover:bg-slate-50' }}">All</a>
                <a href="{{ route('admin.moderation.posts', ['hidden' => 1]) }}" class="inline-flex items-center rounded-2xl px-3 py-2 text-sm font-semibold transition {{ $onlyHidden ? 'bg-slate-900 text-white' : 'bg-white text-slate-900 ring-1 ring-slate-900/10 hover:bg-slate-50' }}">Hidden</a>
                <a href="{{ route('admin.moderation.comments') }}" class="inline-flex items-center rounded-2xl bg-white px-3 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50">Comments</a>
            </div>
        </div>

        <div class="grid gap-3">
            @forelse($posts as $post)
                <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="truncate text-sm font-semibold text-slate-900">{{ $post->title }}</div>
                                @if($post->is_hidden)
                                    <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-800">Hidden</span>
                                @endif
                                @if($post->is_private)
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">Private</span>
                                @endif
                            </div>
                            <div class="mt-1 text-xs text-slate-600">{{ $post->user?->name ?? 'User' }} · {{ $post->created_at?->diffForHumans() }}</div>
                            @if($post->is_hidden && $post->hidden_reason)
                                <div class="mt-2 text-xs font-semibold text-rose-700">Reason: {{ $post->hidden_reason }}</div>
                            @endif
                        </div>

                        <div class="shrink-0">
                            @if(!$post->is_hidden)
                                <form method="POST" action="{{ route('admin.moderation.posts.hide', $post) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input name="reason" class="w-44 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs" placeholder="Reason (optional)" />
                                    <button class="inline-flex items-center justify-center rounded-2xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-rose-500">Hide</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.moderation.posts.unhide', $post) }}">
                                    @csrf
                                    <button class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-500">Unhide</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                    <div class="text-sm text-slate-600">No posts.</div>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>
