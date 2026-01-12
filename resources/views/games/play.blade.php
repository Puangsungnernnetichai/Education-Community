@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-10">
    <div class="flex items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold">Play: {{ $game->name }}</h1>
        <div class="text-sm text-gray-600">Your points: <span id="user-points" class="font-semibold text-gray-900">{{ auth()->user()->points ?? 0 }}</span></div>
    </div>

    @if(session('success'))
        <div class="mt-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    @if(($game->type ?? null) === 'math_sprint')
        <style>
            @keyframes ms-shake {
                0%, 100% { transform: translateX(0); }
                20% { transform: translateX(-8px); }
                40% { transform: translateX(8px); }
                60% { transform: translateX(-6px); }
                80% { transform: translateX(6px); }
            }
            .ms-shake { animation: ms-shake 320ms ease-in-out; }
        </style>

        <div class="mt-6 bg-white shadow rounded p-6">
            <!-- Countdown overlay (3..2..1..GO) -->
            <div id="ms-countdown-overlay" class="fixed inset-0 z-[150] hidden items-center justify-center bg-slate-950/60 opacity-0 transition-opacity duration-200 pointer-events-none">
                <div class="relative flex items-center justify-center">
                    <div id="ms-countdown-lottie" class="absolute inset-0 -z-10 h-64 w-64"></div>
                    <div class="rounded-3xl bg-white/95 px-10 py-8 text-center shadow-sm ring-1 ring-slate-900/10 backdrop-blur">
                        <div class="text-sm font-semibold text-slate-600">Get ready</div>
                        <div id="ms-countdown-text" class="mt-2 text-6xl font-black tracking-tight text-slate-900">3</div>
                    </div>
                </div>
            </div>

            <!-- End overlay (score reveal + confetti) -->
            <div id="ms-end-overlay" class="fixed inset-0 z-[150] hidden items-center justify-center bg-slate-950/60 opacity-0 transition-opacity duration-200 pointer-events-none">
                <div class="relative w-full max-w-md">
                    <div id="ms-confetti" class="pointer-events-none absolute -inset-10"></div>
                    <div class="relative z-10 rounded-3xl bg-white/95 p-8 text-center shadow-sm ring-1 ring-slate-900/10 backdrop-blur">
                        <div class="text-sm font-semibold text-slate-600">Time’s up!</div>
                        <div class="mt-2 text-4xl font-black tracking-tight text-slate-900">Your score</div>
                        <div id="ms-score-reveal" class="mt-3 text-6xl font-black tracking-tight text-indigo-600">0</div>
                        <div class="mt-4 text-sm text-slate-600">Tap Done to save your points.</div>

                        <div class="mt-6 flex w-full flex-col items-center justify-center gap-3">
                            <button id="ms-end-done" type="button" data-mode="save" class="inline-flex w-full max-w-xs items-center justify-center rounded-xl bg-green-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-green-700 disabled:opacity-50" disabled>
                                Done
                            </button>

                            <button id="ms-end-retry" type="button" data-start-url="{{ route('games.math_sprint.start', $game) }}" class="hidden inline-flex w-full max-w-xs items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50" disabled>
                                Retry
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between gap-4">
                <div class="text-sm text-gray-600">Answer as many questions as you can in 60 seconds.</div>
                <div class="text-sm font-semibold text-gray-900">Time: <span id="ms-time">60</span>s</div>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <button id="ms-start" type="button" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50">
                    Start
                </button>
            </div>

            <div class="mt-3 h-2 w-full rounded bg-gray-200 overflow-hidden">
                <div id="ms-timer-bar" class="h-full bg-indigo-600 transition-all duration-1000" style="width:100%"></div>
            </div>

            <div id="ms-card" class="mt-6 rounded-lg border bg-gray-50 p-6 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">Score: <span id="ms-score" class="font-semibold text-gray-900">0</span></div>
                    <div class="text-sm text-gray-600">Combo: <span id="ms-combo" class="font-semibold text-gray-900">0</span></div>
                </div>
                <div class="relative">
                    <div id="ms-question" class="mt-4 text-3xl font-bold text-gray-900">Loading...</div>
                    <div id="ms-feedback-lottie" class="pointer-events-none absolute -right-6 -top-6 h-20 w-20 opacity-0 transition-opacity duration-150"></div>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="button" class="ms-choice w-full rounded-lg bg-white px-4 py-3 text-lg font-semibold text-gray-900 ring-1 ring-gray-200 transition hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-200" data-index="0"></button>
                    <button type="button" class="ms-choice w-full rounded-lg bg-white px-4 py-3 text-lg font-semibold text-gray-900 ring-1 ring-gray-200 transition hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-200" data-index="1"></button>
                    <button type="button" class="ms-choice w-full rounded-lg bg-white px-4 py-3 text-lg font-semibold text-gray-900 ring-1 ring-gray-200 transition hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-200" data-index="2"></button>
                    <button type="button" class="ms-choice w-full rounded-lg bg-white px-4 py-3 text-lg font-semibold text-gray-900 ring-1 ring-gray-200 transition hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-200" data-index="3"></button>
                </div>

                <div id="ms-feedback" class="mt-4 text-sm font-semibold"></div>
            </div>

            <form id="ms-submit" action="{{ route('games.submit') }}" method="POST" class="mt-6">
                @csrf
                <input type="hidden" name="game_id" value="{{ $game->id }}">
                <input type="hidden" name="nonce" value="{{ $mathSprintNonce ?? '' }}">
                <input type="hidden" name="answers" id="ms-answers" value="[]">
                <input type="hidden" name="duration" id="ms-duration" value="60">

                <button id="ms-finish" type="submit" class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700 disabled:opacity-50" disabled>
                    Done
                </button>
            </form>
        </div>
    @else
        <div class="mt-6 bg-white shadow rounded p-6">
            <p class="text-sm text-gray-600">This is a simple play area. Click to generate a score and submit it to record your session.</p>

            <div id="play-area" class="mt-6 border rounded h-48 flex items-center justify-center bg-gray-50">
                <button id="generate" class="px-4 py-2 bg-indigo-600 text-white rounded">Generate Score</button>
            </div>

            <form id="submit-form" action="{{ route('games.submit') }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="game_id" value="{{ $game->id }}">
                <input type="hidden" name="score" id="score-input" value="0">
                <input type="hidden" name="duration" id="duration-input" value="0">
                <button type="submit" class="mt-3 px-4 py-2 bg-green-600 text-white rounded">Submit Score</button>
            </form>

            <div id="last-result" class="mt-4 text-gray-700"></div>
        </div>
    @endif
