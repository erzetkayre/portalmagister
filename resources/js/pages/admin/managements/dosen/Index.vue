<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch, onMounted } from 'vue';
import { Head, useForm, router, usePage} from '@inertiajs/vue3';

import { useAlert } from '@/composables/UseAlert';
import { useDebounce } from '@/composables/useDebounce';
import { usePaginationFilters } from '@/composables/usePaginationFilters';

import { type BreadcrumbItem } from '@/types';

import {
    Badge, Button, AlertNotification, DataTable, FilterBar, ActionButtons, ConfirmationDialog,
    Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, Input
} from '@/components/ui';

import {
    Plus, Eye, Edit, Trash2, Upload
 } from 'lucide-vue-next';

// interface
interface Dosen {
    id: number;
    name: string;
    nip: string;
    kode_dosen: string;
    status: 'active' | 'inactive';
    email: string;
    created_at: string;
}

interface PaginatedDosen {
    data: Dosen[];
    current_page: number;
    from: number;
    to: number;
    total: number;
    last_page: number;
    per_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface Props {
    dosen: PaginatedDosen;
    filters: {
        search?: string;
        status?: string;
    };
}

// Composables
const { showAlert, alertType, alertTitle, alertDescription, showSuccessAlert, showErrorAlert, closeAlert } = useAlert();
const props = defineProps<Props>();
const page = usePage();

onMounted(() => {
    if (page.props.flash?.message) {
        const msg = page.props.flash.message;
        if (msg.type === 'success') {
            showSuccessAlert(msg.title, msg.message);
        } else if (msg.type === 'error') {
            showErrorAlert(msg.title, msg.message);
        }
    }
});

// States
const isDialogOpen = ref(false);
const searchTerm = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');
const sortBy = ref('created_at');
const sortDirection = ref('desc');
const form = useForm({ file: null as File | null, });

// Handle Import Excel
const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.file = target.files[0] || null;
    }
};
const submitImport = () => {
    form.post('/admin/dosen/import', {
        onSuccess: () => {
            isDialogOpen.value = false;
            form.reset();
        },
    });
};

// Filtering
const { applyFilters, clearFilters, goToPage } = usePaginationFilters(
        { search: searchTerm, status: selectedStatus, sort: sortBy, sortDirection: sortDirection },
        '/admin/dosen'
    );
const debouncedSearchTerm = useDebounce(searchTerm, 300);
watch(debouncedSearchTerm, () => applyFilters());

// Filter Configuration
const filterConfig = ref([
    {
        key: 'status',
        label: 'Status',
        options: [
            { value: 'active', label: 'Aktif' },
            { value: 'inactive', label: 'Tidak Aktif' }
        ],
        clearLabel: 'Semua Status'
    }
]);

const handleFilterChange = (key: string, value: string) => {
    if (key === 'status') {
        selectedStatus.value = value;
    }
    applyFilters();
};

const handleSort = (column: string) => {
    if (sortBy.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = column;
        sortDirection.value = 'asc';
    }
    applyFilters();
};

// Action
const showDosen = (dosenId: number) => { router.get(`/admin/dosen/${dosenId}`) };
const editDosen = (dosenId: number) => { router.get(`/admin/dosen/${dosenId}/edit`) };

const confirmDeleteDosen = () => {
    if (selectedDosenId.value) {
        router.delete(`/admin/dosen/${selectedDosenId.value}`, {
            onSuccess: () => {
                deleteDosenDialog.value = false;
                selectedDosenId.value = null;
                showSuccessAlert('Dosen Berhasil Dihapus', 'Dosen telah dihapus secara permanen dari sistem.');
            },
            onError: (errors) => {
                deleteDosenDialog.value = false;
                selectedDosenId.value = null;
                let errorMessage = 'Terjadi kesalahan saat menghapus dosen';
                if (errors?.error) {
                    errorMessage = errors.error;
                } else if (typeof errors === 'string') {
                    errorMessage = errors;
                }
                showErrorAlert('Gagal Menghapus Dosen', errorMessage);
            },
            preserveState: true,
            preserveScroll: true
        });
    }
};

// Confirmation dialog states
const deleteDosenDialog = ref(false);
const selectedDosenId = ref<number | null>(null);

const openDeleteDosenDialog = (dosenId: number) => {
    selectedDosenId.value = dosenId;
    deleteDosenDialog.value = true;
};

// Table Configuration
const tableColumns = ref([
    { key: 'no', label: 'No', class: 'text-center', sortable: false },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'nip', label: 'NIP', sortable: true },
    { key: 'kode_dosen', label: 'Kode Dosen', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'created_at', label: 'Dibuat Pada', sortable: true },
    { key: 'actions', label: 'Tombol Aksi', sortable: false }
]);

