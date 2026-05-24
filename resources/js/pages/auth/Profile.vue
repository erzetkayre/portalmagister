<script setup lang="ts">
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Camera, Trash2, Upload, PenLine, UploadCloud, X, ImageIcon, ShieldCheck, Palette, User } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import { Button, Input, Label, Card, CardContent, Separator,
    Avatar, AvatarFallback, AvatarImage,
    DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger
} from '@/components/ui';

import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Props {
    gender?: string | null;
    signature_url?: string | null;
}

const props = defineProps<Props>();
const page  = usePage();
const user  = computed(() => page.props.auth.user as any);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Pengaturan Profil', href: '/pengaturan/profil' },
];

// Photo
const photoInput = ref<HTMLInputElement>();

const handlePhotoChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) profileForm.photo = file;
};
const avatarUrl = computed((): string | undefined => {
    if (profileForm.photo) return URL.createObjectURL(profileForm.photo as File);
    return user.value.photo ?? undefined;
});
const getInitials = (name: string) =>
    name?.split(' ').map((n: string) => n[0]).join('').toUpperCase() ?? '';

const deletePhoto = () => {
    if (profileForm.photo) {
        profileForm.photo = null;
        if (photoInput.value) photoInput.value.value = '';
        return;
    }
    router.delete(route('profile.photo.delete'), {
        preserveScroll: true,
        onSuccess: () => router.reload({ only: ['auth'] }),
        onError:   () => toast.error('Gagal menghapus foto profil.'),
    });
};

// Gender
const getGenderText = (v: string) =>
    v === 'L' ? 'Laki-laki' : v === 'P' ? 'Perempuan' : 'Pilih gender';

// Signature
const signatureFile     = ref<File | null>(null);
const isDragging        = ref(false);
const sigInputRef       = ref<HTMLInputElement | null>(null);
const savedSignatureUrl = ref<string | null>(props.signature_url ?? null);

const signaturePreview = computed(() =>
    signatureFile.value ? URL.createObjectURL(signatureFile.value) : null
);

