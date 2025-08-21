<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '../../components/PlaceholderPattern.vue';
import {
    Badge, Button, StatCard, AlertNotification, DataTable, FilterBar, ActionButtons, ConfirmationDialog
} from '@/components/ui';
import {
    BookUser, GraduationCap, UsersRound, Plus, Eye, KeyRound, Edit, Trash2
 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard Admin',
        href: '/admin/dashboard',
    },
];

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    status: 'active' | 'inactive';
    created_at: string;
}

interface PaginatedUsers {
    data: User[];
    current_page: number;
    from: number;
    to: number;
    total: number;
    last_page: number;
    per_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}
interface Props {
    users: PaginatedUsers;
    roles: string[];
    status_pratesis: string;
    status_ujiantesis: string;
    stats: {
        totalUsers: number;
        totalAdmin: number;
        totalMahasiswa: number;
        totalDosen: number;
    };
}
const props = defineProps<Props>();
const page = usePage();
</script>

<template>
    <Head title="Dashboard Admin" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <StatCard title="Pengajuan MK Pratesis":value="props.status_pratesis"description="Status pengajuan MK" :icon="BookUser" href="mahasiswa.draft.index"/>
            <StatCard title="Pengajuan Ujian Pratesis":value="props.status_ujiantesis"description="Status pengajuan MK" :icon="BookUser"/>
                <!-- <StatCard title="User Admin" :value="props.stats.totalAdmin" description="Jumlah user admin terdaftar" :icon="BookUser" :badge="`${Math.round((props.stats.totalAdmin / props.stats.totalUsers) * 100)}%`"/>
            <StatCard title="User Dosen" :value="props.stats.totalDosen" description="Jumlah user dosen terdaftar" :icon="UsersRound" :badge="`${Math.round((props.stats.totalDosen / props.stats.totalUsers) * 100)}%`"/>
            <StatCard title="User Mahasiswa" :value="props.stats.totalMahasiswa" description="Jumlah user mahasiswa terdaftar" :icon="GraduationCap" :badge="`${Math.round((props.stats.totalMahasiswa / props.stats.totalUsers) * 100)}%`"/> -->
            </div>
        </div>
    </AppLayout>
</template>
