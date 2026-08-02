<script setup lang="ts">
import { computed, ref } from 'vue';
import { duration, fullDate } from '@/lib/format';

/**
 * Lateness read as deviation, not as a count. Each day is a bar measured from
 * the opening-time baseline: above the line you arrived early, below it you
 * arrived late. The shape of the fortnight is legible at a glance.
 */
type Day = {
    date: string;
    label: string;
    offset: number | null;
    status: string | null;
    is_workday: boolean;
};

const props = defineProps<{
    days: Day[];
    workStart: string;
    graceMinutes: number;
}>();

const CLAMP = 75; // minutes at which a bar hits full height
const HALF = 46; // px available above and below the baseline

const hovered = ref<string | null>(null);

const bars = computed(() =>
    props.days.map((day) => {
        const offset = day.offset;
        const magnitude =
            offset === null ? 0 : Math.min(Math.abs(offset), CLAMP);
        const height =
            offset === null ? 0 : Math.max(4, (magnitude / CLAMP) * HALF);
        const late = offset !== null && offset > props.graceMinutes;

        return {
            ...day,
            height,
            late,
            // Late bars hang below the baseline, early ones stand above it.
            below: offset !== null && offset > 0,
            tone: offset === null ? 'none' : late ? 'brass' : 'signal',
        };
    }),
);

const summary = computed(() => {
    const recorded = props.days.filter((day) => day.offset !== null);
    const late = recorded.filter(
        (day) => (day.offset ?? 0) > props.graceMinutes,
    );

    return { recorded: recorded.length, late: late.length };
});
</script>

<template>
    <div>
        <div
            class="mb-4 flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1"
        >
            <p class="text-[13px] text-muted">
                Arrival against the
                <span class="font-mono font-medium text-text">{{
                    workStart
                }}</span>
                baseline
            </p>
            <p class="tabular text-[13px] text-faint">
                {{ summary.late }} late of {{ summary.recorded }} recorded
            </p>
        </div>

        <div class="relative flex items-stretch gap-1 sm:gap-1.5">
            <!-- The baseline is the opening hour. -->
            <div
                class="pointer-events-none absolute inset-x-0 top-1/2 h-px -translate-y-1/2 bg-line"
                aria-hidden="true"
            />

            <div
                v-for="day in bars"
                :key="day.date"
                class="group relative flex flex-1 flex-col items-center"
                :style="{ height: `${HALF * 2}px` }"
                @mouseenter="hovered = day.date"
                @mouseleave="hovered = null"
                @focusin="hovered = day.date"
                @focusout="hovered = null"
            >
                <!-- Above the line: early. -->
                <div class="flex w-full flex-1 items-end justify-center">
                    <div
                        v-if="day.offset !== null && !day.below"
                        class="w-full max-w-[18px] rounded-t-[3px] bg-signal transition-all duration-500 ease-out group-hover:brightness-115"
                        :style="{ height: `${day.height}px` }"
                    />
                </div>

                <!-- Below the line: late. -->
                <div class="flex w-full flex-1 items-start justify-center">
                    <div
                        v-if="day.offset !== null && day.below"
                        :class="[
                            'w-full max-w-[18px] rounded-b-[3px] transition-all duration-500 ease-out group-hover:brightness-115',
                            day.late ? 'bg-brass' : 'bg-signal',
                        ]"
                        :style="{ height: `${day.height}px` }"
                    />
                    <div
                        v-else-if="day.offset === null"
                        :class="[
                            'mt-1 size-1.5 rounded-full',
                            day.is_workday ? 'bg-line' : 'bg-transparent',
                        ]"
                        :title="day.is_workday ? 'No record' : 'Not a workday'"
                    />
                </div>

                <!-- Tooltip -->
                <Transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="opacity-0 translate-y-1"
                    leave-active-class="transition duration-100 ease-in"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-if="hovered === day.date"
                        class="pointer-events-none absolute -top-1 left-1/2 z-20 w-max -translate-x-1/2 -translate-y-full rounded-lg border border-line bg-panel-raised px-2.5 py-1.5 shadow-lift"
                    >
                        <p class="text-[11px] font-semibold tracking-tight">
                            {{ fullDate(day.date) }}
                        </p>
                        <p
                            class="tabular mt-0.5 font-mono text-[11px] text-muted"
                        >
                            <template v-if="day.offset === null">
                                {{ day.is_workday ? 'No record' : 'Rest day' }}
                            </template>
                            <template v-else-if="day.offset > 0">
                                +{{ duration(day.offset) }} after
                                {{ workStart }}
                            </template>
                            <template v-else-if="day.offset < 0">
                                −{{ duration(Math.abs(day.offset)) }} before
                                {{ workStart }}
                            </template>
                            <template v-else>On the hour</template>
                        </p>
                    </div>
                </Transition>
            </div>
        </div>

        <div class="mt-2 flex items-stretch gap-1 sm:gap-1.5">
            <p
                v-for="day in bars"
                :key="`${day.date}-label`"
                :class="[
                    'flex-1 text-center font-mono text-[10px] tracking-tight',
                    day.is_workday ? 'text-faint' : 'text-faint/40',
                ]"
            >
                {{ day.label.slice(0, 2) }}
            </p>
        </div>
    </div>
</template>
