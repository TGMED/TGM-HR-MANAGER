<script setup lang="ts">
/**
 * Two artworks ship with the brand: dark ink for light surfaces and light ink
 * for dark ones. Swapping the two by theme lets the mark sit directly on the
 * surface rather than on a white plate. The wordmark is close to 10:1, so the
 * heights below stay small enough to keep it inside the 264px nav rail.
 */
withDefaults(
    defineProps<{
        size?: 'sm' | 'md' | 'lg';
        subtitle?: string;
    }>(),
    { size: 'md' },
);

const marks = {
    sm: 'h-5',
    md: 'h-6',
    lg: 'h-7',
} as const;
</script>

<template>
    <div class="flex items-center gap-3">
        <span class="relative inline-flex shrink-0 items-center">
            <img
                src="/images/tgm-logo.png"
                alt="TGM Education"
                :class="['w-auto dark:hidden', marks[size]]"
                width="932"
                height="98"
                decoding="async"
            />
            <img
                src="/images/tgm-logo-dark.png"
                alt=""
                aria-hidden="true"
                :class="['hidden w-auto dark:block', marks[size]]"
                width="932"
                height="98"
                decoding="async"
            />
        </span>

        <span
            v-if="subtitle"
            class="h-6 w-px shrink-0 bg-line"
            aria-hidden="true"
        />
        <p v-if="subtitle" class="eyebrow truncate">{{ subtitle }}</p>
    </div>
</template>
