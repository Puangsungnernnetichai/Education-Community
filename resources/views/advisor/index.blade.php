@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-slate-100">AI Advisor</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">คุยเรื่องการเรียน การสอบ แรงจูงใจ และเส้นทางชีวิต แบบเป็นกันเอง</p>
        </div>
        <div class="text-xs font-semibold text-slate-500 dark:text-slate-300">Private to you</div>
    </div>

    <div class="mt-6 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10">
        <div class="border-b border-slate-100 px-6 py-4 dark:border-white/10">
            <div class="flex items-center justify-between gap-3">
                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Chat</div>
                <div class="text-xs text-slate-500 dark:text-slate-300">Be kind to yourself. One step at a time.</div>
            </div>
        </div>

        <div id="advisor-chat" class="h-[60vh] overflow-y-auto px-4 py-5 sm:px-6">
            <div id="advisor-chat-inner" class="space-y-3">
                @foreach (($messages ?? []) as $m)
                    @php($isUser = ($m['role'] ?? '') === 'user')
                    <div class="flex {{ $isUser ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] sm:max-w-[70%]">
                            <div class="rounded-3xl px-4 py-3 text-sm leading-6 shadow-sm ring-1 {{ $isUser
                                ? 'bg-indigo-600 text-white ring-indigo-600/10'
                                : 'bg-slate-50 text-slate-900 ring-slate-900/5 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10' }}">
                                {!! nl2br(e($m['text'] ?? '')) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div id="advisor-bottom" class="h-1"></div>
        </div>

        <div class="border-t border-slate-100 p-4 dark:border-white/10">
            <form id="advisor-form" class="flex items-end gap-3">
                @csrf
                <div class="min-w-0 flex-1">
                    <label for="advisor-input" class="sr-only">Message</label>
                    <textarea
                        id="advisor-input"
                        name="message"
                        rows="1"
                        class="max-h-40 w-full resize-none rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                        placeholder="พิมพ์คำถามของคุณ... (กด Enter เพื่อส่ง, Shift+Enter เพื่อขึ้นบรรทัดใหม่)"
                    ></textarea>
                    <p id="advisor-error" class="mt-2 hidden text-sm font-semibold text-rose-700"></p>
                </div>

                <button
                    id="advisor-send"
                    type="submit"
                    class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-50 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
                >
                    Send
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const chat = document.getElementById('advisor-chat');
        const inner = document.getElementById('advisor-chat-inner');
        const bottom = document.getElementById('advisor-bottom');
        const form = document.getElementById('advisor-form');
        const input = document.getElementById('advisor-input');
        const sendBtn = document.getElementById('advisor-send');
        const errEl = document.getElementById('advisor-error');

        function scrollToBottom(smooth = true) {
            if (!bottom) return;
            bottom.scrollIntoView({ behavior: smooth ? 'smooth' : 'auto', block: 'end' });
        }

        function setError(msg) {
            if (!errEl) return;
            if (!msg) {
                errEl.classList.add('hidden');
                errEl.textContent = '';
                return;
            }
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
        }

        function bubble(role, text, opts = {}) {
            const isUser = role === 'user';
            const row = document.createElement('div');
            row.className = `flex ${isUser ? 'justify-end' : 'justify-start'}`;

            const wrap = document.createElement('div');
            wrap.className = 'max-w-[85%] sm:max-w-[70%]';

            const b = document.createElement('div');
            b.className = 'rounded-3xl px-4 py-3 text-sm leading-6 shadow-sm ring-1 transition duration-200 opacity-0 translate-y-1 ' + (isUser
                ? 'bg-indigo-600 text-white ring-indigo-600/10'
                : 'bg-slate-50 text-slate-900 ring-slate-900/5 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10');

            if (opts.typing) {
                b.innerHTML = '<span class="inline-flex items-center gap-1 text-slate-500 dark:text-slate-300">กำลังพิมพ์<span class="animate-pulse">...</span></span>';
            } else {
                b.textContent = text;
                b.innerHTML = b.textContent.replace(/\n/g, '<br>');
            }

            wrap.appendChild(b);
            row.appendChild(wrap);

            inner.appendChild(row);
            requestAnimationFrame(() => {
                b.classList.remove('opacity-0', 'translate-y-1');
                b.classList.add('opacity-100', 'translate-y-0');
            });

            scrollToBottom(true);
            return { row, bubble: b };
        }

        function autoGrow() {
            if (!input) return;
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 160) + 'px';
        }

        input?.addEventListener('input', autoGrow);

        // Send on Enter (Shift+Enter for newline)
        input?.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form?.requestSubmit();
            }
        });

        async function sendMessage(text) {
            setError('');

            sendBtn.disabled = true;
            input.disabled = true;

            bubble('user', text);
            const typing = bubble('ai', '', { typing: true });

            try {
                const res = await fetch(@json(route('advisor.message')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ message: text })
                });

                const data = await res.json().catch(() => null);
                if (!res.ok || !data || data.ok !== true) {
                    const msg = (data && data.message) ? data.message : `ส่งข้อความไม่สำเร็จ (HTTP ${res.status})`;
                    throw new Error(msg);
                }

                // Replace typing bubble
                typing.bubble.innerHTML = '';
                typing.bubble.textContent = (data.ai && data.ai.text) ? data.ai.text : '';
                typing.bubble.innerHTML = typing.bubble.textContent.replace(/\n/g, '<br>');
            } catch (e) {
                if (typing.row && typing.row.parentNode) typing.row.parentNode.removeChild(typing.row);
                setError((e && e.message) ? e.message : 'ส่งข้อความไม่สำเร็จ');
            } finally {
                sendBtn.disabled = false;
                input.disabled = false;
                input.focus();
                scrollToBottom(true);
            }
        }

        form?.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = (input?.value || '').trim();
            if (!text) return;
            input.value = '';
            autoGrow();
            sendMessage(text);
        });

        // Initial scroll
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => scrollToBottom(false), { once: true });
        } else {
            scrollToBottom(false);
        }

        autoGrow();
    })();
</script>
@endsection
