<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppButton from '@/components/ui/AppButton.vue';
import PasswordChecklist from '@/components/ui/PasswordChecklist.vue';
import TextField from '@/components/ui/TextField.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';

const props = defineProps<{ token: string; email: string }>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Choose a new password" />

    <AuthLayout
        eyebrow="Account recovery"
        heading="Choose a new password"
        lede="Pick something you have not used here before."
    >
        <form class="space-y-4" @submit.prevent="submit">
            <TextField
                size="lg"
                v-model="form.email"
                label="Work email"
                type="email"
                autocomplete="username"
                required
                :error="form.errors.email"
            />

            <TextField
                size="lg"
                v-model="form.password"
                label="New password"
                type="password"
                autocomplete="new-password"
                required
                :error="form.errors.password"
            />

            <TextField
                size="lg"
                v-model="form.password_confirmation"
                label="Confirm new password"
                type="password"
                autocomplete="new-password"
                required
                :error="form.errors.password_confirmation"
            />

            <PasswordChecklist :password="form.password" />

            <AppButton
                type="submit"
                size="lg"
                block
                :loading="form.processing"
                class="!mt-6"
            >
                Set new password
            </AppButton>
        </form>
    </AuthLayout>
</template>
