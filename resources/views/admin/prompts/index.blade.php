<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Prompt management</h1>
                <p class="mt-1 text-sm text-slate-600">Manage AI prompt templates and activate versions.</p>
            </div>
            @include('admin.partials.nav')
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <div class="grid gap-4">
            @forelse($prompts as $prompt)
                <a href="{{ route('admin.prompts.show', $prompt) }}" class="block rounded-3xl bg-white p-6 ring-1 ring-slate-900/5 transition hover:bg-slate-50">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">{{ $prompt->name }}</div>
                            <div class="mt-1 text-xs text-slate-600">Key: <span class="font-semibold">{{ $prompt->key }}</span></div>
                            @if($prompt->description)
                                <div class="mt-2 text-sm text-slate-600">{{ $prompt->description }}</div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-semibold text-slate-500">Active</div>
                            <div class="mt-1 inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                v{{ $prompt->activeVersion?->version ?? 0 }}
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                    <div class="text-sm text-slate-600">No prompts yet.</div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
