<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import FormInput from '@/components/FormInput.vue';
import FormSelect from '@/components/FormSelect.vue';
import DataTable from '@/components/DataTable.vue';
import TableFilters from '@/components/FilterTable.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import {
    AlertDialog, AlertDialogContent, AlertDialogHeader, AlertDialogTitle,
    AlertDialogDescription, AlertDialogFooter, AlertDialogCancel,
} from '@/components/ui/alert-dialog';
import { TooltipProvider, Tooltip, TooltipTrigger, TooltipContent } from '@/components/ui/tooltip';
import { route } from 'ziggy-js';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { buttonVariants } from '@/components/ui/button';
import { Pencil, Trash2, FilePlus } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useTable } from '@/composables/useTable';
import type { MataKuliah } from '@/types/user';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Mata Kuliah', href: route('admin.mk.index') },
];

interface Pagination<T> {
    data: T[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number
    to: number
    links: { url: string | null; label: string; active: boolean }[]
}

interface Filters {
    search: string
    status_mk: string | null
    sort_by: string
    sort_direction: 'asc' | 'desc'
    per_page: number
}

interface Props {
    mk: Pagination<MataKuliah>
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
    handlePerPageChange,
} = useTable({
    endpoint: route('admin.mk.index'),
    initialFilters: props.filters,
});

const columns = [
    { key: 'number', label: '#', sortable: false, class: 'w-[5%]',  cellClass: 'text-center', align: 'center' as const },
    { key: 'kode_mk', label: 'Kode MK', sortable: true,  class: 'w-[13%]' },
    { key: 'nama_mk', label: 'Nama MK', sortable: true,  class: 'w-[37%]' },
    { key: 'sks', label: 'SKS', sortable: true,  class: 'w-[8%]',  cellClass: 'text-center', align: 'center' as const },
    { key: 'status_mk', label: 'Status', sortable: false, class: 'w-[15%]', cellClass: 'text-center', align: 'center' as const },
    { key: 'actions', label: 'Opsi', sortable: false, class: 'w-[10%]', cellClass: 'justify-center', align: 'center' as const },
];

const filterConfigs = computed(() => [
    {
        key: 'status_mk',
        label: 'Status',
        options: [
            { value: 'aktif', label: 'Aktif' },
            { value: 'tidak aktif', label: 'Tidak Aktif' },
        ],
        clearLabel: 'Semua Status',
    },
]);

const paginationData = computed(() => ({
    currentPage: props.mk.current_page,
    total: props.mk.total,
    perPage: props.mk.per_page,
    lastPage: props.mk.last_page,
    from: props.mk.from,
    to: props.mk.to,
}));

const getRowNumber = (index: number) => props.mk.from + index;

// Create modal
const showCreate = ref(false);
const createForm = useForm({
    nama_mk: '',
    kode_mk: '',
    sks: '' as string | number,
    status_mk: 'aktif',
});

const hasCreateData = computed(() => !!createForm.nama_mk);

const submitCreate = () => {
    createForm.post(route('admin.mk.store'), {
        preserveScroll: true,
        onSuccess: () => { createForm.reset(); showCreate.value = false; },
        onError: () => toast.error('Gagal menyimpan.', { description: 'Periksa kembali data yang diisi.' }),
    });
};

// Edit modal
const showEdit   = ref(false);
const editingId  = ref<number | null>(null);
const editingName = ref('');
const editForm = useForm({
    nama_mk:   '',
    kode_mk:   '',
    sks:       '' as string | number,
    status_mk: 'aktif',
});

const openEdit = (item: MataKuliah) => {
    editingId.value = item.id;
    editingName.value = item.nama_mk;
    editForm.nama_mk = item.nama_mk;
    editForm.kode_mk = item.kode_mk || '';
    editForm.sks = item.sks ?? '';
    editForm.status_mk = item.status_mk;
    showEdit.value = true;
};

const submitEdit = () => {
    editForm.put(route('admin.mk.update', editingId.value!), {
        preserveScroll: true,
        onSuccess: () => { showEdit.value = false; },
        onError: () => toast.error('Gagal memperbarui.', { description: 'Periksa kembali data yang diisi.' }),
    });
};

// Delete
const confirmDelete = (id: number) => {
    router.delete(route('admin.mk.delete', id), { preserveScroll: true });
};
</script>

<template>
    <Head title="Daftar Mata Kuliah" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader title="Daftar Mata Kuliah" description="Kelola mata kuliah yang ditawarkan program studi.">
                <template #actions>
                    <Button size="sm" class="h-9 flex items-center gap-2" @click="showCreate = true">
                        <FilePlus class="w-4 h-4" />
                        Tambah Mata Kuliah
                    </Button>
                </template>
            </PageHeader>
            <!-- Table -->
            <TableFilters
                v-model:search-value="searchQuery"
                search-placeholder="Cari nama atau kode MK..."
                :filters="filterConfigs"
                :filter-values="activeFilters"
                @filter-change="setFilter"
                @clear-all-filters="clearAllFilters" />
            <DataTable
                :columns="columns"
                :data="mk.data"
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
                <template #kode_mk="{ item }">
                    <span class="font-mono text-sm">{{ item.kode_mk || '—' }}</span>
                </template>
                <template #sks="{ item }">
                    <span class="font-semibold tabular-nums">{{ item.sks ?? '—' }}</span>
                </template>
                <template #status_mk="{ item }">
                    <Badge :variant="item.status_mk === 'aktif' ? 'primary-outline' : 'destructive-outline'" class="capitalize">
                        {{ item.status_mk }}
                    </Badge>
                </template>
                <template #actions="{ item }">
                    <div class="flex justify-center gap-3">
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <button
                                        class="text-warning hover:opacity-70 transition-opacity"
                                        @click="openEdit(item)">
                                        <Pencil class="w-4 h-4" />
                                    </button>
                                </TooltipTrigger>
                                <TooltipContent><p>Edit Mata Kuliah</p></TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <div class="inline-flex">
                                        <ConfirmDialog
                                            title="Hapus Mata Kuliah"
                                            confirm-text="Ya, Hapus"
                                            :confirm-class="buttonVariants({ variant: 'destructive' })"
                                            :on-confirm="() => confirmDelete(item.id)">
                                            <template #trigger>
                                                <button class="text-destructive hover:opacity-70 transition-opacity">
                                                    <Trash2 class="w-4 h-4" />
                                                </button>
                                            </template>
                                            <template #icon>
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-destructive/10">
                                                    <Trash2 class="h-5 w-5 text-destructive" />
                                                </div>
                                            </template>
                                            <template #description>
                                                <AlertDialogDescription class="text-justify">
                                                    Hapus mata kuliah
                                                    <span class="font-semibold text-foreground">{{ item.nama_mk }}</span>?
                                                    <span class="text-destructive font-semibold"> Tindakan ini tidak dapat dibatalkan.</span>
                                                </AlertDialogDescription>
                                            </template>
                                        </ConfirmDialog>
                                    </div>
                                </TooltipTrigger>
                                <TooltipContent><p>Hapus Mata Kuliah</p></TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </template>
            </DataTable>
        </div>
    </AppLayout>

