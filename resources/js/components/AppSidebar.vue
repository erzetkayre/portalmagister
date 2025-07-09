<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarGroup } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, BookUser, GraduationCap, LayoutGrid, UsersRound } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed} from 'vue';

const { props } = usePage();

console.log(props);

const dashboardRoute = computed(() => {
    const user = props.auth?.user as any;
    if (!user) return '/login';

    const role = user.role?.nama_role;
    // console.log(user);

    return route(`${role}.dashboard`);
});

const userRole = computed(() => {
    const user = props.auth?.user as any;
    return user?.role?.nama_role || 'guest';
});

// Menu Admin
const adminMainNavItems: NavItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard'), routeName: 'admin.dashboard', icon: LayoutGrid },
];

const managementNavItems: NavItem[] = [
    { title: 'Dosen', href: route('admin.dosen.index'), routeName: 'admin.dosen.*', icon: UsersRound },
    { title: 'Mahasiswa', href: route('admin.mahasiswa.index'), routeName: 'admin.mahasiswa.*', icon: GraduationCap },
    { title: 'User', href: route('admin.users.index'), routeName: 'admin.users.*', icon: BookUser },
];



// Menu Koordinator
const koordinatorMainNavItems: NavItem[] = [
    { title: 'Dashboard', href: route('koordinator.dashboard'), routeName: 'koordinator.dashboard', icon: LayoutGrid },

];

// Menu Dosen
const dosenMainNavItems: NavItem[] = [
    { title: 'Dashboard', href: route('dosen.dashboard'), routeName: 'dosen.dashboard', icon: LayoutGrid },
];

// Menu Mahasiswa
const mahasiswaMainNavItems: NavItem[] = [
    { title: 'Dashboard', href: route('mahasiswa.dashboard'), routeName: 'mahasiswa.dashboard', icon: LayoutGrid },

];





const footerNavItems: NavItem[] = [
    {
        title: 'Manual Book',
        href: 'https://github.com/laravel/vue-starter-kit',
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
                        <Link :href="dashboardRoute">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <SidebarGroup v-if="userRole === 'admin'" class="px-2 py-0">
                <NavMain :label="'Menu Utama'" :items="adminMainNavItems"/>
                <NavMain :label="'Manajemen Data'" :items="managementNavItems"/>
            </SidebarGroup>
            <SidebarGroup v-else-if="userRole === 'koordinator'" class="px-2 py-0">
                <NavMain :label="'Manajemen'" :items="koordinatorMainNavItems"/>
            </SidebarGroup>
            <SidebarGroup v-else-if="userRole === 'dosen'" class="px-2 py-0">
                <NavMain :label="'Manajemen'" :items="dosenMainNavItems"/>
            </SidebarGroup>
            <SidebarGroup v-else class="px-2 py-0">
                <NavMain :label="'Menu Utama'" :items="mahasiswaMainNavItems"/>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
