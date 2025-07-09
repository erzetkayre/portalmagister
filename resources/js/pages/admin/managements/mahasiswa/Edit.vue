<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { User, Mail, ArrowLeft, Save, IdCard, GraduationCap, Users, ChevronDown } from 'lucide-vue-next';

interface MahasiswaData {
    id: number;
    name: string;
    email: string;
    nim: string;
    angkatan: number;
    gender: string;
}

interface Props {
    mahasiswa: MahasiswaData;
    angkatan: number[];
}

const props = defineProps<Props>();

const form = useForm({
    name: props.mahasiswa.name,
    email: props.mahasiswa.email,
    nim: props.mahasiswa.nim || '',
    angkatan: props.mahasiswa.angkatan,
    gender: props.mahasiswa.gender || 'L',
});

const submit = () => {
    form.put(`/admin/mahasiswa/${props.mahasiswa.id}`);
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Manajemen Mahasiswa', href: '/admin/mahasiswa' },
    { title: 'Edit Mahasiswa', href: `/admin/mahasiswa/${props.mahasiswa.id}/edit` },
];
</script>

<template>
    <Head title="Edit Mahasiswa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Edit Mahasiswa</h1>
                    <p class="text-muted-foreground mt-1">Update informasi mahasiswa</p>
                </div>
                <Button variant="outline" @click="router.get('/admin/mahasiswa')" class="gap-2">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Button>
            </div>
            <div class="flex justify-center">
                <Card class="max-w-4xl w-full">
                    <CardContent class="p-8">
                        <div class="flex flex-col items-center space-y-4 mb-8">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary/20 to-primary/10 flex items-center justify-center border-4 border-primary/20">
                                    <User class="w-12 h-12 text-primary" />
                                </div>
                            </div>
                            <div class="text-center">
                                <h2 class="text-2xl font-bold text-foreground">{{ form.name || 'Nama Mahasiswa' }}</h2>
                                <p class="text-muted-foreground">{{ form.email || 'email@mahasiswa.com' }}</p>
                            </div>
                        </div>
                        <Form @submit="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField name="name">
                                    <FormItem>
                                        <FormLabel>Nama Lengkap</FormLabel>
                                        <FormControl>
                                            <div class="relative">
                                                <User class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                                <Input
                                                    v-model="form.name"
                                                    placeholder="Masukkan nama lengkap mahasiswa"
                                                    class="pl-10"
                                                    :class="{ 'border-destructive': form.errors.name }"
                                                    required
                                                />
                                            </div>
                                        </FormControl>
                                        <FormMessage v-if="form.errors.name">{{ form.errors.name }}</FormMessage>
                                    </FormItem>
                                </FormField>
                                <FormField name="email">
                                    <FormItem>
                                        <FormLabel>Email Address</FormLabel>
                                        <FormControl>
                                            <div class="relative">
                                                <Mail class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                                <Input
                                                    v-model="form.email"
                                                    type="email"
                                                    placeholder="contoh@email.com"
                                                    class="pl-10"
                                                    :class="{ 'border-destructive': form.errors.email }"
                                                    required
                                                />
                                            </div>
                                        </FormControl>
                                        <FormMessage v-if="form.errors.email">{{ form.errors.email }}</FormMessage>
                                    </FormItem>
                                </FormField>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField name="nim">
                                    <FormItem>
                                        <FormLabel>NIM</FormLabel>
                                        <FormControl>
                                            <div class="relative">
                                                <IdCard class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                                <Input
                                                    v-model="form.nim"
                                                    placeholder="Masukkan NIM"
                                                    class="pl-10"
                                                    :class="{ 'border-destructive': form.errors.nim }"
                                                    required
                                                />
                                            </div>
                                        </FormControl>
                                        <FormMessage v-if="form.errors.nim">{{ form.errors.nim }}</FormMessage>
                                    </FormItem>
                                </FormField>
                                <FormField name="angkatan">
                                    <FormItem>
                                        <FormLabel>Angkatan</FormLabel>
                                        <FormControl>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger as-child>
                                                    <button
                                                        type="button"
                                                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                        :class="{ 'border-destructive': form.errors.angkatan }">
                                                        <div class="flex items-center gap-2">
                                                            <GraduationCap class="h-4 w-4 text-muted-foreground" />
                                                            <span>{{ form.angkatan || 'Pilih Angkatan' }}</span>
                                                        </div>
                                                        <ChevronDown class="h-4 w-4 text-muted-foreground" />
                                                    </button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent class="w-[var(--radix-dropdown-menu-trigger-width)] min-w-[400px]">
                                                    <DropdownMenuItem
                                                        v-for="angkatan in props.angkatan"
                                                        :key="angkatan"
                                                        @click="form.angkatan = angkatan"
                                                        class="cursor-pointer">
                                                        {{ angkatan }}
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </FormControl>
                                        <FormMessage v-if="form.errors.angkatan">{{ form.errors.angkatan }}</FormMessage>
                                    </FormItem>
                                </FormField>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField name="gender">
                                    <FormItem>
                                        <FormLabel>Gender</FormLabel>
                                        <FormControl>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger as-child>
                                                    <button
                                                        type="button"
                                                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                        :class="{ 'border-destructive': form.errors.gender }">
                                                        <div class="flex items-center gap-2">
                                                            <Users class="h-4 w-4 text-muted-foreground" />
                                                            <span>{{ form.gender === 'L' ? 'Laki-laki' : form.gender === 'P' ? 'Perempuan' : 'Pilih Gender' }}</span>
                                                        </div>
                                                        <ChevronDown class="h-4 w-4 text-muted-foreground" />
                                                    </button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent class="w-[var(--radix-dropdown-menu-trigger-width)] min-w-[400px]">
                                                    <DropdownMenuItem
                                                        @click="form.gender = 'L'"
                                                        class="cursor-pointer">
                                                        Laki-laki
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem
                                                        @click="form.gender = 'P'"
                                                        class="cursor-pointer">
                                                        Perempuan
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </FormControl>
                                        <FormMessage v-if="form.errors.gender">{{ form.errors.gender }}</FormMessage>
                                    </FormItem>
                                </FormField>
                                <div></div>
                            </div>
                            <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                                <Button type="button" variant="outline" @click="router.get('/admin/mahasiswa')" :disabled="form.processing">
                                    Batal
                                </Button>
                                <Button type="submit" :disabled="form.processing" class="gap-2">
                                    <Save class="h-4 w-4" />
                                    {{ form.processing ? 'Menyimpan...' : 'Update Mahasiswa' }}
                                </Button>
                            </div>
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
