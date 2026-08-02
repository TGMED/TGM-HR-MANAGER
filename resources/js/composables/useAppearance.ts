import { onMounted, ref } from 'vue';

export type Appearance = 'light' | 'dark';

const STORAGE_KEY = 'tgm.appearance';
const appearance = ref<Appearance>('dark');

/**
 * `app.ts` is also evaluated in Node when Vite warms up the Inertia SSR module
 * graph, where there is no window, document or localStorage. Every browser-only
 * call here has to be guarded or that warm-up fails.
 */
const inBrowser = (): boolean => typeof window !== 'undefined';

function read(): Appearance | null {
    if (!inBrowser()) {
        return null;
    }

    try {
        return localStorage.getItem(STORAGE_KEY) as Appearance | null;
    } catch {
        // Private browsing and blocked storage both throw on access.
        return null;
    }
}

function write(value: Appearance) {
    if (!inBrowser()) {
        return;
    }

    try {
        localStorage.setItem(STORAGE_KEY, value);
    } catch {
        // Not being able to remember the choice is not worth an error.
    }
}

function apply(value: Appearance) {
    if (!inBrowser()) {
        return;
    }

    document.documentElement.classList.toggle('dark', value === 'dark');
}

/** Runs before Vue mounts so the first paint is already the right theme. */
export function initAppearance() {
    appearance.value = read() ?? 'dark';
    apply(appearance.value);
}

export function useAppearance() {
    onMounted(() => {
        const stored = read();

        if (stored) {
            appearance.value = stored;
        }
    });

    function toggle() {
        appearance.value = appearance.value === 'dark' ? 'light' : 'dark';
        write(appearance.value);
        apply(appearance.value);
    }

    return { appearance, toggle };
}
