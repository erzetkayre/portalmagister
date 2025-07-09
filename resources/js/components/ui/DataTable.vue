
<script setup lang="ts">
import {
    Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
    Pagination, PaginationContent, PaginationItem, PaginationNext, PaginationPrevious, PaginationEllipsis
} from '@/components/ui';
import { ChevronUp, ChevronDown } from 'lucide-vue-next';

interface Column {
    key: string;
    label: string;
    class?: string;
    cellClass?: string;
    sortable?: boolean;
}

interface PaginationData {
    currentPage: number;
    from: number;
    to: number;
    total: number;
    lastPage: number;
    perPage: number;
    prevPageUrl: string | null;
    nextPageUrl: string | null;
}

interface Props {
    columns: Column[];
    data: any[];
    pagination: PaginationData;
    itemKey?: string;
    sortBy?: string;
    sortDirection?: string;
}

interface Emits {
    (e: 'pageChange', page: number): void;
    (e: 'sort', column: string): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const getItemKey = (item: any, index: number) => {
    return props.itemKey ? item[props.itemKey] : index;
};

const getNestedValue = (obj: any, path: string) => {
    return path.split('.').reduce((current, prop) => current?.[prop], obj);
};

const getSortIcon = (column: Column) => {
    if (!column.sortable) return null;
    if (props.sortBy === column.key) {
        return props.sortDirection === 'asc' ? ChevronUp : ChevronDown;
    }
    return null;
};

const handleSort = (column: Column) => {
    if (column.sortable) {
        emit('sort', column.key);
    }
};
</script>

<template>
    <div class="space-y-4">
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead
                            v-for="column in columns"
                            :key="column.key"
                            :class="[column.class, { 'cursor-pointer select-none hover:bg-muted/50': column.sortable }]"
                            @click="handleSort(column)"
                        >
                            <div class="flex items-center gap-2">
                                <span>{{ column.label }}</span>
                                <component
                                    :is="getSortIcon(column)"
                                    v-if="getSortIcon(column)"
                                    class="h-4 w-4"
                                />
                            </div>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(item, index) in data" :key="getItemKey(item, index)">
                        <TableCell
                            v-for="column in columns"
                            :key="column.key"
                            :class="column.cellClass"
                        >
                            <slot
                                :name="column.key"
                                :item="item"
                                :index="index"
                                :value="getNestedValue(item, column.key)"
                            >
                                {{ getNestedValue(item, column.key) }}
                            </slot>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between border-t pt-4">
            <div class="text-sm text-muted-foreground">
                Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total || 0 }} results
            </div>

            <Pagination
                v-if="pagination.lastPage > 1"
                v-slot="{ page }"
                :items-per-page="pagination.perPage"
                :total="pagination.total"
                :default-page="pagination.currentPage"
            >
                <PaginationContent v-slot="{ items }">
                    <PaginationPrevious
                        @click="$emit('pageChange', pagination.currentPage - 1)"
                        :class="{ 'pointer-events-none opacity-50': !pagination.prevPageUrl }"
                    />

                    <template v-for="(item, index) in items" :key="index">
                        <PaginationItem
                            v-if="item.type === 'page'"
                            :value="item.value"
                            :is-active="item.value === page"
                            @click="$emit('pageChange', item.value)"
                        >
                            {{ item.value }}
                        </PaginationItem>
                    </template>

                    <PaginationEllipsis v-if="pagination.lastPage > 7" />

                    <PaginationNext
                        @click="$emit('pageChange', pagination.currentPage + 1)"
                        :class="{ 'pointer-events-none opacity-50': !pagination.nextPageUrl }"
                    />
                </PaginationContent>
            </Pagination>
        </div>
    </div>
</template>
