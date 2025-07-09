<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch, onMounted } from 'vue';
import { Head, useForm, router, usePage} from '@inertiajs/vue3';

import { useAlert } from '@/composables/UseAlert';
import { useDebounce } from '@/composables/useDebounce';
import { usePaginationFilters } from '@/composables/usePaginationFilters';

import { type BreadcrumbItem } from '@/types';

import {
    Badge, Button, StatCard, AlertNotification, DataTable, FilterBar, ActionButtons, ConfirmationDialog
} from '@/components/ui';

import {
    BookUser, GraduationCap, UsersRound, Plus, Eye, KeyRound, Edit, Trash2
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
    stats: {
        totalUsers: number;
        totalAdmin: number;
        totalMahasiswa: number;
        totalDosen: number;
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
const searchTerm = ref(props.filters.search || '');
const selectedRole = ref(props.filters.role || '');
const selectedStatus = ref(props.filters.status || '');
const sortBy = ref('created_at');
const sortDirection = ref('desc');


// Handle Import Excel
// const handleFileUpload = (event: Event) => {
//     const target = event.target as HTMLInputElement;
//     if (target.files && target.files[0]) {
//         form.file = target.files[0] || null;
//     }
// };
// const submitImport = () => {
//     form.post('/admin/user/import', {
//         onSuccess: () => {
//             isDialogOpen.value = false;
//             form.reset();
//         },
//     });
// };

// Filtering
const { applyFilters, clearFilters, goToPage } = usePaginationFilters(
        { search: searchTerm, role: selectedRole, status: selectedStatus, sort: sortBy, sortDirection: sortDirection },
        '/admin/user'
    );
const debouncedSearchTerm = useDebounce(searchTerm, 300);
watch(debouncedSearchTerm, () => applyFilters());

// Filter Configuration
const filterConfig = ref([
    {
        key: 'role',
        label: 'Role',
        options: props.roles.map(role => ({ value: role, label: role })),
        clearLabel: 'Semua Role'
    },
    {
        key: 'status',
        label: 'Status',
        options: [
            { value: 'active', label: 'Aktif' },
            { value: 'inactive', label: 'Tidak Aktif' }
        ],
        clearLabel: 'Semua Status'
    }
]);

const handleFilterChange = (key: string, value: string) => {
    if (key === 'role') {
        selectedRole.value = value;
    } else if (key === 'status') {
        selectedStatus.value = value;
    }
    applyFilters();
};

const handleSort = (column: string) => {
    if (sortBy.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = column;
        sortDirection.value = 'asc';
    }
    applyFilters();
};

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


// Table Configuration
const tableColumns = ref([
    { key: 'no', label: 'No', class: 'text-center', sortable: false },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'role', label: 'Role', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'created_at', label: 'Dibuat Pada', sortable: true },
    { key: 'actions', label: 'Tombol Aksi', sortable: false }
]);

// User Actions Configuration
const userActions = ref([
    { key: 'view', icon: Eye, tooltip: 'Lihat Detail'},
    { key: 'edit', icon: Edit, tooltip: 'Edit User'},
    { key: 'reset-password', icon: KeyRound, tooltip: 'Reset Password'},
    { key: 'delete', icon: Trash2, tooltip: 'Hapus User', class: 'text-destructive hover:text-destructive' },
]);
const handleUserAction = (actionKey: string, user: User) => {
    switch (actionKey) {
        case 'view':
            showUser(user.id);
            break;
        case 'edit':
            editUser(user.id);
            break;
        case 'reset-password':
            openResetPasswordDialog(user.id);
            break;
        case 'delete':
            openDeleteUserDialog(user.id);
            break;
    }
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
            <AlertNotification :show="showAlert" :type="alertType" :title="alertTitle" :description="alertDescription" @close="closeAlert"/>

            <!-- <div>
                <h2 class="text-2xl font-bold tracking-tight">Daftar User</h2>
            </div> -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <StatCard title="User Admin" :value="props.stats.totalAdmin" description="Jumlah user admin terdaftar" :icon="BookUser" :badge="`${Math.round((props.stats.totalAdmin / props.stats.totalUsers) * 100)}%`"/>
                <StatCard title="User Dosen" :value="props.stats.totalDosen" description="Jumlah user dosen terdaftar" :icon="UsersRound" :badge="`${Math.round((props.stats.totalDosen / props.stats.totalUsers) * 100)}%`"/>
                <StatCard title="User Mahasiswa" :value="props.stats.totalMahasiswa" description="Jumlah user mahasiswa terdaftar" :icon="GraduationCap" :badge="`${Math.round((props.stats.totalMahasiswa / props.stats.totalUsers) * 100)}%`"/>
            </div>
            <div class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border shadow-xs">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Daftar Users</h3>
                        <div class="items-center space-x-2">
                            <Button @click="router.get('/admin/user/create')">
                                <Plus class="h-4 w-4" />
                                Tambah User
                            </Button>
                            <!-- <Dialog v-model:open="isDialogOpen">
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
                                                <Upload class="h-4 w-4" />
                                                Import
                                            </Button>
                                        </div>
                                    </form>
                                </DialogContent>
                            </Dialog> -->
                        </div>
                    </div>

                    <!-- Filters -->
                    <FilterBar
                        v-model:searchValue="searchTerm"
                        search-placeholder="Cari nama atau email..."
                        :filters="filterConfig"
                        :filter-values="{ role: selectedRole, status: selectedStatus }"
                        @filter-change="handleFilterChange"
                        @clear-all-filters="clearFilters"
                    />

                    <DataTable
                        :columns="tableColumns"
                        :data="props.users.data"
                        :pagination="{
                            currentPage: props.users.current_page,
                            from: props.users.from,
                            to: props.users.to,
                            total: props.users.total,
                            lastPage: props.users.last_page,
                            perPage: props.users.per_page,
                            prevPageUrl: props.users.prev_page_url,
                            nextPageUrl: props.users.next_page_url
                        }"
                        item-key="id"
                        :sort-by="sortBy"
                        :sort-direction="sortDirection"
                        @page-change="goToPage"
                        @sort="handleSort"
                    >
                        <template #no="{ index }">
                            <div class="text-center">
                                {{ (props.users.current_page - 1) * props.users.per_page + index + 1 }}
                            </div>
                        </template>

                        <template #name="{ item }">
                            <div class="font-medium">{{ item.name }}</div>
                        </template>

                        <template #role="{ item }">
                            <Badge variant="outline">{{ item.role }}</Badge>
                        </template>

                        <template #status="{ item }">
                            <Badge
                                variant="outline"
                                :class="item.status === 'active' ? 'border-success text-success dark:border-success/60 dark:text-success/90 hover:bg-success/10' : 'border-destructive text-destructive dark:border-destructive/60 dark:text-destructive/90 hover:bg-destructive/10'"
                            >
                                {{ item.status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                            </Badge>
                        </template>

                        <template #created_at="{ item }">
                            {{ new Date(item.created_at).toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            }) }}
                        </template>

                        <template #actions="{ item }">
                            <ActionButtons
                                :actions="userActions"
                                :item="item"
                                @action="handleUserAction"
                            />
                        </template>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Reset Password Confirmation Dialog -->
        <ConfirmationDialog
            v-model:open="resetPasswordDialog"
            title="Reset Password"
            description="Apakah Anda yakin ingin mereset password pengguna ini? Hal ini akan:"
            :icon="KeyRound"
            icon-class="h-5 w-5 text-orange-600"
            confirm-text="Reset Password"
            :confirm-icon="KeyRound"
            confirm-class="bg-orange-600 hover:bg-orange-700"
            :points="[
                'Set password ke nomor induk pengguna',
                'Memaksa pengguna untuk mengganti password saat login',
                'Pengguna akan menerima notifikasi tentang reset password'
            ]"
            @confirm="confirmResetPassword"
            @cancel="resetPasswordDialog = false"
        />

        <!-- Delete User Confirmation Dialog -->
        <ConfirmationDialog
            v-model:open="deleteUserDialog"
            title="Hapus User"
            description="Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan."
            :icon="Trash2"
            icon-class="h-5 w-5 text-destructive"
            confirm-text="Hapus User"
            :confirm-icon="Trash2"
            confirm-variant="destructive"
            :warning="{
                title: 'Peringatan: Tindakan ini permanen',
                message: 'Semua data pengguna dan record terkait akan dihapus secara permanen.'
            }"
            :warning-icon="Trash2"
            @confirm="confirmDeleteUser"
            @cancel="deleteUserDialog = false"
        />
    </AppLayout>
</template>
