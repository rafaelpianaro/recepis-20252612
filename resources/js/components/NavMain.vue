<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { ChevronRight } from 'lucide-vue-next';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();

function hasActiveChild(item: NavItem): boolean {
    if (!item.items) return false;
    return item.items.some(child => urlIsActive(child.href, page.url));
}
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <!-- Menu com subitems (Collapsible) -->
                <Collapsible 
                    v-if="item.items && item.items.length > 0"
                    as-child
                    :default-open="hasActiveChild(item)"
                    class="group/collapsible"
                >
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :tooltip="item.title">
                                <component v-if="item.icon" :is="item.icon" />
                                <span>{{ item.title }}</span>
                                <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="urlIsActive(subItem.href, page.url)"
                                    >
                                        <!-- Link externo (sem Inertia) -->
                                        <a v-if="subItem.external" :href="subItem.href">
                                            <component v-if="subItem.icon" :is="subItem.icon" />
                                            <span>{{ subItem.title }}</span>
                                        </a>
                                        
                                        <!-- Link interno (com Inertia) -->
                                        <Link v-else :href="subItem.href">
                                            <component v-if="subItem.icon" :is="subItem.icon" />
                                            <span>{{ subItem.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <!-- Menu simples (sem subitems) -->
                <SidebarMenuButton
                    v-else
                    as-child
                    :is-active="urlIsActive(item.href, page.url)"
                    :tooltip="item.title"
                >
                    <!-- Link externo (sem Inertia navigation) -->
                    <a 
                        v-if="item.external"
                        :href="item.href"
                    >
                        <component v-if="item.icon" :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </a>
                    
                    <!-- Link interno (com Inertia navigation) -->
                    <Link v-else :href="item.href">
                        <component v-if="item.icon" :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
