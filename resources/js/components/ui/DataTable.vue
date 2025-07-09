
<script setup lang="ts">
import {
    Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
    Pagination, PaginationContent, PaginationItem, PaginationNext, PaginationPrevious, PaginationEllipsis
} from '@/components/ui';

interface Column {
    key: string;
    label: string;
    class?: string;
    cellClass?: string;
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
}

interface Emits {
    (e: 'pageChange', page: number): void;
}

const props = defineProps<Props>();
defineEmits<Emits>();

const getItemKey = (item: any, index: number) => {
    return props.itemKey ? item[props.itemKey] : index;
};

const getNestedValue = (obj: any, path: string) => {
    return path.split('.').reduce((current, prop) => current?.[prop], obj);
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
                            :class="column.class"
                        >
                            {{ column.label }}
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
