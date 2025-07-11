<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch, onMounted, computed } from 'vue';
import { Head, useForm, router, usePage} from '@inertiajs/vue3';

import { useAlert } from '@/composables/UseAlert';
import { useDebounce } from '@/composables/useDebounce';
import { usePaginationFilters } from '@/composables/usePaginationFilters';

import { type BreadcrumbItem } from '@/types';

import {
    Badge, Button, AlertNotification, DataTable, FilterBar, ActionButtons, Dialog, DialogContent, DialogHeader, DialogTitle, Input, Label, Textarea, Separator
} from '@/components/ui';

import {
    FileText, CheckCircle, Eye, FileDown, Upload, X
} from 'lucide-vue-next';

interface Draft {
    id: number;
    us_judul: string;
    us_abstrak: string;
    status: string;
    tgl_pengajuan: string;
    file_sk_pembimbing: string;
    file_khs: string;
    file_krs: string;
    ket_dospem_satu: string;
    ket_dospem_dua: string;
    mahasiswa: {
        nim: string;
        nama_mhs: string;
        user: {
            name: string;
        };
    };
    dosen_pembimbing_satu: {
        nama_dosen: string;
        user: {
            name: string;
        };
    };
    dosen_pembimbing_dua: {
        nama_dosen: string;
        user: {
            name: string;
        };
    };
    status_badge: {
        text: string;
        class: string;
    };
}

interface PaginatedDrafts {
    data: Draft[];
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
    drafts: PaginatedDrafts;
    filters: {
        search?: string;
        status?: string;
    };
}

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

const searchTerm = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');
const sortBy = ref('created_at');
const sortDirection = ref('desc');

const { applyFilters, clearFilters, goToPage } = usePaginationFilters(
    { search: searchTerm, status: selectedStatus, sort: sortBy, sortDirection: sortDirection },
    '/admin/draft'
);
const debouncedSearchTerm = useDebounce(searchTerm, 300);
watch(debouncedSearchTerm, () => applyFilters());

const filterConfig = ref([
    {
        key: 'status',
        label: 'Status',
        options: [
            { value: 'pending', label: 'WAITING' },
            { value: 'approved', label: 'SETUJU' },
            { value: 'rejected', label: 'TOLAK' }
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

// PDF Dialog
const showPdfModal = ref(false);
const pdfUrl = ref('');
const pdfTitle = ref('');

function openPdfModal(url: string, title: string) {
    pdfUrl.value = url;
    pdfTitle.value = title;
    showPdfModal.value = true;
}

function closePdfModal() {
    showPdfModal.value = false;
    pdfUrl.value = '';
    pdfTitle.value = '';
}

// Selected draft for approval
const selectedDraft = ref<Draft | null>(null);

// Approval Dialog
const showApprovalModal = ref(false);
const approvalForm = useForm({
    action: 'approve',
    file_sk_pembimbing: null as File | null,
    alasan_penolakan: ''
});

function openApprovalModal(draft: Draft) {
    selectedDraft.value = draft;
    showApprovalModal.value = true;
    approvalForm.reset();
}

function closeApprovalModal() {
    showApprovalModal.value = false;
    selectedDraft.value = null;
    approvalForm.reset();
}

function handleFileUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        approvalForm.file_sk_pembimbing = target.files[0];
    }
}

function submitApproval() {
    if (!selectedDraft.value) return;

    approvalForm.post(`/admin/draft/${selectedDraft.value.id}`, {
        onSuccess: () => {
            closeApprovalModal();
            showSuccessAlert('Sukses', 'Draft pratesis berhasil disetujui');
        },
        onError: (errors) => {
            showErrorAlert('Error', 'Terjadi kesalahan saat memproses persetujuan');
        }
    });
}

function rejectDraft() {
    if (!selectedDraft.value) return;

    approvalForm.action = 'reject';
    approvalForm.post(`/admin/draft/${selectedDraft.value.id}`, {
        onSuccess: () => {
            closeApprovalModal();
            showSuccessAlert('Sukses', 'Draft pratesis berhasil ditolak');
        },
        onError: (errors) => {
            showErrorAlert('Error', 'Terjadi kesalahan saat menolak draft');
        }
    });
}

// Action handlers
const showDraft = (draft: Draft) => router.get(`/admin/draft/${draft.id}`);
const showKRS = (draft: Draft) => openPdfModal(`/storage/file_lampiran_krs/${draft.mahasiswa.nim}_file_krs.pdf`, 'File KRS');
const showKHS = (draft: Draft) => openPdfModal(`/storage/file_lampiran_khs/${draft.mahasiswa.nim}_file_khs.pdf`, 'File KHS');


// Table Configuration
const tableColumns = ref([
    { key: 'no', label: 'No', class: 'text-center', sortable: false },
    { key: 'nama', label: 'Nama', sortable: true },
    { key: 'nim', label: 'NIM', sortable: true },
    { key: 'us_judul', label: 'Usulan Judul', sortable: true },
    { key: 'us_dospem_satu', label: 'Usulan Pem 1', sortable: false },
    { key: 'us_dospem_dua', label: 'Usulan Pem 2', sortable: false },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'actions', label: 'Aksi', class: 'text-center', sortable: false }
]);

// Draft Actions Configuration
const getDraftActions = (item: Draft) => {
    const baseActions = [
        { key: 'show', icon: Eye, tooltip: 'Lihat Detail'},
        { key: 'show-krs', icon: FileDown, tooltip: 'Lihat KRS'},
        { key: 'show-khs', icon: FileDown, tooltip: 'Lihat KHS'},
    ];

    // Only add approval button if status is pending
    if (item.status === 'pending') {
        baseActions.push({ key: 'approve', icon: CheckCircle, tooltip: 'Persetujuan', class: 'text-green-600 hover:text-green-800' });
    }

    return baseActions;
};

const handleDraftAction = (actionKey: string, draft: Draft) => {
    switch (actionKey) {
        case 'show':
            showDraft(draft);
            break;
        case 'show-krs':
            showKRS(draft);
            break;
        case 'show-khs':
            showKHS(draft);
            break;
        case 'approve':
            openApprovalModal(draft);
            break;
    }
};


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Draft Pratesis', href: '/admin/draft' },
];

