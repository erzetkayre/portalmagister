<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavMainDropdown from '@/components/NavMainDropdown.vue';
import NavUser from '@/components/NavUser.vue';
import AppLogo from './AppLogo.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarGroup } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    BookOpen, BookOpenCheck, GraduationCap, IdCard, LayoutGrid, List, UsersRound,
    ScrollText, CalendarRange, ClipboardList, NotebookPen, FlaskConical,
    ArrowRightLeft, CalendarClock, Award, BookText, BookMarked, Presentation,
    ListCheck,
    FilePenLine,
} from 'lucide-vue-next';
import { useAuth } from '@/composables/useAuth';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const { isAdmin, isDosen, isMahasiswa, isElektro, isPWK, isKoordinator } = useAuth();

// Detect thesis_path from shared props (if available)
const page  = usePage();
const path  = computed(() => (page.props as any).auth?.mahasiswa?.thesis_path as string | null | undefined);
const isRes = computed(() => path.value === 'penelitian');

// Menu Admin (master data)
const administratorMenu: NavItem[] = [
    { title: 'User Management', href: route('admin.users.index'), routeName: 'admin.users.index', icon: UsersRound },
    { title: 'Daftar Dosen', href: route('admin.dosen.index'), routeName: 'admin.dosen.index', icon: IdCard },
    { title: 'Daftar Mahasiswa', href: route('admin.mahasiswa.index'), routeName: 'admin.mahasiswa.index', icon: GraduationCap },
    {
        title: 'Data Pendukung', href: '#', icon: List,
        items: [
            { title: 'Mata Kuliah', href: route('admin.mk.index'), routeName: 'admin.mk.index' },
            { title: 'Ruang', href: route('admin.ruang.index'), routeName: 'admin.ruang.index' },
            { title: 'Jabatan', href: route('admin.jabatan.index'), routeName: 'admin.jabatan.index' },
        ],
    },
];

// Menu Mahasiswa PWK
const pwkStudentThesisMenu: NavItem[] = [
    {
        title: 'Tesis', href: '#', icon: BookText,
        items : [
            { title: 'Tesis Saya',      href: route('pwk.mahasiswa.tesis.index'),   routeName: 'pwk.mahasiswa.tesis.index' },
            { title: 'Logbook Tesis',   href: route('pwk.mahasiswa.logbook.index'), routeName: 'pwk.mahasiswa.logbook.index' },
            { title: 'Perubahan Judul', href: '#', routeName: '#' },
        ],
    },
    { title: 'Ujian Pratesis', href: route('pwk.mahasiswa.ujian.show', 'pratesis'), routeName: 'pwk.mahasiswa.ujian.show', icon: NotebookPen },
    { title: 'Ujian Tesis 1',  href: route('pwk.mahasiswa.ujian.show', 'tesis_1'),  routeName: 'pwk.mahasiswa.ujian.show', icon: BookOpen },
    { title: 'Ujian Tesis 2',  href: route('pwk.mahasiswa.ujian.show', 'tesis_2'),  routeName: 'pwk.mahasiswa.ujian.show', icon: Presentation },
];

// Menu Dosen PWK
const pwkAdvisorThesisMenu: NavItem[] = [
    { title: 'Bimbingan Akademik', href: route('pwk.dosen.akademik.index'),  routeName: 'pwk.dosen.akademik.index',  icon: GraduationCap },
    { title: 'Bimbingan Tesis',    href: route('pwk.dosen.bimbingan.index'), routeName: 'pwk.dosen.bimbingan.index', icon: BookMarked },
];
const pwkExamThesisMenu: NavItem[] = [
    { title: 'Ujian Pratesis', href: route('pwk.dosen.ujian.show', 'pratesis'), routeName: 'pwk.dosen.ujian.show', icon: NotebookPen },
    { title: 'Ujian Tesis 1',  href: route('pwk.dosen.ujian.show', 'tesis_1'),  routeName: 'pwk.dosen.ujian.show', icon: BookOpen },
    { title: 'Ujian Tesis 2',  href: route('pwk.dosen.ujian.show', 'tesis_2'),  routeName: 'pwk.dosen.ujian.show', icon: Presentation },
];

