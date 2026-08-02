<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppButton from '@/components/ui/AppButton.vue';
import LocationSelect from '@/components/ui/LocationSelect.vue';
import TextField from '@/components/ui/TextField.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';

type SignupLocation = {
    id: number;
    name: string;
    address: string;
    city: string | null;
    work_starts_at: string;
    work_ends_at: string;
    timezone: string;
    radius_meters: number;
};

const props = defineProps<{ locations: SignupLocation[] }>();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    department: '',
    position: '',
    location_id: null as number | null,
    password: '',
    password_confirmation: '',
});

const chosen = computed(() =>
    props.locations.find((location) => location.id === form.location_id),
);

// Folding the chosen-site summary into the hint keeps the page height fixed,
// so picking a site cannot push the button below the fold.
const locationHint = computed(() => {
    if (chosen.value) {
        return `Within ${chosen.value.radius_meters}m of ${chosen.value.address}. Day starts ${chosen.value.work_starts_at}.`;
    }

    return props.locations.length
        ? 'You can set this later, but you need a site before you can clock in.'
        : 'No sites are open for signup yet — create your account anyway and pick one from your dashboard.';
});

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Create your account" />

    <AuthLayout
        eyebrow="New staff"
        heading="Create your TGM account"
        lede="Set up your account. You can pick the site you report to now or later."
        width="lg"
    >
        <form class="space-y-4" @submit.prevent="submit">
            <div class="grid gap-4 sm:grid-cols-2">
                <TextField
                    v-model="form.name"
                    size="lg"
                    label="Full name"
                    placeholder="Amara Nwosu"
                    autocomplete="name"
                    required
                    :error="form.errors.name"
                />
                <TextField
                    v-model="form.email"
                    size="lg"
                    label="Work email"
                    type="email"
                    placeholder="you@tgm.test"
                    autocomplete="username"
                    required
                    :error="form.errors.email"
                />
            </div>

            <LocationSelect
                v-model="form.location_id"
                label="Work location"
                optional
                :options="locations"
                placeholder="Choose your site"
                :error="form.errors.location_id"
                :hint="locationHint"
            />

            <div class="grid gap-4 sm:grid-cols-3">
                <TextField
                    v-model="form.department"
                    size="lg"
                    label="Department"
                    placeholder="Engineering"
                    :error="form.errors.department"
                />
                <TextField
                    v-model="form.position"
                    size="lg"
                    label="Job title"
                    placeholder="Technician"
                    :error="form.errors.position"
                />
                <TextField
                    v-model="form.phone"
                    size="lg"
                    label="Phone"
                    placeholder="080 0000 0000"
                    autocomplete="tel"
                    :error="form.errors.phone"
                />
            </div>

            <div class="h-px bg-line-soft" />

            <div class="grid gap-4 sm:grid-cols-2">
                <TextField
                    v-model="form.password"
                    size="lg"
                    label="Password"
                    type="password"
                    autocomplete="new-password"
                    required
                    :error="form.errors.password"
                />
                <TextField
                    v-model="form.password_confirmation"
                    size="lg"
                    label="Confirm password"
                    type="password"
                    autocomplete="new-password"
                    required
                />
            </div>

            <AppButton type="submit" size="lg" block :loading="form.processing">
                Create account
            </AppButton>
        </form>

        <p class="mt-5 text-sm text-muted">
            Already have an account?
            <Link
                href="/login"
                class="font-medium text-beacon transition-opacity hover:opacity-75"
            >
                Sign in
            </Link>
        </p>
    </AuthLayout>
</template>
