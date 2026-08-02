<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppButton from '@/components/ui/AppButton.vue';
import Panel from '@/components/ui/Panel.vue';
import TextField from '@/components/ui/TextField.vue';
import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.put('/settings/password', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => form.reset('current_password'),
    });
}
</script>

<template>
    <Head title="Change password" />

    <AppLayout heading="Change password" lede="Keep your account yours">
        <div class="max-w-lg">
            <Panel
                eyebrow="Security"
                title="Set a new password"
                subtitle="You will stay signed in on this device."
            >
                <form class="space-y-4" @submit.prevent="submit">
                    <TextField
                        v-model="form.current_password"
                        label="Current password"
                        type="password"
                        autocomplete="current-password"
                        required
                        :error="form.errors.current_password"
                    />

                    <div class="h-px bg-line-soft" />

                    <TextField
                        v-model="form.password"
                        label="New password"
                        type="password"
                        autocomplete="new-password"
                        required
                        :error="form.errors.password"
                    />

                    <TextField
                        v-model="form.password_confirmation"
                        label="Confirm new password"
                        type="password"
                        autocomplete="new-password"
                        required
                        :error="form.errors.password_confirmation"
                    />

                    <div class="flex justify-end pt-2">
                        <AppButton type="submit" :loading="form.processing">
                            Change password
                        </AppButton>
                    </div>
                </form>
            </Panel>
        </div>
    </AppLayout>
</template>
