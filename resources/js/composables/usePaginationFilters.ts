import { router } from '@inertiajs/vue3';
import { Ref } from 'vue';

interface Filters {
    search?: Ref<string>;
    role?: Ref<string>;
    status?: Ref<string>;
}

export function usePaginationFilters(filters: Filters, baseUrl: string) {
    const buildFilters = (page?: number) => {
        const params: Record<string, any> = {};
        if (page) params.page = page;
        if (filters.search?.value) params.search = filters.search.value;
        if (filters.role?.value) params.role = filters.role.value;
        if (filters.status?.value) params.status = filters.status.value;
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
