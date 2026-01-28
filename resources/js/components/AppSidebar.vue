<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavMainDropdown from '@/components/NavMainDropdown.vue';
import NavUser from '@/components/NavUser.vue';
import AppLogo from './AppLogo.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarGroup } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, BookOpenCheck, GraduationCap, IdCard, LayoutGrid, List, UsersRound } from 'lucide-vue-next';
import { useAuth } from '@/composables/useAuth';

const {
    user,
    can,
    program,
    isAuthenticated,
    isAdmin,
    isKoordinator,
    isPWK,
    isElektro
} = useAuth();

// Menu Dashboard
const dashboardNavItems: NavItem[] = [
    { title: 'Dashboard', href: route('dashboard'), routeName: 'dashboard', icon: LayoutGrid },
];

// Menu Admin
const  adminMainNavItems: NavItem[] = [
    { title: 'User Management', href: route('admin.users.index'), routeName: 'admin.users.index', icon: UsersRound },
    { title: 'Daftar Mahasiswa', href: route('dashboard'), routeName: 'dashboard', icon: GraduationCap },
    { title: 'Daftar Dosen', href: route('dashboard'), routeName: 'dashboard', icon: IdCard },
    { title: 'Data Pendukung', href: '#',icon: List,
    items: [
            { title: 'Daftar Pembimbing Akademik', href: route('dashboard'), routeName: 'dashboard' },
            { title: 'Daftar Mata Kuliah', href: route('dashboard'), routeName: 'admin.pratesis.draft.*'},
            { title: 'Daftar Ruang', href: route('dashboard'), routeName: 'admin.sempro.*'},
            { title: 'Daftar Jabatan', href: route('dashboard'), routeName: 'admin.sempro.*'},
        ]},
];

const managementNavItems: NavItem[] = [
    // { title: 'Dosen', href: route('admin.dosen.index'), routeName: 'admin.dosen.*', icon: UsersRound },
    // { title: 'Mahasiswa', href: route('admin.mahasiswa.index'), routeName: 'admin.mahasiswa.*', icon: GraduationCap },
    // { title: 'Users', href: route('admin.users.index'), routeName: 'admin.users.*', icon: BookUser },
];

// Menu Koordinator
const koordinatorMainNavItems: NavItem[] = [
    // { title: 'Dashboard', href: route('koordinator.dashboard'), routeName: 'koordinator.dashboard', icon: LayoutGrid },

];

// Menu Dosen
const dosenMainNavItems: NavItem[] = [
    // { title: 'Dashboard', href: route('dosen.dashboard'), routeName: 'dosen.dashboard', icon: LayoutGrid },
];

// Menu Mahasiswa
const mahasiswaMainNavItems: NavItem[] = [
    // { title: 'Dashboard', href: route('mahasiswa.dashboard'), routeName: 'mahasiswa.dashboard', icon: LayoutGrid },
    { title: 'Proposal Tesis', href: '#',icon: BookOpenCheck,
        items: [
            // { title: 'Pengajuan Proposal', href: route('mahasiswa.draft.index'), routeName: 'mahasiswa.draft.*'},
            // { title: 'Seminar Proposal', href: route('mahasiswa.sempro.index'), routeName: 'mahasiswa.sempro.*' },
        ]},
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
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <SidebarGroup class="px-2 py-0">
                <NavMain :label="''" :items="dashboardNavItems"/>
            </SidebarGroup>
            <SidebarGroup v-if="isAdmin" class="px-2 py-0">
                <NavMainDropdown :label="'Administrator'" :items="adminMainNavItems"/>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
