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
import { Head, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge'
import { Pencil, Trash2, UserRoundPlus, FileUp, Eye, KeyRound } from 'lucide-vue-next'
import { computed, ref } from 'vue';
import { useTable } from '@/composables/useTable';
import { buttonVariants } from '@/components/ui/button';
import type { User } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/admin/users' },
];

// Define Interface and Props
interface UsersPagination<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

interface Filters {
    search: string;
    is_active: number | null;
    sort_by: string;
    sort_direction: 'asc' | 'desc';
    per_page: number;
}

interface Props {
    users: UsersPagination<User>;
    filters: Filters;
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
    { key: 'name', label: 'Nama', sortable: false, class: 'w-[20%]'},
    { key: 'email', label: 'Email', sortable: false, class: 'w-[20%]'},
    { key: 'nomor_induk', label: 'Nomor Induk', sortable: true, class: 'w-[15%]'},
    { key: 'is_active', label: 'Status', sortable: true, class: 'w-[10%]'},
    { key: 'created_at', label: 'Tanggal Terdaftar', sortable: true, class: 'w-[10%]'},
    { key: 'actions', label: 'Opsi', sortable: false, class: 'w-[20%] text-center', cellClass: 'justify-center'},
];

const filterConfigs = [
    {
        key: 'is_active',
        label: 'Status',
        options: [
            { value: '1', label: 'Aktif' },
            { value: '0', label: 'Tidak Aktif' }
        ],
        clearLabel: 'Semua Status'
    }
];

const paginationData = computed(() => ({
    currentPage: props.users.current_page,
    total: props.users.total,
    perPage: props.users.per_page,
    lastPage: props.users.last_page,
    from: props.users.from,
    to: props.users.to,
}));

const getRowNumber = (index: number): number => {
    return props.users.from + index;
};

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
                <div class="flex flex-wrap  gap-2">
                    <Button
                        variant="default"
                        size="sm"
                        class="h-9"
                        @click="$emit('clearAllFilters')">
                        <UserRoundPlus class="h-4 w-4" />
                        Tambah User
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
                <template #is_active="{ value }">
                    <Badge :variant="value ? 'default' : 'destructive'">
                        {{ value ? 'Aktif' : 'Tidak Aktif' }}
                    </Badge>
                </template>
                <template #actions="{ item }">
                    <div class="flex justify-center gap-4">
                        <TextLink
                            :href="`/admin/users/${item.id}`"
                            class="hover:underline text-sm"
                            :tooltip="`Lihat ${item.name}`">
                            <Eye class="w-4 h-4 text-primary" />
                        </TextLink>
                            <TextLink
                            :href="`/admin/users/${item.id}/edit`"
                            class="hover:underline text-sm"
                            :tooltip="`Edit ${item.name}`">
                            <Pencil class="w-4 h-4 text-warning" />
                        </TextLink>
                        <!-- Reset Password and Delete Button -->
                        <AlertDialog>
                            <AlertDialogTrigger as-child>
                                <button
                                    class="text-secondary hover:underline text-sm">
                                    <KeyRound class="w-4 h-4" />
                                </button>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                                <AlertDialogHeader>
                                    <AlertDialogTitle>Reset Password</AlertDialogTitle>
                                    <AlertDialogDescription>
                                        Apakah Anda yakin ingin mereset password user
                                        <span class="font-semibold text-foreground">{{ item.name }}</span>?
                                        Password akan direset ke default.
                                    </AlertDialogDescription>
                                </AlertDialogHeader>
                                <AlertDialogFooter>
                                    <AlertDialogCancel>Batal</AlertDialogCancel>
                                    <AlertDialogAction type="button" @click="confirmResetPassword(item.id)" :class="buttonVariants({ variant: 'ghost' })">
                                        Ya, Reset Password
                                    </AlertDialogAction>
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>
                        <AlertDialog>
                            <AlertDialogTrigger as-child>
                                <button
                                    class="text-destructive hover:text-underline text-sm">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                                <AlertDialogHeader>
                                    <AlertDialogTitle>Hapus User</AlertDialogTitle>
                                    <AlertDialogDescription>
                                        Apakah Anda yakin ingin menghapus user
                                        <span class="font-semibold text-foreground">{{ item.name }}</span>?<br>
                                        <span class="text-destructive font-semibold">Tindakan ini tidak dapat dibatalkan!</span><br>
                                    </AlertDialogDescription>
                                </AlertDialogHeader>
                                <AlertDialogFooter>
                                    <AlertDialogCancel>Batal</AlertDialogCancel>
                                    <AlertDialogAction
                                        class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                        @click="confirmDelete(item.id)">
                                        Ya, Hapus User
                                    </AlertDialogAction>
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>
                    </div>
                    </template>
            </DataTable>
        </div>
    </AppLayout>
</template>
