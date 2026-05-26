<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import FormInput from '@/components/FormInput.vue';
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
import { Button } from '@/components/ui/button';
import { buttonVariants } from '@/components/ui/button';
import { Pencil, Trash2, SquarePlus } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useTable } from '@/composables/useTable';
import type { Ruang } from '@/types/user';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Ruang', href: route('admin.ruang.index') },
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
    sort_by: string
    sort_direction: 'asc' | 'desc'
    per_page: number
}

interface Props {
    ruang: Pagination<Ruang>
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
    endpoint: route('admin.ruang.index'),
    initialFilters: props.filters,
});

const columns = [
    { key: 'number', label: '#', sortable: false, class: 'w-[5%]',  cellClass: 'text-center', align: 'center' as const },
    { key: 'nama_ruang', label: 'Nama Ruang', sortable: true,  class: 'w-[80%]' },
    { key: 'actions', label: 'Opsi', sortable: false, class: 'w-[15%]', cellClass: 'justify-center', align: 'center' as const },
];

const paginationData = computed(() => ({
    currentPage: props.ruang.current_page,
    total: props.ruang.total,
    perPage: props.ruang.per_page,
    lastPage: props.ruang.last_page,
    from: props.ruang.from,
    to: props.ruang.to,
}));

const getRowNumber = (index: number) => props.ruang.from + index;

// Create modal
const showCreate = ref(false);
const createForm = useForm({ nama_ruang: '' });

const hasCreateData = computed(() => !!createForm.nama_ruang);

const submitCreate = () => {
    createForm.post(route('admin.ruang.store'), {
        preserveScroll: true,
        onSuccess: () => { createForm.reset(); showCreate.value = false; },
        onError: () => toast.error('Gagal menyimpan.', { description: 'Periksa kembali data yang diisi.' }),
    });
};

// Edit modal
const showEdit = ref(false);
const editingId = ref<number | null>(null);
const editingName = ref('');
const editForm = useForm({ nama_ruang: '' });

const openEdit = (item: Ruang) => {
    editingId.value = item.id;
    editingName.value = item.nama_ruang;
    editForm.nama_ruang = item.nama_ruang;
    showEdit.value = true;
};

const submitEdit = () => {
    editForm.put(route('admin.ruang.update', editingId.value!), {
        preserveScroll: true,
        onSuccess: () => { showEdit.value = false; },
        onError: () => toast.error('Gagal memperbarui.', { description: 'Periksa kembali data yang diisi.' }),
    });
};

// Delete
const confirmDelete = (id: number) => {
    router.delete(route('admin.ruang.delete', id), { preserveScroll: true });
};
</script>

<template>
    <Head title="Daftar Ruang" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader title="Daftar Ruang" description="Kelola ruang yang tersedia untuk kegiatan akademik.">
                <template #actions>
                    <Button size="sm" class="h-9 flex items-center gap-2" @click="showCreate = true">
                        <SquarePlus class="w-4 h-4" />
                        Tambah Ruang
                    </Button>
                </template>
            </PageHeader>
            <!-- Table -->
            <TableFilters
                v-model:search-value="searchQuery"
                search-placeholder="Cari nama ruang..."
                :filters="[]"
                :filter-values="activeFilters"
                @filter-change="setFilter"
                @clear-all-filters="clearAllFilters" />
            <DataTable
                :columns="columns"
                :data="ruang.data"
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
                                <TooltipContent><p>Edit Ruang</p></TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <div class="inline-flex">
                                        <ConfirmDialog
                                            title="Hapus Ruang"
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
                                                    Hapus ruang
                                                    <span class="font-semibold text-foreground">{{ item.nama_ruang }}</span>?
                                                    <span class="text-destructive font-semibold"> Tindakan ini tidak dapat dibatalkan.</span>
                                                </AlertDialogDescription>
                                            </template>
                                        </ConfirmDialog>
                                    </div>
                                </TooltipTrigger>
                                <TooltipContent><p>Hapus Ruang</p></TooltipContent>
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
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <SquarePlus class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <AlertDialogTitle>Tambah Ruang</AlertDialogTitle>
                </div>
            </AlertDialogHeader>
            <AlertDialogDescription class="text-justify">
                Isi nama ruang baru yang tersedia untuk ujian atau kegiatan akademik.
            </AlertDialogDescription>
            <form @submit.prevent="submitCreate" novalidate class="flex flex-col gap-4">
                <FormInput label="Nama Ruang" v-model="createForm.nama_ruang" placeholder="Contoh: Ruang A101" :error="createForm.errors.nama_ruang" />
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
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/30">
                        <Pencil class="h-5 w-5 text-orange-500 dark:text-orange-400" />
                    </div>
                    <AlertDialogTitle>Edit Ruang</AlertDialogTitle>
                </div>
            </AlertDialogHeader>
            <AlertDialogDescription class="text-justify">
                Perbarui nama ruang
                <span class="font-semibold text-foreground">{{ editingName }}</span>.
            </AlertDialogDescription>
            <form @submit.prevent="submitEdit" novalidate class="flex flex-col gap-4">
                <FormInput label="Nama Ruang" v-model="editForm.nama_ruang" :error="editForm.errors.nama_ruang" />
                <AlertDialogFooter>
                    <AlertDialogCancel>Batal</AlertDialogCancel>
                    <Button type="submit" :disabled="editForm.processing">Simpan</Button>
                </AlertDialogFooter>
            </form>
        </AlertDialogContent>
    </AlertDialog>
</template>