// Menu Koordinator PWK
const pwkCoordinatorThesisMenu: NavItem[] = [
    { title: 'Pengajuan Tesis', href: route('pwk.koordinator.tesis.pengajuan.index'), routeName: 'pwk.koordinator.tesis.pengajuan.index', icon: BookMarked },
    { title: 'Daftar Tesis',    href: route('pwk.koordinator.tesis.index'),            routeName: 'pwk.koordinator.tesis.index',            icon: ListCheck },
    { title: 'Logbook Tesis',   href: route('pwk.koordinator.logbook.index'),          routeName: 'pwk.koordinator.logbook.index',          icon: ScrollText },
    {
        title: 'Pratesis', href: '#', icon: NotebookPen,
        items: [
            { title: 'Pengajuan Pratesis', href: route('pwk.koordinator.ujian.pengajuan', 'pratesis'), routeName: 'pwk.koordinator.ujian.pengajuan' },
            { title: 'Daftar Pratesis',    href: route('pwk.koordinator.ujian.daftar', 'pratesis'),    routeName: 'pwk.koordinator.ujian.daftar' },
        ],
    },
    {
        title: 'Ujian Tesis 1', href: '#', icon: BookOpen,
        items: [
            { title: 'Pengajuan Tesis 1', href: route('pwk.koordinator.ujian.pengajuan', 'tesis_1'), routeName: 'pwk.koordinator.ujian.pengajuan' },
            { title: 'Daftar Tesis 1',    href: route('pwk.koordinator.ujian.daftar', 'tesis_1'),    routeName: 'pwk.koordinator.ujian.daftar' },
        ],
    },
    {
        title: 'Ujian Tesis 2', href: '#', icon: Presentation,
        items: [
            { title: 'Pengajuan Tesis 2', href: route('pwk.koordinator.ujian.pengajuan', 'tesis_2'), routeName: 'pwk.koordinator.ujian.pengajuan' },
            { title: 'Daftar Tesis 2',    href: route('pwk.koordinator.ujian.daftar', 'tesis_2'),    routeName: 'pwk.koordinator.ujian.daftar' },
        ],
    },
    { title: 'Perubahan Judul', href: route('pwk.koordinator.perubahan.judul.index'), routeName: 'pwk.koordinator.perubahan.judul.index', icon: FilePenLine },
];

// ─── Elektro admin (thesis management) ────────────────────────────────────────
const adminThesisNavItems: NavItem[] = [
    { title: 'Semua Tesis',        href: route('elektro.admin.thesis.index'),              routeName: 'elektro.admin.thesis.index',              icon: ScrollText },
    { title: 'Pendaftaran Masuk',  href: route('elektro.admin.thesis.registration.index'), routeName: 'elektro.admin.thesis.registration.index', icon: ClipboardList },
    { title: 'Penjadwalan Ujian',  href: route('elektro.admin.exam.index'),                routeName: 'elektro.admin.exam.index',                icon: CalendarRange },
    { title: 'Finalisasi Nilai',   href: route('elektro.admin.exam.grade.index'),          routeName: 'elektro.admin.exam.grade.index',          icon: Award },
    { title: 'Logbook Monitor',    href: route('elektro.admin.logbook.index'),             routeName: 'elektro.admin.logbook.index',             icon: NotebookPen },
    {
        title: 'Perubahan', href: '#', icon: ArrowRightLeft,
        items: [
            { title: 'Perubahan Judul',       href: route('elektro.admin.changes.title.index'),      routeName: 'elektro.admin.changes.title.index' },
            { title: 'Perubahan Pembimbing',  href: route('elektro.admin.changes.supervisor.index'), routeName: 'elektro.admin.changes.supervisor.index' },
            { title: 'Perpanjangan Thesis',   href: route('elektro.admin.changes.extension.index'),  routeName: 'elektro.admin.changes.extension.index' },
        ],
    },
];

// ─── Dosen (Elektro) ──────────────────────────────────────────────────────────
const dosenNavItems: NavItem[] = [
    { title: 'Bimbingan Akademik', href: route('elektro.dosen.akademik.index'),        routeName: 'elektro.dosen.akademik.index',        icon: GraduationCap },
    { title: 'Bimbingan Thesis',   href: route('elektro.dosen.thesis.index'),           routeName: 'elektro.dosen.thesis.index',           icon: BookOpenCheck },
    { title: 'Penilaian Ujian',    href: route('elektro.dosen.exam.index'),             routeName: 'elektro.dosen.exam.index',             icon: Award },
    { title: 'Logbook Review',     href: route('elektro.dosen.logbook.index', { thesisId: 0 }), routeName: 'elektro.dosen.logbook.index',   icon: NotebookPen },
    {
        title: 'Perubahan', href: '#', icon: ArrowRightLeft,
        items: [
            { title: 'Perubahan Judul',      href: route('elektro.dosen.changes.title.index'),      routeName: 'elektro.dosen.changes.title.index' },
            { title: 'Perubahan Pembimbing', href: route('elektro.dosen.changes.supervisor.index'), routeName: 'elektro.dosen.changes.supervisor.index' },
            { title: 'Perpanjangan',         href: route('elektro.dosen.changes.extension.index'),  routeName: 'elektro.dosen.changes.extension.index' },
        ],
    },
];

