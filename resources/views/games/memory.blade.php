@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-slate-950">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
            <div class="mb-6 flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Memory Rush</div>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">จำแพทเทิร์นให้ได้ แล้วกดตาม</h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">ผ่านรอบ +5 แต้ม กดผิดหรือหมดเวลา = จบเกม</p>
                </div>

                <a
                    href="{{ route('games.index') }}"
                    class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200 dark:hover:bg-slate-900"
                >
                    กลับหน้าเกม
                </a>
            </div>

            <div
                x-data="memoryRushGame()"
                x-init="init()"
                x-cloak
                class="rounded-3xl bg-white p-5 ring-1 ring-slate-900/10 dark:bg-slate-900/40 dark:ring-white/10 sm:p-7"
            >
                <div
                    class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/40 px-4 backdrop-blur-sm"
                    x-show="state === 'countdown'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                >
                    <div class="w-full max-w-sm rounded-3xl bg-white p-6 text-center ring-1 ring-slate-900/10 dark:bg-slate-900/80 dark:ring-white/10">
                        <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">เริ่มใน</div>
                        <div class="mt-2 text-6xl font-black tracking-tight text-indigo-600 dark:text-indigo-400" x-text="countdownValue"></div>
                        <div class="mt-3 text-sm text-slate-600 dark:text-slate-300">เตรียมดูแพทเทิร์นให้ดีนะ</div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        คะแนน: <span class="text-indigo-600 dark:text-indigo-400" x-text="earnedPoints"></span>
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-300">
                        รอบ: <span class="font-semibold" x-text="round"></span>
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-300">
                        สถานะ: <span class="font-semibold" x-text="statusText"></span>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-900/10 dark:bg-slate-800 dark:ring-white/10">
                        <div
                            class="h-full rounded-full bg-indigo-600 transition-[width] duration-75"
                            :style="'width: ' + progressPercent + '%'"
                        ></div>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>เวลา (ตอนกดตาม)</span>
                        <span x-text="Math.ceil(timeLeftMs / 1000) + 's'"></span>
                    </div>
                </div>

                <template x-if="state === 'idle'">
                    <div class="mt-6 rounded-2xl bg-slate-50 p-5 text-sm text-slate-700 ring-1 ring-slate-900/10 dark:bg-slate-950/50 dark:text-slate-200 dark:ring-white/10">
                        <div class="font-semibold">วิธีเล่น</div>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            <li>ระบบจะ “โชว์ลำดับ” ให้ดู</li>
                            <li>จากนั้นคุณต้อง “กดตามลำดับ” ภายใน 10 วินาที</li>
                            <li>ผ่านรอบได้ +5 แต้ม และลำดับจะยาวขึ้น</li>
                            <li>กดผิดหรือหมดเวลา = จบเกม</li>
                        </ul>

                        <button
                            type="button"
                            @click="start()"
                            class="mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 active:scale-95 sm:w-auto"
                        >
                            เริ่มเล่น
                        </button>
                    </div>
                </template>

                <div class="mt-6">
                    <div
                        :key="boardKey"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="grid grid-cols-2 gap-3"
                    >
                        <template x-for="(pad, idx) in pads" :key="idx">
                            <button
                                type="button"
                                @click="tap(idx)"
                                :disabled="!canTap"
                                class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 text-left ring-1 ring-slate-900/5 transition active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-70 dark:border-slate-700 dark:bg-slate-900/60 dark:ring-white/10"
                                :class="pad.flash ? pad.flashClass : ''"
                            >
                                <div
                                    class="pointer-events-none absolute -inset-6 rounded-full bg-indigo-500/15 blur-2xl transition-opacity duration-150 dark:bg-indigo-400/15"
                                    :class="pad.flash ? 'opacity-100' : 'opacity-0'"
                                ></div>
                                <div
                                    class="pointer-events-none absolute inset-0 rounded-3xl bg-indigo-500/5 transition-opacity duration-150 dark:bg-indigo-400/10"
                                    :class="pad.flash ? 'opacity-100' : 'opacity-0'"
                                ></div>

                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100" x-text="pad.label"></div>
                                    <div class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-100 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-200" x-text="pad.key"></div>
                                </div>
                                <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">แตะเพื่อกด</div>
                            </button>
                        </template>
                    </div>

                    <div class="mt-4 text-sm text-slate-600 dark:text-slate-300">
                        <span class="font-semibold">ความคืบหน้า:</span>
                        <span x-text="inputIndex"></span>/<span x-text="sequence.length"></span>
                    </div>
                </div>

                <template x-if="state === 'over'">
                    <div class="mt-6 rounded-3xl bg-slate-50 p-6 ring-1 ring-slate-900/10 dark:bg-slate-950/50 dark:ring-white/10">
                        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">จบเกม</div>
                        <div class="mt-2 text-sm text-slate-600 dark:text-slate-300" x-text="endReasonText"></div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-900/10 dark:bg-slate-900/60 dark:ring-white/10">
                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">คะแนนที่ได้</div>
                                <div class="mt-1 text-2xl font-bold text-indigo-600 dark:text-indigo-400" x-text="earnedPoints"></div>
                            </div>
                            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-900/10 dark:bg-slate-900/60 dark:ring-white/10">
                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">รอบที่ไปได้</div>
                                <div class="mt-1 text-2xl font-bold text-slate-900 dark:text-slate-100" x-text="round"></div>
                            </div>
                        </div>

                        <div class="mt-5 rounded-2xl bg-white p-4 text-sm ring-1 ring-slate-900/10 dark:bg-slate-900/60 dark:ring-white/10">
                            <div class="flex items-center justify-between gap-3">
                                <div class="font-semibold text-slate-900 dark:text-slate-100">สถานะการบันทึก</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400" x-text="saveStatus"></div>
                            </div>
                            <template x-if="saveError">
                                <p class="mt-2 text-sm font-semibold text-rose-700" x-text="saveError"></p>
                            </template>
                        </div>

                        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <button
                                type="button"
                                @click="restart()"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 active:scale-95 sm:w-auto"
                            >
                                เล่นอีกครั้ง
                            </button>
                            <a
                                href="{{ route('games.index') }}"
                                class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200 dark:hover:bg-slate-900 sm:w-auto"
                            >
                                ไปหน้าเกมอื่น
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function memoryRushGame() {
            const INPUT_TIME_MS = 10_000;
            const POINTS_PER_ROUND = 5;
            const START_COUNTDOWN = 3;

            const padDefs = [
                // Use a very obvious flash style (bg + border + ring + slight scale) so it's noticeable on mobile.
                // Keep palette consistent with the app (indigo + slate).
                { label: 'Pad A', key: 'A', flashClass: 'bg-indigo-50 border-indigo-300 ring-4 ring-indigo-300 scale-[1.02] dark:bg-indigo-500/10 dark:border-indigo-500/40 dark:ring-indigo-500/40' },
                { label: 'Pad B', key: 'B', flashClass: 'bg-indigo-50 border-indigo-300 ring-4 ring-indigo-300 scale-[1.02] dark:bg-indigo-500/10 dark:border-indigo-500/40 dark:ring-indigo-500/40' },
                { label: 'Pad C', key: 'C', flashClass: 'bg-indigo-50 border-indigo-300 ring-4 ring-indigo-300 scale-[1.02] dark:bg-indigo-500/10 dark:border-indigo-500/40 dark:ring-indigo-500/40' },
                { label: 'Pad D', key: 'D', flashClass: 'bg-indigo-50 border-indigo-300 ring-4 ring-indigo-300 scale-[1.02] dark:bg-indigo-500/10 dark:border-indigo-500/40 dark:ring-indigo-500/40' },
            ];

            function randIndex(maxExclusive) {
                return Math.floor(Math.random() * maxExclusive);
            }

            return {
                state: 'idle',
                boardKey: 0,

                countdownValue: START_COUNTDOWN,
                countdownId: null,

                pads: [],
                sequence: [],
                inputIndex: 0,

                earnedPoints: 0,
                round: 0,

                canTap: false,
                lock: false,

                startedAtMs: null,
                timeLeftMs: INPUT_TIME_MS,
                progressPercent: 100,
                timerId: null,

                endReason: null,
                saveStatus: 'ยังไม่เริ่ม',
                saveError: '',
                saved: false,

                get statusText() {
                    if (this.state === 'idle') return 'พร้อมเริ่ม';
                    if (this.state === 'showing') return 'ดูแพทเทิร์น';
                    if (this.state === 'input') return 'กดตาม';
                    if (this.state === 'over') return 'จบเกม';
                    return '';
                },

                get endReasonText() {
                    if (this.endReason === 'timeout') return 'หมดเวลา';
                    if (this.endReason === 'wrong') return 'กดผิดลำดับ';
                    return 'จบเกม';
                },

                init() {
                    this.pads = padDefs.map(p => ({ ...p, flash: false }));
                },

                start() {
                    this.resetState();
                    this.startedAtMs = Date.now();
                    this.beginCountdown();
                },

                restart() {
                    this.stopTimer();
                    this.stopCountdown();
                    this.state = 'idle';
                    this.resetState();
                },

                resetState() {
                    this.stopCountdown();
                    this.sequence = [];
                    this.inputIndex = 0;
                    this.earnedPoints = 0;
                    this.round = 0;
                    this.canTap = false;
                    this.lock = false;
                    this.endReason = null;
                    this.saveStatus = 'ยังไม่เริ่ม';
                    this.saveError = '';
                    this.saved = false;
                    this.timeLeftMs = INPUT_TIME_MS;
                    this.progressPercent = 100;
                    this.boardKey++;
                },

                beginCountdown() {
                    this.stopCountdown();
                    this.stopTimer();
                    this.unflashAll();

                    this.state = 'countdown';
                    this.canTap = false;
                    this.lock = true;
                    this.countdownValue = START_COUNTDOWN;

                    this.countdownId = window.setInterval(() => {
                        this.countdownValue -= 1;
                        if (this.countdownValue <= 0) {
                            this.stopCountdown();
                            this.lock = false;
                            this.nextRound();
                        }
                    }, 800);
                },

                stopCountdown() {
                    if (this.countdownId) {
                        window.clearInterval(this.countdownId);
                        this.countdownId = null;
                    }
                },

                async nextRound() {
                    if (this.state === 'over') return;

                    this.round += 1;
                    this.sequence.push(randIndex(this.pads.length));
                    this.inputIndex = 0;
                    this.canTap = false;
                    this.state = 'showing';
                    this.stopTimer();
                    this.timeLeftMs = INPUT_TIME_MS;
                    this.progressPercent = 100;

                    await this.showSequence();

                    this.state = 'input';
                    this.canTap = true;
                    this.startInputTimer();
                },

                async showSequence() {
                    const flashOne = async (idx) => {
                        this.flashPad(idx);
                        await this.sleep(420);
                        this.unflashAll();
                        await this.sleep(140);
                    };

                    await this.sleep(160);
                    for (const step of this.sequence) {
                        await flashOne(step);
                    }
                },

                tap(idx) {
                    if (!this.canTap || this.lock || this.state !== 'input') return;
                    this.lock = true;

                    this.flashPad(idx);
                    window.setTimeout(() => this.unflashAll(), 140);

                    const expected = this.sequence[this.inputIndex];
                    if (idx !== expected) {
                        this.gameOver('wrong');
                        return;
                    }

                    this.inputIndex += 1;

                    if (this.inputIndex >= this.sequence.length) {
                        this.stopTimer();
                        this.canTap = false;
                        this.earnedPoints += POINTS_PER_ROUND;

                        window.setTimeout(() => {
                            this.lock = false;
                            this.boardKey++;
                            this.nextRound();
                        }, 220);
                        return;
                    }

                    this.lock = false;
                },

                flashPad(idx) {
                    this.unflashAll();
                    if (this.pads[idx]) this.pads[idx].flash = true;
                },

                unflashAll() {
                    for (const p of this.pads) p.flash = false;
                },

                sleep(ms) {
                    return new Promise(resolve => window.setTimeout(resolve, ms));
                },

                startInputTimer() {
                    this.stopTimer();
                    const started = Date.now();

                    this.timerId = window.setInterval(() => {
                        const elapsed = Date.now() - started;
                        const left = Math.max(0, INPUT_TIME_MS - elapsed);
                        this.timeLeftMs = left;
                        this.progressPercent = Math.max(0, Math.min(100, (left / INPUT_TIME_MS) * 100));

                        if (left <= 0) {
                            this.stopTimer();
                            this.gameOver('timeout');
                        }
                    }, 50);
                },

                stopTimer() {
                    if (this.timerId) {
                        window.clearInterval(this.timerId);
                        this.timerId = null;
                    }
                },

                async gameOver(reason) {
                    if (this.state === 'over') return;
                    this.stopTimer();
                    this.canTap = false;
                    this.state = 'over';
                    this.endReason = reason;

                    const durationSec = this.startedAtMs ? Math.max(0, Math.round((Date.now() - this.startedAtMs) / 1000)) : null;
                    await this.submitFinish(durationSec);
                },

                async submitFinish(durationSec) {
                    if (this.saved) return;

                    this.saveStatus = 'กำลังบันทึก...';
                    this.saveError = '';

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch('{{ url('/games/memory/finish') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token || '',
                            },
                            body: JSON.stringify({
                                score: this.earnedPoints,
                                duration: durationSec,
                                round: this.round,
                            }),
                        });

                        const data = await res.json().catch(() => null);
                        if (!res.ok || !data || data.ok !== true) {
                            const msg = data?.message || 'บันทึกไม่สำเร็จ ลองใหม่อีกครั้ง';
                            throw new Error(msg);
                        }

                        this.saved = true;
                        this.saveStatus = 'บันทึกแล้ว';
                    } catch (e) {
                        this.saveStatus = 'บันทึกไม่สำเร็จ';
                        this.saveError = e?.message ? String(e.message) : 'บันทึกไม่สำเร็จ';
                    }
                },
            };
        }
    </script>
@endsection
