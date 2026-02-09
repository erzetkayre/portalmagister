<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '../../../../components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';
import DataTable from '@/components/DataTable.vue';
import TableFilters from '@/components/FilterTable.vue';
import TextLink from '@/components/TextLink.vue';
import { route } from 'ziggy-js';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'
import { type BreadcrumbItem } from '@/types';
import { Head, router, Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge'
import { Pencil, Trash2, UserRoundPlus, FileUp, Eye, KeyRound } from 'lucide-vue-next'
import { computed } from 'vue';
import { useTable } from '@/composables/useTable';
import { buttonVariants } from '@/components/ui/button';
import type { User } from '@/types';
import ConfirmDialog from '@/components/ConfirmDialog.vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/admin/mahasiswa' },
];

// Define Interface and Props
interface UsersPagination<T> {
    data: T[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number
    to: number
    links: {
        url: string | null
        label: string
        active: boolean
    }[];
}

interface Filters {
    search: string
    sort_by: string
    sort_direction: 'asc' | 'desc'
    per_page: number
}

interface Props {
    mahasiswa: UsersPagination<User>
    filters: Filters
}

const props = defineProps<Props>();

const {
    searchQuery,
    filters: activeFilters,
    sortBy,
    sortDirection,
    setFilter,
    clearAllFilters,
    handleSort,
    handlePageChange,
    handlePerPageChange
} = useTable({
    endpoint: '/admin/users',
    initialFilters: props.filters,
});

// Table Variables
const columns = [
    { key: 'number', label: '#', sortable: false, class: 'w-[5%]', cellClass: 'text-center'},
    { key: 'nama_mhs', label: 'Nama', sortable: false, class: 'w-[15%]'},
    { key: 'nim', label: 'Nomor Induk', sortable: true, class: 'w-[15%]'},
    { key: 'angkatan', label: 'Angkatan', sortable: false, class: 'w-[15%]'},
    { key: 'status', label: 'Status', sortable: false, class: 'w-[20%]', cellClass: 'text-center flex justify-center' },
    { key: 'created_at', label: 'Tanggal Terdaftar', sortable: true, class: 'w-[20%]'},
    { key: 'actions', label: 'Opsi', sortable: false, class: 'w-[10%] text-center', cellClass: 'justify-center'},
];

const paginationData = computed(() => ({
    currentPage: props.mahasiswa.current_page,
    total: props.mahasiswa.total,
    perPage: props.mahasiswa.per_page,
    lastPage: props.mahasiswa.last_page,
    from: props.mahasiswa.from,
    to: props.mahasiswa.to,
}));

// Numbering
const getRowNumber = (index: number): number => {
    return props.users.from + index;
};

// Post Request Reset and Delete
const confirmResetPassword = (userId: number) => {
    router.post(
        route('admin.users.reset.password', userId),
        {},
        { preserveScroll: true }
    );
};
const confirmDelete = (userId: number) => {
    router.delete(
        route('admin.users.delete', userId),
        { preserveScroll: true }
    );
};

const importUsers = () => {
    router.post(
        route('admin.users.import'),
        { preserveScroll: true }
    );
};
</script>

<template>
    <Head title="User Management" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading :title="'Daftar Pengguna'" description="Kelola pengguna dan hak akses sistem." />
            <div class="flex flex-wrap gap-2 justify-between">
                <TableFilters
                    v-model:search-value="searchQuery"
                    search-placeholder="Cari nama atau email..."
                    :filters="filterConfigs"
                    :filter-values="activeFilters"
                    @filter-change="setFilter"
                    @clear-all-filters="clearAllFilters"/>
                <div class="flex flex-wrap gap-2">
                    <Button variant="default" size="sm" as-child class="h-9">
                        <Link :href="route('admin.users.create')" class="flex items-center">
                            <UserRoundPlus class="w-4 h-4" />
                            Tambah User
                        </Link>
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        class="h-9"
                        @click="$emit('clearAllFilters')">
                        <FileUp class="h-4 w-4" />
                        Import User
                    </Button>
                    <AlertDialog>
                        <AlertDialogTrigger as-child>
                            <Button
                                variant="outline"
                                size="sm"
                                class="h-9">
                                <FileUp class="h-4 w-4" />
                                Import User
                            </Button>
                        </AlertDialogTrigger>

