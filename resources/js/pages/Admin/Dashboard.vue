<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Users, Shield, TrendingUp, Clock } from 'lucide-vue-next';

interface Stats {
    total_users: number;
    admins: number;
    managers: number;
    users: number;
    users_last_7_days: number;
    users_last_30_days: number;
}

interface RecentUser {
    id: number;
    name: string;
    email: string;
    role: string;
    role_label: string;
    created_at: string;
    created_at_human: string;
}

interface UserByRole {
    role: string;
    count: number;
}

interface Props {
    stats: Stats;
    recent_users: RecentUser[];
    users_by_role: UserByRole[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: '/admin/dashboard',
    },
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
];

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
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold">Admin Dashboard</h1>
                <p class="text-muted-foreground mt-2">
                    Overview of system metrics and recent activity
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_users }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            All registered users
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Administrators</CardTitle>
                        <Shield class="h-4 w-4 text-destructive" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.admins }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            System administrators
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Managers</CardTitle>
                        <Users class="h-4 w-4 text-blue-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.managers }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Active managers
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Regular Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.users }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Standard users
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Growth Cards -->
            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">New Users (7 days)</CardTitle>
                        <TrendingUp class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.users_last_7_days }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Registered in the last week
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">New Users (30 days)</CardTitle>
                        <TrendingUp class="h-4 w-4 text-blue-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.users_last_30_days }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Registered in the last month
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Users -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Recent Users</CardTitle>
                            <CardDescription>Latest users registered in the system</CardDescription>
                        </div>
                        <Link href="/admin/users">
                            <span class="text-sm text-primary hover:underline">View all →</span>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div
                            v-for="user in recent_users"
                            :key="user.id"
                            class="flex items-center justify-between rounded-lg border p-4"
                        >
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <p class="font-medium">{{ user.name }}</p>
                                        <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <Badge :variant="getRoleBadgeVariant(user.role)">
                                    {{ user.role_label }}
                                </Badge>
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Clock class="h-3 w-3" />
                                    <span>{{ user.created_at_human }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-if="recent_users.length === 0" class="text-center py-8 text-muted-foreground">
                            No users registered yet
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Quick Actions -->
            <Card>
                <CardHeader>
                    <CardTitle>Quick Actions</CardTitle>
                    <CardDescription>Common administrative tasks</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-3">
                        <Link
                            href="/admin/users"
                            class="flex flex-col items-center justify-center rounded-lg border p-6 hover:bg-accent transition-colors"
                        >
                            <Users class="h-8 w-8 mb-2 text-primary" />
                            <span class="font-medium">Manage Users</span>
                            <span class="text-xs text-muted-foreground mt-1">View and edit users</span>
                        </Link>

                        <div class="flex flex-col items-center justify-center rounded-lg border p-6 opacity-50 cursor-not-allowed">
                            <Shield class="h-8 w-8 mb-2 text-muted-foreground" />
                            <span class="font-medium">Settings</span>
                            <span class="text-xs text-muted-foreground mt-1">Coming soon</span>
                        </div>

                        <div class="flex flex-col items-center justify-center rounded-lg border p-6 opacity-50 cursor-not-allowed">
                            <Clock class="h-8 w-8 mb-2 text-muted-foreground" />
                            <span class="font-medium">Activity Log</span>
                            <span class="text-xs text-muted-foreground mt-1">Coming soon</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