// Dosen Actions Configuration
const dosenActions = ref([
    { key: 'view', icon: Eye, tooltip: 'Lihat Detail'},
    { key: 'edit', icon: Edit, tooltip: 'Edit Dosen'},
    { key: 'delete', icon: Trash2, tooltip: 'Hapus Dosen', class: 'text-destructive hover:text-destructive' },
]);
const handleDosenAction = (actionKey: string, dosen: Dosen) => {
    switch (actionKey) {
        case 'view':
            showDosen(dosen.id);
            break;
        case 'edit':
            editDosen(dosen.id);
            break;
        case 'delete':
            openDeleteDosenDialog(dosen.id);
            break;
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Manajemen Dosen', href: '/admin/dosen' },
];

</script>

<template>
    <Head title="Manajemen Dosen" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Alert Notification -->
            <AlertNotification :show="showAlert" :type="alertType" :title="alertTitle" :description="alertDescription" @close="closeAlert"/>

            <div class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border shadow-xs">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Daftar Dosen</h3>
                        <div class="items-center space-x-2">
                            <!-- <Button @click="router.get('/admin/dosen/create')">
                                <Plus class="h-4 w-4" />
                                Tambah Dosen
                            </Button> -->
                            <Dialog v-model:open="isDialogOpen">
                                <DialogTrigger as-child>
                                    <Button variant="outline">
                                        <Upload class="h-4 w-4" />
                                        Import Dosen
                                    </Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>Import Dosen dari Excel</DialogTitle>
                                    </DialogHeader>
                                    <form @submit.prevent="submitImport" class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium mb-2">Pilih File Excel (.xlsx)</label>
                                            <Input
                                            type="file"
                                            accept=".xlsx,.xls"
                                            @change="handleFileUpload"
                                            class="cursor-pointer"
                                            />
                                            <p class="text-sm text-muted-foreground mt-1">Format: Nama | NIP | Email | Kode Dosen</p>
                                        </div>
                                        <div class="flex justify-end space-x-2">
                                            <Button type="button" variant="outline" @click="isDialogOpen = false">
                                                Batal
                                            </Button>
                                            <Button type="submit" :disabled="!form.file || form.processing">
                                                <Upload class="h-4 w-4" />
                                                Import
                                            </Button>
                                        </div>
                                    </form>
                                </DialogContent>
                            </Dialog>
                        </div>
                    </div>

                    <!-- Filters -->
                    <FilterBar
                        v-model:searchValue="searchTerm"
                        search-placeholder="Cari nama, NIP, atau kode dosen..."
                        :filters="filterConfig"
                        :filter-values="{ status: selectedStatus }"
                        @filter-change="handleFilterChange"
                        @clear-all-filters="clearFilters"
                    />

                    <DataTable
                        :columns="tableColumns"
                        :data="props.dosen.data"
                        :pagination="{
                            currentPage: props.dosen.current_page,
                            from: props.dosen.from,
                            to: props.dosen.to,
                            total: props.dosen.total,
                            lastPage: props.dosen.last_page,
                            perPage: props.dosen.per_page,
                            prevPageUrl: props.dosen.prev_page_url,
                            nextPageUrl: props.dosen.next_page_url
                        }"
                        item-key="id"
                        :sort-by="sortBy"
                        :sort-direction="sortDirection"
                        @page-change="goToPage"
                        @sort="handleSort"
                    >
                        <template #no="{ index }">
                            <div class="text-center">
                                {{ (props.dosen.current_page - 1) * props.dosen.per_page + index + 1 }}
                            </div>
                        </template>

                        <template #name="{ item }">
                            <div class="font-medium">{{ item.name }}</div>
                        </template>

                        <template #nip="{ item }">
                            <div class="font-mono">{{ item.nip }}</div>
                        </template>

                        <template #kode_dosen="{ item }">
                            <Badge variant="outline">{{ item.kode_dosen }}</Badge>
                        </template>

                        <template #status="{ item }">
                            <Badge
                                variant="outline"
                                :class="item.status === 'active' ? 'border-success text-success dark:border-success/60 dark:text-success/90 hover:bg-success/10' : 'border-destructive text-destructive dark:border-destructive/60 dark:text-destructive/90 hover:bg-destructive/10'"
                            >
                                {{ item.status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                            </Badge>
                        </template>

                        <template #created_at="{ item }">
                            {{ new Date(item.created_at).toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            }) }}
                        </template>

                        <template #actions="{ item }">
                            <ActionButtons
                                :actions="dosenActions"
                                :item="item"
                                @action="handleDosenAction"
                            />
                        </template>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Delete Dosen Confirmation Dialog -->
        <ConfirmationDialog
            v-model:open="deleteDosenDialog"
            title="Hapus Dosen"
            description="Apakah Anda yakin ingin menghapus dosen ini? Tindakan ini tidak dapat dibatalkan."
            :icon="Trash2"
            icon-class="h-5 w-5 text-destructive"
            confirm-text="Hapus Dosen"
            :confirm-icon="Trash2"
            confirm-variant="destructive"
            :warning="{
                title: 'Peringatan: Tindakan ini permanen',
                message: 'Semua data dosen dan record terkait akan dihapus secara permanen.'
            }"
            :warning-icon="Trash2"
            @confirm="confirmDeleteDosen"
            @cancel="deleteDosenDialog = false"
        />
    </AppLayout>
</template>
