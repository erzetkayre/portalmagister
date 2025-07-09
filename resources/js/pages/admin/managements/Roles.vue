<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../../../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Daftar Roles',
        href: '/admin/roles',
    },
];
</script>

<template>
    <Head title="Manajemen Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        Roles
    </AppLayout>
</template>
