<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Users</h1>
                <p class="mt-1 text-sm text-slate-600">Search, set roles, ban/unban, and adjust points.</p>
            </div>

            @include('admin.partials.nav')
        </div>
    </x-slot>

    <div
        class="mx-auto max-w-6xl px-4 py-10 sm:px-6"
        x-data="{
            banOpen: false,
            banAction: '',
            banUserName: '',
            duration: 'permanent',
            bannedUntil: '',
            reason: '',
            openBan(userId, userName) {
                this.banAction = '{{ url('/admin/users') }}/' + userId + '/ban';
                this.banUserName = userName;
                this.duration = 'permanent';
                this.bannedUntil = '';
                this.reason = '';
                this.banOpen = true;
            },
            closeBan() {
                this.banOpen = false;
            },
            durationLabel() {
                if (this.duration === 'custom') return this.bannedUntil ? ('จนถึง ' + this.bannedUntil) : 'custom';
                const map = { permanent: 'ถาวร', '1h': '1 ชั่วโมง', '1d': '1 วัน', '3d': '3 วัน', '7d': '7 วัน', '30d': '30 วัน' };
                return map[this.duration] || this.duration;
            }
        }"
    >
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center">
            <input
                name="q"
                value="{{ $q ?? '' }}"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 sm:max-w-md"
                placeholder="Search name or email..."
            />
            <div class="flex items-center gap-2">
                <button class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Search</button>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50">Clear</a>
            </div>
        </form>

        <div class="overflow-hidden rounded-3xl bg-white ring-1 ring-slate-900/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Points</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Created</th>
                            <th class="sticky right-0 z-10 bg-slate-50 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $user)
                            <tr>
                                @php
                                    $now = now();
                                    $isBanned = (bool) ($user->banned_at && (! $user->banned_until || $user->banned_until->greaterThan($now)));
                                @endphp
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-900">{{ $user->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">{{ $user->email }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ $user->role ?? 'user' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">{{ number_format((int) ($user->points ?? 0)) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    @if($isBanned)
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex w-fit items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-800">
                                                Banned
                                                @if($user->banned_until)
                                                    ({{ $user->banned_until->diffForHumans() }})
                                                @else
                                                    (Permanent)
                                                @endif
                                            </span>
                                            @if($user->banned_until)
                                                <span class="text-xs font-semibold text-slate-600">Until {{ $user->banned_until->diffForHumans() }}</span>
                                            @else
                                                <span class="text-xs font-semibold text-slate-600">Permanent</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Active</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">{{ $user->created_at?->diffForHumans() }}</td>
                                <td class="sticky right-0 bg-white px-6 py-4 text-sm shadow-[-8px_0_16px_-16px_rgba(15,23,42,0.35)]">
                                    <div class="grid min-w-[16rem] gap-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex flex-wrap items-center gap-2">
                                                @csrf
                                                <select name="role" class="rounded-2xl border border-slate-200 bg-white py-2 pl-3 pr-9 text-xs">
                                                    <option value="user" @selected(($user->role ?? 'user') === 'user')>user</option>
                                                    <option value="admin" @selected(($user->role ?? 'user') === 'admin')>admin</option>
                                                </select>
                                                <button class="inline-flex items-center justify-center rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50">Set role</button>
                                            </form>

                                            @if(!$isBanned)
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center justify-center rounded-2xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-rose-500"
                                                    x-on:click="openBan({{ (int) $user->id }}, @js($user->name))"
                                                >
                                                    Ban…
                                                </button>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                                                    @csrf
                                                    <button class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-500">Unban</button>
                                                </form>
                                            @endif
                                        </div>

                                        <form method="POST" action="{{ route('admin.users.points', $user) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            <input name="delta" type="number" step="1" class="w-24 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs" placeholder="+/-" required />
                                            <input name="reason" class="w-44 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs" placeholder="Reason (optional)" />
                                            <button class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">Adjust</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-6 py-4">
                {{ $users->links() }}
            </div>
        </div>

        <div
            x-cloak
            x-show="banOpen"
            x-on:keydown.escape.window="closeBan()"
            class="fixed inset-0 z-[200] flex items-center justify-center px-4"
        >
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur" x-on:click="closeBan()"></div>

            <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 shadow-xl ring-1 ring-slate-900/10">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold text-slate-900">Ban user</div>
                        <div class="mt-1 text-sm text-slate-600">กำหนดระยะเวลา + เหตุผล แล้วกดยืนยัน</div>
                    </div>
                    <button type="button" class="-m-2 rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700" x-on:click="closeBan()">✕</button>
                </div>

                <form method="POST" x-bind:action="banAction" class="mt-5 grid gap-4">
                    @csrf

                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-900/5">
                        <div class="text-sm font-semibold text-slate-900">ยืนยันการแบน</div>
                        <div class="mt-1 text-sm text-slate-700">
                            ต้องการแบน <span class="font-semibold" x-text="banUserName"></span>
                            เป็นเวลา <span class="font-semibold" x-text="durationLabel()"></span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Duration</label>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <select name="duration" x-model="duration" class="min-w-[10rem] rounded-2xl border border-slate-200 bg-white py-2 pl-3 pr-10 text-sm">
                                <option value="permanent">Permanent</option>
                                <option value="1h">1 hour</option>
                                <option value="1d">1 day</option>
                                <option value="3d">3 days</option>
                                <option value="7d">7 days</option>
                                <option value="30d">30 days</option>
                                <option value="custom">Custom…</option>
                            </select>

                            <input
                                name="banned_until"
                                type="datetime-local"
                                x-model="bannedUntil"
                                x-bind:disabled="duration !== 'custom'"
                                x-bind:required="duration === 'custom'"
                                class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm disabled:bg-slate-100"
                            />
                        </div>
                        <div class="mt-2 text-xs text-slate-500">เลือก Custom ถ้าต้องการกำหนดวันเวลาที่แน่นอน</div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Reason (optional)</label>
                        <textarea name="reason" x-model="reason" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900" placeholder="ระบุเหตุผล (ถ้ามี)"></textarea>
                    </div>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50" x-on:click="closeBan()">Cancel</button>
                        <button
                            class="inline-flex items-center justify-center rounded-2xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500 disabled:opacity-50"
                            x-bind:disabled="duration === 'custom' && !bannedUntil"
                        >
                            Confirm ban
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
