<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import DataTable from '@/components/DataTable.vue';
import TableFilters from '@/components/FilterTable.vue';
import TextLink from '@/components/TextLink.vue';
import { route } from 'ziggy-js';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Pencil, Eye } from 'lucide-vue-next';
import { computed } from 'vue';
import { useTable } from '@/composables/useTable';
import type { Dosen } from '@/types/user';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dosen', href: route('admin.dosen.index') },
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
    status_dsn: string | null
    sort_by: string
    sort_direction: 'asc' | 'desc'
    per_page: number
}

interface Props {
    dosen: Pagination<Dosen>
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
    endpoint: route('admin.dosen.index'),
    initialFilters: props.filters,
});

const columns = [
    { key: 'number', label: '#', sortable: false, class: 'w-[5%]', cellClass: 'text-center', align: 'center' as const },
    { key: 'kode_dsn', label: 'Kode Dosen', sortable: true,  class: 'w-[12%]' },
    { key: 'nama_dsn', label: 'Nama', sortable: false, class: 'w-[30%]' },
    { key: 'nip', label: 'NIP', sortable: true,  class: 'w-[22%]' },
    { key: 'status_dsn', label: 'Status', sortable: true,  class: 'w-[15%]', cellClass: 'text-center', align: 'center' as const },
    { key: 'actions', label: 'Opsi', sortable: false, class: 'w-[10%]', cellClass: 'justify-center', align: 'center' as const },
];

const filterConfigs = computed(() => [
    {
        key: 'status_dsn',
        label: 'Status',
        options: [
            { value: 'aktif', label: 'Aktif' },
            { value: 'tidak aktif', label: 'Tidak Aktif' },
        ],
        clearLabel: 'Semua Status',
    },
]);

const paginationData = computed(() => ({
    currentPage: props.dosen.current_page,
    total: props.dosen.total,
    perPage: props.dosen.per_page,
    lastPage: props.dosen.last_page,
    from: props.dosen.from,
    to: props.dosen.to,
}));

const getRowNumber = (index: number) => props.dosen.from + index;
</script>

<template>
    <Head title="Daftar Dosen" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader title="Daftar Dosen" description="Kelola data dosen dan status keaktifan." />
            <TableFilters
                v-model:search-value="searchQuery"
                search-placeholder="Cari nama, NIP, atau kode..."
                :filters="filterConfigs"
                :filter-values="activeFilters"
                @filter-change="setFilter"
                @clear-all-filters="clearAllFilters" />
            <DataTable
                :columns="columns"
                :data="dosen.data"
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
                <template #kode_dsn="{ item }">
                    <span class="font-mono text-sm">{{ item.kode_dsn || '—' }}</span>
                </template>
                <template #status_dsn="{ item }">
                    <Badge :variant="item.status_dsn === 'aktif' ? 'default' : 'destructive'" class="capitalize">
                        {{ item.status_dsn }}
                    </Badge>
                </template>
                <template #actions="{ item }">
                    <div class="flex justify-center gap-3">
                        <TextLink
                            :href="route('admin.dosen.show', item.id)"
                            :tooltip="`Lihat Dosen`">
                            <Eye class="w-4 h-4 text-primary" />
                        </TextLink>
                        <TextLink
                            :href="route('admin.dosen.edit', item.id)"
                            :tooltip="`Edit Dosen`">
                            <Pencil class="w-4 h-4 text-warning" />
                        </TextLink>
                    </div>
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>
