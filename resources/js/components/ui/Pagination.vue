<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: Array<{ url: string | null; label: string; active: boolean }>;
    from: number | null;
    to: number | null;
    total: number;
}>();
</script>

<template>
    <div
        v-if="total > 0"
        class="flex flex-wrap items-center justify-between gap-3 border-t border-line-soft px-5 py-3.5"
    >
        <p class="tabular text-[12.5px] text-faint">
            Showing {{ from ?? 0 }}–{{ to ?? 0 }} of {{ total }}
        </p>

        <nav v-if="links.length > 3" class="flex items-center gap-1">
            <template v-for="(link, index) in links" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-scroll
                    :class="[
                        'grid h-8 min-w-8 place-items-center rounded-lg px-2 text-[12.5px] font-medium',
                        'transition-colors duration-150',
                        link.active
                            ? 'bg-signal text-[#06231b]'
                            : 'text-muted hover:bg-line-soft hover:text-text',
                    ]"
                    v-html="link.label"
                />
                <span
                    v-else
                    class="grid h-8 min-w-8 place-items-center px-2 text-[12.5px] text-faint/50"
                    v-html="link.label"
                />
            </template>
        </nav>
    </div>
</template>
