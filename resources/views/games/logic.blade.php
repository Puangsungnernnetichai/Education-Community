@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-slate-950">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
            <div class="mb-6 flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Logic Rush</div>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">ตอบไว คิดให้ชัด ภายใน 10 วินาที</h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">ตอบถูก +5 แต้ม ผิดหรือหมดเวลา = จบเกม</p>
                </div>

                <a
                    href="{{ route('games.index') }}"
                    class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200 dark:hover:bg-slate-900"
                >
                    กลับหน้าเกม
                </a>
            </div>

            <div
                x-data="logicRushGame()"
                x-init="init()"
                x-cloak
                class="rounded-3xl bg-white p-5 ring-1 ring-slate-900/10 dark:bg-slate-900/40 dark:ring-white/10 sm:p-7"
            >
                <div class="flex items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        คะแนน: <span class="text-indigo-600 dark:text-indigo-400" x-text="earnedPoints"></span>
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-300">
                        ถูก: <span class="font-semibold" x-text="correctCount"></span>
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
                        <span>เวลา</span>
                        <span x-text="Math.ceil(timeLeftMs / 1000) + 's'"></span>
                    </div>
                </div>

                <template x-if="state === 'idle'">
                    <div class="mt-6 rounded-2xl bg-slate-50 p-5 text-sm text-slate-700 ring-1 ring-slate-900/10 dark:bg-slate-950/50 dark:text-slate-200 dark:ring-white/10">
                        <div class="font-semibold">กติกา</div>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            <li>มี 1 คำถามต่อครั้ง</li>
                            <li>มี 10 วินาทีต่อข้อ</li>
                            <li>ตอบถูกได้ +5 แต้ม และไปข้อถัดไป</li>
                            <li>ตอบผิดหรือหมดเวลา = จบเกม</li>
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
                            :key="questionKey"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="rounded-2xl bg-white p-5 ring-1 ring-slate-900/10 dark:bg-slate-950/40 dark:ring-white/10"
                        >
                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                ข้อ <span x-text="questionIndex + 1"></span> / <span x-text="questions.length"></span>
                            </div>
                            <div class="mt-2 text-base font-semibold text-slate-900 dark:text-slate-100" x-text="currentQuestion.question"></div>

                            <div class="mt-4 grid gap-3">
                                <template x-for="(choice, idx) in currentQuestion.choices" :key="idx">
                                    <button
                                        type="button"
                                        @click="answer(idx)"
                                        :disabled="lock"
                                        class="group flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-left text-sm font-semibold text-slate-800 transition hover:-translate-y-0.5 hover:bg-slate-50 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-70 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-100 dark:hover:bg-slate-900"
                                    >
                                        <span class="min-w-0 flex-1" x-text="choice"></span>
                                        <span class="ml-3 inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-xs font-bold text-slate-600 transition group-hover:bg-indigo-50 group-hover:text-indigo-700 dark:bg-slate-800 dark:text-slate-200 dark:group-hover:bg-indigo-500/10 dark:group-hover:text-indigo-300" x-text="String.fromCharCode(65 + idx)"></span>
                                    </button>
                                </template>
                            </div>

                            <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">Tip: ถ้าไม่แน่ใจ ลองตัดช้อยส์ที่เป็นไปไม่ได้ก่อน</p>
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
                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">ตอบถูก</div>
                                <div class="mt-1 text-2xl font-bold text-slate-900 dark:text-slate-100" x-text="correctCount"></div>
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
        function logicRushGame() {
            const QUESTION_TIME_MS = 10_000;
            const POINTS_PER_CORRECT = 5;

            const questionBank = [
                {
                    question: 'ข้อไหน “เข้าพวก” กับคำว่า: แมว, สุนัข, ม้า',
                    choices: ['สัตว์เลี้ยงลูกด้วยนม', 'สัตว์ปีก', 'สัตว์เลื้อยคลาน', 'ปลา'],
                    correct_index: 0,
                },
                {
                    question: 'ถ้า A > B และ B > C ข้อใด “ต้องจริง”',
                    choices: ['A > C', 'C > A', 'A = C', 'สรุปไม่ได้'],
                    correct_index: 0,
                },
                {
                    question: 'คำไหนเป็น “ตรงข้าม” ของคำว่า “โปร่งใส”',
                    choices: ['ทึบแสง', 'สว่าง', 'บาง', 'สะอาด'],
                    correct_index: 0,
                },
                {
                    question: 'เลือกข้อที่เป็นรูปแบบเดียวกัน: 2, 4, 8, 16, ?',
                    choices: ['18', '24', '32', '34'],
                    correct_index: 2,
                },
                {
                    question: 'ถ้าทุกคนที่ “ขยัน” จะ “ทำการบ้าน” และ นัท “ขยัน” ข้อใดสรุปได้',
                    choices: ['นัททำการบ้าน', 'นัทไม่ทำการบ้าน', 'นัทขี้เกียจ', 'สรุปไม่ได้'],
                    correct_index: 0,
                },
                {
                    question: 'ข้อใดไม่เข้าพวก: สามเหลี่ยม, สี่เหลี่ยม, วงกลม, ห้าเหลี่ยม',
                    choices: ['สามเหลี่ยม', 'สี่เหลี่ยม', 'วงกลม', 'ห้าเหลี่ยม'],
                    correct_index: 2,
                },
                {
                    question: 'ถ้า “วันนี้ฝนตก” แล้ว “ถนนเปียก” ข้อใดเป็นเหตุผลที่เหมาะสม',
                    choices: ['ฝนทำให้พื้นเปียก', 'ถนนทำให้ฝนตก', 'ถนนแห้งเสมอ', 'ไม่มีความเกี่ยวข้อง'],
                    correct_index: 0,
                },
                {
                    question: 'เลือกคำที่สัมพันธ์กัน: หนังสือ : อ่าน = เพลง : ?',
                    choices: ['วาด', 'ฟัง', 'ดม', 'จับ'],
                    correct_index: 1,
                },
                {
                    question: 'ถ้า 5 คนแบ่งขนม 20 ชิ้นเท่า ๆ กัน คนละกี่ชิ้น',
                    choices: ['2', '3', '4', '5'],
                    correct_index: 2,
                },
                {
                    question: 'ข้อใดเป็น “กฎ” ที่สอดคล้อง: ถ้าเลขคู่ → หาร 2 ลงตัว',
                    choices: ['6 หาร 2 ลงตัว', '9 หาร 2 ลงตัว', '7 หาร 2 ลงตัว', '11 หาร 2 ลงตัว'],
                    correct_index: 0,
                },
            ];

            return {
                state: 'idle',

                questions: [],
                questionIndex: 0,
                questionKey: 0,
                lock: false,

                earnedPoints: 0,
                correctCount: 0,

                startedAtMs: null,
                timeLeftMs: QUESTION_TIME_MS,
                timerId: null,
                progressPercent: 100,

                endReason: null,
                saveStatus: 'ยังไม่เริ่ม',
                saveError: '',
                saved: false,

                get currentQuestion() {
                    return this.questions[this.questionIndex] || { question: '', choices: [], correct_index: 0 };
                },

                get endReasonText() {
                    if (this.endReason === 'timeout') return 'หมดเวลา';
                    if (this.endReason === 'wrong') return 'ตอบผิด';
                    if (this.endReason === 'complete') return 'ทำครบทุกข้อ เยี่ยมมาก!';
                    return 'จบเกม';
                },

                init() {
                    this.questions = questionBank.map(q => ({
                        question: q.question,
                        choices: q.choices,
                        correct_index: q.correct_index,
                    }));
                },

                start() {
                    this.resetState();
                    this.state = 'playing';
                    this.startedAtMs = Date.now();
                    this.startTimer();
                },

                restart() {
                    this.stopTimer();
                    this.state = 'idle';
                    this.resetState();
                },

                resetState() {
                    this.questionIndex = 0;
                    this.questionKey++;
                    this.lock = false;

                    this.earnedPoints = 0;
                    this.correctCount = 0;

                    this.endReason = null;
                    this.saveStatus = 'ยังไม่เริ่ม';
                    this.saveError = '';
                    this.saved = false;

                    this.timeLeftMs = QUESTION_TIME_MS;
                    this.progressPercent = 100;
                },

                startTimer() {
                    this.stopTimer();
                    this.timeLeftMs = QUESTION_TIME_MS;
                    this.progressPercent = 100;

                    const started = Date.now();
                    this.timerId = window.setInterval(() => {
                        const elapsed = Date.now() - started;
                        const left = Math.max(0, QUESTION_TIME_MS - elapsed);
                        this.timeLeftMs = left;
                        this.progressPercent = Math.max(0, Math.min(100, (left / QUESTION_TIME_MS) * 100));

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

                answer(index) {
                    if (this.lock || this.state !== 'playing') return;
                    this.lock = true;
                    this.stopTimer();

                    const correct = this.currentQuestion.correct_index;
                    if (index === correct) {
                        this.correctCount += 1;
                        this.earnedPoints += POINTS_PER_CORRECT;

                        const nextIndex = this.questionIndex + 1;
                        if (nextIndex >= this.questions.length) {
                            this.gameOver('complete');
                            return;
                        }

                        window.setTimeout(() => {
                            this.questionIndex = nextIndex;
                            this.questionKey++;
                            this.lock = false;
                            this.startTimer();
                        }, 180);
                        return;
                    }

                    this.gameOver('wrong');
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
                        const res = await fetch('{{ url('/games/logic/finish') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token || '',
                            },
                            body: JSON.stringify({
                                score: this.earnedPoints,
                                duration: durationSec,
                                correct_count: this.correctCount,
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
