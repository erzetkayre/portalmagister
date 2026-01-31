<script setup lang="ts">
import { computed } from 'vue';
import {
    Input, Button,
    DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger
} from '@/components/ui';
import { Search, Filter, X } from 'lucide-vue-next';

interface FilterOption {
    value: string | number;
    label: string;
}

interface FilterConfig {
    key: string;
    label: string;
    options: FilterOption[];
    clearLabel?: string;
}

const props = defineProps<{
    searchValue: string;
    searchPlaceholder?: string;
    filters: FilterConfig[];
    filterValues: Record<string, any>;
    excludeFromReset?: string[];
}>();

const emit = defineEmits<{
    'update:searchValue': [value: string | number];
    filterChange: [key: string, value: string];
    clearAllFilters: [];
}>();

const showResetButton = computed(() => {
    const hasSearch = props.searchValue?.trim().length > 0;

    const hasFilters = Object.entries(props.filterValues).some(([key, value]) => {
        const excluded = props.excludeFromReset || ['per_page'];
        if (excluded.includes(key)) return false;
        return value !== null && value !== '' && value !== undefined;
    });

    return hasSearch || hasFilters;
});

const getFilterValue = (key: string): string => {
    return props.filterValues[key] || '';
};

const getFilterLabel = (key: string): string => {
    const value = getFilterValue(key);
    if (!value) return '';

    const filter = props.filters.find(f => f.key === key);
    const option = filter?.options.find(opt => String(opt.value) === String(value));
    return option?.label || String(value);
};

const isOptionActive = (filterKey: string, optionValue: string | number): boolean => {
    return String(getFilterValue(filterKey)) === String(optionValue);
};

const handleFilterChange = (key: string, value: string | number) => {
    emit('filterChange', key, String(value));
};

const handleClearFilter = (key: string) => {
    emit('filterChange', key, '');
};
</script>

<template>
    <div class="flex flex-wrap items-center gap-2">
        <div class="relative w-full sm:w-72">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
                :model-value="searchValue"
                @update:model-value="$emit('update:searchValue', $event)"
                :placeholder="searchPlaceholder || 'Cari...'"
                class="pl-9 h-9"
            />
        </div>
        <DropdownMenu v-for="filter in filters" :key="filter.key">
            <DropdownMenuTrigger as-child>
                <Button variant="outline" size="sm" class="border-dashed h-9">
                    <Filter class="h-4 w-4" />
                    <span>{{ filter.label }}</span>
                    <span
                        v-if="getFilterValue(filter.key)"
                        class="ml-2 bg-primary text-primary-foreground px-2 py-0.5 rounded-full text-xs font-medium">
                        {{ getFilterLabel(filter.key) }}
                    </span>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="start" class="w-48">
                <DropdownMenuItem
                    @click="handleClearFilter(filter.key)"
                    :class="{ 'bg-muted': !getFilterValue(filter.key) }">
                    {{ filter.clearLabel || `Semua ${filter.label}` }}
                </DropdownMenuItem>
                <DropdownMenuItem
                    v-for="option in filter.options"
                    :key="option.value"
                    @click="handleFilterChange(filter.key, option.value)"
                    :class="{ 'bg-muted': isOptionActive(filter.key, option.value) }">
                    {{ option.label }}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
        <Button
            v-if="showResetButton"
            variant="ghost"
            size="sm"
            class="h-9"
            @click="$emit('clearAllFilters')">
            <X class="h-4 w-4" />
            Reset
        </Button>
    </div>
</template>
