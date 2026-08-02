<script setup lang="ts">
import { onBeforeUnmount, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        open: boolean;
        title: string;
        subtitle?: string;
        width?: 'md' | 'lg' | 'xl';
    }>(),
    { width: 'lg' },
);

const emit = defineEmits<{ close: [] }>();

const widths = {
    md: 'max-w-md',
    lg: 'max-w-xl',
    xl: 'max-w-3xl',
} as const;

function onKeydown(event: KeyboardEvent) {
    if (event.key === 'Escape') emit('close');
}

watch(
    () => props.open,
    (open) => {
        document.body.style.overflow = open ? 'hidden' : '';
        if (open) {
            window.addEventListener('keydown', onKeydown);
        } else {
            window.removeEventListener('keydown', onKeydown);
        }
    },
);

onBeforeUnmount(() => {
    document.body.style.overflow = '';
    window.removeEventListener('keydown', onKeydown);
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="opacity-0"
        >
            <div
                v-if="open"
                class="fixed inset-0 z-50 overflow-y-auto bg-[#050912]/70 p-4 backdrop-blur-sm sm:p-6"
                role="dialog"
                aria-modal="true"
                @click.self="emit('close')"
            >
                <Transition
                    enter-active-class="transition duration-300 cubic-bezier(0.22,1,0.36,1)"
                    enter-from-class="opacity-0 translate-y-6 scale-[0.97]"
                    leave-active-class="transition duration-150 ease-in"
                    leave-to-class="opacity-0 scale-[0.98]"
                    appear
                >
                    <div
                        v-if="open"
                        :class="[
                            'mx-auto my-4 w-full overflow-hidden rounded-2xl border border-line bg-panel shadow-lift',
                            widths[width],
                        ]"
                        @click.stop
                    >
                        <header
                            class="flex items-start justify-between gap-4 border-b border-line-soft px-5 py-4"
                        >
                            <div class="min-w-0">
                                <h2
                                    class="font-display text-lg font-semibold tracking-tight"
                                >
                                    {{ title }}
                                </h2>
                                <p
                                    v-if="subtitle"
                                    class="mt-0.5 text-[13px] text-muted"
                                >
                                    {{ subtitle }}
                                </p>
                            </div>

                            <button
                                type="button"
                                aria-label="Close"
                                class="grid size-8 shrink-0 place-items-center rounded-lg text-faint transition-colors hover:bg-line-soft hover:text-text"
                                @click="emit('close')"
                            >
                                <svg
                                    class="size-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                >
                                    <path d="M18 6 6 18M6 6l12 12" />
                                </svg>
                            </button>
                        </header>

                        <div class="px-5 py-5"><slot /></div>

                        <footer
                            v-if="$slots.footer"
                            class="flex items-center justify-end gap-2 border-t border-line-soft bg-sunken/40 px-5 py-4"
                        >
                            <slot name="footer" />
                        </footer>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
