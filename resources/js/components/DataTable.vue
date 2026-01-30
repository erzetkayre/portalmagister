<script setup lang="ts">
import {
    Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
    Pagination, PaginationContent, PaginationItem, PaginationNext,
    PaginationPrevious, PaginationEllipsis,
} from '@/components/ui';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ChevronUp, ChevronDown } from 'lucide-vue-next';

// ============================================
// TYPES
// ============================================
interface Column {
    key: string;
    label: string;
    sortable?: boolean;
    class?: string;
    cellClass?: string;
}

interface PaginationData {
    currentPage: number;
    total: number;
    perPage: number;
    lastPage: number;
    from: number;
    to: number;
    prevPageUrl: string | null;
    nextPageUrl: string | null;
}

// ============================================
// PROPS & EMITS
// ============================================
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

// ============================================
// METHODS
// ============================================
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
    if (column.sortable) {
        emit('sort', column.key);
    }
};

const handlePageChange = (page: number) => {
    if (page >= 1 && page <= props.pagination.lastPage) {
        emit('pageChange', page);
    }
};

const handlePerPageChange = (value: string) => {
    emit('perPageChange', Number(value));
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
                            :class="[
                                column.class,
                                column.sortable && 'cursor-pointer select-none hover:bg-muted/50'
                            ]"
                            @click="handleSort(column)"
                        >
                            <div class="flex items-center gap-2">
                                <span>{{ column.label }}</span>
                                <component
                                    :is="getSortIcon(column)"
                                    v-if="getSortIcon(column)"
                                    class="h-4 w-4 flex-shrink-0"
                                />
                            </div>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="(item, index) in data"
                        :key="getRowKey(item, index)"
                    >
                        <TableCell
                            v-for="column in columns"
                            :key="column.key"
                            :class="column.cellClass"
                        >
                            <slot
                                :name="column.key"
                                :item="item"
                                :index="index"
                                :value="getValue(item, column.key)"
                            >
                                {{ getValue(item, column.key) }}
                            </slot>
                        </TableCell>
                    </TableRow>

                    <!-- Empty State -->
                    <TableRow v-if="!data.length">
                        <TableCell
                            :colspan="columns.length"
                            class="text-center py-8 text-muted-foreground"
                        >
                            Tidak ada data
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Pagination & Controls -->
        <div class="flex items-center justify-between pt-4 border-t min-h-[40px]">
            <!-- Left: Row per page (FIXED WIDTH) -->
            <div class="flex items-center gap-2 w-[180px] flex-shrink-0">
                <span class="text-xs text-muted-foreground whitespace-nowrap">
                    Row per page
                </span>
                <Select
                    :model-value="String(pagination.perPage)"
                    @update:model-value="handlePerPageChange"
                >
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

            <!-- Right: Pagination (ALWAYS RENDERED) -->
            <div class="flex-shrink-0">
                <Pagination
                    :items-per-page="pagination.perPage"
                    :total="pagination.total"
                    :default-page="pagination.currentPage"
                >
                    <PaginationContent>
                        <PaginationPrevious
                            @click="handlePageChange(pagination.currentPage - 1)"
                            :class="{ 'pointer-events-none opacity-50': pagination.currentPage === 1 }"
                        />

                        <!-- Hanya tampilkan page numbers jika lastPage > 1 -->
                        <template v-if="pagination.lastPage > 1">
                            <PaginationItem
                                v-for="pageNum in Math.min(pagination.lastPage, 5)"
                                :key="pageNum"
                                :value="pageNum"
                                :is-active="pageNum === pagination.currentPage"
                                @click="handlePageChange(pageNum)"
                            >
                                {{ pageNum }}
                            </PaginationItem>

                            <PaginationEllipsis v-if="pagination.lastPage > 5" />
                        </template>

                        <PaginationNext
                            @click="handlePageChange(pagination.currentPage + 1)"
                            :class="{ 'pointer-events-none opacity-50': pagination.currentPage === pagination.lastPage }"
                        />
                    </PaginationContent>
                </Pagination>
            </div>
        </div>
    </div>
</template>
