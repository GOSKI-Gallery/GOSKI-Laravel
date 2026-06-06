(function () {
    const KEY = 'goski-theme';
    const DARK_CLASS = 'dark';

    function getStored() {
        try {
            return localStorage.getItem(KEY);
        } catch {
            return null;
        }
    }

    function setStored(value) {
        try {
            localStorage.setItem(KEY, value);
        } catch {}
    }

    function getPreferred() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? DARK_CLASS : 'light';
    }

    function applyTheme(theme) {
        const isDark = theme === DARK_CLASS || (theme !== 'light' && getPreferred() === DARK_CLASS);
        document.documentElement.classList.toggle(DARK_CLASS, isDark);
        setStored(isDark ? DARK_CLASS : 'light');
        updateMetaThemeColor(isDark);
    }

    function updateMetaThemeColor(isDark) {
        let meta = document.querySelector('meta[name="theme-color"]');
        if (!meta) {
            meta = document.createElement('meta');
            meta.name = 'theme-color';
            document.head.appendChild(meta);
        }
        meta.content = isDark ? '#09090b' : '#ECECEC';
    }

    function toggleTheme() {
        const isDark = document.documentElement.classList.contains(DARK_CLASS);
        applyTheme(isDark ? 'light' : DARK_CLASS);
    }

    const stored = getStored();
    applyTheme(stored || 'system');

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (!getStored()) applyTheme('system');
    });

    window.__toggleTheme = toggleTheme;
})();