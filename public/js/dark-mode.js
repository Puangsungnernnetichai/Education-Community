(function () {
    var STORAGE_KEY = 'theme';

    function prefersDark() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    function getSavedTheme() {
        try {
            return localStorage.getItem(STORAGE_KEY);
        } catch (e) {
            return null;
        }
    }

    function saveTheme(theme) {
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (e) {
            // ignore
        }
    }

    function applyTheme(theme) {
        var root = document.documentElement;
        var isDark = theme === 'dark';
        root.classList.toggle('dark', isDark);

        var checkbox = document.getElementById('dark-toggle');
        if (checkbox) checkbox.checked = isDark;
    }

    function initTheme() {
        var saved = getSavedTheme();
        if (saved === 'dark' || saved === 'light') {
            applyTheme(saved);
            return;
        }
        applyTheme(prefersDark() ? 'dark' : 'light');
    }

    function toggleTheme() {
        var isDark = document.documentElement.classList.contains('dark');
        var next = isDark ? 'light' : 'dark';
        saveTheme(next);
        applyTheme(next);
    }

    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn, { once: true });
            return;
        }
        fn();
    }

    onReady(function () {
        window.__themeToggleInitialized = true;

        initTheme();

        var checkbox = document.getElementById('dark-toggle');
        if (checkbox) {
            checkbox.addEventListener('change', function () {
                toggleTheme();
            });
        }

        if (window.matchMedia) {
            var mq = window.matchMedia('(prefers-color-scheme: dark)');
            if (mq && mq.addEventListener) {
                mq.addEventListener('change', function () {
                    var saved = getSavedTheme();
                    if (saved) return;
                    applyTheme(prefersDark() ? 'dark' : 'light');
                });
            }
        }
    });
})();
