<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth.user;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manager',
        href: '/manager/dashboard',
    },
    {
        title: 'Reports',
        href: '/manager/reports',
    },
];
</script>

<template>
    <Head title="Reports" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mb-6">
                <h1 class="text-3xl font-bold">Reports</h1>
                <p class="text-muted-foreground mt-2">
                    View and analyze reports
                </p>
            </div>

            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                <p class="text-muted-foreground">Reports interface - Coming soon</p>
            </div>
        </div>
    </AppLayout>
</template>
