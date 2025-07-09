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
interface Mahasiswa {
    id: number;
    name: string;
    nim: string;
    angkatan: number;
    gender: string;
    status: 'active' | 'inactive';
    email: string;
    created_at: string;
}

interface PaginatedMahasiswa {
    data: Mahasiswa[];
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
    mahasiswa: PaginatedMahasiswa;
    angkatan: number[];
    gender: string[];
    filters: {
        search?: string;
        angkatan?: number;
        status?: string;
        gender?: string;
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
const selectedAngkatan = ref(props.filters.angkatan ? props.filters.angkatan.toString() : '');
const selectedStatus = ref(props.filters.status || '');
const selectedGender = ref(props.filters.gender || '');
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
    form.post('/admin/mahasiswa/import', {
        onSuccess: () => {
            isDialogOpen.value = false;
            form.reset();
        },
    });
};

// Filtering
const { applyFilters, clearFilters, goToPage } = usePaginationFilters(
        { search: searchTerm, angkatan: selectedAngkatan, status: selectedStatus, gender: selectedGender, sort: sortBy, sortDirection: sortDirection },
        '/admin/mahasiswa'
    );
const debouncedSearchTerm = useDebounce(searchTerm, 300);
watch(debouncedSearchTerm, () => applyFilters());

// Filter Configuration
const filterConfig = ref([
    {
        key: 'angkatan',
        label: 'Angkatan',
        options: props.angkatan.map(angkatan => ({ value: angkatan.toString(), label: angkatan.toString() })),
        clearLabel: 'Semua Angkatan'
    },
    {
        key: 'gender',
        label: 'Gender',
        options: [
            { value: 'L', label: 'Laki-laki' },
            { value: 'P', label: 'Perempuan' }
        ],
        clearLabel: 'Semua Gender'
    },
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
    if (key === 'angkatan') {
        selectedAngkatan.value = value;
    } else if (key === 'gender') {
        selectedGender.value = value;
    } else if (key === 'status') {
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
const showMahasiswa = (mahasiswaId: number) => { router.get(`/admin/mahasiswa/${mahasiswaId}`) };
const editMahasiswa = (mahasiswaId: number) => { router.get(`/admin/mahasiswa/${mahasiswaId}/edit`) };

const confirmDeleteMahasiswa = () => {
    if (selectedMahasiswaId.value) {
        router.delete(`/admin/mahasiswa/${selectedMahasiswaId.value}`, {
            onSuccess: () => {
                deleteMahasiswaDialog.value = false;
                selectedMahasiswaId.value = null;
                showSuccessAlert('Mahasiswa Berhasil Dihapus', 'Mahasiswa telah dihapus secara permanen dari sistem.');
            },
            onError: (errors) => {
                deleteMahasiswaDialog.value = false;
                selectedMahasiswaId.value = null;
                let errorMessage = 'Terjadi kesalahan saat menghapus mahasiswa';
                if (errors?.error) {
                    errorMessage = errors.error;
                } else if (typeof errors === 'string') {
                    errorMessage = errors;
                }
                showErrorAlert('Gagal Menghapus Mahasiswa', errorMessage);
            },
            preserveState: true,
            preserveScroll: true
        });
    }
};

// Confirmation dialog states
const deleteMahasiswaDialog = ref(false);
const selectedMahasiswaId = ref<number | null>(null);

const openDeleteMahasiswaDialog = (mahasiswaId: number) => {
    selectedMahasiswaId.value = mahasiswaId;
    deleteMahasiswaDialog.value = true;
};

// Table Configuration
const tableColumns = ref([
    { key: 'no', label: 'No', class: 'text-center', sortable: false },
    { key: 'name', label: 'Nama', sortable: true },
    { key: 'nim', label: 'NIM', sortable: true },
    { key: 'angkatan', label: 'Angkatan', sortable: true },
    { key: 'gender', label: 'Gender', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'created_at', label: 'Dibuat Pada', sortable: true },
    { key: 'actions', label: 'Tombol Aksi', sortable: false }
]);

// Mahasiswa Actions Configuration
const mahasiswaActions = ref([
    { key: 'view', icon: Eye, tooltip: 'Lihat Detail'},
    { key: 'edit', icon: Edit, tooltip: 'Edit Mahasiswa'},
    { key: 'delete', icon: Trash2, tooltip: 'Hapus Mahasiswa', class: 'text-destructive hover:text-destructive' },
]);
const handleMahasiswaAction = (actionKey: string, mahasiswa: Mahasiswa) => {
    switch (actionKey) {
        case 'view':
            showMahasiswa(mahasiswa.id);
            break;
        case 'edit':
            editMahasiswa(mahasiswa.id);
            break;
        case 'delete':
            openDeleteMahasiswaDialog(mahasiswa.id);
            break;
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Manajemen Mahasiswa', href: '/admin/mahasiswa' },
];

</script>

<template>
    <Head title="Manajemen Mahasiswa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Alert Notification -->
            <AlertNotification :show="showAlert" :type="alertType" :title="alertTitle" :description="alertDescription" @close="closeAlert"/>

            <div class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border shadow-xs">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Daftar Mahasiswa</h3>
                        <div class="items-center space-x-2">
                            <!-- <Button @click="router.get('/admin/mahasiswa/create')">
                                <Plus class="h-4 w-4" />
                                Tambah Mahasiswa
                            </Button> -->
                            <Dialog v-model:open="isDialogOpen">
                                <DialogTrigger as-child>
                                    <Button variant="outline">
                                        <Upload class="h-4 w-4" />
                                        Import Mahasiswa
                                    </Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>Import Mahasiswa dari Excel</DialogTitle>
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
                                            <p class="text-sm text-muted-foreground mt-1">Format: Nama | NIM | Email | Angkatan | Gender (L/P)</p>
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
                        search-placeholder="Cari nama atau NIM..."
                        :filters="filterConfig"
                        :filter-values="{ angkatan: selectedAngkatan, gender: selectedGender, status: selectedStatus }"
                        @filter-change="handleFilterChange"
                        @clear-all-filters="clearFilters"
                    />

                    <DataTable
                        :columns="tableColumns"
                        :data="props.mahasiswa.data"
                        :pagination="{
                            currentPage: props.mahasiswa.current_page,
                            from: props.mahasiswa.from,
                            to: props.mahasiswa.to,
                            total: props.mahasiswa.total,
                            lastPage: props.mahasiswa.last_page,
                            perPage: props.mahasiswa.per_page,
                            prevPageUrl: props.mahasiswa.prev_page_url,
                            nextPageUrl: props.mahasiswa.next_page_url
                        }"
                        item-key="id"
                        :sort-by="sortBy"
                        :sort-direction="sortDirection"
                        @page-change="goToPage"
                        @sort="handleSort"
                    >
                        <template #no="{ index }">
                            <div class="text-center">
                                {{ (props.mahasiswa.current_page - 1) * props.mahasiswa.per_page + index + 1 }}
                            </div>
                        </template>

                        <template #name="{ item }">
                            <div class="font-medium">{{ item.name }}</div>
                        </template>

                        <template #nim="{ item }">
                            <div class="font-mono">{{ item.nim }}</div>
                        </template>

                        <template #angkatan="{ item }">
                            <Badge variant="outline">{{ item.angkatan }}</Badge>
                        </template>

                        <template #gender="{ item }">
                            <Badge variant="outline">{{ item.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</Badge>
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
                                :actions="mahasiswaActions"
                                :item="item"
                                @action="handleMahasiswaAction"
                            />
                        </template>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Delete Mahasiswa Confirmation Dialog -->
        <ConfirmationDialog
            v-model:open="deleteMahasiswaDialog"
            title="Hapus Mahasiswa"
            description="Apakah Anda yakin ingin menghapus mahasiswa ini? Tindakan ini tidak dapat dibatalkan."
            :icon="Trash2"
            icon-class="h-5 w-5 text-destructive"
            confirm-text="Hapus Mahasiswa"
            :confirm-icon="Trash2"
            confirm-variant="destructive"
            :warning="{
                title: 'Peringatan: Tindakan ini permanen',
                message: 'Semua data mahasiswa dan record terkait akan dihapus secara permanen.'
            }"
            :warning-icon="Trash2"
            @confirm="confirmDeleteMahasiswa"
            @cancel="deleteMahasiswaDialog = false"
        />
    </AppLayout>
</template>
