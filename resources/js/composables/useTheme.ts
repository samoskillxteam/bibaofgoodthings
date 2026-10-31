import { ref, watch } from 'vue';

// Module-level singleton — shared across all components
const isDark = ref(false);
let initialised = false;

function applyTheme(dark: boolean) {
    if (typeof document === 'undefined') return;
    if (dark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

function init() {
    if (initialised || typeof window === 'undefined') return;
    initialised = true;
    const stored = localStorage.getItem('biba_theme');
    if (stored === 'dark') {
        isDark.value = true;
    } else if (stored === 'light') {
        isDark.value = false;
    } else {
        isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
        // Listen for OS preference changes when no manual preference stored
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('biba_theme')) {
                isDark.value = e.matches;
                applyTheme(isDark.value);
            }
        });
    }
    applyTheme(isDark.value);
}

export function useTheme() {
    // Always call init — idempotent after first call
    if (typeof window !== 'undefined') init();

    const toggleTheme = () => {
        isDark.value = !isDark.value;
        localStorage.setItem('biba_theme', isDark.value ? 'dark' : 'light');
        applyTheme(isDark.value);
    };

    const setTheme = (dark: boolean) => {
        isDark.value = dark;
        localStorage.setItem('biba_theme', dark ? 'dark' : 'light');
        applyTheme(dark);
    };

    return { isDark, toggleTheme, setTheme };
}
