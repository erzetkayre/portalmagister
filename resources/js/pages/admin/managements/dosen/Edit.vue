<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { User, Mail, ArrowLeft, Save, IdCard, GraduationCap, Hash, BookOpen, Users, ChevronDown } from 'lucide-vue-next';

interface DosenData {
    id: number;
    name: string;
    email: string;
    nip: string;
    kode_dosen: string;
    bidang_keahlian: string;
    gender: string;
}

interface Props {
    dosen: DosenData;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.dosen.name,
    email: props.dosen.email,
    nip: props.dosen.nip || '',
    kode_dosen: props.dosen.kode_dosen || '',
    bidang_keahlian: props.dosen.bidang_keahlian || '',
    gender: props.dosen.gender || 'L',
});

const submit = () => {
    form.put(`/admin/dosen/${props.dosen.id}`);
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Manajemen Dosen', href: '/admin/dosen' },
    { title: 'Edit Dosen', href: `/admin/dosen/${props.dosen.id}/edit` },
];
</script>

<template>
    <Head title="Edit Dosen" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Edit Dosen</h1>
                    <p class="text-muted-foreground mt-1">Update informasi dosen</p>
                </div>
                <Button variant="outline" @click="router.get('/admin/dosen')" class="gap-2">
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
                                    <GraduationCap class="w-12 h-12 text-primary" />
                                </div>
                            </div>
                            <div class="text-center">
                                <h2 class="text-2xl font-bold text-foreground">{{ form.name || 'Nama Dosen' }}</h2>
                                <p class="text-muted-foreground">{{ form.email || 'email@dosen.com' }}</p>
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
                                                    placeholder="Masukkan nama lengkap dosen"
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
                                <FormField name="nip">
                                    <FormItem>
                                        <FormLabel>NIP</FormLabel>
                                        <FormControl>
                                            <div class="relative">
                                                <IdCard class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                                <Input
                                                    v-model="form.nip"
                                                    placeholder="Masukkan NIP"
                                                    class="pl-10"
                                                    :class="{ 'border-destructive': form.errors.nip }"
                                                    required
                                                />
                                            </div>
                                        </FormControl>
                                        <FormMessage v-if="form.errors.nip">{{ form.errors.nip }}</FormMessage>
                                    </FormItem>
                                </FormField>
                                <FormField name="kode_dosen">
                                    <FormItem>
                                        <FormLabel>Kode Dosen</FormLabel>
                                        <FormControl>
                                            <div class="relative">
                                                <Hash class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                                <Input
                                                    v-model="form.kode_dosen"
                                                    placeholder="Masukkan kode dosen"
                                                    class="pl-10"
                                                    :class="{ 'border-destructive': form.errors.kode_dosen }"
                                                    required
                                                />
                                            </div>
                                        </FormControl>
                                        <FormMessage v-if="form.errors.kode_dosen">{{ form.errors.kode_dosen }}</FormMessage>
                                    </FormItem>
                                </FormField>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField name="bidang_keahlian">
                                    <FormItem>
                                        <FormLabel>Bidang Keahlian</FormLabel>
                                        <FormControl>
                                            <div class="relative">
                                                <BookOpen class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                                <Input
                                                    v-model="form.bidang_keahlian"
                                                    placeholder="Masukkan bidang keahlian"
                                                    class="pl-10"
                                                    :class="{ 'border-destructive': form.errors.bidang_keahlian }"
                                                    required
                                                />
                                            </div>
                                        </FormControl>
                                        <FormMessage v-if="form.errors.bidang_keahlian">{{ form.errors.bidang_keahlian }}</FormMessage>
                                    </FormItem>
                                </FormField>
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
                            </div>
                            <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                                <Button type="button" variant="outline" @click="router.get('/admin/dosen')" :disabled="form.processing">
                                    Batal
                                </Button>
                                <Button type="submit" :disabled="form.processing" class="gap-2">
                                    <Save class="h-4 w-4" />
                                    {{ form.processing ? 'Menyimpan...' : 'Update Dosen' }}
                                </Button>
                            </div>
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
