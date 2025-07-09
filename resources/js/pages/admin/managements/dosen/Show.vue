<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { User, Mail, ArrowLeft, Edit, IdCard, GraduationCap, Users, BookOpen } from 'lucide-vue-next';

interface DosenData {
    id: number;
    name: string;
    email: string;
    nip: string;
    kode_dosen: string;
    bidang_keahlian: string;
    gender: string;
    updated_at: string;
}

interface Props {
    dosen: DosenData;
}

const props = defineProps<Props>();

const editDosen = () => {
    router.get(`/admin/dosen/${props.dosen.id}/edit`);
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Manajemen Dosen', href: '/admin/dosen' },
    { title: 'Detail Dosen', href: `/admin/dosen/${props.dosen.id}` },
];
</script>

<template>
    <Head :title="`Detail Dosen - ${dosen.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Detail Dosen</h1>
                    <p class="text-muted-foreground mt-1">Informasi lengkap dosen {{ dosen.name }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" @click="router.get('/admin/dosen')" class="gap-2">
                        <ArrowLeft class="h-4 w-4" />
                        Kembali
                    </Button>
                    <Button @click="editDosen" class="gap-2">
                        <Edit class="h-4 w-4" />
                        Edit
                    </Button>
                </div>
            </div>

            <!-- Dosen Profile Card -->
            <div class="flex justify-center">
                <Card class="w-full max-w-4xl">
                    <CardContent class="p-8">
                        <!-- Avatar and Name Section -->
                        <div class="flex flex-col items-center space-y-4 mb-8">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary/20 to-primary/10 flex items-center justify-center border-4 border-primary/20">
                                    <GraduationCap class="w-12 h-12 text-primary" />
                                </div>
                            </div>
                            <div class="text-center">
                                <h2 class="text-2xl font-bold text-foreground">{{ dosen.name }}</h2>
                                <p class="text-muted-foreground">{{ dosen.email }}</p>
                            </div>
                        </div>

                        <!-- Information Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <User class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Nama Lengkap</p>
                                        <p class="font-semibold">{{ dosen.name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <Mail class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Email Address</p>
                                        <p class="font-semibold">{{ dosen.email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <BookOpen class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Bidang Keahlian</p>
                                        <p class="font-semibold">{{ dosen.bidang_keahlian ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <IdCard class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">NIP</p>
                                        <p class="font-semibold">{{ dosen.nip }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <GraduationCap class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Kode Dosen</p>
                                        <p class="font-semibold">{{ dosen.kode_dosen }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <Users class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Gender</p>
                                        <Badge variant="outline" class="font-medium">
                                            {{ dosen.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

    </AppLayout>
</template>
