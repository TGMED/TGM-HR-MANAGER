import { ref } from 'vue';
import type { Toast } from '@/types';

export type ActiveToast = Toast & { id: number; title?: string };

const toasts = ref<ActiveToast[]>([]);
let nextId = 1;

export function useToasts() {
    function push(toast: Toast & { title?: string }, timeout = 5200) {
        const id = nextId++;

        toasts.value = [...toasts.value, { ...toast, id }];

        if (timeout > 0) {
            window.setTimeout(() => dismiss(id), timeout);
        }

        return id;
    }

    function dismiss(id: number) {
        toasts.value = toasts.value.filter((toast) => toast.id !== id);
    }

    return { toasts, push, dismiss };
}
