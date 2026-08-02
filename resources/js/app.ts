import { createInertiaApp } from '@inertiajs/vue3';
import { initAppearance } from '@/composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'TGM HR';

initAppearance();

createInertiaApp({
    title: (title) => (title ? `${title} · ${appName}` : appName),
    progress: {
        color: '#35d0a5',
        showSpinner: false,
    },
});
