<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Performance</h1>
                <p class="mt-1 text-sm text-slate-600">Lightweight monitoring (last 24h).</p>
            </div>
            @include('admin.partials.nav')
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                <div class="text-sm font-semibold text-slate-900">AI requests</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($aiStats['total'] ?? 0) }}</div>
            </div>
            <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                <div class="text-sm font-semibold text-slate-900">AI errors</div>
                <div class="mt-2 text-3xl font-semibold text-rose-700">{{ number_format($aiStats['errors'] ?? 0) }}</div>
            </div>
            <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                <div class="text-sm font-semibold text-slate-900">Avg AI latency</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($aiStats['avg_latency_ms'] ?? 0) }} ms</div>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                <div class="text-sm font-semibold text-slate-900">Recent AI requests</div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">When</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-600">Latency</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentAi as $r)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $r->created_at?->diffForHumans() }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $r->user?->email ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        @if($r->status === 'error')
                                            <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-800">error</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">ok</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-slate-700">{{ $r->latency_ms ?? '-' }} ms</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5">
                <div class="text-sm font-semibold text-slate-900">Slow / error requests</div>
                <p class="mt-1 text-sm text-slate-600">Logged when slow or failed.</p>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Path</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-600">Duration</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($slowRequests as $r)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $r->method }} {{ $r->path }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $r->status_code ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-slate-700">{{ $r->duration_ms ?? '-' }} ms</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
