<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        tone?: 'signal' | 'brass' | 'alert' | 'beacon' | 'neutral';
        dot?: boolean;
        pulse?: boolean;
    }>(),
    { tone: 'neutral', dot: false, pulse: false },
);

const tones = {
    signal: 'bg-signal-soft text-signal',
    brass: 'bg-brass-soft text-brass',
    alert: 'bg-alert-soft text-alert',
    beacon: 'bg-beacon-soft text-beacon',
    neutral: 'bg-line-soft text-muted',
} as const;

const classes = computed(() => tones[props.tone]);
</script>

<template>
    <span
        :class="[
            'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1',
            'text-[11.5px] font-semibold tracking-tight whitespace-nowrap',
            classes,
        ]"
    >
        <span v-if="dot" class="relative flex size-1.5">
            <span
                v-if="pulse"
                class="absolute inline-flex size-full animate-ping rounded-full bg-current opacity-70"
            />
            <span
                class="relative inline-flex size-1.5 rounded-full bg-current"
            />
        </span>
        <slot />
    </span>
</template>
