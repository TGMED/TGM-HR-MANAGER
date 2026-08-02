<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

export type SelectableSite = {
    id: number;
    name: string;
    address: string;
    city: string | null;
    work_starts_at?: string;
    work_ends_at?: string;
    radius_meters?: number;
};

const props = withDefaults(
    defineProps<{
        options: SelectableSite[];
        label?: string;
        hint?: string;
        error?: string;
        required?: boolean;
        optional?: boolean;
        placeholder?: string;
        id?: string;
    }>(),
    { required: false, optional: false, placeholder: 'Select a location' },
);

const model = defineModel<number | null>();

const fallbackId = `site-${Math.random().toString(36).slice(2, 9)}`;
const fieldId = computed(() => props.id ?? fallbackId);

const open = ref(false);
const active = ref(-1);
const root = ref<HTMLElement | null>(null);
const list = ref<HTMLElement | null>(null);

const selected = computed(
    () => props.options.find((o) => o.id === model.value) ?? null,
);

function addressOf(site: SelectableSite): string {
    return site.city ? `${site.address}, ${site.city}` : site.address;
}

async function toggle() {
    open.value = !open.value;

    if (open.value) {
        active.value = props.options.findIndex((o) => o.id === model.value);
        await nextTick();
        scrollActiveIntoView();
    }
}

function close() {
    open.value = false;
    active.value = -1;
}

function choose(site: SelectableSite) {
    model.value = site.id;
    close();
}

function scrollActiveIntoView() {
    const el = list.value?.children[active.value] as HTMLElement | undefined;
    el?.scrollIntoView({ block: 'nearest' });
}

function move(step: number) {
    if (!props.options.length) return;

    if (!open.value) {
        open.value = true;
    }

    const next = active.value + step;
    active.value = Math.min(props.options.length - 1, Math.max(0, next));
    nextTick(scrollActiveIntoView);
}

function onKeydown(event: KeyboardEvent) {
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            move(active.value < 0 ? 0 : 1);
            break;
        case 'ArrowUp':
            event.preventDefault();
            move(-1);
            break;
        case 'Home':
            if (open.value) {
                event.preventDefault();
                active.value = 0;
                nextTick(scrollActiveIntoView);
            }
            break;
        case 'End':
            if (open.value) {
                event.preventDefault();
                active.value = props.options.length - 1;
                nextTick(scrollActiveIntoView);
            }
            break;
        case 'Enter':
        case ' ':
            event.preventDefault();
            if (open.value && active.value >= 0) {
                choose(props.options[active.value]);
            } else {
                toggle();
            }
            break;
        case 'Escape':
            if (open.value) {
                event.preventDefault();
                close();
            }
            break;
        case 'Tab':
            close();
            break;
    }
}

function onPointerDown(event: MouseEvent) {
    if (open.value && !root.value?.contains(event.target as Node)) {
        close();
    }
}

onMounted(() => document.addEventListener('mousedown', onPointerDown));
onBeforeUnmount(() => document.removeEventListener('mousedown', onPointerDown));

// A site vanishing from the list (retired mid-session) must not leave a stale id.
watch(
    () => props.options,
    (options) => {
        if (model.value !== null && !options.some((o) => o.id === model.value)) {
            model.value = null;
        }
    },
);
</script>

<template>
    <div class="space-y-1.5">
        <label
            v-if="label"
            :for="fieldId"
            class="flex items-center gap-1.5 text-sm font-medium text-muted"
        >
            {{ label }}
            <span v-if="required" class="text-alert" aria-hidden="true">*</span>
            <span v-else-if="optional" class="text-[13px] font-normal text-faint">
                optional
            </span>
        </label>

        <div ref="root" class="relative">
            <button
                :id="fieldId"
                type="button"
                role="combobox"
                aria-haspopup="listbox"
                :aria-expanded="open"
                :aria-controls="`${fieldId}-list`"
                :aria-invalid="!!error"
                :class="[
                    'flex h-12 w-full items-center gap-3 rounded-xl border px-4 text-left',
                    'bg-panel-raised transition-all duration-200 ease-out',
                    'focus:ring-4 focus:outline-none',
                    error
                        ? 'border-alert focus:border-alert focus:ring-alert/15'
                        : open
                          ? 'border-beacon ring-4 ring-beacon/15'
                          : 'border-line hover:border-faint focus:border-beacon focus:ring-beacon/15',
                ]"
                @click="toggle"
                @keydown="onKeydown"
            >
                <span class="min-w-0 flex-1 truncate">
                    <template v-if="selected">
                        <span class="text-[15px] text-text">
                            {{ selected.name }}
                        </span>
                        <span
                            v-if="selected.city"
                            class="ml-2 text-[13px] text-faint"
                        >
                            {{ selected.city }}
                        </span>
                    </template>
                    <span v-else class="text-[15px] text-faint">
                        {{ placeholder }}
                    </span>
                </span>

                <svg
                    :class="[
                        'size-4 shrink-0 text-faint transition-transform duration-200',
                        open && 'rotate-180',
                    ]"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </button>

                <ul
                    v-if="open"
                    :id="`${fieldId}-list`"
                    ref="list"
                    role="listbox"
                    class="animate-drop-in absolute z-30 mt-2 max-h-72 w-full overflow-y-auto rounded-xl border border-line bg-panel-raised p-1 shadow-lift"
                >
                    <li
                        v-for="(site, index) in options"
                        :key="site.id"
                        role="option"
                        :aria-selected="site.id === model"
                        :class="[
                            'flex cursor-pointer items-start gap-3 rounded-lg px-3 py-2.5',
                            'transition-colors duration-100',
                            index === active ? 'bg-line-soft' : '',
                        ]"
                        @click="choose(site)"
                        @mousemove="active = index"
                    >
                        <span
                            :class="[
                                'mt-1 grid size-4 shrink-0 place-items-center rounded-full border-2 transition-colors',
                                site.id === model
                                    ? 'border-signal'
                                    : 'border-line',
                            ]"
                        >
                            <span
                                :class="[
                                    'size-2 rounded-full bg-signal transition-transform duration-150',
                                    site.id === model ? 'scale-100' : 'scale-0',
                                ]"
                            />
                        </span>

                        <span class="min-w-0 flex-1">
                            <span
                                class="block truncate text-[14px] font-semibold tracking-tight"
                            >
                                {{ site.name }}
                            </span>
                            <span
                                class="mt-0.5 block text-[12.5px] leading-snug text-muted"
                            >
                                {{ addressOf(site) }}
                            </span>
                            <span
                                v-if="site.work_starts_at"
                                class="tabular mt-1 block font-mono text-[11.5px] text-faint"
                            >
                                {{ site.work_starts_at }}–{{ site.work_ends_at }}
                                · within {{ site.radius_meters }}m
                            </span>
                        </span>
                    </li>
                </ul>
        </div>

        <p v-if="error" class="animate-fade-in text-sm text-alert">
            {{ error }}
        </p>
        <p v-else-if="hint" class="text-sm text-faint">{{ hint }}</p>
    </div>
</template>
