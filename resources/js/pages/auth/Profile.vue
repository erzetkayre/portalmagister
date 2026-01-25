<script setup lang="ts">
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { Home, Camera, Trash2, Upload } from 'lucide-vue-next';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import { Button, Input, Label, Card, CardContent, CardDescription, CardHeader, CardTitle,
    Avatar, AvatarFallback, AvatarImage, AlertNotification, DropdownMenu, DropdownMenuContent,
    DropdownMenuItem, DropdownMenuTrigger
} from '@/components/ui';

import { useAlert } from '@/composables/UseAlert';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type User } from '@/types';

// Interface
interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

// States
defineProps<Props>();
const page = usePage();
const user = page.props.auth.user as User;
const dashboardRoute = computed(() => {
    const user = page.props.auth?.user as any;
    if (!user) return '/login';

    const role = user.role?.nama_role;
    // console.log(user);

    return route(`${role}.dashboard`);
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Pengaturan', href: '/pengaturan/profil',},
];

// Composables
const { showAlert, alertType, alertTitle, alertDescription, showSuccessAlert, showErrorAlert, closeAlert } = useAlert();

onMounted(() => {
    if (page.props.flash?.message) {
        const msg = page.props.flash.message;
        if (msg.type === 'success') {
            showSuccessAlert(msg.title, msg.message);
        } else if (msg.type === 'error') {
            showErrorAlert(msg.title, msg.message);
        }
    }
});

// Photo handling
const photoInput = ref<HTMLInputElement>();
const triggerFileInput = () => {
    photoInput.value?.click();
};
const handlePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        profileForm.photo = target.files[0];
    }
};
const getAvatarUrl = () => {
    if (profileForm.photo) {
        return URL.createObjectURL(profileForm.photo);
    }
    return user.photo ? route('profile.photo.current') : null;
};
const getInitials = (name: string) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
};
const deletePhoto = () => {
    router.delete(route('profile.photo.delete'), {
        preserveScroll: true,
        onSuccess: () => {
            profileForm.photo = null;
            showSuccessAlert('Foto Berhasil Dihapus', 'Foto profile Anda telah dihapus.');
            router.reload({ only: ['auth'] });
        },
        onError: () => {
            showErrorAlert('Gagal Menghapus Foto', 'Terjadi kesalahan saat menghapus foto. Silakan coba lagi.');
        }
    });
};

// Gender handling
const getGenderText = (value: string) => {
    switch (value) {
        case 'L': return 'Laki-laki';
        case 'P': return 'Perempuan';
        default: return 'Pilih gender';
    }
};
const handleGenderSelect = (value: string) => {
    profileForm.gender = value;
};

// Forms
const profileForm = useForm({
    nama: user.name || '',
    email: user.email || '',
    nomor_induk: user.nomor_induk || '',
    phone: user.phone || '',
    gender: user.role_data?.gender || '',
    photo: null as File | null,
});

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});
const submitProfile = () => {
    profileForm.post(route('profile.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            showSuccessAlert('Profile Berhasil Diperbarui', 'Informasi profile Anda telah disimpan.');
            router.reload({ only: ['auth'] });
        },
        onError: () => {
            showErrorAlert('Gagal Memperbarui Profile', 'Terjadi kesalahan saat menyimpan profile. Silakan coba lagi.');
        }
    });
};

