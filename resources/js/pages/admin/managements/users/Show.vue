<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { User, Mail, UserCheck, ArrowLeft, Edit, Calendar, IdCard } from 'lucide-vue-next';

interface UserData {
    id: number;
    name: string;
    email: string;
    nomor_induk: string;
    role: string;
    status: 'active' | 'inactive';
    first_login: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    user: UserData;
}

const props = defineProps<Props>();

const editUser = () => {
    router.get(`/admin/user/${props.user.id}/edit`);
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Manajemen Users', href: '/admin/user' },
    { title: 'Detail User', href: `/admin/user/${props.user.id}` },
];
</script>

<template>
    <Head :title="`Detail User - ${user.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Detail User</h1>
                    <p class="text-muted-foreground mt-1">Informasi lengkap pengguna {{ user.name }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" @click="router.get('/admin/user')" class="gap-2">
                        <ArrowLeft class="h-4 w-4" />
                        Kembali
                    </Button>
                    <Button @click="editUser" class="gap-2">
                        <Edit class="h-4 w-4" />
                        Edit
                    </Button>
                </div>
            </div>

            <!-- User Profile Card -->
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
                                <h2 class="text-2xl font-bold text-foreground">{{ user.name }}</h2>
                                <p class="text-muted-foreground">{{ user.email }}</p>
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
                                        <p class="font-semibold">{{ user.name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <Mail class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Email Address</p>
                                        <p class="font-semibold">{{ user.email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <UserCheck class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Status</p>
                                        <Badge
                                            :variant="user.status === 'active' ? 'outline' : 'destructive'"
                                            :class="user.status === 'active' ? 'border-success text-success dark:border-success/60 dark:text-success/90 hover:bg-success/10' : ''"
                                            class="font-medium"
                                        >
                                            {{ user.status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                        </Badge>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <UserCheck class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Role</p>
                                        <Badge variant="outline" class="font-medium">{{ user.role }}</Badge>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <IdCard class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Nomor Induk</p>
                                        <p class="font-semibold">{{ user.nomor_induk }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 rounded-lg bg-muted/30 hover:bg-muted/50 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <Calendar class="w-5 h-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-muted-foreground">Created At</p>
                                        <p class="font-semibold">{{ new Date(user.created_at).toLocaleString('id-ID') }}</p>
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
