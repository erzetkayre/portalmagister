<script setup lang="ts">
import { computed } from 'vue';
import {
    Input, Button,
    DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger
} from '@/components/ui';
import { Search, Filter, X } from 'lucide-vue-next';

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

const props = defineProps<{
    searchValue: string;
    searchPlaceholder?: string;
    filters: FilterConfig[];
    filterValues: Record<string, string>;
}>();

const emit = defineEmits<{
    'update:searchValue': [value: string];
    filterChange: [key: string, value: string];
    clearAllFilters: [];
}>();

const hasActiveFilters = computed(() => {
    const hasSearch = !!props.searchValue;
    const hasFilters = Object.values(props.filterValues).some(v => v);
    return hasSearch || hasFilters;
});

const getCurrentFilter = (key: string) => {
    return props.filterValues[key] || '';
};

const getFilterLabel = (key: string) => {
    const value = getCurrentFilter(key);
    if (!value) return '';

    const filter = props.filters.find(f => f.key === key);
    const option = filter?.options.find(opt => opt.value === value);
    return option?.label || value;
};

const handleFilterChange = (key: string, value: string) => {
    emit('filterChange', key, value);
};

const handleClearFilter = (key: string) => {
    emit('filterChange', key, '');
};
</script>

<template>
    <div class="space-y-3 sm:space-y-0">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1 sm:max-w-sm">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                    :model-value="searchValue"
                    @update:model-value="$emit('update:searchValue', $event)"
                    :placeholder="searchPlaceholder || 'Cari...'"
                    class="pl-9"
                />
            </div>
            <Button
                v-if="hasActiveFilters"
                variant="ghost"
                size="sm"
                @click="$emit('clearAllFilters')"
                class="hidden sm:inline-flex"
            >
                <X class="h-4 w-4 mr-2" />
                Hapus Semua Filter
            </Button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <template v-for="filter in filters" :key="filter.key">
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button
                            variant="outline"
                            size="sm"
                            class="border-dashed"
                        >
                            <Filter class="h-4 w-4 mr-2 flex-shrink-0" />
                            <span class="hidden sm:inline">{{ filter.label }}</span>
                            <span class="sm:hidden">{{ filter.label.split(' ')[0] }}</span>
                            <span
                                v-if="getCurrentFilter(filter.key)"
                                class="ml-2 bg-primary text-primary-foreground px-2 py-0.5 rounded-full text-xs font-medium"
                            >
                                {{ getFilterLabel(filter.key) }}
                            </span>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="start" class="w-48">
                        <DropdownMenuItem
                            @click="handleClearFilter(filter.key)"
                            :class="!getCurrentFilter(filter.key) && 'bg-muted'"
                        >
                            {{ filter.clearLabel || `Semua ${filter.label}` }}
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            v-for="option in filter.options"
                            :key="option.value"
                            @click="handleFilterChange(filter.key, option.value)"
                            :class="getCurrentFilter(filter.key) === option.value && 'bg-muted'"
                        >
                            {{ option.label }}
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </template>
            <Button
                v-if="hasActiveFilters"
                variant="ghost"
                size="sm"
                @click="$emit('clearAllFilters')"
                class="sm:hidden"
            >
                <X class="h-4 w-4 mr-2" />
                Hapus Filter
            </Button>
        </div>
    </div>
</template>
