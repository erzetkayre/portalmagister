<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch, onMounted } from 'vue';
import { Head, useForm, router, usePage} from '@inertiajs/vue3';

import { useAlert } from '@/composables/UseAlert';
import { useDebounce } from '@/composables/useDebounce';
import { usePaginationFilters } from '@/composables/usePaginationFilters';

import { type BreadcrumbItem } from '@/types';

import {
    Card, CardHeader, CardContent, CardTitle,
    Badge, Button, Input, Alert,
    Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
    Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, DialogFooter,
    Pagination, PaginationContent, PaginationItem, PaginationNext, PaginationPrevious, PaginationEllipsis,
    DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger,
    Tooltip, TooltipContent, TooltipProvider, TooltipTrigger
} from '@/components/ui';

import {
    BookUser, GraduationCap, UsersRound, Plus, Upload, Search, Filter, Eye, KeyRound, Edit, Trash2,
    CheckCircle, XCircle, X
 } from 'lucide-vue-next';

// interface
interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    status: 'active' | 'inactive';
    created_at: string;
}

interface PaginatedUsers {
    data: User[];
    current_page: number;
    from: number;
    to: number;
    total: number;
    last_page: number;
    per_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface Props {
    users: PaginatedUsers;
    roles: string[];
    filters: {
        search?: string;
        role?: string;
        status?: string;
    };
}

// Composables
const { showAlert, alertType, alertTitle, alertDescription, showSuccessAlert, showErrorAlert, closeAlert } = useAlert();
const props = defineProps<Props>();
const page = usePage();

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

// States
const isDialogOpen = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const searchTerm = ref(props.filters.search || '');
const selectedRole = ref(props.filters.role || '');
const selectedStatus = ref(props.filters.status || '');
const form = useForm({ file: null as File | null, });


// Handle Import Excel
const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.file = target.files[0] || null;
    }
};
const submitImport = () => {
    form.post('/admin/user/import', {
        onSuccess: () => {
            isDialogOpen.value = false;
            form.reset();
        },
    });
};

// Filtering
const { applyFilters, clearFilters, goToPage } = usePaginationFilters(
        { search: searchTerm, role: selectedRole, status: selectedStatus },
        '/admin/user'
    );
const debouncedSearchTerm = useDebounce(searchTerm, 300);
watch(debouncedSearchTerm, () => applyFilters());

// Action
const showUser = (userId: number) => { router.get(`/admin/user/${userId}`) };
const editUser = (userId: number) => { router.get(`/admin/user/${userId}/edit`) };

const confirmResetPassword = () => {
    if (selectedUserId.value) {
        router.put(`/admin/user/${selectedUserId.value}/reset-password`, {}, {
            onSuccess: () => {
                resetPasswordDialog.value = false;
                selectedUserId.value = null;
                showSuccessAlert('Password Berhasil Direset', 'Password user telah direset ke nomor induk dan harus diganti saat login berikutnya.');
            },
            onError: (errors) => {
                showErrorAlert('Gagal Reset Password', 'Silakan coba lagi atau hubungi support jika masalah berlanjut.');
            },
            preserveState: true,
            preserveScroll: true
        });
    }
};

const confirmDeleteUser = () => {
    if (selectedUserId.value) {
        router.delete(`/admin/user/${selectedUserId.value}`, {
            onSuccess: () => {
                deleteUserDialog.value = false;
                selectedUserId.value = null;
                showSuccessAlert('User Berhasil Dihapus', 'User telah dihapus secara permanen dari sistem.');
            },
            onError: (errors) => {
                deleteUserDialog.value = false;
                selectedUserId.value = null;
                let errorMessage = 'Terjadi kesalahan saat menghapus user';
                if (errors?.error) {
                    errorMessage = errors.error;
                } else if (typeof errors === 'string') {
                    errorMessage = errors;
                }
                showErrorAlert('Gagal Menghapus User', errorMessage);
            },
            preserveState: true,
            preserveScroll: true
        });
    }
};

