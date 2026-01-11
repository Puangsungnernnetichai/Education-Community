const STORAGE_KEY = 'theme';

function getSystemTheme() {
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function applyTheme(theme) {
    const root = document.documentElement;
    const isDark = theme === 'dark';
    root.classList.toggle('dark', isDark);

    const toggle = document.querySelector('[data-theme-toggle]');
    if (toggle) {
        toggle.setAttribute('aria-checked', String(isDark));
        toggle.setAttribute('data-theme', theme);
    }
}

function initTheme() {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved === 'dark' || saved === 'light') {
        applyTheme(saved);
        return;
    }

    applyTheme(getSystemTheme());
}

function toggleTheme() {
    const isDark = document.documentElement.classList.contains('dark');
    const next = isDark ? 'light' : 'dark';
    localStorage.setItem(STORAGE_KEY, next);
    applyTheme(next);
}

function onReady(callback) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
        return;
    }
    callback();
}

onReady(() => {
    initTheme();

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-theme-toggle]');
        if (!button) return;
        event.preventDefault();
        toggleTheme();
    });

    // Keep in sync if system theme changes and user hasn't chosen.
    window.matchMedia?.('(prefers-color-scheme: dark)')?.addEventListener('change', () => {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) return;
        applyTheme(getSystemTheme());
    });
});