const submitPassword = () => {
    passwordForm.post(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            showSuccessAlert('Password Berhasil Diperbarui', 'Password Anda telah berhasil diubah.');
        },
        onError: (errors: any) => {
            if (errors.password) {
                passwordForm.reset('password', 'password_confirmation');
                if (passwordInput.value instanceof HTMLInputElement) {
                    passwordInput.value.focus();
                }
            }
            if (errors.current_password) {
                passwordForm.reset('current_password');
                if (currentPasswordInput.value instanceof HTMLInputElement) {
                    currentPasswordInput.value.focus();
                }
            }
            showErrorAlert('Gagal Memperbarui Password', 'Terjadi kesalahan saat mengubah password. Periksa kembali password lama Anda.');
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />
        <div class="flex flex-1 flex-col rounded-xl p-4">
            <AlertNotification :show="showAlert" :type="alertType" :title="alertTitle" :description="alertDescription" @close="closeAlert"/>
            <div class="space-y-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Pengaturan Profil</h1>
                        <p class="text-muted-foreground mt-1">Perbarui informasi profil, kata sandi, dan pengaturan tampilan Anda</p>
                    </div>
                    <Button @click="router.get(dashboardRoute)" class="gap-2">
                        <Home class="h-4 w-4" />
                        Dashboard
                    </Button>
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column - Profile Information -->
                    <div class="flex flex-col">
                        <Card class="flex-1">
                            <CardHeader>
                                <CardTitle>Informasi Profil</CardTitle>
                                <CardDescription>Perbarui informasi profil dan detail pribadi Anda</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <form @submit.prevent="submitProfile" class="space-y-6">
                                    <!-- Avatar Section -->
                                    <div class="flex flex-col items-center space-y-4">
                                        <div class="relative">
                                            <Avatar class="w-24 h-24">
                                                <AvatarImage :src="getAvatarUrl()" />
                                                <AvatarFallback class="text-lg">{{ getInitials(user.nama) }}</AvatarFallback>
                                            </Avatar>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger asChild>
                                                    <Button size="sm" class="absolute bottom-0 right-0 bg-primary text-primary-foreground rounded-full p-2 hover:bg-primary/90 transition-colors">
                                                        <Camera class="w-4 h-4" />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="end">
                                                    <DropdownMenuItem @click="triggerFileInput" class="cursor-pointer">
                                                        <Upload class="w-4 h-4 mr-2" />
                                                        Upload Photo
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem
                                                        v-if="user.photo"
                                                        @click="deletePhoto"
                                                        class="cursor-pointer text-red-600 hover:text-red-700"
                                                    >
                                                        <Trash2 class="w-4 h-4 mr-2" />
                                                        Delete Photo
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                            <input
                                                ref="photoInput"
                                                type="file"
                                                class="hidden"
                                                @change="handlePhotoChange"
                                                accept="image/*"
                                            />
                                        </div>
                                        <InputError :message="profileForm.errors.photo" />
                                    </div>

                                    <!-- Profile Fields -->
                                    <div class="grid gap-2">
                                        <Label for="nama">Nama Lengkap</Label>
                                        <Input
                                            id="nama"
                                            v-model="profileForm.nama"
                                            required
                                            autocomplete="username"
                                            placeholder="Username"
                                        />
                                        <InputError :message="profileForm.errors.nama" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="email">AlamatEmail</Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            v-model="profileForm.email"
                                            required
                                            autocomplete="email"
                                            placeholder="Email address"
                                        />
                                        <InputError :message="profileForm.errors.email" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="nomor_induk">Nomor Induk</Label>
                                        <Input
                                            id="nomor_induk"
                                            v-model="profileForm.nomor_induk"
                                            required
                                            placeholder="NIP/NIM"
                                        />
                                        <InputError :message="profileForm.errors.nomor_induk" />
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="grid gap-2">
                                            <Label for="phone">Nomor Telepon</Label>
                                            <Input
                                                id="phone"
                                                type="tel"
                                                v-model="profileForm.phone"
                                                placeholder="WhatsApp number"
                                            />
                                            <InputError :message="profileForm.errors.phone" />
                                        </div>

                                        <div class="grid gap-2">
                                            <Label for="gender">Gender</Label>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger asChild>
                                                    <Button variant="outline" class="w-full justify-between">
                                                        {{ getGenderText(profileForm.gender) }}
                                                        <svg class="w-4 h-4 opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="m6 9 6 6 6-6"/>
                                                        </svg>
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent class="w-full">
                                                    <DropdownMenuItem @click="handleGenderSelect('L')">
                                                        Laki-laki
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem @click="handleGenderSelect('P')">
                                                        Perempuan
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                            <InputError :message="profileForm.errors.gender" />
                                        </div>
                                    </div>

                                    <!-- Email Verification Notice -->
                                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                                        <p class="-mt-4 text-sm text-muted-foreground">
                                            Your email address is unverified.
                                            <Link
                                                :href="route('verification.send')"
                                                method="post"
                                                as="button"
                                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                            >
                                                Click here to resend the verification email.
                                            </Link>
                                        </p>

                                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                                            A new verification link has been sent to your email address.
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <Button :disabled="profileForm.processing">Update Profile</Button>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Right Column - Appearance & Password -->
                    <div class="space-y-8 flex flex-col">
                        <Card class="flex-1">
                            <CardHeader>
                                <CardTitle>Pengaturan Tampilan</CardTitle>
                                <CardDescription>Perbarui pengaturan tampilan akun Anda</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <AppearanceTabs />
                            </CardContent>
                        </Card>

                        <Card class="flex-1">
                            <CardHeader>
                                <CardTitle>Perbarui Password</CardTitle>
                                <CardDescription>Pastikan akun Anda menggunakan password yang panjang dan acak untuk menjaga keamanan</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <form @submit.prevent="submitPassword" class="space-y-6">
                                    <div class="grid gap-2">
                                        <Label for="current_password">Password saat ini</Label>
                                        <Input
                                            id="current_password"
                                            ref="currentPasswordInput"
                                            v-model="passwordForm.current_password"
                                            type="password"
                                            autocomplete="current-password"
                                            placeholder="Password saat ini"
                                        />
                                        <InputError :message="passwordForm.errors.current_password" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="password">Password baru</Label>
                                        <Input
                                            id="password"
                                            ref="passwordInput"
                                            v-model="passwordForm.password"
                                            type="password"
                                            autocomplete="new-password"
                                            placeholder="Password baru"
                                        />
                                        <InputError :message="passwordForm.errors.password" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="password_confirmation">Konfirmasi password</Label>
                                        <Input
                                            id="password_confirmation"
                                            v-model="passwordForm.password_confirmation"
                                            type="password"
                                            autocomplete="new-password"
                                            placeholder="Konfirmasi password"
                                        />
                                        <InputError :message="passwordForm.errors.password_confirmation" />
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <Button :disabled="passwordForm.processing">Update Password</Button>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