    <!-- Section: Create Modal -->
    <AlertDialog :open="showCreate" @update:open="(v) => { if (!v) showCreate = false }">
        <AlertDialogContent class="sm:max-w-md">
            <AlertDialogHeader>
                <div class="flex items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <FilePlus class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <AlertDialogTitle>Tambah Mata Kuliah</AlertDialogTitle>
                </div>
            </AlertDialogHeader>
            <AlertDialogDescription class="text-justify">
                Isi data mata kuliah baru yang akan ditawarkan dalam program studi.
            </AlertDialogDescription>
            <form @submit.prevent="submitCreate" novalidate class="flex flex-col gap-4">
                <FormInput label="Nama Mata Kuliah" v-model="createForm.nama_mk" :error="createForm.errors.nama_mk" />
                <div class="grid grid-cols-2 gap-3">
                    <FormInput label="Kode MK" v-model="createForm.kode_mk" placeholder="Contoh: MK001" :error="createForm.errors.kode_mk" />
                    <FormInput label="SKS" type="number" v-model="createForm.sks" placeholder="0" :error="createForm.errors.sks" />
                </div>
                <FormSelect
                    label="Status"
                    v-model="createForm.status_mk"
                    :options="[{ label: 'Aktif', value: 'aktif' }, { label: 'Tidak Aktif', value: 'tidak aktif' }]"
                    :modal="false"
                    :error="createForm.errors.status_mk" />
                <AlertDialogFooter>
                    <AlertDialogCancel>Batal</AlertDialogCancel>
                    <Button type="submit" :disabled="createForm.processing || !hasCreateData">Simpan</Button>
                </AlertDialogFooter>
            </form>
        </AlertDialogContent>
    </AlertDialog>

    <!-- Section: Edit Modal -->
    <AlertDialog :open="showEdit" @update:open="(v) => { if (!v) showEdit = false }">
        <AlertDialogContent class="sm:max-w-md">
            <AlertDialogHeader>
                <div class="flex items-center gap-3">
                    <Pencil class="h-4 w-4 warning" />
                    <AlertDialogTitle>Edit Mata Kuliah</AlertDialogTitle>
                </div>
            </AlertDialogHeader>
            <AlertDialogDescription class="text-justify">
                Perbarui data mata kuliah
                <span class="font-semibold text-foreground">{{ editingName }}</span>.
            </AlertDialogDescription>
            <form @submit.prevent="submitEdit" novalidate class="flex flex-col gap-4">
                <FormInput label="Nama Mata Kuliah" v-model="editForm.nama_mk" :error="editForm.errors.nama_mk" />
                <div class="grid grid-cols-2 gap-3">
                    <FormInput label="Kode MK" v-model="editForm.kode_mk" placeholder="Contoh: MK001" :error="editForm.errors.kode_mk" />
                    <FormInput label="SKS" type="number" v-model="editForm.sks" :error="editForm.errors.sks" />
                </div>
                <FormSelect
                    label="Status"
                    v-model="editForm.status_mk"
                    :options="[{ label: 'Aktif', value: 'aktif' }, { label: 'Tidak Aktif', value: 'tidak aktif' }]"
                    :modal="false"
                    :error="editForm.errors.status_mk" />
                <AlertDialogFooter>
                    <AlertDialogCancel>Batal</AlertDialogCancel>
                    <Button type="submit" :disabled="editForm.processing">Simpan</Button>
                </AlertDialogFooter>
            </form>
        </AlertDialogContent>
    </AlertDialog>
</template>
