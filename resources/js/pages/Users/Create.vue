<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
    {
        title: 'Create User',
        href: '/users/create',
    },
];

interface Props {
    roles: string[];
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: '',
});

const submit = () => {
    form.post('/users', {
        preserveScroll: true,
        onSuccess: () => {
            router.visit('/users');
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create User" />

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div>
                <h1 class="text-2xl font-bold">Create User</h1>
                <p class="text-sm text-muted-foreground">
                    Add a new user to the system
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>User Information</CardTitle>
                </CardHeader>
                <CardContent>
                    <Form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                autocomplete="name"
                                placeholder="Full name"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email Address</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                autocomplete="email"
                                placeholder="Email address"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password">Password</Label>
                            <Input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Password"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="role">Role</Label>
                            <select
                                id="role"
                                v-model="form.role"
                                required
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="">Select a role</option>
                                <option
                                    v-for="role in props.roles"
                                    :key="role"
                                    :value="role"
                                >
                                    {{ role }}
                                </option>
                            </select>
                            <InputError :message="form.errors.role" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                Create User
                            </Button>
                            <Button
                                type="button"
                                variant="secondary"
                                @click="router.visit('/users')"
                            >
                                Cancel
                            </Button>
                        </div>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

