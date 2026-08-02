<script setup lang="ts">
import { useToasts } from '@/composables/useToasts';

const { toasts, dismiss } = useToasts();

const tones = {
    success: 'text-signal',
    error: 'text-alert',
    info: 'text-beacon',
} as const;
</script>

<template>
    <Teleport to="body">
        <div
            class="pointer-events-none fixed inset-x-0 bottom-0 z-[60] flex flex-col items-center gap-2 p-4 sm:inset-x-auto sm:right-0 sm:items-end sm:p-6"
            role="status"
            aria-live="polite"
        >
            <TransitionGroup
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 translate-y-3 scale-95"
                leave-active-class="transition duration-200 ease-in absolute"
                leave-to-class="opacity-0 translate-x-4 scale-95"
                move-class="transition duration-200"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border border-line bg-panel-raised p-3.5 shadow-lift"
                >
                    <span
                        :class="[
                            'mt-0.5 grid size-5 shrink-0 place-items-center',
                            tones[toast.type],
                        ]"
                    >
                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <template v-if="toast.type === 'success'">
                                <circle cx="12" cy="12" r="9" />
                                <path d="m8.5 12.2 2.4 2.4 4.6-4.9" />
                            </template>
                            <template v-else-if="toast.type === 'error'">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 7.8v5M12 16.2h.01" />
                            </template>
                            <template v-else>
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 16.2v-5M12 7.8h.01" />
                            </template>
                        </svg>
                    </span>

                    <div class="min-w-0 flex-1">
                        <p
                            v-if="toast.title"
                            class="text-[13px] font-semibold tracking-tight"
                        >
                            {{ toast.title }}
                        </p>
                        <p class="text-[13px] leading-snug text-muted">
                            {{ toast.message }}
                        </p>
                    </div>

                    <button
                        type="button"
                        aria-label="Dismiss"
                        class="-m-1 grid size-6 shrink-0 place-items-center rounded-md text-faint transition-colors hover:text-text"
                        @click="dismiss(toast.id)"
                    >
                        <svg
                            class="size-3.5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                        >
                            <path d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>
