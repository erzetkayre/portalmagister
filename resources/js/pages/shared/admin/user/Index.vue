<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
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
import { Pencil, Trash2, UserRoundPlus, FileUp, Eye, KeyRound, UploadCloud, FileSpreadsheet, X } from 'lucide-vue-next'
import { TooltipProvider, Tooltip, TooltipTrigger, TooltipContent } from '@/components/ui/tooltip'
import { computed, ref } from 'vue';
import { useTable } from '@/composables/useTable';
import { buttonVariants } from '@/components/ui/button';
import type { User } from '@/types';
import ConfirmDialog from '@/components/ConfirmDialog.vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/admin/users' },
];

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

interface Role {
    id: number
    role_name: string
}

interface Filters {
    search: string
    role: string | null
    sort_by: string
    sort_direction: 'asc' | 'desc'
    per_page: number
}

interface Props {
    users: UsersPagination<User>
    filters: Filters
    roles: Role[]
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

const columns = [
    { key: 'number',     label: '#',                 sortable: false, class: 'w-[5%]',  cellClass: 'text-center', align: 'center' as const },
    { key: 'name',       label: 'Nama',              sortable: false, class: 'w-[20%]' },
    { key: 'nomor_induk',label: 'Nomor Induk',       sortable: true,  class: 'w-[15%]' },
    { key: 'email',      label: 'Email',             sortable: false, class: 'w-[20%]' },
    { key: 'roles',      label: 'Hak Akses',         sortable: false, class: 'w-[20%]', cellClass: 'text-center', align: 'center' as const },
    { key: 'created_at', label: 'Terdaftar',         sortable: true,  class: 'w-[10%]' },
    { key: 'actions',    label: 'Opsi',              sortable: false, class: 'w-[10%]', cellClass: 'justify-center', align: 'center' as const },
];

const filterConfigs = computed(() => [
    {
        key: 'role',
        label: 'Role',
        options: [
            { value: 'admin',        label: 'Admin' },
            { value: 'dosen',        label: 'Dosen' },
            { value: 'koordinator',  label: 'Koordinator' },
            { value: 'mahasiswa',    label: 'Mahasiswa' },
        ],
        clearLabel: 'Semua Role',
    },
])

const paginationData = computed(() => ({
    currentPage: props.users.current_page,
    total:       props.users.total,
    perPage:     props.users.per_page,
    lastPage:    props.users.last_page,
    from:        props.users.from,
    to:          props.users.to,
}));

const getRowNumber = (index: number): number => {
    return props.users.from + index;
};

// Import modal state
const excelFile   = ref<File | null>(null)
const importType  = ref<'mahasiswa' | 'dosen'>('mahasiswa')
const isDragging  = ref(false)
const fileInputRef = ref<HTMLInputElement | null>(null)

const triggerFileInput = () => fileInputRef.value?.click()

const handleFileChange = (e: Event) => {
    excelFile.value = (e.target as HTMLInputElement).files?.[0] ?? null
}

const handleDrop = (e: DragEvent) => {
    isDragging.value = false
    const file = e.dataTransfer?.files[0]
    if (file && /\.xlsx?$/.test(file.name)) excelFile.value = file
}

const formatFileSize = (bytes: number): string => {
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}

const submitImport = () => {
    if (!excelFile.value) return
    const data = new FormData()
    data.append('file', excelFile.value)
    data.append('type', importType.value)
    router.post(route('admin.users.import'), data, {
        preserveScroll: true,
        onSuccess: () => { excelFile.value = null },
    })
}

const confirmResetPassword = (userId: number) => {
    router.post(route('admin.users.reset.password', userId), {}, { preserveScroll: true });
};

const confirmDelete = (userId: number) => {
    router.delete(route('admin.users.delete', userId), { preserveScroll: true });
};
</script>

<template>
    <Head title="User Management" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader title="Daftar Pengguna" description="Kelola pengguna dan hak akses sistem.">
                <template #actions>
                    <Button variant="default" size="sm" as-child class="h-9">
                        <Link :href="route('admin.users.create')" class="flex items-center gap-2">
                            <UserRoundPlus class="w-4 h-4" />
                            Tambah User
                        </Link>
                    </Button>
                    <AlertDialog>
                        <AlertDialogTrigger as-child>
                            <Button variant="outline" size="sm" class="h-9 flex items-center gap-2">
                                <FileUp class="h-4 w-4" />
                                Import User
                            </Button>
                        </AlertDialogTrigger>
                        <AlertDialogContent class="sm:max-w-md">
                            <AlertDialogHeader>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                                        <FileUp class="h-5 w-5 text-green-600 dark:text-green-400" />
                                    </div>
                                    <AlertDialogTitle>Import User</AlertDialogTitle>
                                </div>
                            </AlertDialogHeader>

