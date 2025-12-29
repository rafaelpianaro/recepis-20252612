<script setup lang="ts">
import AdminUserController from '@/actions/App/Http/Controllers/Admin/UserController';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import { computed, ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import { Users, UserPlus, Pencil, Trash2, Shield } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    role_label: string;
    email_verified_at: string | null;
    created_at: string;
}

interface Props {
    users: {
        data: User[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    stats: {
        total: number;
        admins: number;
        managers: number;
        users: number;
    };
    filters: {
        search?: string;
        role?: string;
    };
    roles: Array<{
        value: string;
        label: string;
    }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: '/admin/dashboard',
    },
    {
        title: 'Users',
        href: '/admin/users',
    },
];

// Filters
const search = ref(props.filters.search || '');
const roleFilter = ref<string | undefined>(props.filters.role || undefined);

// Debounced search
const debouncedSearch = useDebounceFn((value: string) => {
    router.get(
        '/admin/users',
        { search: value, role: roleFilter.value },
        { preserveState: true, replace: true }
    );
}, 300);

watch(search, (value) => {
    debouncedSearch(value);
});

watch(roleFilter, (value) => {
    router.get(
        '/admin/users',
        { search: search.value, role: value || '' },
        { preserveState: true, replace: true }
    );
});

// Create User Modal
const showCreateModal = ref(false);
const createForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
});

function createUser() {
    createForm.post('/admin/users', {
        preserveScroll: true,
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
    });
}

// Edit User Modal
const showEditModal = ref(false);
const editingUser = ref<User | null>(null);
const editForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
});

function openEditModal(user: User) {
    editingUser.value = user;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.password = '';
    editForm.password_confirmation = '';
    editForm.role = user.role;
    showEditModal.value = true;
}

function updateUser() {
    if (!editingUser.value) return;
    
    editForm.patch(`/admin/users/${editingUser.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            editForm.reset();
            editingUser.value = null;
        },
    });
}

// Delete User Modal
const showDeleteModal = ref(false);
const deletingUser = ref<User | null>(null);

function openDeleteModal(user: User) {
    deletingUser.value = user;
    showDeleteModal.value = true;
}

function deleteUser() {
    if (!deletingUser.value) return;

    router.delete(`/admin/users/${deletingUser.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deletingUser.value = null;
        },
    });
}

// Role change
const editingRoleUserId = ref<number | null>(null);
const selectedRole = ref<string>('');

function startRoleEdit(user: User) {
    editingRoleUserId.value = user.id;
    selectedRole.value = user.role;
}

function cancelRoleEdit() {
    editingRoleUserId.value = null;
    selectedRole.value = '';
}

function updateRole(userId: number) {
    router.patch(
        `/admin/users/${userId}/role`,
        { role: selectedRole.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                editingRoleUserId.value = null;
                selectedRole.value = '';
            },
        }
    );
}

