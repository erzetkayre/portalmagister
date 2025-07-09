<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../../../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Daftar Dosen',
        href: '/admin/dosen',
    },
];
</script>

<template>
    <Head title="Manajemen Dosen" />

    <AppLayout :breadcrumbs="breadcrumbs">
        Dosen
    </AppLayout>
</template>
