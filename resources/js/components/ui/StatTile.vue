<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        label: string;
        value: number;
        suffix?: string;
        decimals?: number;
        caption?: string;
        tone?: 'default' | 'signal' | 'brass' | 'alert';
    }>(),
    { decimals: 0, tone: 'default' },
);

const tones = {
    default: 'text-text',
    signal: 'text-signal',
    brass: 'text-brass',
    alert: 'text-alert',
} as const;

// Numbers count up on mount so the panel feels like it is settling.
const shown = ref(0);

function animate(to: number) {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        shown.value = to;

        return;
    }

    const from = shown.value;
    const start = performance.now();
    const span = 700;

    function frame(time: number) {
        const progress = Math.min(1, (time - start) / span);
        const eased = 1 - Math.pow(1 - progress, 3);

        shown.value = from + (to - from) * eased;

        if (progress < 1) requestAnimationFrame(frame);
    }

    requestAnimationFrame(frame);
}

onMounted(() => animate(props.value));
watch(() => props.value, animate);

const display = computed(() => shown.value.toFixed(props.decimals));
</script>

<template>
    <div
        class="rounded-2xl border border-line bg-panel p-4 shadow-panel transition-colors hover:border-faint/40"
    >
        <p class="eyebrow truncate">{{ label }}</p>
        <p class="tabular mt-2 font-mono text-[26px] leading-none font-semibold tracking-tight" :class="tones[tone]">
            {{ display }}<span v-if="suffix" class="text-[15px] text-faint">{{ suffix }}</span>
        </p>
        <p v-if="caption" class="mt-1.5 text-[12px] text-faint">{{ caption }}</p>
    </div>
</template>
