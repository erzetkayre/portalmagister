<script setup lang="ts">
import {
    Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
    Pagination, PaginationContent, PaginationItem, PaginationNext,
    PaginationPrevious, PaginationEllipsis,
} from '@/components/ui';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ChevronUp, ChevronDown, Inbox } from 'lucide-vue-next';
import { computed } from 'vue';

interface Column {
    key: string;
    label: string;
    sortable?: boolean;
    class?: string;
    cellClass?: string;
    align?: 'left' | 'center' | 'right';
}

interface PaginationData {
    currentPage: number;
    total: number;
    perPage: number;
    lastPage: number;
    from: number;
    to: number;
}

const props = defineProps<{
    columns: Column[];
    data: any[];
    pagination: PaginationData;
    itemKey?: string;
    sortBy?: string;
    sortDirection?: 'asc' | 'desc';
}>();

const emit = defineEmits<{
    sort: [column: string];
    pageChange: [page: number];
    perPageChange: [value: number];
}>();

const getRowKey = (item: any, index: number) => {
    return props.itemKey ? item[props.itemKey] : index;
};

const getValue = (obj: any, path: string) => {
    return path.split('.').reduce((current, prop) => current?.[prop], obj);
};

const getSortIcon = (column: Column) => {
    if (!column.sortable || props.sortBy !== column.key) return null;
    return props.sortDirection === 'asc' ? ChevronUp : ChevronDown;
};

const handleSort = (column: Column) => {
    if (!column.sortable) return;
    emit('sort', column.key);
};

const handlePageChange = (page: number) => {
    if (page >= 1 && page <= props.pagination.lastPage) {
        emit('pageChange', page);
    }
};

const handlePerPageChange = (value: unknown) => {
    const perPage = Number(value);
    if (Number.isNaN(perPage)) return;
    emit('perPageChange', perPage);
};

const rowInfo = computed(() => {
    const p = props.pagination;
    if (!p || p.total === 0) return 'Tidak ada data';
    return `${p.from}–${p.to} dari ${p.total} data`;
});

// Sliding window pagination — always shows 5 pages centered on current page
const visiblePages = computed(() => {
    const total = props.pagination.lastPage;
    const current = props.pagination.currentPage;
    if (total <= 5) return Array.from({ length: total }, (_, i) => i + 1);
    let start = Math.max(1, current - 2);
    const end = Math.min(total, start + 4);
    start = Math.max(1, end - 4);
    return Array.from({ length: end - start + 1 }, (_, i) => start + i);
});

const showStartEllipsis = computed(() => visiblePages.value[0] > 2);
const showEndEllipsis = computed(() => {
    const last = visiblePages.value[visiblePages.value.length - 1];
    return last < props.pagination.lastPage - 1;
});

const headerAlignClass = (col: Column) => {
    if (col.align === 'center') return 'justify-center';
    if (col.align === 'right') return 'justify-end';
    return 'justify-start';
};
</script>

