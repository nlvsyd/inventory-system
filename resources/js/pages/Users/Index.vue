<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { CheckCircle2, Plus, Pencil, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    role: string;
}

interface Props {
    users: User[];
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

interface PageProps {
    flash?: {
        success?: string;
    };
}

const successMessage = computed(() => (page.props as PageProps).flash?.success);

const deleteUser = (userId: number) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(`/users/${userId}`, {
            preserveScroll: true,
            onSuccess: () => {
                // Success message will be shown via flash
            },
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Users" />

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Users</h1>
                    <p class="text-sm text-muted-foreground">
                        Manage system users
                    </p>
                </div>
                <Link :href="'/users/create'">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Add User
                    </Button>
                </Link>
            </div>

            <Alert v-if="successMessage" variant="default" class="bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800">
                <CheckCircle2 class="h-4 w-4 text-green-600 dark:text-green-400" />
                <AlertDescription class="text-green-800 dark:text-green-200">
                    {{ successMessage }}
                </AlertDescription>
            </Alert>

            <Card>
                <CardHeader>
                    <CardTitle>User List</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="px-4 py-3 text-left text-sm font-medium">Name</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Email</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Role</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Verified</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">Created</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="user in users"
                                    :key="user.id"
                                    class="border-b hover:bg-muted/50"
                                >
                                    <td class="px-4 py-3 text-sm">{{ user.name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ user.email }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary">
                                            {{ user.role }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span
                                            v-if="user.email_verified_at"
                                            class="text-green-600 dark:text-green-400"
                                        >
                                            Verified
                                        </span>
                                        <span v-else class="text-muted-foreground">
                                            Not verified
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-muted-foreground">
                                        {{ new Date(user.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/users/${user.id}/edit`">
                                                <Button variant="ghost" size="sm">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                @click="deleteUser(user.id)"
                                            >
                                                <Trash2 class="h-4 w-4 text-red-600" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="users.length === 0">
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-muted-foreground">
                                        No users found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

