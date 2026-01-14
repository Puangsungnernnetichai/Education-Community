<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Admin Dashboard</h1>
                <p class="mt-1 text-sm text-slate-600">Quick overview and shortcuts.</p>
            </div>

            @include('admin.partials.nav')
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                <div class="text-sm font-semibold text-slate-900">Total users</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($totalUsers) }}</div>
            </div>

            <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                <div class="text-sm font-semibold text-slate-900">Total posts</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($totalPosts) }}</div>
            </div>
        </div>

        <div class="mt-8 rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Recent posts</div>
                    <div class="mt-1 text-sm text-slate-600">Latest activity across public and private posts.</div>
                </div>
            </div>

            <div class="mt-4 grid gap-3">
                @forelse ($recentPosts as $post)
                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-900/5">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-slate-900">{{ $post->title }}</div>
                                <div class="mt-1 text-xs text-slate-600">
                                    {{ $post->user?->name ?? 'User' }} · {{ $post->created_at->diffForHumans() }}
                                    @if ($post->is_private)
                                        · <span class="font-semibold text-slate-700">Private</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-xs text-slate-600">
                                @if ($post->user)
                                    Owner: {{ $post->user->email }}
                                @else
                                    Owner: (none)
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-slate-600">No posts yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
