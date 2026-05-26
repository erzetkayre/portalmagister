<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import DataTable from '@/components/DataTable.vue';
import TableFilters from '@/components/FilterTable.vue';
import TextLink from '@/components/TextLink.vue';
import FormSelect from '@/components/FormSelect.vue';
import { AlertDialog, AlertDialogContent, AlertDialogHeader, AlertDialogTitle,
    AlertDialogDescription, AlertDialogFooter, AlertDialogCancel,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { route } from 'ziggy-js';
import { type BreadcrumbItem } from '@/types';
import { type DosenOption, type Mahasiswa } from '@/types/user';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Badge } from '@/components/ui/badge';
import { TooltipProvider, Tooltip, TooltipTrigger, TooltipContent } from '@/components/ui/tooltip';
import { Pencil, Eye, SquarePen } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useTable } from '@/composables/useTable';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Mahasiswa', href: route('admin.mahasiswa.index') },
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
    status_mhs: string | null
    angkatan: string | null
    sort_by: string
    sort_direction: 'asc' | 'desc'
    per_page: number
}

interface Props {
    mahasiswa: Pagination<Mahasiswa>
    filters: Filters
    dosenOptions: DosenOption[]
    angkatanOptions: { value: string; label: string }[]
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
    endpoint: route('admin.mahasiswa.index'),
    initialFilters: props.filters,
});

const columns = [
    { key: 'number', label: '#', sortable: false, class: 'w-[5%]',  cellClass: 'text-center', align: 'center' as const },
    { key: 'nim', label: 'NIM', sortable: true,  class: 'w-[12%]' },
    { key: 'nama_mhs', label: 'Nama Mahasiswa', sortable: false, class: 'w-[30%]' },
    { key: 'angkatan', label: 'Angkatan', sortable: true,  class: 'w-[16%]', cellClass: 'text-center', align: 'center' as const },
    { key: 'status_mhs', label: 'Status', sortable: true,  class: 'w-[15%]', cellClass: 'text-center', align: 'center' as const },
    { key: 'actions', label: 'Opsi', sortable: false, class: 'w-[12%]', cellClass: 'justify-center', align: 'center' as const },
];

const filterConfigs = computed(() => [
    {
        key: 'status_mhs',
        label: 'Status',
        options: [
            { value: 'aktif', label: 'Aktif' },
            { value: 'lulus', label: 'Lulus' },
            { value: 'dropout', label: 'Dropout' },
        ],
        clearLabel: 'Semua Status',
    },
    {
        key: 'angkatan',
        label: 'Angkatan',
        options: props.angkatanOptions,
        clearLabel: 'Semua Angkatan',
    },
]);

const paginationData = computed(() => ({
    currentPage: props.mahasiswa.current_page,
    total: props.mahasiswa.total,
    perPage: props.mahasiswa.per_page,
    lastPage: props.mahasiswa.last_page,
    from: props.mahasiswa.from,
    to: props.mahasiswa.to,
}));

const getRowNumber = (index: number) => props.mahasiswa.from + index;

const statusVariant = (status: string) => {
    if (status === 'aktif')   return 'default';
    if (status === 'lulus')   return 'success';
    return 'destructive';
};

// PA Modal
const selectedMhs = ref<Mahasiswa | null>(null);
const paForm = useForm({ pem_akademik: '' });

const openPaDialog = (item: Mahasiswa) => {
    selectedMhs.value = item;
    paForm.reset();
};

const submitPa = () => {
    if (!selectedMhs.value) return;
    paForm.post(route('admin.mahasiswa.pem.akademik', selectedMhs.value.id), {
        preserveScroll: true,
        onSuccess: () => { selectedMhs.value = null; },
        onError: () => toast.error('Gagal menyimpan.', { description: 'Periksa kembali data yang diisi.' }),
    });
};
</script>

<template>
    <Head title="Daftar Mahasiswa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader title="Daftar Mahasiswa" description="Data mahasiswa aktif program studi." />

            <TableFilters
                v-model:search-value="searchQuery"
                search-placeholder="Cari nama atau NIM..."
                :filters="filterConfigs"
                :filter-values="activeFilters"
                @filter-change="setFilter"
                @clear-all-filters="clearAllFilters" />

            <DataTable
                :columns="columns"
                :data="mahasiswa.data"
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

                <template #angkatan="{ item }">
                    <span class="text-sm">{{ item.angkatan ?? '—' }}</span>
                </template>

                <template #status_mhs="{ item }">
                    <Badge :variant="statusVariant(item.status_mhs)" class="capitalize">
                        {{ item.status_mhs }}
                    </Badge>
                </template>

                <template #actions="{ item }">
                    <div class="flex justify-center gap-3">
                        <TextLink
                            :href="route('admin.mahasiswa.show', item.id)"
                            tooltip="Lihat Mahasiswa">
                            <Eye class="w-4 h-4 text-primary" />
                        </TextLink>
                        <TextLink
                            :href="route('admin.mahasiswa.edit', item.id)"
                            tooltip="Edit Mahasiswa">
                            <Pencil class="w-4 h-4 text-warning" />
                        </TextLink>
                        <TooltipProvider v-if="item.pem_akademik === null">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <button
                                        class="text-secondary hover:opacity-70 transition-opacity"
                                        @click="openPaDialog(item)">
                                        <SquarePen class="w-4 h-4" />
                                    </button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Tambah Pembimbing Akademik</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </template>
            </DataTable>
        </div>
    </AppLayout>

    <!-- PA Assignment -->
    <AlertDialog :open="selectedMhs !== null" @update:open="(v) => { if (!v) selectedMhs = null }">
        <AlertDialogContent class="sm:max-w-md">
            <AlertDialogHeader>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <SquarePen class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <AlertDialogTitle>Tambah Pembimbing Akademik</AlertDialogTitle>
                </div>
            </AlertDialogHeader>
            <AlertDialogDescription class="text-justify">
                Pilih dosen pembimbing akademik untuk
                <span class="font-semibold text-foreground">{{ selectedMhs?.nama_mhs }}</span>.
            </AlertDialogDescription>
            <FormSelect
                label="Pembimbing Akademik"
                v-model="paForm.pem_akademik"
                placeholder="Pilih dosen..."
                :options="dosenOptions.map(d => ({ label: d.label, value: String(d.id) }))"
                :error="paForm.errors.pem_akademik"
                :modal="false" />
            <AlertDialogFooter>
                <AlertDialogCancel>Batal</AlertDialogCancel>
                <Button type="button" :disabled="paForm.processing || !paForm.pem_akademik" @click="submitPa">Simpan</Button>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
