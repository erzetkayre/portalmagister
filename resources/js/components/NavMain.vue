<script setup lang="ts">
import { SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
    label: string;
}>();

</script>

<template>
    <SidebarGroupLabel class="pt-2">{{ label }}</SidebarGroupLabel>
    <SidebarMenu>
        <SidebarMenuItem v-for="item in items" :key="item.title">
            <SidebarMenuButton as-child :is-active="item.routeName ? route().current(item.routeName) : false" :tooltip="item.title">
                <Link :href="item.href">
                    <component :is="item.icon" />
                    <span>{{ item.title }}</span>
                </Link>
            </SidebarMenuButton>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