// ─── Mahasiswa (Elektro) — conditional by thesis_path ────────────────────────
const mahasiswaThesisNavItems = computed((): NavItem[] => [
    { title: 'Tesis Saya',         href: route('elektro.mahasiswa.thesis.index'),         routeName: 'elektro.mahasiswa.thesis.index',         icon: BookOpenCheck },
    { title: 'Logbook',            href: route('elektro.mahasiswa.thesis.logbook.index'), routeName: 'elektro.mahasiswa.thesis.logbook.index', icon: NotebookPen },
    { title: 'Seminar Proposal',   href: route('elektro.mahasiswa.thesis.exam.show', { examType: 'seminar_proposal' }), icon: CalendarClock },
    ...(isRes.value ? [{ title: 'Seminar Kemajuan', href: route('elektro.mahasiswa.thesis.exam.show', { examType: 'seminar_kemajuan' }), icon: FlaskConical }] : []),
    { title: 'Seminar Hasil',      href: route('elektro.mahasiswa.thesis.exam.show', { examType: 'seminar_hasil' }),    icon: CalendarClock },
    { title: 'Ujian Tesis',        href: route('elektro.mahasiswa.thesis.exam.show', { examType: 'ujian_tesis' }),      icon: Award },
    {
        title: 'Perubahan', href: '#', icon: ArrowRightLeft,
        items: [
            { title: 'Perubahan Judul',      href: route('elektro.mahasiswa.thesis.changes.title.index'),      routeName: 'elektro.mahasiswa.thesis.changes.title.index' },
            { title: 'Perubahan Pembimbing', href: route('elektro.mahasiswa.thesis.changes.supervisor.index'), routeName: 'elektro.mahasiswa.thesis.changes.supervisor.index' },
            { title: 'Perpanjangan',         href: route('elektro.mahasiswa.thesis.changes.extension.index'),  routeName: 'elektro.mahasiswa.thesis.changes.extension.index' },
        ],
    },
]);

const dashboardNavItems: NavItem[] = [
    { title: 'Dashboard', href: route('dashboard'), routeName: 'dashboard', icon: LayoutGrid },
];

const footerNavItems: NavItem[] = [
    { title: 'Manual Book', href: 'https://github.com/laravel/vue-starter-kit', icon: BookOpen },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')"><AppLogo /></Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <SidebarGroup class="px-2 py-0">
                <NavMain :label="''" :items="dashboardNavItems" />
            </SidebarGroup>

            <!-- Admin: master data -->
            <SidebarGroup v-if="isAdmin" class="px-2 py-0">
                <NavMainDropdown :label="'Administrator ADMIN'" :items="administratorMenu" />
            </SidebarGroup>

            <!-- Mahasiswa PWK -->
            <SidebarGroup v-if="isMahasiswa && isPWK" class="px-2 py-0">
                <NavMainDropdown :label="'Tesis MAHASISWA'" :items="pwkStudentThesisMenu" />
            </SidebarGroup>

            <!-- Dosen PWK -->
            <SidebarGroup v-if="isDosen && isPWK" class="px-2 py-0">
                <NavMainDropdown :label="'Bimbingan DOSEN'" :items="pwkAdvisorThesisMenu" />
            </SidebarGroup>
            <SidebarGroup v-if="isDosen && isPWK" class="px-2 py-0">
                <NavMainDropdown :label="'Seminar DOSEN'" :items="pwkExamThesisMenu" />
            </SidebarGroup>

            <!-- Koordinator PWK -->
            <SidebarGroup v-if="isKoordinator && isPWK" class="px-2 py-0">
                <NavMainDropdown :label="'Administrasi Tesis KOORD'" :items="pwkCoordinatorThesisMenu" />
            </SidebarGroup>

            <!--  -->

            <!-- Admin + Elektro: thesis management -->
            <SidebarGroup v-if="isAdmin && isElektro" class="px-2 py-0">
                <NavMain :label="'Manajemen Tesis'" :items="adminThesisNavItems" />
            </SidebarGroup>

            <!-- Dosen: Elektro features -->
            <SidebarGroup v-if="isDosen && isElektro" class="px-2 py-0">
                <NavMainDropdown :label="'Dosen'" :items="dosenNavItems" />
            </SidebarGroup>

            <!-- Mahasiswa: Elektro thesis (only shown if path is set) -->
            <SidebarGroup v-if="isMahasiswa && isElektro && path" class="px-2 py-0">
                <NavMainDropdown :label="'Tesis'" :items="mahasiswaThesisNavItems" />
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
