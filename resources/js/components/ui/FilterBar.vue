
<script setup lang="ts">
import { computed } from 'vue';
import {
    Input, Button,
    DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger
} from '@/components/ui';
import { Search, Filter } from 'lucide-vue-next';

interface FilterOption {
    value: string;
    label: string;
}

interface FilterConfig {
    key: string;
    label: string;
    options: FilterOption[];
    clearLabel?: string;
}

interface Props {
    searchValue: string;
    searchPlaceholder?: string;
    filters: FilterConfig[];
    filterValues: Record<string, string>;
}

interface Emits {
    (e: 'update:searchValue', value: string): void;
    (e: 'filterChange', key: string, value: string): void;
    (e: 'clearAllFilters'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const getFilterValue = (key: string) => {
    return props.filterValues[key] || '';
};

const getFilterDisplayValue = (key: string) => {
    const value = getFilterValue(key);
    const filter = props.filters.find(f => f.key === key);
    const option = filter?.options.find(opt => opt.value === value);
    return option?.label || value;
};

const setFilter = (key: string, value: string) => {
    emit('filterChange', key, value);
};

const clearFilter = (key: string) => {
    emit('filterChange', key, '');
};

const hasActiveFilters = computed(() => {
    return props.searchValue || Object.values(props.filterValues).some(value => value);
});
</script>
<template>
    <div class="flex items-center space-x-4 mb-4">
        <!-- Search Input -->
        <div class="flex items-center space-x-2">
            <Search class="h-4 w-4 text-gray-500" />
            <Input
                :model-value="searchValue"
                @update:model-value="$emit('update:searchValue', $event)"
                :placeholder="searchPlaceholder"
                class="w-64"
            />
        </div>

        <!-- Filter Dropdowns -->
        <template v-for="filter in filters" :key="filter.key">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="border-dashed">
                        <Filter class="h-4 w-4 mr-2" />
                        {{ filter.label }}
                        <span
                            v-if="getFilterValue(filter.key)"
                            class="ml-2 bg-primary text-primary-foreground px-2 py-1 rounded-full text-xs"
                        >
                            {{ getFilterDisplayValue(filter.key) }}
                        </span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                    <DropdownMenuItem @click="clearFilter(filter.key)">
                        {{ filter.clearLabel || `Semua ${filter.label}` }}
                    </DropdownMenuItem>
                    <DropdownMenuItem
                        v-for="option in filter.options"
                        :key="option.value"
                        @click="setFilter(filter.key, option.value)"
                    >
                        {{ option.label }}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </template>

        <!-- Clear All Filters -->
        <Button
            variant="ghost"
            @click="$emit('clearAllFilters')"
            v-if="hasActiveFilters"
        >
            Hapus Filter
        </Button>
    </div>
</template>