// Confirmation dialog states
const resetPasswordDialog = ref(false);
const deleteUserDialog = ref(false);
const selectedUserId = ref<number | null>(null);

const openResetPasswordDialog = (userId: number) => {
    selectedUserId.value = userId;
    resetPasswordDialog.value = true;
};
const openDeleteUserDialog = (userId: number) => {
    selectedUserId.value = userId;
    deleteUserDialog.value = true;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Manajemen Users', href: '/admin/user' },
];

</script>

<template>
    <Head title="Manajemen Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
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

            <!-- <div>
                <h2 class="text-2xl font-bold tracking-tight">Daftar User</h2>
            </div> -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <Card class="gap-2">
                    <CardHeader class="flex flex-row justify-between items-center">
                        <CardTitle class="text-lg font-medium ">
                            Total User
                        </CardTitle>
                        <BookUser class="h-6 w-6 text-destructive" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold ">
                            2,350
                        </div>
                        <div class="flex items-center pt-2 justify-between">
                            <p class="text-sm ">
                                Jumlah user terdaftar dalam sistem
                            </p>
                            <Badge class="h-fit align-middle">
                                1,890 aktif
                            </Badge>
                        </div>
                    </CardContent>
                </Card>
                <Card class="gap-2">
                    <CardHeader class="flex flex-row justify-between items-center">
                        <CardTitle class="text-lg font-medium ">
                            User Mahasiswa
                        </CardTitle>
                        <GraduationCap class="h-6 w-6 text-destructive" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold ">
                            2,350
                        </div>
                        <div class="flex items-center pt-2 justify-between">
                            <p class="text-sm ">
                                Jumlah pengguna mahasiswa
                            </p>
                            <Badge class="h-fit align-middle">
                                1,890 aktif
                            </Badge>
                        </div>
                    </CardContent>
                </Card>
                <Card class="gap-2">
                    <CardHeader class="flex flex-row justify-between items-center">
                        <CardTitle class="text-lg font-medium ">
                            User Dosen
                        </CardTitle>
                        <UsersRound class="h-6 w-6 text-destructive" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold ">
                            2,350
                        </div>
                        <div class="flex items-center pt-2 justify-between">
                            <p class="text-sm ">
                                Jumlah pengguna dosen
                            </p>
                            <Badge class="h-fit align-middle">
                                1,890 aktif
                            </Badge>
                        </div>
                    </CardContent>
                </Card>
            </div>
            <div class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border shadow-xs">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Daftar Users</h3>
                        <Button @click="router.get('/admin/user/create')">
                            <Plus class="h-4 w-4 mr-2" />
                            Tambah User
                        </Button>
                        <Dialog v-model:open="isDialogOpen">
                            <DialogTrigger as-child>
                                <Button variant="outline">
                                    <Upload class="h-4 w-4" />
                                    Import Users
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Import Users dari Excel</DialogTitle>
                                </DialogHeader>
                                <form @submit.prevent="submitImport" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Pilih File Excel (.xlsx)</label>
                                        <Input
                                            ref="fileInput"
                                            type="file"
                                            accept=".xlsx,.xls"
                                            @change="handleFileUpload"
                                            class="cursor-pointer"
                                        />
                                    </div>
                                    <div class="flex justify-end space-x-2">
                                        <Button type="button" variant="outline" @click="isDialogOpen = false">
                                            Batal
                                        </Button>
                                        <Button type="submit" :disabled="!form.file || form.processing">
                                            <Upload class="h-4 w-4 mr-2" />
                                            Import
                                        </Button>
                                    </div>
                                </form>
                            </DialogContent>
                        </Dialog>
                    </div>

                    <!-- Filters -->
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex items-center space-x-2">
                            <Search class="h-4 w-4 text-gray-500" />
                            <Input
                                v-model="searchTerm"
                                placeholder="Cari nama atau email..."
                                class="w-64"
                            />
                        </div>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline" class="border-dashed">
                                    <Filter class="h-4 w-4 mr-2" />
                                    Peran
                                    <span v-if="selectedRole" class="ml-2 bg-primary text-primary-foreground px-2 py-1 rounded-full text-xs">
                                        {{ selectedRole }}
                                    </span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent>
                                <DropdownMenuItem @click="selectedRole = ''; applyFilters()">
                                    Semua Peran
                                </DropdownMenuItem>
                                <DropdownMenuItem v-for="role in roles" :key="role" @click="selectedRole = role; applyFilters()">
                                    {{ role }}
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline" class="border-dashed">
                                    <Filter class="h-4 w-4 mr-2" />
                                    Status
                                    <span v-if="selectedStatus" class="ml-2 bg-primary text-primary-foreground px-2 py-1 rounded-full text-xs">
                                        {{ selectedStatus }}
                                    </span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent>
                                <DropdownMenuItem @click="selectedStatus = ''; applyFilters()">
                                    Semua Status
                                </DropdownMenuItem>
                                <DropdownMenuItem @click="selectedStatus = 'active'; applyFilters()">
                                    Aktif
                                </DropdownMenuItem>
                                <DropdownMenuItem @click="selectedStatus = 'inactive'; applyFilters()">
                                    Tidak Aktif
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <Button variant="ghost" @click="clearFilters" v-if="searchTerm || selectedRole || selectedStatus">
                            Hapus Filter
                        </Button>
                    </div>

                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="text-center">No</TableHead>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Role</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Dibuat Pada</TableHead>
                                    <TableHead>Tombol Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(user, index) in props.users.data" :key="user.id">
                                    <TableCell class="text-center">{{ (props.users.current_page - 1) * props.users.per_page + index + 1 }}</TableCell>
                                    <TableCell class="font-medium">{{ user.name }}</TableCell>
                                    <TableCell>{{ user.email }}</TableCell>
                                    <TableCell>
                                        <Badge variant="outline">{{ user.role }}</Badge>
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            :variant="'outline'"
                                            :class="user.status === 'active' ? 'border-success text-success dark:border-success/60 dark:text-success/90 hover:bg-success/10' : 'border-destructive text-destructive dark:border-destructive/60 dark:text-destructive/90 hover:bg-destructive/10'"
                                        >
                                            {{ user.status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>{{
                                        new Date(user.created_at).toLocaleDateString('id-ID', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                        })
                                    }}</TableCell>
                                    <TableCell>
                                        <div class="flex items-center gap-2">
                                            <TooltipProvider>
                                                <Tooltip>
                                                    <TooltipTrigger as-child>
                                                        <Button size="sm" variant="ghost" @click="showUser(user.id)">
                                                            <Eye class="h-4 w-4" />
                                                        </Button>
                                                    </TooltipTrigger>
                                                    <TooltipContent>
                                                        <p>Lihat Detail</p>
                                                    </TooltipContent>
                                                </Tooltip>
                                            </TooltipProvider>
                                            <TooltipProvider>
                                                <Tooltip>
                                                    <TooltipTrigger as-child>
                                                        <Button size="sm" variant="ghost" @click="editUser(user.id)">
                                                            <Edit class="h-4 w-4" />
                                                        </Button>
                                                    </TooltipTrigger>
                                                    <TooltipContent>
                                                        <p>Edit User</p>
                                                    </TooltipContent>
                                                </Tooltip>
                                            </TooltipProvider>
                                            <TooltipProvider>
                                                <Tooltip>
                                                    <TooltipTrigger as-child>
                                                        <Button size="sm" variant="ghost" @click="openResetPasswordDialog(user.id)">
                                                            <KeyRound class="h-4 w-4" />
                                                        </Button>
                                                    </TooltipTrigger>
                                                    <TooltipContent>
                                                        <p>Reset Password</p>
                                                    </TooltipContent>
                                                </Tooltip>
                                            </TooltipProvider>
                                            <TooltipProvider>
                                                <Tooltip>
                                                    <TooltipTrigger as-child>
                                                        <Button size="sm" variant="ghost" @click="openDeleteUserDialog(user.id)" class="text-destructive hover:text-destructive">
                                                            <Trash2 class="h-4 w-4" />
                                                        </Button>
                                                    </TooltipTrigger>
                                                    <TooltipContent>
                                                        <p>Hapus User</p>
                                                    </TooltipContent>
                                                </Tooltip>
                                            </TooltipProvider>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 flex items-center justify-between border-t pt-4">
                        <div class="text-sm text-muted-foreground">
                            Showing {{ props.users.from || 0 }} to {{ props.users.to || 0 }} of {{ props.users.total || 0 }} results
                        </div>

                        <Pagination v-if="props.users.last_page > 1" v-slot="{ page }" :items-per-page="props.users.per_page" :total="props.users.total" :default-page="props.users.current_page">
                            <PaginationContent v-slot="{ items }">
                                <PaginationPrevious @click="goToPage(props.users.current_page - 1)" :class="{ 'pointer-events-none opacity-50': !props.users.prev_page_url }" />

                                <template v-for="(item, index) in items" :key="index">
                                    <PaginationItem
                                        v-if="item.type === 'page'"
                                        :value="item.value"
                                        :is-active="item.value === page"
                                        @click="goToPage(item.value)"
                                    >
                                        {{ item.value }}
                                    </PaginationItem>
                                </template>

                                <PaginationEllipsis v-if="props.users.last_page > 7" />

                                <PaginationNext @click="goToPage(props.users.current_page + 1)" :class="{ 'pointer-events-none opacity-50': !props.users.next_page_url }" />
                            </PaginationContent>
                        </Pagination>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reset Password Confirmation Dialog -->
        <Dialog v-model:open="resetPasswordDialog">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <KeyRound class="h-5 w-5 text-orange-600" />
                        Reset Password
                    </DialogTitle>
                </DialogHeader>
                <div class="space-y-4">
                    <p class="text-sm text-muted-foreground">
                        Apakah Anda yakin ingin mereset password pengguna ini? Hal ini akan:
                    </p>
                    <ul class="text-sm text-muted-foreground space-y-1 ml-2">
                        <li>Set password ke nomor induk pengguna</li>
                        <li>Memaksa pengguna untuk mengganti password saat login</li>
                        <li>Pengguna akan menerima notifikasi tentang reset password</li>
                    </ul>
                </div>
                <DialogFooter class="flex-row justify-end space-x-2">
                    <Button variant="outline" @click="resetPasswordDialog = false">
                        Batal
                    </Button>
                    <Button @click="confirmResetPassword" class="bg-orange-600 hover:bg-orange-700">
                        <KeyRound class="h-4 w-4 mr-2" />
                        Reset Password
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Delete User Confirmation Dialog -->
        <Dialog v-model:open="deleteUserDialog">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <Trash2 class="h-5 w-5 text-destructive" />
                        Hapus User
                    </DialogTitle>
                </DialogHeader>
                <div class="space-y-4">
                    <p class="text-sm text-muted-foreground">
                        Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="bg-destructive/10 border border-destructive/20 rounded-lg p-3">
                        <div class="flex items-center gap-2 text-destructive text-sm font-medium">
                            <Trash2 class="h-4 w-4" />
                            Peringatan: Tindakan ini permanen
                        </div>
                        <p class="text-destructive/80 text-sm mt-1">
                            Semua data pengguna dan record terkait akan dihapus secara permanen.
                        </p>
                    </div>
                </div>
                <DialogFooter class="flex-row justify-end space-x-2">
                    <Button variant="outline" @click="deleteUserDialog = false">
                        Batal
                    </Button>
                    <Button variant="destructive" @click="confirmDeleteUser">
                        <Trash2 class="h-4 w-4 mr-2" />
                        Hapus User
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
