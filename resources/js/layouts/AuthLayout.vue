<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import BrandMark from '@/components/BrandMark.vue';
import ToastHost from '@/components/ui/ToastHost.vue';

withDefaults(
    defineProps<{
        eyebrow: string;
        heading: string;
        lede: string;
        // Signup carries far more fields, so it gets a wider column and lays
        // them out side by side rather than running off the bottom of the page.
        width?: 'sm' | 'lg';
    }>(),
    { width: 'sm' },
);

// The brand panel carries a live clock, because that is what the product is.
const now = ref(new Date());
let ticker: number | undefined;

onMounted(() => {
    ticker = window.setInterval(() => (now.value = new Date()), 1000);
});

onBeforeUnmount(() => window.clearInterval(ticker));

function stamp(date: Date) {
    return date.toLocaleTimeString('en-GB', { hour12: false });
}
</script>

<template>
    <div class="grid min-h-dvh lg:grid-cols-[1.05fr_1fr]">
        <!-- Brand panel -->
        <aside
            class="relative hidden overflow-hidden bg-sunken lg:flex lg:flex-col lg:justify-between lg:p-12"
        >
            <!-- Concentric gradations, a dial face at rest. -->
            <svg
                class="pointer-events-none absolute -right-[22%] -bottom-[28%] size-[820px] opacity-[0.16]"
                viewBox="0 0 400 400"
                fill="none"
                aria-hidden="true"
            >
                <circle
                    v-for="(r, index) in [190, 158, 126, 94, 62]"
                    :key="r"
                    cx="200"
                    cy="200"
                    :r="r"
                    stroke="var(--text)"
                    :stroke-width="index === 0 ? 1.5 : 0.75"
                />
                <g
                    stroke="var(--text)"
                    stroke-width="1.5"
                    stroke-linecap="round"
                >
                    <line
                        v-for="tick in 60"
                        :key="tick"
                        x1="200"
                        :y1="tick % 5 === 0 ? 12 : 18"
                        x2="200"
                        y2="24"
                        :transform="`rotate(${tick * 6} 200 200)`"
                        :opacity="tick % 5 === 0 ? 0.9 : 0.35"
                    />
                </g>
            </svg>

            <div class="relative">
                <BrandMark size="lg" subtitle="Time &amp; attendance" />
            </div>

            <div class="relative max-w-md">
                <p class="eyebrow">On site, on time</p>
                <p
                    class="mt-4 font-display text-[42px] leading-[1.05] font-bold tracking-tight text-balance"
                >
                    Every punch carries a time and a place.
                </p>
                <p class="mt-5 text-[15px] leading-relaxed text-muted">
                    Clock in from the office, not from the road. TGM checks your
                    position against the geofence on every attempt and keeps the
                    record either way.
                </p>
            </div>

            <div class="relative flex items-end justify-between gap-6">
                <div>
                    <p class="eyebrow">Office clock</p>
                    <p
                        class="tabular mt-1.5 font-mono text-[28px] leading-none font-semibold tracking-tight"
                    >
                        {{ stamp(now) }}
                    </p>
                </div>
                <p
                    class="max-w-[180px] text-right text-[11.5px] leading-snug text-faint"
                >
                    Trouble signing in? Your administrator can reset your
                    access.
                </p>
            </div>
        </aside>

        <!-- Form panel -->
        <main class="flex items-center justify-center px-5 py-6 sm:px-8">
            <div
                :class="[
                    'animate-rise w-full',
                    width === 'lg' ? 'max-w-2xl' : 'max-w-sm',
                ]"
            >
                <div class="mb-8 lg:hidden">
                    <BrandMark size="md" />
                </div>

                <p class="eyebrow">{{ eyebrow }}</p>
                <h1
                    class="mt-2 font-display text-[30px] leading-tight font-bold tracking-tight"
                >
                    {{ heading }}
                </h1>
                <p class="mt-2.5 text-[15px] leading-relaxed text-muted">
                    {{ lede }}
                </p>

                <div class="mt-6"><slot /></div>
            </div>
        </main>

        <ToastHost />
    </div>
</template>
