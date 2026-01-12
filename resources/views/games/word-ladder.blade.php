@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-slate-950">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
            <div class="mb-6 flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Word Ladder</div>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">เปลี่ยนคำทีละตัวอักษร</h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">เปลี่ยนได้ครั้งละ 1 ตัวอักษร และต้องเป็นคำในลิสต์</p>
                </div>

                <a
                    href="{{ route('games.index') }}"
                    class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200 dark:hover:bg-slate-900"
                >
                    กลับหน้าเกม
                </a>
            </div>

            <div
                x-data="wordLadderGame()"
                x-init="init()"
                x-cloak
                class="rounded-3xl bg-white p-5 ring-1 ring-slate-900/10 dark:bg-slate-900/40 dark:ring-white/10 sm:p-7"
            >
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        คะแนน: <span class="text-indigo-600 dark:text-indigo-400" x-text="earnedPoints"></span>
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-300">
                        ก้าว: <span class="font-semibold" x-text="steps"></span>
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-300">
                        เป้าหมาย: <span class="font-semibold" x-text="puzzle.target"></span>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-900/10 dark:bg-slate-800 dark:ring-white/10">
                        <div class="h-full rounded-full bg-indigo-600 transition-[width] duration-75" :style="'width:' + progressPercent + '%'"></div>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>เวลา</span>
                        <span x-text="Math.ceil(timeLeftMs / 1000) + 's'"></span>
                    </div>
                </div>

                <template x-if="state === 'idle'">
                    <div class="mt-6 rounded-2xl bg-slate-50 p-5 text-sm text-slate-700 ring-1 ring-slate-900/10 dark:bg-slate-950/50 dark:text-slate-200 dark:ring-white/10">
                        <div class="font-semibold">กติกา</div>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            <li>เริ่มจากคำเริ่มต้น → ไปให้ถึงคำเป้าหมาย</li>
                            <li>แต่ละก้าว เปลี่ยนได้แค่ 1 ตัวอักษร</li>
                            <li>คำใหม่ต้องอยู่ในลิสต์คำของด่าน</li>
                            <li>หมดเวลา = จบเกม</li>
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

                <template x-if="state === 'playing'">
                    <div class="mt-6">
                        <div
                            :key="stepKey"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="rounded-2xl bg-white p-5 ring-1 ring-slate-900/10 dark:bg-slate-950/40 dark:ring-white/10"
                        >
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-900/10 dark:bg-slate-900/60 dark:ring-white/10">
                                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-300">เริ่มต้น</div>
                                    <div class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100" x-text="puzzle.start"></div>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-900/10 dark:bg-slate-900/60 dark:ring-white/10">
                                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-300">คำปัจจุบัน</div>
                                    <div class="mt-1 text-xl font-black text-indigo-600 dark:text-indigo-400" x-text="current"></div>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                                <input
                                    type="text"
                                    x-model.trim="input"
                                    @keydown.enter.prevent="submitStep()"
                                    maxlength="10"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                    placeholder="พิมพ์คำถัดไป…"
                                />
                                <button
                                    type="button"
                                    @click="submitStep()"
                                    :disabled="lock"
                                    class="inline-flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 active:scale-95 disabled:opacity-70 sm:w-auto"
                                >
                                    ยืนยัน
                                </button>
                            </div>

                            <template x-if="hint">
                                <p class="mt-3 text-sm font-semibold text-rose-700" x-text="hint"></p>
                            </template>

                            <div class="mt-4">
                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">เส้นทางของคุณ</div>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <template x-for="(w, i) in path" :key="i">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-white/10 dark:text-slate-200" x-text="w"></span>
                                    </template>
                                </div>
                            </div>

                            <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">คะแนน: +2 ต่อก้าวที่ถูกต้อง และ +10 เมื่อถึงเป้าหมาย</p>
                        </div>
                    </div>
                </template>

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
                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">ก้าว</div>
                                <div class="mt-1 text-2xl font-bold text-slate-900 dark:text-slate-100" x-text="steps"></div>
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
        function wordLadderGame() {
            const TIME_MS = 45_000;
            const POINTS_PER_STEP = 2;
            const BONUS_SOLVE = 10;

            const puzzles = [
                {
                    start: 'cold',
                    target: 'warm',
                    words: ['cold', 'cord', 'card', 'ward', 'warm', 'word', 'worm', 'work'],
                },
                {
                    start: 'game',
                    target: 'code',
                    words: ['game', 'came', 'case', 'cose', 'code', 'cade', 'gape', 'gave'],
                },
            ];

            function differsByOne(a, b) {
                if (!a || !b) return false;
                if (a.length !== b.length) return false;
                let diff = 0;
                for (let i = 0; i < a.length; i++) {
                    if (a[i] !== b[i]) diff++;
                    if (diff > 1) return false;
                }
                return diff === 1;
            }

            return {
                state: 'idle',
                lock: false,
                stepKey: 0,

                puzzle: puzzles[0],
                current: '',
                input: '',
                path: [],

                timeLeftMs: TIME_MS,
                progressPercent: 100,
                timerId: null,
                startedAtMs: null,

                earnedPoints: 0,
                steps: 0,

                endReason: null,
                saveStatus: 'ยังไม่เริ่ม',
                saveError: '',
                saved: false,

                get endReasonText() {
                    if (this.endReason === 'timeout') return 'หมดเวลา';
                    if (this.endReason === 'invalid') return 'คำไม่ถูกต้องตามกติกา';
                    if (this.endReason === 'solve') return 'ทำสำเร็จ!';
                    return 'จบเกม';
                },

                init() {
                    this.puzzle = puzzles[Math.floor(Math.random() * puzzles.length)];
                },

                start() {
                    this.reset();
                    this.state = 'playing';
                    this.startedAtMs = Date.now();
                    this.startTimer();
                },

                restart() {
                    this.stopTimer();
                    this.state = 'idle';
                    this.reset();
                },

                reset() {
                    this.lock = false;
                    this.stepKey++;
                    this.puzzle = puzzles[Math.floor(Math.random() * puzzles.length)];
                    this.current = this.puzzle.start;
                    this.input = '';
                    this.path = [this.puzzle.start];
                    this.timeLeftMs = TIME_MS;
                    this.progressPercent = 100;
                    this.earnedPoints = 0;
                    this.steps = 0;
                    this.endReason = null;
                    this.saveStatus = 'ยังไม่เริ่ม';
                    this.saveError = '';
                    this.saved = false;
                    this.hint = '';
                },

                startTimer() {
                    this.stopTimer();
                    const started = Date.now();
                    this.timerId = window.setInterval(() => {
                        const elapsed = Date.now() - started;
                        const left = Math.max(0, TIME_MS - elapsed);
                        this.timeLeftMs = left;
                        this.progressPercent = Math.max(0, Math.min(100, (left / TIME_MS) * 100));
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

                submitStep() {
                    if (this.lock || this.state !== 'playing') return;
                    this.hint = '';

                    const next = (this.input || '').toLowerCase();
                    this.input = '';

                    if (!differsByOne(this.current, next)) {
                        this.hint = 'ต้องเปลี่ยนแค่ 1 ตัวอักษร และความยาวต้องเท่ากัน';
                        this.gameOver('invalid');
                        return;
                    }

                    if (!this.puzzle.words.includes(next)) {
                        this.hint = 'คำนี้ไม่อยู่ในลิสต์ของด่าน';
                        this.gameOver('invalid');
                        return;
                    }

                    this.current = next;
                    this.path.push(next);
                    this.steps += 1;
                    this.earnedPoints += POINTS_PER_STEP;
                    this.stepKey++;

                    if (next === this.puzzle.target) {
                        this.earnedPoints += BONUS_SOLVE;
                        this.gameOver('solve');
                    }
                },

                async gameOver(reason) {
                    if (this.state === 'over') return;
                    this.stopTimer();
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
                        const res = await fetch('{{ url('/games/word-ladder/finish') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token || '',
                            },
                            body: JSON.stringify({
                                score: this.earnedPoints,
                                duration: durationSec,
                                steps: this.steps,
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
