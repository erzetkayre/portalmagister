<script setup lang="ts">
import { 
    SidebarGroupLabel, 
    SidebarMenu, 
    SidebarMenuButton, 
    SidebarMenuItem, 
    SidebarMenuSub, 
    SidebarMenuSubButton, 
    SidebarMenuSubItem 
} from '@/components/ui/sidebar';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';

defineProps<{
    items: NavItem[];
    label: string;
}>();

function isActiveItem(item: NavItem): boolean {
    if (item.routeName && route().current(item.routeName)) {
        return true;
    }
    if (item.items) {
        return item.items.some(subItem => isActiveItem(subItem));
    }
    return false;
}
</script>

<template>
    <SidebarGroupLabel class="pt-2">{{ label }}</SidebarGroupLabel>
    <SidebarMenu>
        <SidebarMenuItem v-for="item in items" :key="item.title">
            <!-- Item with dropdown -->
            <template v-if="item.items && item.items.length > 0">
                <Collapsible :default-open="isActiveItem(item)" class="group/collapsible">
                    <CollapsibleTrigger as-child>
                        <SidebarMenuButton :tooltip="item.title" :is-active="isActiveItem(item)">
                            <component :is="item.icon" v-if="item.icon" />
                            <span>{{ item.title }}</span>
                            <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                        </SidebarMenuButton>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                        <SidebarMenuSub>
                            <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                                <SidebarMenuSubButton 
                                    as-child 
                                    :is-active="subItem.routeName ? route().current(subItem.routeName) : false"
                                >
                                    <Link :href="subItem.href">
                                        <component :is="subItem.icon" v-if="subItem.icon" />
                                        <span>{{ subItem.title }}</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                        </SidebarMenuSub>
                    </CollapsibleContent>
                </Collapsible>
            </template>
            
            <!-- Regular item without dropdown -->
            <template v-else>
                <SidebarMenuButton 
                    as-child 
                    :is-active="item.routeName ? route().current(item.routeName) : false" 
                    :tooltip="item.title"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" v-if="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </template>
        </SidebarMenuItem>
    </SidebarMenu>
</template>