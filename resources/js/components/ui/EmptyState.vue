<script setup lang="ts">
defineProps<{
    title: string;
    message: string;
}>();
</script>

<template>
    <div class="flex flex-col items-center px-6 py-14 text-center">
        <div
            class="mb-4 grid size-12 place-items-center rounded-2xl border border-line bg-sunken text-faint"
        >
            <slot name="icon">
                <svg
                    class="size-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                >
                    <circle cx="12" cy="12" r="8.5" />
                    <path d="M12 7.5V12l3 2" />
                </svg>
            </slot>
        </div>

        <p class="font-display text-[15px] font-semibold tracking-tight">
            {{ title }}
        </p>
        <p class="mt-1 max-w-xs text-[13px] leading-relaxed text-muted">
            {{ message }}
        </p>

        <div v-if="$slots.action" class="mt-5"><slot name="action" /></div>
    </div>
</template>
