<script setup lang="ts">
/**
 * The supplied logo is dark ink with no light-on-dark variant, so a second
 * artwork is generated with the neutral ink lifted to the panel's text colour
 * and the brand red left untouched. Swapping the two by theme lets the mark sit
 * directly on the surface rather than on a white plate.
 */
withDefaults(
    defineProps<{
        size?: 'sm' | 'md' | 'lg';
        subtitle?: string;
    }>(),
    { size: 'md' },
);

const marks = {
    sm: 'h-8',
    md: 'h-11',
    lg: 'h-14',
} as const;
</script>

<template>
    <div class="flex items-center gap-3">
        <span class="relative inline-flex shrink-0 items-center">
            <img
                src="/images/tgm-logo.png"
                alt="TGMarchnata"
                :class="['w-auto dark:hidden', marks[size]]"
                width="774"
                height="239"
                decoding="async"
            />
            <img
                src="/images/tgm-logo-dark.png"
                alt=""
                aria-hidden="true"
                :class="['hidden w-auto dark:block', marks[size]]"
                width="774"
                height="239"
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