function getRoleBadgeVariant(role: string) {
    switch (role) {
        case 'admin':
            return 'destructive';
        case 'manager':
            return 'default';
        default:
            return 'secondary';
    }
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="User Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">User Management</h1>
                    <p class="text-muted-foreground mt-2">
                        Manage user accounts, roles and permissions
                    </p>
                </div>
                <Button @click="showCreateModal = true" class="gap-2">
                    <UserPlus class="h-4 w-4" />
                    Add User
                </Button>
            </div>

            <!-- Statistics Cards -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Administrators</CardTitle>
                        <Shield class="h-4 w-4 text-destructive" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.admins }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Managers</CardTitle>
                        <Users class="h-4 w-4 text-blue-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.managers }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Regular Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.users }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Filters -->
            <div class="flex flex-col gap-4 sm:flex-row">
                <div class="flex-1">
                    <Input
                        v-model="search"
                        type="search"
                        placeholder="Search by name or email..."
                        class="w-full"
                    />
                </div>
                <div class="flex w-full gap-2 sm:w-64">
                    <Select v-model="roleFilter">
                        <SelectTrigger>
                            <SelectValue placeholder="All roles" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="role in roles"
                                :key="role.value"
                                :value="role.value"
                            >
                                {{ role.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Button
                        v-if="roleFilter"
                        variant="outline"
                        size="icon"
                        @click="roleFilter = undefined"
                        title="Clear filter"
                    >
                        ✕
                    </Button>
                </div>
            </div>

            <!-- Users Table -->
            <div class="rounded-lg border">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr class="border-b">
                                <th class="px-4 py-3 text-left text-sm font-medium">Name</th>
                                <th class="px-4 py-3 text-left text-sm font-medium">Email</th>
                                <th class="px-4 py-3 text-left text-sm font-medium">Role</th>
                                <th class="px-4 py-3 text-left text-sm font-medium">Joined</th>
                                <th class="px-4 py-3 text-right text-sm font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                class="border-b transition-colors hover:bg-muted/50"
                            >
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ user.name }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-muted-foreground">
                                    {{ user.email }}
                                </td>
                                <td class="px-4 py-3">
                                    <div v-if="editingRoleUserId === user.id" class="w-48">
                                        <Select v-model="selectedRole">
                                            <SelectTrigger class="h-8">
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="role in roles"
                                                    :key="role.value"
                                                    :value="role.value"
                                                >
                                                    {{ role.label }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <Badge v-else :variant="getRoleBadgeVariant(user.role)">
                                        {{ user.role_label }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-sm text-muted-foreground">
                                    {{ formatDate(user.created_at) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div v-if="editingRoleUserId === user.id" class="flex justify-end gap-2">
                                        <Button
                                            size="sm"
                                            variant="default"
                                            @click="updateRole(user.id)"
                                        >
                                            Save
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            @click="cancelRoleEdit"
                                        >
                                            Cancel
                                        </Button>
                                    </div>
                                    <div v-else class="flex justify-end gap-2">
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            @click="openEditModal(user)"
                                        >
                                            <Pencil class="h-3 w-3" />
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="destructive"
                                            @click="openDeleteModal(user)"
                                        >
                                            <Trash2 class="h-3 w-3" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="users.last_page > 1" class="flex items-center justify-between border-t px-4 py-3">
                    <div class="text-sm text-muted-foreground">
                        Showing {{ (users.current_page - 1) * users.per_page + 1 }} to
                        {{ Math.min(users.current_page * users.per_page, users.total) }} of
                        {{ users.total }} users
                    </div>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in users.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1 rounded text-sm border',
                                link.active
                                    ? 'bg-primary text-primary-foreground border-primary'
                                    : 'hover:bg-muted',
                                !link.url && 'opacity-50 cursor-not-allowed',
                            ]"
                            :disabled="!link.url"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Create User Modal -->
        <Dialog v-model:open="showCreateModal">
            <DialogContent class="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>Create New User</DialogTitle>
                    <DialogDescription>
                        Add a new user to the system with their role and credentials.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="createUser" class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="create-name">Full Name</Label>
                        <Input
                            id="create-name"
                            v-model="createForm.name"
                            type="text"
                            placeholder="John Doe"
                            required
                        />
                        <InputError :message="createForm.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-email">Email Address</Label>
                        <Input
                            id="create-email"
                            v-model="createForm.email"
                            type="email"
                            placeholder="john@example.com"
                            required
                        />
                        <InputError :message="createForm.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-password">Password</Label>
                        <Input
                            id="create-password"
                            v-model="createForm.password"
                            type="password"
                            placeholder="••••••••"
                            required
                        />
                        <InputError :message="createForm.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-password-confirmation">Confirm Password</Label>
                        <Input
                            id="create-password-confirmation"
                            v-model="createForm.password_confirmation"
                            type="password"
                            placeholder="••••••••"
                            required
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-role">Role</Label>
                        <Select v-model="createForm.role">
                            <SelectTrigger id="create-role">
                                <SelectValue placeholder="Select role" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="role in roles"
                                    :key="role.value"
                                    :value="role.value"
                                >
                                    {{ role.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="createForm.errors.role" />
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            @click="showCreateModal = false"
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            :disabled="createForm.processing"
                        >
                            Create User
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Edit User Modal -->
        <Dialog v-model:open="showEditModal">
            <DialogContent class="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>Edit User</DialogTitle>
                    <DialogDescription>
                        Update user information. Leave password empty to keep current password.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="updateUser" class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="edit-name">Full Name</Label>
                        <Input
                            id="edit-name"
                            v-model="editForm.name"
                            type="text"
                            required
                        />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-email">Email Address</Label>
                        <Input
                            id="edit-email"
                            v-model="editForm.email"
                            type="email"
                            required
                        />
                        <InputError :message="editForm.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-password">New Password (optional)</Label>
                        <Input
                            id="edit-password"
                            v-model="editForm.password"
                            type="password"
                            placeholder="Leave empty to keep current"
                        />
                        <InputError :message="editForm.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-password-confirmation">Confirm New Password</Label>
                        <Input
                            id="edit-password-confirmation"
                            v-model="editForm.password_confirmation"
                            type="password"
                            placeholder="Confirm new password"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-role">Role</Label>
                        <Select v-model="editForm.role">
                            <SelectTrigger id="edit-role">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="role in roles"
                                    :key="role.value"
                                    :value="role.value"
                                >
                                    {{ role.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="editForm.errors.role" />
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            @click="showEditModal = false"
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            :disabled="editForm.processing"
                        >
                            Update User
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete User Modal -->
        <Dialog v-model:open="showDeleteModal">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Delete User</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete this user? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>

                <div v-if="deletingUser" class="rounded-lg bg-muted p-4">
                    <div class="font-medium">{{ deletingUser.name }}</div>
                    <div class="text-sm text-muted-foreground">{{ deletingUser.email }}</div>
                    <Badge :variant="getRoleBadgeVariant(deletingUser.role)" class="mt-2">
                        {{ deletingUser.role_label }}
                    </Badge>
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="showDeleteModal = false"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="button"
                        variant="destructive"
                        @click="deleteUser"
                    >
                        Delete User
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
