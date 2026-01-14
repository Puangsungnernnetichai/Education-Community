<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-slate-900">{{ $prompt->name }}</h1>
                    <p class="mt-1 text-sm text-slate-600">Key: <span class="font-semibold">{{ $prompt->key }}</span></p>
                </div>
                @include('admin.partials.nav')
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">Active version</div>
                        <div class="mt-1 text-xs text-slate-600">v{{ $prompt->activeVersion?->version ?? 0 }}</div>
                    </div>
                </div>

                <div class="mt-4">
                    <textarea class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900" rows="14" readonly>{{ $prompt->activeVersion?->content ?? '' }}</textarea>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                <div class="text-sm font-semibold text-slate-900">Create new version</div>
                <p class="mt-1 text-sm text-slate-600">Add a new prompt version; you can activate it below.</p>

                <form method="POST" action="{{ route('admin.prompts.versions.store', $prompt) }}" class="mt-4 grid gap-3">
                    @csrf
                    <textarea name="content" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900" rows="10" placeholder="Paste prompt content here..." required></textarea>

                    @error('content')
                        <div class="text-sm font-semibold text-rose-700">{{ $message }}</div>
                    @enderror

                    <button class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Save version
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-8 rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Versions</div>
                    <div class="mt-1 text-sm text-slate-600">Activate any version instantly (no deploy).</div>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Version</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($prompt->versions as $v)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-900">v{{ $v->version }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">{{ $v->created_at?->diffForHumans() }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if($prompt->active_prompt_version_id === $v->id)
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Active</span>
                                    @else
                                        <form method="POST" action="{{ route('admin.prompts.activate', [$prompt, $v]) }}">
                                            @csrf
                                            <button class="inline-flex items-center justify-center rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50">
                                                Activate
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
