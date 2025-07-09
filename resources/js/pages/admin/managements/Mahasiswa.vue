<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../../../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Daftar Mahasiswa',
        href: '/admin/mahasiswa',
    },
];
</script>

<template>
    <Head title="Manajemen Mahasiswa" />

    <AppLayout :breadcrumbs="breadcrumbs">
        Mahasiswa
    </AppLayout>
</template>
