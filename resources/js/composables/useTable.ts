import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

interface UseTableOptions {
    endpoint: string;
    initialFilters?: Record<string, any>;
    debounceMs?: number;
}

export function useTable(options: UseTableOptions) {
    const {
        endpoint,
        initialFilters = {},
        debounceMs = 300
    } = options;

    const searchQuery = ref(initialFilters.search || '');
    const filters = ref<Record<string, any>>({
        per_page: initialFilters.per_page ?? 10,
    })
    const sortBy = ref(initialFilters.sort_by || 'created_at');
    const sortDirection = ref<'asc' | 'desc'>(initialFilters.sort_direction || 'asc');

    const fetchData = (options: {
        preserveScroll?: boolean;
        page?: number;
    } = {}) => {
        const params: Record<string, any> = {
            search: searchQuery.value,
            sort_by: sortBy.value,
            sort_direction: sortDirection.value,
        };

        Object.entries(filters.value).forEach(([key, value]) => {
            if (value !== null && value !== '' && value !== undefined) {
                params[key] = value;
            }
        });

        if (options.page) {
            params.page = options.page;
        }

        router.get(endpoint, params, {
            preserveState: true,
            preserveScroll: options.preserveScroll ?? true,
        });
    };

    const handlePerPageChange = (value: number) => {
        filters.value.per_page = value
        fetchData({ page: 1 })
    }

    let searchTimeout: number | null = null;
    watch(searchQuery, () => {
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchData();
        }, debounceMs);
    });

    const setFilter = (key: any, value: any) => {
        filters.value = { ...filters.value, [key]: value };
        fetchData();
    };

    const clearAllFilters = () => {
        searchQuery.value = '';
        filters.value = {
            per_page: filters.value.per_page ?? 10,
        };
        sortBy.value = 'created_at';
        sortDirection.value = 'asc';
        fetchData({ page: 1 });
    };

    const handleSort = (column: string) => {
        if (sortBy.value === column) {
            sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
        } else {
            sortBy.value = column;
            sortDirection.value = 'asc';
        }
        fetchData({ preserveScroll: false });
    };

    const handlePageChange = (page: number) => {
        fetchData({ preserveScroll: false, page });
    };

    return {
        searchQuery,
        filters,
        sortBy,
        sortDirection,

        setFilter,
        clearAllFilters,
        handleSort,
        handlePageChange,
        handlePerPageChange,
        fetchData,
    };
}