</script>

<template>
    <Head title="Draft Pratesis" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Alert Notification -->
            <AlertNotification :show="showAlert" :type="alertType" :title="alertTitle" :description="alertDescription" @close="closeAlert"/>

            <div class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border shadow-xs">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Daftar Draft Pratesis</h3>
                    </div>

                    <!-- Filters -->
                    <FilterBar
                        v-model:searchValue="searchTerm"
                        search-placeholder="Cari nama atau NIM..."
                        :filters="filterConfig"
                        :filter-values="{ status: selectedStatus }"
                        @filter-change="handleFilterChange"
                        @clear-all-filters="clearFilters"
                    />

                    <DataTable
                        :columns="tableColumns"
                        :data="props.drafts.data"
                        :pagination="{
                            currentPage: props.drafts.current_page,
                            from: props.drafts.from,
                            to: props.drafts.to,
                            total: props.drafts.total,
                            lastPage: props.drafts.last_page,
                            perPage: props.drafts.per_page,
                            prevPageUrl: props.drafts.prev_page_url,
                            nextPageUrl: props.drafts.next_page_url
                        }"
                        item-key="id"
                        :sort-by="sortBy"
                        :sort-direction="sortDirection"
                        show-pagination
                        @page-change="goToPage"
                        @sort="handleSort"
                    >
                        <template #no="{ index }">
                            <div class="text-center">
                                {{ (props.drafts.current_page - 1) * props.drafts.per_page + index + 1 }}
                            </div>
                        </template>

                        <template #nama="{ item }">
                            <div class="font-medium">{{ item.mahasiswa.user.name }}</div>
                        </template>

                        <template #nim="{ item }">
                            <div class="font-mono">{{ item.mahasiswa.nim }}</div>
                        </template>

                        <template #us_judul="{ item }">
                            <div class="max-w-xs truncate text-center" :title="item.us_judul">
                                {{ item.us_judul }}
                            </div>
                        </template>

                        <template #us_dospem_satu="{ item }">
                            <div class="text-sm">{{ item.dosen_pembimbing_satu.user.name }}</div>
                        </template>

                        <template #us_dospem_dua="{ item }">
                            <div class="text-sm">{{ item.dosen_pembimbing_dua.user.name }}</div>
                        </template>

                        <template #status="{ item }">
                            <Badge
                                variant="outline"
                                :class="item.status_badge.class"
                                class="text-center"
                            >
                                {{ item.status_badge.text }}
                            </Badge>
                        </template>

                        <template #actions="{ item }">
                            <div class="flex justify-center">
                                <ActionButtons
                                    :actions="getDraftActions(item)"
                                    :item="item"
                                    @action="handleDraftAction"
                                />
                            </div>
                        </template>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Large PDF Modal -->
        <teleport to="body">
            <div v-if="showPdfModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80">
                <div class="relative bg-white rounded-lg shadow-xl w-[95vw] max-w-6xl h-[90vh] flex flex-col">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-4 border-b bg-white rounded-t-lg">
                        <h2 class="text-lg font-semibold text-gray-800">{{ pdfTitle }}</h2>
                        <button @click="closePdfModal" class="text-gray-500 hover:text-gray-700 p-1">
                            <X class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Content -->
                    <div class="flex-1 overflow-hidden">
                        <iframe
                            :src="pdfUrl"
                            class="w-full h-full"
                            frameborder="0"
                        ></iframe>
                    </div>
                </div>
            </div>
        </teleport>

        <!-- Approval Modal -->
        <Dialog v-model:open="showApprovalModal">
            <DialogContent class="!max-w-3xl">
                <DialogHeader>
                    <DialogTitle>Kelola Persetujuan Draft Pratesis</DialogTitle>
                </DialogHeader>
                <div v-if="selectedDraft" class="space-y-6">
                    <!-- Draft Summary -->
                    <div class="p-4 bg-muted/50 rounded-lg">
                        <h3 class="font-medium mb-2">Ringkasan Draft</h3>
                        <div class="space-y-1 text-sm">
                            <p><strong>Mahasiswa:</strong> {{ selectedDraft.mahasiswa.user.name }} ({{ selectedDraft.mahasiswa.nim }})</p>
                            <p><strong>Judul:</strong> {{ selectedDraft.us_judul }}</p>
                            <p><strong>Pembimbing 1:</strong> {{ selectedDraft.dosen_pembimbing_satu.user.name }}</p>
                            <p><strong>Pembimbing 2:</strong> {{ selectedDraft.dosen_pembimbing_dua.user.name }}</p>
                        </div>
                    </div>

                    <Separator />

                    <!-- Form -->
                    <div class="grid gap-6">
                        <!-- Approval Section -->
                        <div class="space-y-4">
                            <div>
                                <Label class="text-base font-medium text-success">Persetujuan</Label>
                                <p class="text-sm text-muted-foreground mb-3">Upload SK Pembimbing untuk menyetujui draft</p>
                                <div class="space-y-2">
                                    <Label for="sk_pembimbing" class="text-sm">SK Pembimbing (PDF)</Label>
                                    <Input
                                        id="sk_pembimbing"
                                        type="file"
                                        accept=".pdf"
                                        @change="handleFileUpload"
                                    />
                                    <p class="text-xs text-muted-foreground">
                                        File harus berformat PDF dengan ukuran maksimal 2MB
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Rejection Section -->
                        <div class="space-y-4">
                            <div>
                                <Label class="text-base font-medium text-destructive">Penolakan</Label>
                                <p class="text-sm text-muted-foreground mb-3">Berikan alasan jika menolak draft</p>
                                <div class="space-y-2">
                                    <Label for="alasan_penolakan" class="text-sm">Alasan Penolakan</Label>
                                    <Textarea
                                        id="alasan_penolakan"
                                        v-model="approvalForm.alasan_penolakan"
                                        placeholder="Masukkan alasan penolakan..."
                                        class="min-h-[100px]"
                                    />
                                    <p class="text-xs text-muted-foreground">
                                        Maksimal 500 karakter
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <Button
                            variant="outline"
                            @click="closeApprovalModal"
                            :disabled="approvalForm.processing"
                        >
                            Batal
                        </Button>
                        <Button
                            variant="destructive"
                            @click="rejectDraft"
                            :disabled="!approvalForm.alasan_penolakan || approvalForm.processing"
                        >
                            Tolak Draft
                        </Button>
                        <Button
                            @click="submitApproval"
                            :disabled="!approvalForm.file_sk_pembimbing || approvalForm.processing"
                            class="bg-green-600 hover:bg-green-700"
                        >
                            <Upload class="h-4 w-4" />
                            Setujui Draft
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
