<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Switch } from '@/components/ui/switch';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Label } from '@/components/ui/label';
import { User, Mail, UserCheck, ArrowLeft, Save, CheckCircle, XCircle, X, IdCard, RefreshCw, ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';

interface Role {
    id: number;
    nama_role: string;
    deskripsi: string;
}

interface UserData {
    id: number;
    name: string;
    email: string;
    nomor_induk: string;
    role: string;
    is_active: boolean;
}

interface Props {
    user: UserData;
    roles: Role[];
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    nomor_induk: props.user.nomor_induk || '',
    role: props.user.role,
    is_active: Boolean(props.user.is_active),
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
    form.put(`/admin/user/${props.user.id}`, {
        onSuccess: () => {
            showSuccessAlert('User Berhasil Diperbarui', 'Informasi user telah diperbarui.');
        },
        onError: (errors) => {
            showErrorAlert('Gagal Memperbarui User', 'Silakan periksa form dan coba lagi.');
        },
    });
};

const resetPassword = () => {
    router.put(`/admin/user/${props.user.id}/reset-password`, {}, {
        onSuccess: () => {
            setTimeout(() => {
                router.get('/admin/user');
            }, 500);
        },
        onError: () => {
            showErrorAlert('Gagal Reset Password', 'Silakan coba lagi.');
        },
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Manajemen User', href: '/admin/user' },
    { title: 'Edit User', href: `/admin/user/${props.user.id}/edit` },
];
</script>

<template>
    <Head title="Edit User" />
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
                    <h1 class="text-3xl font-bold tracking-tight">Edit User</h1>
                    <p class="text-muted-foreground mt-1">Update user information and settings</p>
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
                            <h2 class="text-2xl font-bold text-foreground">{{ form.name || 'User Name' }}</h2>
                            <p class="text-muted-foreground">{{ form.email || 'user@email.com' }}</p>
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

                            <!-- Nomor Induk and Role - Two Column Layout -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                                <DropdownMenuContent class="w-[var(--radix-dropdown-menu-trigger-width)] max-w-[800px]">
                                                    <DropdownMenuItem
                                                        v-for="role in props.roles"
                                                        :key="role.id"
                                                        @click="form.role = role.nama_role"
                                                        class="cursor-pointer"
                                                    >
                                                        {{ role.nama_role }}
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </FormControl>
                                        <FormMessage v-if="form.errors.role">{{ form.errors.role }}</FormMessage>
                                    </FormItem>
                                </FormField>
                            </div>

                            <!-- Status and Reset Password - Two Column Layout -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Status Toggle -->
                                <FormField name="is_active">
                                    <FormItem>
                                        <FormLabel>Status Aktif</FormLabel>
                                        <FormControl>
                                            <div class="flex items-center h-10 space-x-3">
                                                <Switch
                                                    v-model="form.is_active"
                                                    :checked="form.is_active"
                                                    @update:checked="(val) => form.is_active = val"
                                                    :class="form.is_active ? 'bg-success' : ''"
                                                />
                                                <span class="text-sm font-medium" :class="form.is_active ? 'text-success' : 'text-muted-foreground'">
                                                    {{ form.is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                </span>
                                            </div>
                                        </FormControl>
                                    </FormItem>
                                </FormField>

                                <!-- Password Reset Section -->
                                <div class="space-y-2">
                                    <Label>Reset Password</Label>
                                    <div class="flex items-center h-10">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            class="gap-2"
                                            @click="resetPassword"
                                        >
                                            <RefreshCw class="h-4 w-4" />
                                            Reset Password
                                        </Button>
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
                                {{ form.processing ? 'Menyimpan...' : 'Update User' }}
                            </Button>
                        </div>
                    </Form>
                </CardContent>
            </Card>
            </div>
        </div>
    </AppLayout>
</template>
