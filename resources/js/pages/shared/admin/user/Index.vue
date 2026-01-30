<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PlaceholderPattern from '../../../../components/PlaceholderPattern.vue';
import Heading from '../../../../components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge'
import { useAuth } from '@/composables/useAuth';

import { computed } from 'vue';
import DataTable from '@/components/DataTable.vue';
import TableFilters from '@/components/FilterTable.vue';
import { useTable } from '@/composables/useTable';

interface User {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
    nomor_induk: string;
    created_at: string | null;
}

interface UsersPagination {
    data: User[];
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
    users: UsersPagination;
    filters: Filters;
}

interface Props {
    users: UsersPagination;
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
    { key: 'number', label: 'No', sortable: false, class: 'w-16' },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'nomor_induk', label: 'NIM', sortable: true },
    { key: 'is_active', label: 'Status', sortable: true, class: 'w-32' },
    { key: 'created_at', label: 'Dibuat', sortable: true, class: 'w-40' },
];

const filterConfigs = [
    {
        key: 'is_active',
        label: 'Status',
        options: [
            { value: 1, label: 'Aktif' },
            { value: 0, label: 'Tidak Aktif' }
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
</script>

<template>
    <Head title="Daftar User" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <h1 class="text-2xl font-bold text-gray-900">Daftar User</h1>
                        </div>
                        <TableFilters
                            v-model:search-value="searchQuery"
                            search-placeholder="Cari nama atau email..."
                            :filters="filterConfigs"
                            :filter-values="activeFilters"
                            @filter-change="setFilter"
                            @clear-all-filters="clearAllFilters"
                        />

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
                            @per-page-change="handlePerPageChange"
                        >
                            <!-- Slot: Auto Numbering -->
                            <template #number="{ index }">
                                <span class="text-gray-600 font-medium">
                                    {{ getRowNumber(index) }}
                                </span>
                            </template>

                            <!-- Slot: Status Badge -->
                            <template #is_active="{ value }">
                                <span
                                    :class="[
                                        'px-2 py-1 rounded-full text-xs font-medium',
                                        value
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    ]"
                                >
                                    {{ value ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
</template>