const formatFileSize = (bytes: number) => {
    if (bytes < 1024)        return `${bytes} B`;
    if (bytes < 1048576)     return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / 1048576).toFixed(1)} MB`;
};

const handleSigFileChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) signatureFile.value = file;
};
const handleSigDrop = (e: DragEvent) => {
    isDragging.value = false;
    const file = e.dataTransfer?.files[0];
    if (file?.type === 'image/png') signatureFile.value = file;
};
const clearSigFile = () => {
    signatureFile.value = null;
    if (sigInputRef.value) sigInputRef.value.value = '';
};

const signatureForm = useForm({ signature: null as File | null });

const submitSignature = () => {
    if (!signatureFile.value) return;
    signatureForm.signature = signatureFile.value;
    signatureForm.post(route('signature.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => { savedSignatureUrl.value = signaturePreview.value; clearSigFile(); },
        onError:   () => toast.error('Gagal menyimpan tanda tangan.', { description: 'File harus PNG, maks. 2MB.' }),
    });
};
const deleteSignature = () => {
    router.delete(route('signature.delete'), {
        preserveScroll: true,
        onSuccess: () => { savedSignatureUrl.value = null; },
        onError:   () => toast.error('Gagal menghapus tanda tangan.'),
    });
};

// Profile form
const profileForm = useForm({
    nama:        user.value.name        || '',
    email:       user.value.email       || '',
    nomor_induk: user.value.nomor_induk || '',
    phone:       user.value.phone       || '',
    gender:      props.gender           || '',
    photo:       null as File | null,
});

const initial = {
    nama:        user.value.name        || '',
    email:       user.value.email       || '',
    nomor_induk: user.value.nomor_induk || '',
    phone:       user.value.phone       || '',
    gender:      props.gender           || '',
};

const hasChanges = computed(() =>
    profileForm.nama        !== initial.nama        ||
    profileForm.email       !== initial.email       ||
    profileForm.nomor_induk !== initial.nomor_induk ||
    profileForm.phone       !== initial.phone       ||
    profileForm.gender      !== initial.gender      ||
    profileForm.photo       !== null
);

const submitProfile = () => {
    profileForm.post(route('profile.update'), {
        preserveScroll: true,
        forceFormData:  true,
        onSuccess: () => { profileForm.photo = null; router.reload({ only: ['auth'] }); },
        onError: (errors: any) => {
            if (errors.photo) {
                toast.error('Foto gagal diupload.', { description: errors.photo });
            } else {
                toast.error('Gagal memperbarui profil.', { description: 'Periksa kembali data yang diisi.' });
            }
        },
    });
};

// Password form
const passwordForm = useForm({
    current_password:      '',
    password:              '',
    password_confirmation: '',
});

const hasPasswordData = computed(() =>
    !!passwordForm.current_password &&
    !!passwordForm.password &&
    !!passwordForm.password_confirmation
);

const submitPassword = () => {
    passwordForm.post(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => toast.error('Gagal memperbarui password.', { description: 'Periksa kembali data yang diisi.' }),
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Pengaturan Profil" />
        <div class="flex flex-1 flex-col p-4 md:p-6 max-w-4xl mx-auto w-full gap-0">
            <!-- Section: Identity Header -->
            <div class="flex flex-col items-center gap-3">
                <div class="relative">
                    <Avatar class="h-28 w-28 ring-2 ring-border ring-offset-2 ring-offset-background">
                        <AvatarImage :src="avatarUrl" class="object-cover" />
                        <AvatarFallback class="text-3xl font-bold">
                            {{ getInitials(user.name) }}
                        </AvatarFallback>
                    </Avatar>
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <button class="absolute -bottom-1 -right-1 flex h-8 w-8 items-center justify-center rounded-full bg-primary text-primary-foreground shadow-md ring-2 ring-background hover:bg-primary/90 transition-colors">
                                <Camera class="h-4 w-4" />
                            </button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-40">
                            <DropdownMenuItem @click="photoInput?.click()" class="cursor-pointer gap-2">
                                <Upload class="h-4 w-4" /> Ganti Foto
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                v-if="user.photo || profileForm.photo"
                                @click="deletePhoto"
                                class="cursor-pointer gap-2 hover:bg-destructive/10 hover:text-destructive focus:bg-destructive/10 focus:text-destructive">
                                <Trash2 class="h-4 w-4" /> Hapus Foto
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <input ref="photoInput" type="file" class="hidden" @change="handlePhotoChange" accept="image/*" />
                </div>
                <div class="text-center space-y-1">
                    <h1 class="text-xl font-bold">{{ user.name }}</h1>
                    <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                    <p class="text-xs text-muted-foreground">{{ user.study_program }} · {{ user.nomor_induk }}</p>
                </div>
            </div>

            <!-- Section: Informasi Pribadi -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 py-8">
                <div class="lg:col-span-1 space-y-1">
                    <div class="flex items-center gap-2 text-sm font-semibold">
                        <User class="h-4 w-4" />
                        Informasi Pribadi
                    </div>
                    <p class="text-xs text-muted-foreground leading-relaxed">
                        Nama, email, dan data kontak yang ditampilkan dalam sistem.
                    </p>
                </div>
                <Card class="lg:col-span-2">
                    <CardContent>
                        <form @submit.prevent="submitProfile" class="space-y-5">
                            <div class="grid gap-1.5">
                                <Label for="nama">Nama Lengkap</Label>
                                <Input id="nama" v-model="profileForm.nama" autocomplete="name" placeholder="Nama lengkap" />
                                <InputError :message="profileForm.errors.nama" />
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="email">Alamat Email</Label>
                                <Input id="email" type="email" v-model="profileForm.email" autocomplete="email" placeholder="email@domain.com" />
                                <InputError :message="profileForm.errors.email" />
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="nomor_induk">Nomor Induk (NIP/NIM)</Label>
                                <Input id="nomor_induk" v-model="profileForm.nomor_induk" placeholder="NIP atau NIM" />
                                <InputError :message="profileForm.errors.nomor_induk" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-1.5">
                                    <Label for="phone">Nomor Telepon</Label>
                                    <Input id="phone" type="tel" v-model="profileForm.phone" placeholder="08xx-xxxx-xxxx" />
                                    <InputError :message="profileForm.errors.phone" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label>Gender</Label>
                                    <DropdownMenu>
                                        <DropdownMenuTrigger asChild>
                                            <Button variant="outline" class="w-full justify-between font-normal">
                                                <span :class="profileForm.gender ? '' : 'text-muted-foreground'">
                                                    {{ getGenderText(profileForm.gender) }}
                                                </span>
                                                <svg class="w-4 h-4 opacity-40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent class="w-[--radix-dropdown-menu-trigger-width]">
                                            <DropdownMenuItem @click="profileForm.gender = 'L'">Laki-laki</DropdownMenuItem>
                                            <DropdownMenuItem @click="profileForm.gender = 'P'">Perempuan</DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                    <InputError :message="profileForm.errors.gender" />
                                </div>
                            </div>

                            <div class="flex justify-end pt-1">
                                <Button type="submit" :disabled="profileForm.processing || !hasChanges" class="min-w-28">
                                    {{ profileForm.processing ? 'Menyimpan…' : 'Simpan' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
            <Separator />

            <!-- Section: Password -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 py-8">
                <div class="lg:col-span-1 space-y-1">
                    <div class="flex items-center gap-2 text-sm font-semibold">
                        <ShieldCheck class="h-4 w-4" />
                        Security
                    </div>
                    <p class="text-xs text-muted-foreground leading-relaxed">
                        Perbarui password secara berkala untuk menjaga keamanan akun Anda.
                    </p>
                </div>
                <Card class="lg:col-span-2">
                    <CardContent>
                        <form @submit.prevent="submitPassword" class="space-y-5">
                            <div class="grid gap-1.5">
                                <Label for="current_password">Password Saat Ini</Label>
                                <Input id="current_password" v-model="passwordForm.current_password" type="password" autocomplete="current-password" placeholder="••••••••" />
                                <InputError :message="passwordForm.errors.current_password" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-1.5">
                                    <Label for="password">Password Baru</Label>
                                    <Input id="password" v-model="passwordForm.password" type="password" autocomplete="new-password" placeholder="••••••••" />
                                    <InputError :message="passwordForm.errors.password" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label for="password_confirmation">Konfirmasi</Label>
                                    <Input id="password_confirmation" v-model="passwordForm.password_confirmation" type="password" autocomplete="new-password" placeholder="••••••••" />
                                    <InputError :message="passwordForm.errors.password_confirmation" />
                                </div>
                            </div>
                            <div class="flex justify-end pt-1">
                                <Button type="submit" :disabled="passwordForm.processing || !hasPasswordData" class="min-w-28">
                                    {{ passwordForm.processing ? 'Menyimpan…' : 'Ubah Password' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
            <Separator />

            <!-- Section: Tampilan -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 py-8">
                <div class="lg:col-span-1 space-y-1">
                    <div class="flex items-center gap-2 text-sm font-semibold">
                        <Palette class="h-4 w-4" />
                        Tampilan
                    </div>
                    <p class="text-xs text-muted-foreground leading-relaxed">
                        Pilih tema yang nyaman untuk Anda.
                    </p>
                </div>
                <Card class="lg:col-span-2">
                    <CardContent>
                        <AppearanceTabs />
                    </CardContent>
                </Card>
            </div>

            <Separator />

            <!-- Section: Tanda Tangan -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 py-8">
                <div class="lg:col-span-1 space-y-1">
                    <div class="flex items-center gap-2 text-sm font-semibold">
                        <PenLine class="h-4 w-4" />
                        Tanda Tangan
                    </div>
                    <p class="text-xs text-muted-foreground leading-relaxed">
                        Digunakan pada dokumen PDF seperti surat tugas dan berita acara. Gunakan PNG dengan latar transparan.
                    </p>
                </div>
                <Card class="lg:col-span-2">
                    <CardContent>
                        <form @submit.prevent="submitSignature" class="space-y-4">

                            <!-- Saved signature strip -->
                            <div v-if="savedSignatureUrl" class="flex items-center gap-3 rounded-lg border bg-muted/30 p-3">
                                <div class="flex h-12 w-24 shrink-0 items-center justify-center rounded-md border bg-background overflow-hidden">
                                    <img :src="savedSignatureUrl" alt="Tanda tangan aktif" class="max-h-full max-w-full object-contain p-1" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium">Tanda tangan aktif</p>
                                    <p class="text-xs text-muted-foreground">Upload baru untuk mengganti</p>
                                </div>
                                <Button type="button" variant="ghost" size="sm"
                                    class="shrink-0 gap-1.5 text-destructive hover:text-destructive hover:bg-destructive/10"
                                    @click="deleteSignature">
                                    <Trash2 class="h-3.5 w-3.5" /> Hapus
                                </Button>
                            </div>
                            <!-- Drop zone / file selected -->
                            <Transition name="upload-swap" mode="out-in">
                                <div v-if="!signatureFile" key="dropzone"
                                    @click="sigInputRef?.click()"
                                    @dragover.prevent="isDragging = true"
                                    @dragleave.self="isDragging = false"
                                    @drop.prevent="handleSigDrop"
                                    :class="[
                                        'flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed p-8 cursor-pointer transition-all duration-200 select-none',
                                        isDragging ? 'border-primary bg-primary/5' : 'border-border hover:border-primary/40 hover:bg-muted/40'
                                    ]">
                                    <input ref="sigInputRef" type="file" accept="image/png" class="sr-only" @change="handleSigFileChange" />
                                    <UploadCloud :class="['w-9 h-9 transition-all duration-200', isDragging ? 'text-primary scale-110' : 'text-muted-foreground']" />
                                    <div class="text-center">
                                        <p class="text-sm font-medium">Tarik file ke sini atau <span class="text-primary underline underline-offset-2">klik untuk memilih</span></p>
                                        <p class="mt-1 text-xs text-muted-foreground">PNG &bull; Maks. 2MB</p>
                                    </div>
                                </div>
                                <div v-else key="file-preview"
                                    class="flex items-center gap-3 rounded-xl border-2 border-primary/30 bg-primary/5 p-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 overflow-hidden">
                                        <img v-if="signaturePreview" :src="signaturePreview" class="h-full w-full object-contain" alt="" />
                                        <ImageIcon v-else class="h-5 w-5 text-primary" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium">{{ signatureFile.name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ formatFileSize(signatureFile.size) }}</p>
                                    </div>
                                    <button type="button" @click.stop="clearSigFile"
                                        class="shrink-0 rounded-md p-1 hover:bg-muted transition-colors">
                                        <X class="h-4 w-4 text-muted-foreground" />
                                    </button>
                                </div>
                            </Transition>
                            <InputError :message="signatureForm.errors.signature" />
                            <div class="flex justify-end pt-1">
                                <Button type="submit" :disabled="signatureForm.processing || !signatureFile" class="min-w-28">
                                    {{ signatureForm.processing ? 'Menyimpan…' : 'Simpan' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.upload-swap-enter-active,
.upload-swap-leave-active {
    transition: opacity 0.15s ease, transform 0.15s ease;
}
.upload-swap-enter-from,
.upload-swap-leave-to {
    opacity: 0;
    transform: scale(0.97) translateY(6px);
}
</style>