                            <AlertDialogDescription class="text-justify">
                                Upload file Excel (.xls / .xlsx) untuk menambahkan user secara massal.
                            </AlertDialogDescription>

                            <!-- Type selector -->
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    v-for="t in ['mahasiswa', 'dosen']" :key="t"
                                    @click="importType = t as 'mahasiswa' | 'dosen'"
                                    :class="[
                                        'flex-1 rounded-lg border py-1.5 text-sm font-medium transition-colors capitalize',
                                        importType === t
                                            ? 'border-primary bg-primary text-primary-foreground'
                                            : 'border-border hover:bg-muted'
                                    ]">
                                    {{ t }}
                                </button>
                            </div>

                            <!-- Template download -->
                            <div class="flex items-center gap-2 rounded-lg bg-muted/50 px-3 py-2 text-xs text-muted-foreground">
                                <FileSpreadsheet class="h-4 w-4 shrink-0" />
                                <span>Belum punya template?</span>
                                <a :href="route('admin.users.template', importType)"
                                    class="ml-auto font-medium text-primary underline underline-offset-2 hover:opacity-80">
                                    Download template {{ importType }}
                                </a>
                            </div>

                            <div class="py-2">
                                <Transition name="upload-swap" mode="out-in">
                                    <!-- Idle / drag-over state -->
                                    <div
                                        v-if="!excelFile"
                                        key="dropzone"
                                        @click="triggerFileInput"
                                        @dragover.prevent="isDragging = true"
                                        @dragleave.self="isDragging = false"
                                        @drop.prevent="handleDrop"
                                        :class="[
                                            'flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed p-8 cursor-pointer transition-all duration-200 select-none',
                                            isDragging
                                                ? 'border-primary bg-primary/5'
                                                : 'border-border hover:border-primary/40 hover:bg-muted/40'
                                        ]">
                                        <input
                                            ref="fileInputRef"
                                            type="file"
                                            accept=".xls,.xlsx"
                                            @change="handleFileChange"
                                            class="sr-only" />
                                        <UploadCloud :class="[
                                            'w-10 h-10 transition-all duration-200',
                                            isDragging ? 'text-primary scale-110' : 'text-muted-foreground'
                                        ]" />
                                        <div class="text-center">
                                            <p class="text-sm font-medium">
                                                Seret file ke sini atau
                                                <span class="text-primary underline underline-offset-2">klik untuk memilih</span>
                                            </p>
                                            <p class="mt-1 text-xs text-muted-foreground">XLS / XLSX &bull; Maks. 2MB</p>
                                        </div>
                                    </div>

                                    <!-- File selected state -->
                                    <div
                                        v-else
                                        key="file-preview"
                                        class="flex items-center gap-3 rounded-xl border-2 border-success/40 bg-success/5 p-4">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-success/10">
                                            <FileSpreadsheet class="h-5 w-5 text-success" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium">{{ excelFile.name }}</p>
                                            <p class="text-xs text-muted-foreground">{{ formatFileSize(excelFile.size) }}</p>
                                        </div>
                                        <button
                                            type="button"
                                            @click.stop="excelFile = null"
                                            class="shrink-0 rounded-md p-1 hover:bg-muted transition-colors">
                                            <X class="h-4 w-4 text-muted-foreground" />
                                        </button>
                                    </div>
                                </Transition>
                            </div>
                            <AlertDialogFooter>
                                <AlertDialogCancel @click="excelFile = null">Batal</AlertDialogCancel>
                                <AlertDialogAction
                                    type="button"
                                    :disabled="!excelFile"
                                    @click="submitImport">
                                    Upload
                                </AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </template>
            </PageHeader>

            <TableFilters
                v-model:search-value="searchQuery"
                search-placeholder="Cari nama atau email..."
                :filters="filterConfigs"
                :filter-values="activeFilters"
                @filter-change="setFilter"
                @clear-all-filters="clearAllFilters" />

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
                    <span class="text-muted-foreground font-medium">{{ getRowNumber(index) }}</span>
                </template>
                <template #roles="{ item }">
                    <div class="flex flex-wrap justify-center gap-1">
                        <Badge
                            v-for="role in item.roles"
                            :key="role.id"
                            variant="primary-outline"
                            class="capitalize">
                            {{ role.role_name }}
                        </Badge>
                    </div>
                </template>
                <template #actions="{ item }">
                    <div class="flex justify-center gap-3">
                        <TextLink
                            :href="route('admin.users.show', item.id)"
                            :tooltip="`Lihat ${item.name}`">
                            <Eye class="w-4 h-4 text-primary" />
                        </TextLink>
                        <TextLink
                            :href="route('admin.users.edit', item.id)"
                            :tooltip="`Edit ${item.name}`">
                            <Pencil class="w-4 h-4 text-warning" />
                        </TextLink>

                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <div class="inline-flex">
                                        <ConfirmDialog
                                            title="Reset Password"
                                            confirm-text="Ya, Reset Password"
                                            content-class="sm:max-w-md"
                                            :confirm-class="buttonVariants({ variant: 'ghost' })"
                                            :on-confirm="() => confirmResetPassword(item.id)">
                                            <template #trigger>
                                                <button class="text-secondary hover:opacity-70 transition-opacity">
                                                    <KeyRound class="w-4 h-4" />
                                                </button>
                                            </template>
                                            <template #icon>
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                                    <KeyRound class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                                </div>
                                            </template>
                                            <template #description>
                                                <AlertDialogDescription class="text-justify">
                                                    Password <span class="font-semibold text-foreground">{{ item.name }}</span> akan direset ke nomor induk. Pengguna perlu login ulang.
                                                </AlertDialogDescription>
                                            </template>
                                        </ConfirmDialog>
                                    </div>
                                </TooltipTrigger>
                                <TooltipContent><p>Reset Password</p></TooltipContent>
                            </Tooltip>
                        </TooltipProvider>

                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <div class="inline-flex">
                                        <ConfirmDialog
                                            title="Hapus User"
                                            confirm-text="Ya, Hapus"
                                            content-class="sm:max-w-md"
                                            :confirm-class="buttonVariants({ variant: 'destructive' })"
                                            :on-confirm="() => confirmDelete(item.id)">
                                            <template #trigger>
                                                <button class="text-destructive hover:opacity-70 transition-opacity">
                                                    <Trash2 class="w-4 h-4" />
                                                </button>
                                            </template>
                                            <template #icon>
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                                                    <Trash2 class="h-5 w-5 text-destructive" />
                                                </div>
                                            </template>
                                            <template #description>
                                                <AlertDialogDescription class="text-justify">
                                                    Akun <span class="font-semibold text-foreground">{{ item.name }}</span> akan dihapus permanen.
                                                    <span class="text-destructive font-semibold"> Tindakan ini tidak dapat dibatalkan.</span>
                                                </AlertDialogDescription>
                                            </template>
                                        </ConfirmDialog>
                                    </div>
                                </TooltipTrigger>
                                <TooltipContent><p>Hapus User</p></TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </template>
            </DataTable>
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
