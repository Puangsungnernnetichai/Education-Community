@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-slate-950">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
            <div class="mb-6 flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Mini Sudoku</div>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">ซูโดกุ 4x4 แบบเร็ว</h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">ใส่เลข 1–4 ไม่ซ้ำในแถว/คอลัมน์ และช่องย่อย 2x2</p>
                </div>

                <a
                    href="{{ route('games.index') }}"
                    class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200 dark:hover:bg-slate-900"
                >
                    กลับหน้าเกม
                </a>
            </div>

            <div
                x-data="miniSudokuGame()"
                x-init="init()"
                x-cloak
                class="rounded-3xl bg-white p-5 ring-1 ring-slate-900/10 dark:bg-slate-900/40 dark:ring-white/10 sm:p-7"
            >
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        คะแนน: <span class="text-indigo-600 dark:text-indigo-400" x-text="earnedPoints"></span>
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-300">
                        สถานะ: <span class="font-semibold" x-text="statusText"></span>
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
                            <li>ใส่เลข 1–4</li>
                            <li>ห้ามซ้ำในแถว/คอลัมน์</li>
                            <li>ห้ามซ้ำในช่องย่อย 2x2</li>
                            <li>ทำสำเร็จได้คะแนน (ยิ่งเร็ว ยิ่งได้มาก)</li>
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
                    <div class="mt-6 grid gap-6 lg:grid-cols-2">
                        <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-900/10 dark:bg-slate-950/40 dark:ring-white/10">
                            <div class="grid grid-cols-4 gap-2">
                                <template x-for="cell in cells" :key="cell.key">
                                    <button
                                        type="button"
                                        @click="select(cell.r, cell.c)"
                                        class="relative flex aspect-square items-center justify-center rounded-2xl border bg-white text-lg font-black transition active:scale-[0.98] dark:bg-slate-900/60"
                                        :class="cellClass(cell)"
                                    >
                                        <span x-text="valueAt(cell.r, cell.c) || ''"></span>

                                        <div
                                            class="pointer-events-none absolute inset-0 rounded-2xl ring-2 ring-indigo-400/60"
                                            x-show="selected.r === cell.r && selected.c === cell.c"
                                        ></div>
                                    </button>
                                </template>
                            </div>
                            <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">แตะช่อง แล้วเลือกเลขด้านขวา</p>
                        </div>

                        <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-900/10 dark:bg-slate-950/40 dark:ring-white/10">
                            <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">แป้นตัวเลข</div>
                            <div class="mt-3 grid grid-cols-4 gap-2">
                                <template x-for="n in [1,2,3,4]" :key="n">
                                    <button
                                        type="button"
                                        @click="setNumber(n)"
                                        :disabled="!canEditSelected"
                                        class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500 active:scale-95 disabled:opacity-70"
                                    >
                                        <span x-text="n"></span>
                                    </button>
                                </template>
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <button
                                    type="button"
                                    @click="clearSelected()"
                                    :disabled="!canEditSelected"
                                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 disabled:opacity-70 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200 dark:hover:bg-slate-900"
                                >
                                    ลบ
                                </button>
                                <button
                                    type="button"
                                    @click="giveUp()"
                                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200 dark:hover:bg-slate-900"
                                >
                                    ยอมแพ้
                                </button>
                            </div>

                            <template x-if="hint">
                                <p class="mt-4 text-sm font-semibold text-rose-700" x-text="hint"></p>
                            </template>

                            <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">คะแนน: สำเร็จ = 20 + โบนัสตามเวลาที่เหลือ</p>
                        </div>
                    </div>
                </template>

                <template x-if="state === 'over'">
                    <div class="mt-6 rounded-3xl bg-slate-50 p-6 ring-1 ring-slate-900/10 dark:bg-slate-950/50 dark:ring-white/10">
                        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">จบเกม</div>
                        <div class="mt-2 text-sm text-slate-600 dark:text-slate-300" x-text="endReasonText"></div>

                        <div class="mt-5 rounded-2xl bg-white p-4 ring-1 ring-slate-900/10 dark:bg-slate-900/60 dark:ring-white/10">
                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">คะแนนที่ได้</div>
                            <div class="mt-1 text-2xl font-bold text-indigo-600 dark:text-indigo-400" x-text="earnedPoints"></div>
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
        function miniSudokuGame() {
            const TIME_MS = 90_000;
            const BASE_SCORE = 20;

            // Base solved 4x4 grid (valid sudoku solution). We'll randomize it per run.
            const baseSolution = [
                [1, 2, 3, 4],
                [3, 4, 1, 2],
                [2, 1, 4, 3],
                [4, 3, 2, 1],
            ];

            // Mask for givens: 1 = prefilled, 0 = empty.
            // Keep it simple (same amount of givens each run) while randomizing the underlying solution.
            const givensMask = [
                [1, 0, 1, 0],
                [0, 1, 0, 1],
                [0, 0, 1, 0],
                [1, 0, 0, 1],
            ];

            function deepCopyGrid(g) {
                return g.map(r => r.slice());
            }

            function shuffle(arr) {
                const a = arr.slice();
                for (let i = a.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [a[i], a[j]] = [a[j], a[i]];
                }
                return a;
            }

            function permuteRows(grid) {
                // For 4x4: swap rows within each 2-row band, then optionally swap bands.
                let rows = [0, 1, 2, 3];
                const band0 = shuffle([0, 1]);
                const band1 = shuffle([2, 3]);
                rows = [band0[0], band0[1], band1[0], band1[1]];

                if (Math.random() < 0.5) {
                    rows = [rows[2], rows[3], rows[0], rows[1]];
                }

                return rows.map(r => grid[r].slice());
            }

            function permuteCols(grid) {
                // For 4x4: swap cols within each 2-col stack, then optionally swap stacks.
                const stack0 = shuffle([0, 1]);
                const stack1 = shuffle([2, 3]);
                let cols = [stack0[0], stack0[1], stack1[0], stack1[1]];

                if (Math.random() < 0.5) {
                    cols = [cols[2], cols[3], cols[0], cols[1]];
                }

                const out = [];
                for (let r = 0; r < 4; r++) {
                    out[r] = [];
                    for (let c = 0; c < 4; c++) {
                        out[r][c] = grid[r][cols[c]];
                    }
                }
                return out;
            }

            function permuteDigits(grid) {
                const digits = shuffle([1, 2, 3, 4]);
                const map = {
                    1: digits[0],
                    2: digits[1],
                    3: digits[2],
                    4: digits[3],
                };

                return grid.map(row => row.map(v => map[v]));
            }

            function generateBoard() {
                let sol = deepCopyGrid(baseSolution);
                sol = permuteRows(sol);
                sol = permuteCols(sol);
                sol = permuteDigits(sol);

                const puz = [];
                for (let r = 0; r < 4; r++) {
                    puz[r] = [];
                    for (let c = 0; c < 4; c++) {
                        puz[r][c] = givensMask[r][c] ? sol[r][c] : 0;
                    }
                }

                return { puzzle: puz, solution: sol };
            }

            function inRange(n) {
                return n === 1 || n === 2 || n === 3 || n === 4;
            }

            function blockStart(i) {
                return i < 2 ? 0 : 2;
            }

            return {
                state: 'idle',

                grid: deepCopyGrid(baseSolution).map(r => r.map(() => 0)),
                fixed: deepCopyGrid(baseSolution).map(r => r.map(() => false)),
                solution: deepCopyGrid(baseSolution),

                selected: { r: 0, c: 0 },
                hint: '',

                timeLeftMs: TIME_MS,
                progressPercent: 100,
                timerId: null,
                startedAtMs: null,

                earnedPoints: 0,
                endReason: null,
                saveStatus: 'ยังไม่เริ่ม',
                saveError: '',
                saved: false,

                cells: [],

                get statusText() {
                    if (this.state === 'idle') return 'พร้อมเริ่ม';
                    if (this.state === 'playing') return 'กำลังเล่น';
                    if (this.state === 'over') return 'จบเกม';
                    return '';
                },

                get endReasonText() {
                    if (this.endReason === 'solve') return 'ทำสำเร็จ!';
                    if (this.endReason === 'wrong') return 'ใส่ผิด! เกมจบ';
                    if (this.endReason === 'timeout') return 'หมดเวลา';
                    if (this.endReason === 'giveup') return 'ยอมแพ้';
                    return 'จบเกม';
                },

                get canEditSelected() {
                    return this.state === 'playing' && !this.fixed[this.selected.r][this.selected.c];
                },

                init() {
                    this.cells = [];
                    for (let r = 0; r < 4; r++) {
                        for (let c = 0; c < 4; c++) {
                            this.cells.push({ r, c, key: r + '-' + c });
                        }
                    }
                    this.selected = { r: 0, c: 0 };
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
                    this.generateNewBoard();
                    this.selected = { r: 0, c: 0 };
                    this.hint = '';
                    this.timeLeftMs = TIME_MS;
                    this.progressPercent = 100;
                    this.earnedPoints = 0;
                    this.endReason = null;
                    this.saveStatus = 'ยังไม่เริ่ม';
                    this.saveError = '';
                    this.saved = false;
                },

                generateNewBoard() {
                    const { puzzle, solution } = generateBoard();
                    this.solution = deepCopyGrid(solution);
                    this.grid = deepCopyGrid(puzzle);
                    this.fixed = deepCopyGrid(puzzle).map(r => r.map(v => v !== 0));
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

                valueAt(r, c) {
                    const v = this.grid[r][c];
                    return v === 0 ? null : v;
                },

                select(r, c) {
                    this.selected = { r, c };
                    this.hint = '';
                },

                setNumber(n) {
                    if (this.state !== 'playing') return;
                    if (!this.canEditSelected) return;
                    if (!inRange(n)) return;

                    const { r, c } = this.selected;
                    this.grid[r][c] = n;
                    this.hint = '';

                    // Instant-fail on wrong entry to make the game feel decisive.
                    // (Previously we only highlighted conflicts, which felt like the game didn't end.)
                    if (this.solution?.[r]?.[c] !== n) {
                        this.earnedPoints = 0;
                        this.hint = 'ผิด! ลองเริ่มใหม่อีกครั้ง';
                        this.gameOver('wrong');
                        return;
                    }

                    if (this.isCompleteAndValid()) {
                        const bonus = Math.max(0, Math.floor(this.timeLeftMs / 5000));
                        this.earnedPoints = BASE_SCORE + bonus;
                        this.gameOver('solve');
                    }
                },

                clearSelected() {
                    if (!this.canEditSelected) return;
                    const { r, c } = this.selected;
                    this.grid[r][c] = 0;
                },

                giveUp() {
                    if (this.state !== 'playing') return;
                    this.earnedPoints = 0;
                    this.gameOver('giveup');
                },

                cellClass(cell) {
                    const v = this.grid[cell.r][cell.c];
                    const fixed = this.fixed[cell.r][cell.c];
                    const conflict = this.hasConflict(cell.r, cell.c);

                    let cls = '';
                    cls += fixed
                        ? 'border-slate-200 bg-slate-50 text-slate-900 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100 '
                        : 'border-slate-200 bg-white text-slate-900 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-100 dark:hover:bg-slate-900 ';

                    if (conflict) {
                        cls += 'border-rose-300 bg-rose-50 text-rose-800 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200 ';
                    }

                    // Add thicker separators for 2x2 blocks.
                    const r = cell.r;
                    const c = cell.c;
                    if (r === 1) cls += 'mb-1 ';
                    if (c === 1) cls += 'mr-1 ';

                    return cls;
                },

                hasConflict(r, c) {
                    const v = this.grid[r][c];
                    if (v === 0) return false;
                    if (!inRange(v)) return true;

                    // Row
                    for (let cc = 0; cc < 4; cc++) {
                        if (cc === c) continue;
                        if (this.grid[r][cc] === v) return true;
                    }

                    // Col
                    for (let rr = 0; rr < 4; rr++) {
                        if (rr === r) continue;
                        if (this.grid[rr][c] === v) return true;
                    }

                    // 2x2 block
                    const br = blockStart(r);
                    const bc = blockStart(c);
                    for (let rr = br; rr < br + 2; rr++) {
                        for (let cc = bc; cc < bc + 2; cc++) {
                            if (rr === r && cc === c) continue;
                            if (this.grid[rr][cc] === v) return true;
                        }
                    }

                    return false;
                },

                isCompleteAndValid() {
                    // Must be fully filled, all in range, and no conflicts.
                    for (let r = 0; r < 4; r++) {
                        for (let c = 0; c < 4; c++) {
                            const v = this.grid[r][c];
                            if (!inRange(v)) return false;
                            if (this.hasConflict(r, c)) return false;
                        }
                    }
                    return true;
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

                    const finishUrl = "{{ route('games.mini_sudoku.finish') }}";

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch(finishUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token || '',
                            },
                            body: JSON.stringify({
                                score: this.earnedPoints,
                                duration: durationSec,
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
