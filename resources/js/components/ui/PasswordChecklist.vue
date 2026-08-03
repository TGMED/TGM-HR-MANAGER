<script setup lang="ts">
import { usePasswordRules } from '@/composables/usePasswordRules';

const props = withDefaults(
    defineProps<{
        password: string;
        label?: string;
    }>(),
    { label: 'Your password needs:' },
);

const { rules } = usePasswordRules(() => props.password);
</script>

<template>
    <div class="space-y-2">
        <p class="text-[13px] font-medium text-muted">{{ label }}</p>

        <ul class="grid gap-1.5 sm:grid-cols-2">
            <li
                v-for="rule in rules"
                :key="rule.key"
                :class="[
                    'flex items-center gap-2 text-[13px] transition-colors duration-200',
                    rule.met ? 'text-signal' : 'text-faint',
                ]"
            >
                <span
                    aria-hidden="true"
                    :class="[
                        'grid size-4 shrink-0 place-items-center rounded-full border transition-all duration-200',
                        rule.met
                            ? 'border-signal bg-signal text-panel-raised'
                            : 'border-line',
                    ]"
                >
                    <svg
                        :class="[
                            'size-2.5 transition-transform duration-200 ease-out',
                            rule.met ? 'scale-100' : 'scale-0',
                        ]"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="3.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="m4 12.5 5.5 5.5L20 6.5" />
                    </svg>
                </span>

                <span>{{ rule.label }}</span>
                <span class="sr-only">
                    {{ rule.met ? ', met' : ', not met yet' }}
                </span>
            </li>
        </ul>
    </div>
</template>
