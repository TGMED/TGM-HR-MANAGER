<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppButton from '@/components/ui/AppButton.vue';
import TextField from '@/components/ui/TextField.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';

defineProps<{ status: string | null }>();

const form = useForm({ email: '' });

function submit() {
    form.post('/forgot-password');
}
</script>

<template>
    <Head title="Reset password" />

    <AuthLayout
        eyebrow="Account recovery"
        heading="Reset your password"
        lede="Enter your work email and we will send you a link to set a new password."
    >
        <div
            v-if="status"
            class="animate-pop-in mb-5 rounded-xl border border-signal/30 bg-signal-soft px-4 py-3.5 text-sm text-signal"
        >
            {{ status }}
        </div>

        <form class="space-y-4" @submit.prevent="submit">
            <TextField
                size="lg"
                v-model="form.email"
                label="Work email"
                type="email"
                placeholder="you@tgm.test"
                autocomplete="username"
                required
                :error="form.errors.email"
            />

            <AppButton
                type="submit"
                size="lg"
                block
                :loading="form.processing"
                class="!mt-6"
            >
                Send reset link
            </AppButton>
        </form>

        <Link
            href="/login"
            class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-muted transition-colors hover:text-text"
        >
            <svg
                class="size-3.5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M15 18l-6-6 6-6" />
            </svg>
            Back to sign in
        </Link>
    </AuthLayout>
</template>