<template>
    <div class="space-y-4">
        <!-- Table -->
        <div class="rounded-md border overflow-x-auto">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead
                            v-for="column in columns"
                            :key="column.key"
                            :class="[column.class, column.sortable && 'cursor-pointer select-none hover:bg-muted/50']"
                            @click="handleSort(column)">
                            <div :class="['flex items-center gap-2', headerAlignClass(column)]">
                                <span>{{ column.label }}</span>
                                <component
                                    :is="getSortIcon(column)"
                                    v-if="getSortIcon(column)"
                                    class="h-4 w-4 flex-shrink-0" />
                            </div>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="(item, index) in data"
                        :key="getRowKey(item, index)">
                        <TableCell
                            v-for="column in columns"
                            :key="column.key"
                            :class="column.cellClass">
                            <slot
                                :name="column.key"
                                :item="item"
                                :index="index"
                                :value="getValue(item, column.key)">
                                {{ getValue(item, column.key) }}
                            </slot>
                        </TableCell>
                    </TableRow>
                    <!-- Empty State -->
                    <TableRow v-if="!data.length">
                        <TableCell :colspan="columns.length" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-2 text-muted-foreground">
                                <Inbox class="h-10 w-10 opacity-25" />
                                <p class="text-sm">Belum ada data.</p>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Pagination & Controls -->
        <div class="flex flex-wrap items-center justify-between pt-4 border-t min-h-[40px]">
            <div class="flex-wrap text-center">
                <span class="text-sm text-muted-foreground">{{ rowInfo }}</span>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="text-xs text-muted-foreground whitespace-nowrap">Baris per halaman</span>
                <Select
                    :model-value="String(pagination.perPage)"
                    @update:model-value="handlePerPageChange">
                    <SelectTrigger class="w-[70px] h-9">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem value="5">5</SelectItem>
                            <SelectItem value="10">10</SelectItem>
                            <SelectItem value="20">20</SelectItem>
                            <SelectItem value="30">30</SelectItem>
                            <SelectItem value="50">50</SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </div>

            <!-- Desktop pagination -->
            <div v-if="pagination.lastPage > 1" class="hidden md:flex justify-end">
                <Pagination
                    :items-per-page="pagination.perPage"
                    :total="pagination.total"
                    :default-page="pagination.currentPage">
                    <PaginationContent>
                        <PaginationPrevious
                            @click="handlePageChange(pagination.currentPage - 1)"
                            :class="{ 'pointer-events-none opacity-50': pagination.currentPage === 1 }" />

                        <PaginationItem v-if="visiblePages[0] > 1" :value="1" @click="handlePageChange(1)">1</PaginationItem>
                        <PaginationEllipsis v-if="showStartEllipsis" />

                        <PaginationItem
                            v-for="pageNum in visiblePages"
                            :key="pageNum"
                            :value="pageNum"
                            :is-active="pageNum === pagination.currentPage"
                            @click="handlePageChange(pageNum)">
                            {{ pageNum }}
                        </PaginationItem>

                        <PaginationEllipsis v-if="showEndEllipsis" />
                        <PaginationItem
                            v-if="visiblePages[visiblePages.length - 1] < pagination.lastPage"
                            :value="pagination.lastPage"
                            @click="handlePageChange(pagination.lastPage)">
                            {{ pagination.lastPage }}
                        </PaginationItem>

                        <PaginationNext
                            @click="handlePageChange(pagination.currentPage + 1)"
                            :class="{ 'pointer-events-none opacity-50': pagination.currentPage === pagination.lastPage }" />
                    </PaginationContent>
                </Pagination>
            </div>

            <!-- Mobile pagination -->
            <div v-if="pagination.lastPage > 1" class="flex justify-center w-full mt-4 md:hidden">
                <Pagination
                    :items-per-page="pagination.perPage"
                    :total="pagination.total"
                    :default-page="pagination.currentPage">
                    <PaginationContent>
                        <PaginationPrevious
                            @click="handlePageChange(pagination.currentPage - 1)"
                            :class="{ 'pointer-events-none opacity-50': pagination.currentPage === 1 }" />

                        <PaginationItem v-if="visiblePages[0] > 1" :value="1" @click="handlePageChange(1)">1</PaginationItem>
                        <PaginationEllipsis v-if="showStartEllipsis" />

                        <PaginationItem
                            v-for="pageNum in visiblePages"
                            :key="pageNum"
                            :value="pageNum"
                            :is-active="pageNum === pagination.currentPage"
                            @click="handlePageChange(pageNum)">
                            {{ pageNum }}
                        </PaginationItem>

                        <PaginationEllipsis v-if="showEndEllipsis" />
                        <PaginationItem
                            v-if="visiblePages[visiblePages.length - 1] < pagination.lastPage"
                            :value="pagination.lastPage"
                            @click="handlePageChange(pagination.lastPage)">
                            {{ pagination.lastPage }}
                        </PaginationItem>

                        <PaginationNext
                            @click="handlePageChange(pagination.currentPage + 1)"
                            :class="{ 'pointer-events-none opacity-50': pagination.currentPage === pagination.lastPage }" />
                    </PaginationContent>
                </Pagination>
            </div>
        </div>
    </div>
</template>
