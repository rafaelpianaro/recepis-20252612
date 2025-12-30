<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Shield, Users, FileText, Settings } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
    ];

    // Admin-only menu with subitems
    if (user.value?.role === 'admin') {
        items.push({
            title: 'Admin Config',
            href: '#',  // Não é clicável diretamente
            icon: Settings,
            items: [
                {
                    title: 'Dashboard',
                    href: '/admin/dashboard',
                    icon: Shield,
                },
                {
                    title: 'Users',
                    href: '/admin/users',
                    icon: Users,
                },
                {
                    title: 'Logs',
                    href: '/admin/logs',
                    icon: FileText,
                    external: true,  // Link externo
                },
            ],
        });
    }

    // Manager-only menu items
    if (user.value?.role === 'manager') {
        items.push({
            title: 'Manager',
            href: '/manager/dashboard',
            icon: Shield,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
