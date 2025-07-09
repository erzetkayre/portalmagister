<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { User, Mail, UserCheck, ArrowLeft, Edit, Calendar, IdCard, GraduationCap, Users } from 'lucide-vue-next';

interface MahasiswaData {
    id: number;
    name: string;
    email: string;
    nim: string;
    angkatan: number;
    gender: string;
    status: 'active' | 'inactive';
    created_at: string;
    updated_at: string;
}

interface Props {
    mahasiswa: MahasiswaData;
}

const props = defineProps<Props>();

const editMahasiswa = () => {
    router.get(`/admin/mahasiswa/${props.mahasiswa.id}/edit`);
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Manajemen Mahasiswa', href: '/admin/mahasiswa' },
    { title: 'Detail Mahasiswa', href: `/admin/mahasiswa/${props.mahasiswa.id}` },
];
</script>

<template>
    <Head :title="`Detail Mahasiswa - ${mahasiswa.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Detail Mahasiswa</h1>
                    <p class="text-muted-foreground mt-1">Informasi lengkap mahasiswa {{ mahasiswa.name }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" @click="router.get('/admin/mahasiswa')" class="gap-2">
                        <ArrowLeft class="h-4 w-4" />
                        Kembali
                    </Button>
                    <Button @click="editMahasiswa" class="gap-2">
                        <Edit class="h-4 w-4" />
                        Edit
                    </Button>
                </div>
            </div>

            <!-- Mahasiswa Profile Card -->
            <div class="flex justify-center">
                <Card class="w-full max-w-4xl">
                    <CardContent class="p-8">
                        <!-- Avatar and Name Section -->
                        <div class="flex flex-col items-center space-y-4 mb-8">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary/20 to-primary/10 flex items-center justify-center border-4 border-primary/20">
                                    <User class="w-12 h-12 text-primary" />
                                </div>
                            </div>
                            <div class="text-center">
                                <h2 class="text-2xl font-bold text-foreground">{{ mahasiswa.name }}</h2>
                                <p class="text-muted-foreground">{{ mahasiswa.email }}</p>
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
                                        <p class="font-semibold">{{ mahasiswa.name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <Mail class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Email Address</p>
                                        <p class="font-semibold">{{ mahasiswa.email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <Users class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Gender</p>
                                        <Badge variant="outline" class="font-medium">
                                            {{ mahasiswa.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </Badge>
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
                                        <p class="text-sm font-medium text-muted-foreground">NIM</p>
                                        <p class="font-semibold">{{ mahasiswa.nim }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <GraduationCap class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Angkatan</p>
                                        <Badge variant="outline" class="font-medium">{{ mahasiswa.angkatan }}</Badge>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <Calendar class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Created At</p>
                                        <p class="font-semibold">{{ new Date(mahasiswa.created_at).toLocaleDateString('id-ID', {
                                                day: 'numeric',
                                                month: 'long',
                                                year: 'numeric'
                                            }) }}
                                        </p>
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
