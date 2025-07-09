<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { User, Mail, Lock, UserCheck, ArrowLeft, Save, CheckCircle, XCircle, X, IdCard, ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';

interface Role {
    id: number;
    nama_role: string;
    deskripsi: string;
}

interface Props {
    roles: Role[];
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    email: '',
    nomor_induk: '',
    role: '',
});

// Alert states
const showAlert = ref(false);
const alertType = ref<'success' | 'error'>('success');
const alertTitle = ref('');
const alertDescription = ref('');

// Alert functions
const showSuccessAlert = (title: string, description: string) => {
    alertType.value = 'success';
    alertTitle.value = title;
    alertDescription.value = description;
    showAlert.value = true;
    setTimeout(() => {
        showAlert.value = false;
    }, 5000);
};

const showErrorAlert = (title: string, description: string) => {
    alertType.value = 'error';
    alertTitle.value = title;
    alertDescription.value = description;
    showAlert.value = true;
    setTimeout(() => {
        showAlert.value = false;
    }, 5000);
};

const closeAlert = () => {
    showAlert.value = false;
};

const submit = () => {
    form.post('/admin/user', {
        onSuccess: () => {
            showSuccessAlert('User Berhasil Dibuat', 'User baru telah ditambahkan ke sistem.');
            setTimeout(() => {
                router.get('/admin/user');
            }, 2000);
        },
        onError: (errors) => {
            showErrorAlert('Gagal Membuat User', 'Silakan periksa form dan coba lagi.');
        },
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Manajemen Users', href: '/admin/user' },
    { title: 'Tambah User', href: '/admin/user/create' },
];
</script>

<template>
    <Head title="Tambah User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Alert Notification -->
            <Transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="opacity-0 transform translate-y-2"
                enter-to-class="opacity-100 transform translate-y-0"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100 transform translate-y-0"
                leave-to-class="opacity-0 transform translate-y-2"
            >
                <Alert v-if="showAlert" :variant="alertType === 'error' ? 'destructive' : 'default'" :class="[
                    'mb-4',
                    alertType === 'success' ? 'text-success bg-card [&>svg]:text-current *:data-[slot=alert-description]:text-success/90' : ''
                ]">
                    <CheckCircle v-if="alertType === 'success'" class="h-4 w-4" />
                    <XCircle v-if="alertType === 'error'" class="h-4 w-4" />
                    <AlertTitle>{{ alertTitle }}</AlertTitle>
                    <AlertDescription>{{ alertDescription }}</AlertDescription>
                    <Button variant="ghost" size="sm" @click="closeAlert" class="absolute top-2 right-2 h-6 w-6 p-0">
                        <X class="h-3 w-3" />
                    </Button>
                </Alert>
            </Transition>

            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Tambah User Baru</h1>
                    <p class="text-muted-foreground mt-1">Buat akun pengguna baru untuk sistem portal</p>
                </div>
                <Button variant="outline" @click="router.get('/admin/user')" class="gap-2">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Button>
            </div>

            <!-- Form Card -->
            <div class="flex justify-center">
                <Card class="max-w-4xl w-full">
                    <CardContent class="p-8">
                        <!-- Avatar and Name Section -->
                        <div class="flex flex-col items-center space-y-4 mb-8">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary/20 to-primary/10 flex items-center justify-center border-4 border-primary/20">
                                    <User class="w-12 h-12 text-primary" />
                                </div>
                            </div>
                            <div class="text-center">
                                <h2 class="text-2xl font-bold text-foreground">User Baru</h2>
                                <p class="text-muted-foreground">Buat akun pengguna baru</p>
                            </div>
                        </div>

                        <Form @submit="submit" class="space-y-6">
                                <!-- Two Column Layout for Name and Email -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name Field -->
                                    <FormField name="name">
                                        <FormItem>
                                            <FormLabel>Nama Lengkap</FormLabel>
                                            <FormControl>
                                                <div class="relative">
                                                    <User class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                                    <Input
                                                        v-model="form.name"
                                                        placeholder="Masukkan nama lengkap"
                                                        class="pl-10"
                                                        :class="{ 'border-destructive': form.errors.name }"
                                                        required
                                                    />
                                                </div>
                                            </FormControl>
                                            <FormMessage v-if="form.errors.name">{{ form.errors.name }}</FormMessage>
                                        </FormItem>
                                    </FormField>

                                    <!-- Email Field -->
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

                                <!-- Nomor Induk Field -->
                                <FormField name="nomor_induk">
                                    <FormItem>
                                        <FormLabel>Nomor Induk</FormLabel>
                                        <FormControl>
                                            <div class="relative">
                                                <IdCard class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                                <Input
                                                    v-model="form.nomor_induk"
                                                    placeholder="Masukkan nomor induk"
                                                    class="pl-10"
                                                    :class="{ 'border-destructive': form.errors.nomor_induk }"
                                                    required
                                                />
                                            </div>
                                        </FormControl>
                                        <FormMessage v-if="form.errors.nomor_induk">{{ form.errors.nomor_induk }}</FormMessage>
                                    </FormItem>
                                </FormField>

                                <!-- Role Selection -->
                                <FormField name="role">
                                    <FormItem>
                                        <FormLabel>Pilih Role</FormLabel>
                                        <FormControl>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger as-child>
                                                    <button
                                                        type="button"
                                                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                        :class="{ 'border-destructive': form.errors.role }"
                                                    >
                                                        <div class="flex items-center gap-2">
                                                            <UserCheck class="h-4 w-4 text-muted-foreground" />
                                                            <span>{{ form.role || 'Pilih Role' }}</span>
                                                        </div>
                                                        <ChevronDown class="h-4 w-4 text-muted-foreground" />
                                                    </button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent class="w-[var(--radix-dropdown-menu-trigger-width)] min-w-[800px]">
                                                    <DropdownMenuItem
                                                        v-for="role in props.roles"
                                                        :key="role.id"
                                                        @click="form.role = role.nama_role"
                                                        class="cursor-pointer w-full justify-start px-3 py-2"
                                                    >
                                                        <span class="w-full text-left">{{ role.nama_role }}</span>
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </FormControl>
                                        <FormMessage v-if="form.errors.role">{{ form.errors.role }}</FormMessage>
                                    </FormItem>
                                </FormField>

                                <!-- Password Info -->
                                <div class="bg-primary/10 border border-primary/20 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <Lock class="h-5 w-5 text-primary mt-0.5" />
                                        <div>
                                            <h4 class="text-sm font-medium text-primary">Password Otomatis</h4>
                                            <p class="text-sm text-primary/80 mt-1">
                                                Password akan otomatis diset sama dengan <strong>nomor induk</strong> yang dimasukkan.
                                                User harus mengganti password saat login pertama kali.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        @click="router.get('/admin/user')"
                                        :disabled="form.processing"
                                    >
                                        Batal
                                    </Button>
                                    <Button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="gap-2"
                                    >
                                        <Save class="h-4 w-4" />
                                        {{ form.processing ? 'Menyimpan...' : 'Simpan User' }}
                                    </Button>
                                </div>
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
