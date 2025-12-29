<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';

const page = usePage();
const user = page.props.auth.user;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manager',
        href: '/manager/dashboard',
    },
    {
        title: 'Dashboard',
        href: '/manager/dashboard',
    },
];
</script>

<template>
    <Head title="Manager Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mb-6">
                <h1 class="text-3xl font-bold">Manager Dashboard</h1>
                <p class="text-muted-foreground mt-2">
                    Welcome, {{ user?.name }} ({{ user?.role_label }})
                </p>
            </div>

            <div class="grid auto-rows-min gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h3 class="text-lg font-semibold mb-2">Active Projects</h3>
                    <p class="text-3xl font-bold text-primary">---</p>
                </div>
                <div class="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h3 class="text-lg font-semibold mb-2">Team Members</h3>
                    <p class="text-3xl font-bold text-primary">---</p>
                </div>
            </div>

            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
