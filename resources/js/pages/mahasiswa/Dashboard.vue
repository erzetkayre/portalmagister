<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../../components/PlaceholderPattern.vue';
import {
    Badge, Button, StatCard, AlertNotification, DataTable, FilterBar, ActionButtons, ConfirmationDialog
} from '@/components/ui';

import {
    BookUser, GraduationCap, UsersRound, Plus, Eye, KeyRound, Edit, Trash2
 } from 'lucide-vue-next';

const props = defineProps<{
  status_pratesis: string
  status_ujiantesis: string
}>()


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard Mahasiswa',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard Mahasiswa" />

    <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <StatCard title="Pengajuan MK Pratesis":value="props.status_pratesis"description="Status pengajuan MK" :icon="BookUser" href="mahasiswa.draft.index"/>
            <StatCard title="Pengajuan Ujian Pratesis":value="props.status_ujiantesis"description="Status pengajuan MK" :icon="BookUser"/>
            <!-- <StatCard title="Pengajuan MK Pratesis" :value="'BELUM DAFTAR'" description="Belum mengajukan pendaftaran M" :icon="BookUser"/> -->
            <!-- <StatCard title="Pengajuan Ujian Pratesis" :value="'PENDING'" description="Jumlah user dosen terdaftar" :icon="BookUser"/> -->
            <!-- <StatCard title="Tesis" :value="3" description="Jumlah user mahasiswa terdaftar" :icon="GraduationCap" :badge="3"/> -->
        </div>
    </div>
    </AppLayout>
</template>
