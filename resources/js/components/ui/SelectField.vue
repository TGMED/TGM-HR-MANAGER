<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        label?: string;
        error?: string;
        hint?: string;
        required?: boolean;
        disabled?: boolean;
        id?: string;
        options: Array<{ value: string | number; label: string }>;
    }>(),
    { required: false, disabled: false },
);

const model = defineModel<string | number | null>();

const fallbackId = `select-${Math.random().toString(36).slice(2, 9)}`;
const fieldId = computed(() => props.id ?? fallbackId);
</script>

<template>
    <div class="space-y-1.5">
        <label
            v-if="label"
            :for="fieldId"
            class="flex items-center gap-1 text-[13px] font-medium text-muted"
        >
            {{ label }}
            <span v-if="required" class="text-alert" aria-hidden="true">*</span>
        </label>

        <div class="relative">
            <select
                :id="fieldId"
                v-model="model"
                :required="required"
                :disabled="disabled"
                :aria-invalid="!!error"
                :class="[
                    'h-11 w-full appearance-none rounded-xl border bg-panel-raised px-3.5 pr-10 text-sm text-text',
                    'transition-all duration-200 ease-out disabled:opacity-50',
                    'focus:outline-none focus:ring-4',
                    error
                        ? 'border-alert focus:border-alert focus:ring-alert/15'
                        : 'border-line focus:border-beacon focus:ring-beacon/15',
                ]"
            >
                <slot />
                <option
                    v-for="option in options"
                    :key="option.value"
                    :value="option.value"
                >
                    {{ option.label }}
                </option>
            </select>

            <svg
                class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2 text-faint"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
            >
                <path d="m6 9 6 6 6-6" />
            </svg>
        </div>

        <p v-if="error" class="animate-fade-in text-[13px] text-alert">
            {{ error }}
        </p>
        <p v-else-if="hint" class="text-[13px] text-faint">{{ hint }}</p>
    </div>
</template>
