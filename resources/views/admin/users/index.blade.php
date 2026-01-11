<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Users</h1>
                <p class="mt-1 text-sm text-slate-600">Basic user management (view list).</p>
            </div>

            <a
                href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50"
            >
                Back
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <div class="overflow-hidden rounded-3xl bg-white ring-1 ring-slate-900/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $user)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-900">{{ $user->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">{{ $user->email }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ $user->role ?? 'user' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">{{ $user->created_at?->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-6 py-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
