<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h1 class="text-xl font-semibold text-slate-900">บัญชีถูกระงับการใช้งาน</h1>
            <p class="text-sm text-slate-600">คุณถูกออกจากระบบแล้ว</p>
        </div>
    </x-slot>

    @php
        $reason = is_array($data ?? null) ? ($data['reason'] ?? null) : null;
        $untilHuman = is_array($data ?? null) ? ($data['banned_until_human'] ?? null) : null;
        $untilIso = is_array($data ?? null) ? ($data['banned_until'] ?? null) : null;
    @endphp

    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
        <div class="rounded-3xl bg-white p-8 ring-1 ring-slate-900/5">
            <div class="rounded-2xl bg-rose-50 p-4 ring-1 ring-rose-200/60">
                <div class="text-sm font-semibold text-rose-800">แจ้งเตือน</div>
                <div class="mt-2 text-sm text-rose-800">
                    บัญชีนี้ถูกแบนจากผู้ดูแลระบบ
                </div>
            </div>

            <div class="mt-6 grid gap-3 text-sm text-slate-700">
                @if(is_string($reason) && trim($reason) !== '')
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">เหตุผล</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ $reason }}</div>
                    </div>
                @endif

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">ระยะเวลา</div>
                    @if(is_string($untilIso) && is_string($untilHuman) && trim($untilHuman) !== '')
                        <div class="mt-1 font-semibold text-slate-900">เหลือประมาณ {{ $untilHuman }}</div>
                    @else
                        <div class="mt-1 font-semibold text-slate-900">ถาวร</div>
                    @endif
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    OK
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