                        <AlertDialogContent class="sm:max-w-md">
                            <AlertDialogHeader>
                                <AlertDialogTitle>Import User</AlertDialogTitle>
                                <AlertDialogDescription>
                                    Upload file Excel (.xls / .xlsx) untuk menambahkan user secara massal.
                                </AlertDialogDescription>
                            </AlertDialogHeader>

                            <!-- File Input -->
                            <div class="grid gap-2 py-4">
                                <input
                                    type="file"
                                    accept=".xls,.xlsx"
                                    class="block w-full text-sm
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-primary file:text-primary-foreground
                                        hover:file:bg-primary/90 border-dashed border-2"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Maksimal 2MB. Format: XLS / XLSX
                                </p>
                            </div>

                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction
                                    type="button"
                                    :disabled="!excelFile"
                                    @click="submitImport">
                                    Upload
                                </AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </div>
            </div>
            <!-- Table -->
            <DataTable
                :columns="columns"
                :data="users.data"
                :pagination="paginationData"
                item-key="id"
                :sort-by="sortBy"
                :sort-direction="sortDirection"
                @sort="handleSort"
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange">
                <template #number="{ index }">
                    <span class="text-gray-600 font-medium">
                        {{ getRowNumber(index) }}
                    </span>
                </template>
                <template #roles="{ item }">
                    <div class="flex flex-wrap gap-1">
                        <Badge
                        v-for="role in item.roles"
                        :key="role.id"
                        variant="primary-outline"
                        class="capitalize"
                        >
                        {{ role.role_name }}
                        </Badge>
                    </div>
                </template>
                <!-- Action Button -->
                <template #actions="{ item }">
                    <div class="flex justify-center gap-4">
                        <TextLink
                            :href="route('admin.users.show',item.id)"
                            class="hover:underline text-sm"
                            :tooltip="`Lihat ${item.name}`">
                            <Eye class="w-4 h-4 text-primary" />
                        </TextLink>
                            <TextLink
                            :href="route('admin.users.edit',item.id)"
                            class="hover:underline text-sm"
                            :tooltip="`Edit ${item.name}`">
                            <Pencil class="w-4 h-4 text-warning" />
                        </TextLink>
                        <!-- Reset Password and Delete Button -->
                        <ConfirmDialog
                            title="Reset Password"
                            confirm-text="Ya, Reset Password"
                            :confirm-class="buttonVariants({ variant: 'ghost' })"
                            :on-confirm="() => confirmResetPassword(item.id)">
                            <template #trigger>
                                <button class="text-secondary hover:underline text-sm">
                                <KeyRound class="w-4 h-4" />
                                </button>
                            </template>
                            <template #description>
                                <AlertDialogDescription>
                                Apakah Anda yakin ingin mereset password user
                                <span class="font-semibold text-foreground">
                                    {{ item.name }}
                                </span>?
                                Password akan direset ke default.
                                </AlertDialogDescription>
                            </template>
                        </ConfirmDialog>
                        <ConfirmDialog
                            title="Hapus User"
                            confirm-text="Ya, Hapus User"
                            :confirm-class="buttonVariants({ variant: 'destructive' })"
                            :on-confirm="() => confirmDelete(item.id)">
                            <template #trigger>
                                <button class="text-destructive hover:underline text-sm">
                                <Trash2 class="w-4 h-4" />
                                </button>
                            </template>
                            <template #description>
                                <AlertDialogDescription>
                                    Apakah Anda yakin ingin menghapus user
                                        <span class="font-semibold text-foreground">{{ item.name }}</span>?<br>
                                        <span class="text-destructive font-semibold">Tindakan ini tidak dapat dibatalkan!</span><br>
                                </AlertDialogDescription>
                            </template>
                        </ConfirmDialog>
                    </div>
                    </template>
            </DataTable>
        </div>
    </AppLayout>
</template>
