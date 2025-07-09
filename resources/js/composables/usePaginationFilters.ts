import { router } from '@inertiajs/vue3';
import { Ref } from 'vue';

interface Filters {
    search?: Ref<string>;
    role?: Ref<string>;
    status?: Ref<string>;
    sort?: Ref<string>;
    sortDirection?: Ref<string>;
}

export function usePaginationFilters(filters: Filters, baseUrl: string) {
    const buildFilters = (page?: number) => {
        const params: Record<string, any> = {};
        if (page) params.page = page;
        if (filters.search?.value) params.search = filters.search.value;
        if (filters.role?.value) params.role = filters.role.value;
        if (filters.status?.value) params.status = filters.status.value;
        if (filters.sort?.value) params.sort = filters.sort.value;
        if (filters.sortDirection?.value) params.sortDirection = filters.sortDirection.value;
        return params;
    };

    const applyFilters = () => {
        router.get(baseUrl, buildFilters(), {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const clearFilters = () => {
        if (filters.search) filters.search.value = '';
        if (filters.role) filters.role.value = '';
        if (filters.status) filters.status.value = '';
        if (filters.sort) filters.sort.value = 'created_at';
        if (filters.sortDirection) filters.sortDirection.value = 'desc';
        router.get(baseUrl, {}, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const goToPage = (page: number) => {
        router.get(baseUrl, buildFilters(page), {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return {
        applyFilters,
        clearFilters,
        goToPage,
    };
}