</div>

<script src="https://unpkg.com/lottie-web@5.12.2/build/player/lottie.min.js" defer></script>

<script>
    (function(){
        let mathSprintQuestions = @json($mathSprintQuestions ?? null);

        // Fallback simple game
        const generate = document.getElementById('generate');
        const scoreInput = document.getElementById('score-input');
        const durationInput = document.getElementById('duration-input');
        const last = document.getElementById('last-result');

        let start = Date.now();

        generate?.addEventListener('click', function(){
            const score = Math.floor(Math.random() * 1000);
            const duration = Math.floor((Date.now() - start) / 1000);
            scoreInput.value = score;
            durationInput.value = duration;
            last.textContent = `Score: ${score} — Duration: ${duration}s`;
        });

        // Math Sprint
        if (!Array.isArray(mathSprintQuestions)) return;

        const timeEl = document.getElementById('ms-time');
        const barEl = document.getElementById('ms-timer-bar');
        const questionEl = document.getElementById('ms-question');
        const choiceEls = Array.from(document.querySelectorAll('.ms-choice'));
        const scoreEl = document.getElementById('ms-score');
        const comboEl = document.getElementById('ms-combo');
        const feedbackEl = document.getElementById('ms-feedback');
        const answersInput = document.getElementById('ms-answers');
        const durationHidden = document.getElementById('ms-duration');
        const finishBtn = document.getElementById('ms-finish');
        const startBtn = document.getElementById('ms-start');
        const userPointsEl = document.getElementById('user-points');
        const msForm = document.getElementById('ms-submit');
        const cardEl = document.getElementById('ms-card');
        const nonceInput = msForm?.querySelector('input[name="nonce"]');

        const countdownOverlay = document.getElementById('ms-countdown-overlay');
        const countdownText = document.getElementById('ms-countdown-text');
        const countdownLottieHost = document.getElementById('ms-countdown-lottie');
        const feedbackLottieHost = document.getElementById('ms-feedback-lottie');
        const endOverlay = document.getElementById('ms-end-overlay');
        const confettiHost = document.getElementById('ms-confetti');
        const scoreRevealEl = document.getElementById('ms-score-reveal');
        const endDoneBtn = document.getElementById('ms-end-done');
        const endRetryBtn = document.getElementById('ms-end-retry');

        let lottieReady = false;
        let lottieCountdown = null;
        let lottieSuccess = null;
        let lottieFail = null;
        let lottieConfetti = null;

        let timeLeft = 60;
        let score = 0;
        let streak = 0;
        let answered = [];
        let currentIndex = 0;
        let running = false;
        let startedAt = null;

        function setButtonsEnabled(enabled) {
            choiceEls.forEach(btn => { btn.disabled = !enabled; });
        }

        function updateHud() {
            if (timeEl) timeEl.textContent = String(timeLeft);
            if (scoreEl) scoreEl.textContent = String(score);
            if (comboEl) comboEl.textContent = String(streak);
            if (barEl) barEl.style.width = `${Math.max(0, (timeLeft / 60) * 100)}%`;
            if (timeLeft <= 5 && barEl) {
                barEl.classList.add('animate-pulse');
            }
        }

        function pulseScore() {
            if (!scoreEl) return;
            scoreEl.classList.add('animate-pulse');
            window.setTimeout(() => scoreEl.classList.remove('animate-pulse'), 240);
        }

        function animateCardIn() {
            if (!cardEl) return;
            cardEl.classList.add('opacity-0', 'translate-y-1');
            window.requestAnimationFrame(() => {
                cardEl.classList.remove('opacity-0', 'translate-y-1');
            });
        }

        function showFeedbackLottie(kind) {
            if (!feedbackLottieHost) return;
            if (!lottieReady) {
                feedbackLottieHost.classList.add('opacity-100');
                window.setTimeout(() => feedbackLottieHost.classList.remove('opacity-100'), 140);
                return;
            }

            const anim = kind === 'success' ? lottieSuccess : lottieFail;
            if (!anim) return;
            feedbackLottieHost.classList.add('opacity-100');
            anim.goToAndPlay(0, true);
            window.setTimeout(() => feedbackLottieHost.classList.remove('opacity-100'), 450);
        }

        function markWrong() {
            if (!cardEl) return;
            cardEl.classList.remove('ms-shake');
            // reflow
            void cardEl.offsetWidth;
            cardEl.classList.add('ms-shake');
        }

        function showCountdownAndStart() {
            setButtonsEnabled(false);
            if (startBtn) {
                startBtn.disabled = true;
                startBtn.classList.add('hidden');
            }
            if (countdownOverlay) {
                countdownOverlay.classList.remove('hidden', 'pointer-events-none', 'opacity-0');
                countdownOverlay.classList.add('flex', 'pointer-events-auto');
                // allow layout to apply before fading in
                window.requestAnimationFrame(() => countdownOverlay.classList.add('opacity-100'));
            }

            const steps = ['3', '2', '1', 'GO'];
            let idx = 0;

            function tick() {
                const label = steps[idx];
                if (countdownText) {
                    countdownText.textContent = label;
                    countdownText.classList.remove('scale-100');
                    countdownText.classList.add('scale-110');
                    window.setTimeout(() => {
                        countdownText.classList.remove('scale-110');
                        countdownText.classList.add('scale-100');
                    }, 120);
                }

                idx++;
                if (idx < steps.length) {
                    window.setTimeout(tick, 750);
                    return;
                }

                window.setTimeout(() => {
                    if (countdownOverlay) {
                        countdownOverlay.classList.remove('opacity-100');
                        countdownOverlay.classList.add('opacity-0');
                        // after fade-out, fully disable overlay so it can't intercept clicks
                        window.setTimeout(() => {
                            countdownOverlay.classList.add('hidden', 'pointer-events-none');
                            countdownOverlay.classList.remove('flex', 'pointer-events-auto');
                        }, 220);
                    }

                    running = true;
                    startedAt = Date.now();
                    setButtonsEnabled(true);
                    startTimer();
                }, 450);
            }

            tick();
        }

        function showEndOverlay() {
            if (!endOverlay || !scoreRevealEl) return;
            endOverlay.classList.remove('hidden', 'pointer-events-none', 'opacity-0');
            endOverlay.classList.add('flex', 'pointer-events-auto');
            window.requestAnimationFrame(() => endOverlay.classList.add('opacity-100'));

            // Confetti (non-blocking)
            if (lottieConfetti) {
                lottieConfetti.goToAndPlay(0, true);
            }

            // Score reveal count-up
            const target = score;
            const start = 0;
            const durationMs = 700;
            const t0 = performance.now();

            function step(t) {
                const p = Math.min(1, (t - t0) / durationMs);
                const eased = 1 - Math.pow(1 - p, 3);
                const val = Math.round(start + (target - start) * eased);
                scoreRevealEl.textContent = String(val);
                if (p < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        }

        function hideEndOverlay() {
            if (!endOverlay) return;
            endOverlay.classList.remove('opacity-100');
            endOverlay.classList.add('opacity-0');
            // After fade-out, fully disable overlay so it can't intercept clicks.
            window.setTimeout(() => {
                endOverlay.classList.add('hidden', 'pointer-events-none');
                endOverlay.classList.remove('flex', 'pointer-events-auto');
            }, 220);
        }

        function resetMathSprintToStart(payload) {
            // Stop any existing timer.
            running = false;
            if (timerHandle) {
                window.clearInterval(timerHandle);
                timerHandle = null;
            }

            // Replace questions + nonce (fresh server session)
            if (Array.isArray(payload?.questions)) {
                mathSprintQuestions = payload.questions;
            }
            if (nonceInput && typeof payload?.nonce === 'string') {
                nonceInput.value = payload.nonce;
            }

            // Reset state
            timeLeft = 60;
            score = 0;
            streak = 0;
            answered = [];
            currentIndex = 0;
            startedAt = null;

            if (durationHidden) durationHidden.value = '60';
            if (answersInput) answersInput.value = '[]';

            // Reset UI
            if (barEl) {
                barEl.classList.remove('animate-pulse');
                barEl.style.width = '100%';
            }
            if (feedbackEl) {
                feedbackEl.textContent = '';
                feedbackEl.className = 'mt-4 text-sm font-semibold';
            }

            // Reset bottom Done button
            if (finishBtn) {
                finishBtn.disabled = true;
                finishBtn.textContent = 'Done';
                finishBtn.classList.remove('bg-slate-600');
                finishBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            }

            // Reset end overlay buttons
            if (endDoneBtn) {
                endDoneBtn.dataset.mode = 'save';
                endDoneBtn.textContent = 'Done';
                endDoneBtn.classList.remove('bg-slate-600');
                endDoneBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                endDoneBtn.disabled = true;
            }
            if (endRetryBtn) {
                endRetryBtn.classList.add('hidden');
                endRetryBtn.disabled = true;
                endRetryBtn.textContent = 'Retry';
            }

            if (startBtn) {
                startBtn.classList.remove('hidden');
                startBtn.disabled = false;
            }

            hideEndOverlay();
            updateHud();
            renderQuestion();
            setButtonsEnabled(false);
        }

        async function startNewSessionFromServer() {
            const url = endRetryBtn?.getAttribute('data-start-url');
            if (!url) throw new Error('Start endpoint not configured.');

            const res = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            const contentType = (res.headers.get('content-type') || '').toLowerCase();
            const isJson = contentType.includes('application/json');
            const data = isJson ? await res.json() : null;

            if (!res.ok || !data || data.ok !== true) {
                const msg = (data && data.message) ? data.message : `Start failed (HTTP ${res.status}).`;
                throw new Error(msg);
            }

            resetMathSprintToStart(data);
        }

        function renderQuestion() {
            const q = mathSprintQuestions[currentIndex % mathSprintQuestions.length];
            if (!q) return;

            questionEl.textContent = q.prompt;
            choiceEls.forEach((btn, idx) => {
                btn.textContent = String(q.choices[idx]);
                btn.classList.remove('bg-green-100', 'ring-green-300', 'bg-rose-100', 'ring-rose-300');
                btn.classList.add('bg-white', 'ring-gray-200');
            });
            if (feedbackEl) feedbackEl.textContent = '';
            animateCardIn();
        }

        function finish() {
            if (!running) return;
            running = false;
            setButtonsEnabled(false);

            const duration = Math.min(60, Math.max(0, Math.floor((Date.now() - startedAt) / 1000)));
            if (durationHidden) durationHidden.value = String(duration);
            if (answersInput) answersInput.value = JSON.stringify(answered);

            if (finishBtn) {
                finishBtn.disabled = false;
                finishBtn.focus();
            }

            if (endDoneBtn) {
                endDoneBtn.disabled = false;
            }

            if (feedbackEl) {
                feedbackEl.textContent = 'Time up! Submit your result.';
                feedbackEl.className = 'mt-4 text-sm font-semibold text-slate-700';
            }

            showEndOverlay();

            if (endDoneBtn) {
                endDoneBtn.focus();
            }
        }

        async function submitMathSprint() {
            if (!msForm || !finishBtn) return;
            if (finishBtn.disabled) return;

            finishBtn.disabled = true;
            finishBtn.textContent = 'Saving...';

            if (endDoneBtn) {
                endDoneBtn.disabled = true;
                endDoneBtn.textContent = 'Saving...';
            }

            try {
                const fd = new FormData(msForm);
                const res = await fetch(msForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: fd,
                    credentials: 'same-origin'
                });

                const contentType = (res.headers.get('content-type') || '').toLowerCase();
                const isJson = contentType.includes('application/json');
                const data = isJson ? await res.json() : null;

                if (!res.ok) {
                    const msg = (data && data.message) ? data.message : `Save failed (HTTP ${res.status}).`;
                    throw new Error(msg);
                }

                if (!data || data.ok !== true) {
                    const msg = (data && data.message) ? data.message : 'Save failed.';
                    throw new Error(msg);
                }

                if (userPointsEl && typeof data.points === 'number') {
                    userPointsEl.textContent = String(data.points);
                }

                if (feedbackEl) {
                    feedbackEl.textContent = `Saved! Final score: ${data.score}`;
                    feedbackEl.className = 'mt-4 text-sm font-semibold text-green-700';
                }

                finishBtn.textContent = 'Done';
                finishBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                finishBtn.classList.add('bg-slate-600');
                finishBtn.disabled = true;

                if (endDoneBtn) {
                    endDoneBtn.textContent = 'Close';
                    endDoneBtn.dataset.mode = 'close';
                    endDoneBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    endDoneBtn.classList.add('bg-slate-600');
                    endDoneBtn.disabled = false;
                }

                if (endRetryBtn) {
                    endRetryBtn.classList.remove('hidden');
                    endRetryBtn.disabled = false;
                }
            } catch (e) {
                const msg = (e && e.message) ? e.message : 'Save failed.';

                if (feedbackEl) {
                    feedbackEl.textContent = msg;
                    feedbackEl.className = 'mt-4 text-sm font-semibold text-rose-700';
                }

                finishBtn.textContent = 'Done';
                finishBtn.disabled = false;

                if (endDoneBtn) {
                    endDoneBtn.textContent = 'Done';
                    endDoneBtn.dataset.mode = 'save';
                    endDoneBtn.disabled = false;
                }

                if (endRetryBtn) {
                    endRetryBtn.classList.add('hidden');
                    endRetryBtn.disabled = true;
                }
            }
        }

        function applyChoice(choiceValue) {
            const q = mathSprintQuestions[currentIndex % mathSprintQuestions.length];
            if (!q || !running) return;

            answered.push({ id: q.id, choice: choiceValue });

            const correct = choiceValue === q.answer;
            if (correct) {
                score += 10;
                streak += 1;
                if (streak % 3 === 0) score += 5;

                pulseScore();
                showFeedbackLottie('success');

                if (feedbackEl) {
                    feedbackEl.textContent = 'Correct!';
                    feedbackEl.className = 'mt-4 text-sm font-semibold text-green-700';
                }
            } else {
                score -= 2;
                streak = 0;

                markWrong();
                showFeedbackLottie('fail');
                if (feedbackEl) {
                    feedbackEl.textContent = 'Wrong!';
                    feedbackEl.className = 'mt-4 text-sm font-semibold text-rose-700';
                }
            }

            // Visual feedback
            choiceEls.forEach(btn => {
                const val = parseInt(btn.textContent, 10);
                btn.classList.remove('bg-white', 'ring-gray-200');
                if (val === q.answer) {
                    btn.classList.add('bg-green-100', 'ring-green-300');
                }
                if (!correct && val === choiceValue) {
                    btn.classList.add('bg-rose-100', 'ring-rose-300');
                }
            });

            updateHud();
            setButtonsEnabled(false);
            window.setTimeout(() => {
                currentIndex += 1;
                renderQuestion();
                setButtonsEnabled(running);
            }, 350);
        }

        choiceEls.forEach((btn) => {
            btn.addEventListener('click', function(){
                const v = parseInt(btn.textContent, 10);
                if (Number.isNaN(v)) return;
                applyChoice(v);
            });
        });

        msForm?.addEventListener('submit', function (e) {
            // When finished, save and update points without redirect.
            e.preventDefault();
            submitMathSprint();
        });

        endDoneBtn?.addEventListener('click', function () {
            const mode = endDoneBtn?.dataset?.mode || 'save';
            if (mode === 'close') {
                endDoneBtn.disabled = true;
                endDoneBtn.textContent = 'Resetting...';
                startNewSessionFromServer()
                    .catch((e) => {
                        const msg = (e && e.message) ? e.message : 'Reset failed.';
                        if (feedbackEl) {
                            feedbackEl.textContent = msg;
                            feedbackEl.className = 'mt-4 text-sm font-semibold text-rose-700';
                        }
                        // Allow closing anyway
                        hideEndOverlay();
                    })
                    .finally(() => {
                        if (endDoneBtn) {
                            endDoneBtn.textContent = 'Done';
                            endDoneBtn.dataset.mode = 'save';
                            endDoneBtn.disabled = true;
                            endDoneBtn.classList.remove('bg-slate-600');
                            endDoneBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                        }
                    });
                return;
            }
            submitMathSprint();
        });

        endRetryBtn?.addEventListener('click', function () {
            endRetryBtn.disabled = true;
            endRetryBtn.textContent = 'Loading...';
            startNewSessionFromServer()
                .catch((e) => {
                    const msg = (e && e.message) ? e.message : 'Retry failed.';
                    if (feedbackEl) {
                        feedbackEl.textContent = msg;
                        feedbackEl.className = 'mt-4 text-sm font-semibold text-rose-700';
                    }
                })
                .finally(() => {
                    endRetryBtn.textContent = 'Retry';
                    endRetryBtn.disabled = false;
                });
        });

        startBtn?.addEventListener('click', function () {
            // Start countdown only after explicit user action.
            showCountdownAndStart();
        });

        let timerHandle = null;

        function startTimer() {
            if (timerHandle) return;
            timerHandle = window.setInterval(() => {
                if (!running) {
                    window.clearInterval(timerHandle);
                    timerHandle = null;
                    return;
                }

                timeLeft -= 1;
                if (timeLeft <= 0) {
                    timeLeft = 0;
                    updateHud();
                    finish();
                    window.clearInterval(timerHandle);
                    timerHandle = null;
                    return;
                }

                updateHud();
            }, 1000);
        }

        function initLottie() {
            if (!window.lottie || !lottieCountdown) {
                // try later (defer script)
                if (!window.lottie) return false;
            }
            try {
                if (countdownLottieHost) {
                    lottieCountdown = window.lottie.loadAnimation({
                        container: countdownLottieHost,
                        renderer: 'svg',
                        loop: true,
                        autoplay: true,
                        path: '/lottie/pulse.json'
                    });
                }
                if (feedbackLottieHost) {
                    lottieSuccess = window.lottie.loadAnimation({
                        container: feedbackLottieHost,
                        renderer: 'svg',
                        loop: false,
                        autoplay: false,
                        path: '/lottie/success_burst.json'
                    });
                    lottieFail = window.lottie.loadAnimation({
                        container: feedbackLottieHost,
                        renderer: 'svg',
                        loop: false,
                        autoplay: false,
                        path: '/lottie/fail_burst.json'
                    });
                }
                if (confettiHost) {
                    lottieConfetti = window.lottie.loadAnimation({
                        container: confettiHost,
                        renderer: 'svg',
                        loop: false,
                        autoplay: false,
                        path: '/lottie/confetti_simple.json'
                    });
                }
                lottieReady = true;
                return true;
            } catch (e) {
                return false;
            }
        }

        updateHud();
        renderQuestion();
        setButtonsEnabled(false);

        // Wait for lottie script (defer), then start countdown.
        const bootStart = () => {
            initLottie();
            if (startBtn) {
                startBtn.classList.remove('hidden');
                startBtn.disabled = false;
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bootStart, { once: true });
        } else {
            bootStart();
        }
    })();
</script>

@endsection
