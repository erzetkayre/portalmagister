<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import Button from '@/components/ui/button/Button.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link} from '@inertiajs/vue3';
import { useAuth } from '@/composables/useAuth';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

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

</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Dashboard" />
        <template v-if="program === 'pwk'">
            Dashboard PWK
            <!-- <Button as-child><Link :href="route('pwk.dashboard')"></Link></Button> -->
        </template>
        <template v-else-if="program === 'elektro'">
            Dashboard Elektro
            <!-- <Button as-child><Link :href="route('elektro.dashboard')"></Link></Button> -->

            <div v-if="can.admin">
                Admin Panel
            </div>
            <div v-if="can.mahasiswa">
                Mahasiswa Panel
            </div>
            <div v-if="can.koordinator">
                Koordinator Panel
            </div>
            <div v-if="can.dosen">
                dosen Panel
            </div>
        </template>
        <template v-else>
            Tidak memiliki akses
        </template>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
