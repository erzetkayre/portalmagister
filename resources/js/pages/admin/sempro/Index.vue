<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch, onMounted } from 'vue';
import { Head, useForm, router, usePage} from '@inertiajs/vue3';

import { useAlert } from '@/composables/UseAlert';
import { useDebounce } from '@/composables/useDebounce';
import { usePaginationFilters } from '@/composables/usePaginationFilters';

import { type BreadcrumbItem } from '@/types';

import {
    Badge, Button, AlertNotification, DataTable, FilterBar, ActionButtons, Dialog, DialogContent, DialogHeader, DialogTitle, Input, Label, Textarea, Separator
} from '@/components/ui';

import {
    Eye, FileDown, CheckCircle, Calendar, Mail, FileText, X, Upload, Clock, MapPin
} from 'lucide-vue-next';

interface Sempro {
    id: number;
    tesis_id: number;
    tanggal: string | null;
    tempat: string | null;
    jam_mulai: string | null;
    jam_selesai: string | null;
    status_seminar: string;
    kartu_bimbingan: string | null;
    draft_semhas: string | null;
    surat_kelayakan: string | null;
    tgl_upload_surat: string | null;
    summary: string | null;
    catatan: string | null;
    berita_acara: string | null;
    tesis: {
        us_judul: string;
        us_abstrak: string;
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
                email: string;
            };
        };
        dosen_pembimbing_dua: {
            nama_dosen: string;
            user: {
                name: string;
                email: string;
            };
        };
    };
    status_badge: {
        text: string;
        variant: string;
    };
    formatted_date: string | null;
    formatted_time: string | null;
}

interface PaginatedSempro {
    data: Sempro[];
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
    sempros: PaginatedSempro;
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
    '/admin/sempro'
);
const debouncedSearchTerm = useDebounce(searchTerm, 300);
watch(debouncedSearchTerm, () => applyFilters());

const filterConfig = ref([
    {
        key: 'status',
        label: 'Status',
        options: [
            { value: 'kartu_uploaded', label: 'Kartu Uploaded' },
            { value: 'waiting', label: 'Menunggu Review' },
            { value: 'approved', label: 'Disetujui - Atur Jadwal' },
            { value: 'scheduled', label: 'Jadwal Diatur' },
            { value: 'done', label: 'Selesai' },
            { value: 'rejected', label: 'Ditolak' }
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

// Selected sempro for actions
const selectedSempro = ref<Sempro | null>(null);

// Approval Dialog
const showApprovalModal = ref(false);
const approvalForm = useForm({
    action: 'approve',
    catatan: ''
});

function openApprovalModal(sempro: Sempro) {
    selectedSempro.value = sempro;
    showApprovalModal.value = true;
    approvalForm.reset();
}

function closeApprovalModal() {
    showApprovalModal.value = false;
    selectedSempro.value = null;
    approvalForm.reset();
}

function submitApproval() {
    if (!selectedSempro.value) return;

    approvalForm.post(`/admin/sempro/${selectedSempro.value.id}/approve`, {
        onSuccess: () => {
            closeApprovalModal();
            showSuccessAlert('Sukses', 'Pengajuan seminar proposal berhasil disetujui');
        },
        onError: (errors) => {
            showErrorAlert('Error', 'Terjadi kesalahan saat memproses persetujuan');
        }
    });
}

function rejectSempro() {
    if (!selectedSempro.value) return;

    approvalForm.action = 'reject';
    approvalForm.post(`/admin/sempro/${selectedSempro.value.id}/reject`, {
        onSuccess: () => {
            closeApprovalModal();
            showSuccessAlert('Sukses', 'Pengajuan seminar proposal berhasil ditolak');
        },
        onError: (errors) => {
            showErrorAlert('Error', 'Terjadi kesalahan saat menolak pengajuan');
        }
    });
}

// Schedule Approval Dialog
const showScheduleModal = ref(false);
const scheduleForm = useForm({
    approve_schedule: true,
    blast_email: false,
    catatan_jadwal: ''
});

function openScheduleModal(sempro: Sempro) {
    selectedSempro.value = sempro;
    showScheduleModal.value = true;
    scheduleForm.reset();
}

function closeScheduleModal() {
    showScheduleModal.value = false;
    selectedSempro.value = null;
    scheduleForm.reset();
}

function approveSchedule() {
    if (!selectedSempro.value) return;

    scheduleForm.post(`/admin/sempro/${selectedSempro.value.id}/approve-schedule`, {
        onSuccess: () => {
            closeScheduleModal();
            showSuccessAlert('Sukses', 'Jadwal seminar proposal berhasil disetujui');
        },
        onError: (errors) => {
            showErrorAlert('Error', 'Terjadi kesalahan saat menyetujui jadwal');
        }
    });
}

// Action handlers
const showDetail = (sempro: Sempro) => router.get(`/admin/sempro/${sempro.id}`);
const showKartuBimbingan = (sempro: Sempro) => {
    if (sempro.kartu_bimbingan) {
        openPdfModal(`/storage/kartu_bimbingan/${sempro.tesis.mahasiswa.nim}_kartu_bimbingan.pdf`, 'Kartu Bimbingan');
    }
};
const showSuratKelayakan = (sempro: Sempro) => {
    if (sempro.surat_kelayakan) {
        openPdfModal(`/storage/surat_kelayakan/${sempro.tesis.mahasiswa.nim}_surat_kelayakan_ujian.pdf`, 'Surat Kelayakan');
    }
};

// Table Configuration
const tableColumns = ref([
    { key: 'no', label: 'No', class: 'text-center', sortable: false },
    { key: 'nama', label: 'Nama', sortable: true },
    { key: 'nim', label: 'NIM', sortable: true },
    { key: 'us_judul', label: 'Judul Tesis', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'jadwal', label: 'Jadwal', sortable: false },
    { key: 'actions', label: 'Aksi', class: 'text-center', sortable: false }
]);

// Sempro Actions Configuration
const getSemproActions = (item: Sempro) => {
    const baseActions = [
        { key: 'show', icon: Eye, tooltip: 'Lihat Detail' }
    ];

    // Kartu Bimbingan
    if (item.kartu_bimbingan) {
        baseActions.push({
            key: 'show-kartu',
            icon: FileDown,
            tooltip: 'Lihat Kartu Bimbingan'
        });
    }

    // Surat Kelayakan
    if (item.surat_kelayakan) {
        baseActions.push({
            key: 'show-surat',
            icon: FileDown,
            tooltip: 'Lihat Surat Kelayakan'
        });
    }

    // Actions based on status
    if (item.status_seminar === 'kartu_uploaded' || item.status_seminar === 'waiting') {
        baseActions.push({
            key: 'approve',
            icon: CheckCircle,
            tooltip: 'Setujui Pengajuan',
            class: 'text-green-600 hover:text-green-800'
        });
    }

    if (item.status_seminar === 'approved' && item.tanggal && item.tempat) {
        baseActions.push({
            key: 'approve-schedule',
            icon: Calendar,
            tooltip: 'Setujui Jadwal & Cetak Undangan',
            class: 'text-blue-600 hover:text-blue-800'
        });
    }

    return baseActions;
};

const handleSemproAction = (actionKey: string, sempro: Sempro) => {
    switch (actionKey) {
        case 'show':
            showDetail(sempro);
            break;
        case 'show-kartu':
            showKartuBimbingan(sempro);
            break;
        case 'show-surat':
            showSuratKelayakan(sempro);
            break;
        case 'approve':
            openApprovalModal(sempro);
            break;
        case 'approve-schedule':
            openScheduleModal(sempro);
            break;
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Seminar Proposal', href: '/admin/sempro' },
];

</script>

<template>
    <Head title="Seminar Proposal" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Alert Notification -->
            <AlertNotification
                :show="showAlert"
                :type="alertType"
                :title="alertTitle"
                :description="alertDescription"
                @close="closeAlert"
            />

            <div class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border shadow-xs">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Daftar Seminar Proposal</h3>
                    </div>

                    <!-- Filters -->
                    <FilterBar
                        v-model:searchValue="searchTerm"
                        search-placeholder="Cari nama, NIM, atau judul..."
                        :filters="filterConfig"
                        :filter-values="{ status: selectedStatus }"
                        @filter-change="handleFilterChange"
                        @clear-all-filters="clearFilters"
                    />

                    <DataTable
                        :columns="tableColumns"
                        :data="props.sempros.data"
                        :pagination="{
                            currentPage: props.sempros.current_page,
                            from: props.sempros.from,
                            to: props.sempros.to,
                            total: props.sempros.total,
                            lastPage: props.sempros.last_page,
                            perPage: props.sempros.per_page,
                            prevPageUrl: props.sempros.prev_page_url,
                            nextPageUrl: props.sempros.next_page_url
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
                                {{ (props.sempros.current_page - 1) * props.sempros.per_page + index + 1 }}
                            </div>
                        </template>

                        <template #nama="{ item }">
                            <div class="font-medium">{{ item.tesis.mahasiswa.user.name }}</div>
                        </template>

                        <template #nim="{ item }">
                            <div class="font-mono">{{ item.tesis.mahasiswa.nim }}</div>
                        </template>

                        <template #us_judul="{ item }">
                            <div class="max-w-xs truncate" :title="item.tesis.us_judul">
                                {{ item.tesis.us_judul }}
                            </div>
                        </template>

                        <template #status="{ item }">
                            <Badge
                                :variant="item.status_badge.variant"
                                class="text-xs"
                            >
                                {{ item.status_badge.text }}
                            </Badge>
                        </template>

                        <template #jadwal="{ item }">
                            <div v-if="item.formatted_date && item.formatted_time" class="text-sm">
                                <div class="flex items-center gap-1 mb-1">
                                    <Calendar class="h-3 w-3" />
                                    <span>{{ item.formatted_date }}</span>
                                </div>
                                <div class="flex items-center gap-1 mb-1">
                                    <Clock class="h-3 w-3" />
                                    <span>{{ item.formatted_time }}</span>
                                </div>
                                <div v-if="item.tempat" class="flex items-center gap-1">
                                    <MapPin class="h-3 w-3" />
                                    <span>{{ item.tempat }}</span>
                                </div>
                            </div>
                            <div v-else class="text-xs text-muted-foreground">
                                Belum dijadwalkan
                            </div>
                        </template>

                        <template #actions="{ item }">
                            <div class="flex justify-center">
                                <ActionButtons
                                    :actions="getSemproActions(item)"
                                    :item="item"
                                    @action="handleSemproAction"
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
            <DialogContent class="!max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Kelola Persetujuan Seminar Proposal</DialogTitle>
                </DialogHeader>
                <div v-if="selectedSempro" class="space-y-6">
                    <!-- Sempro Summary -->
                    <div class="p-4 bg-muted/50 rounded-lg">
                        <h3 class="font-medium mb-2">Ringkasan Pengajuan</h3>
                        <div class="space-y-1 text-sm">
                            <p><strong>Mahasiswa:</strong> {{ selectedSempro.tesis.mahasiswa.user.name }} ({{ selectedSempro.tesis.mahasiswa.nim }})</p>
                            <p><strong>Judul:</strong> {{ selectedSempro.tesis.us_judul }}</p>
                            <p><strong>Pembimbing 1:</strong> {{ selectedSempro.tesis.dosen_pembimbing_satu.user.name }}</p>
                            <p><strong>Pembimbing 2:</strong> {{ selectedSempro.tesis.dosen_pembimbing_dua.user.name }}</p>
                        </div>
                    </div>

                    <Separator />

                    <!-- Form -->
                    <div class="space-y-4">
                        <div>
                            <Label for="catatan" class="text-sm">Catatan (Opsional)</Label>
                            <Textarea
                                id="catatan"
                                v-model="approvalForm.catatan"
                                placeholder="Masukkan catatan untuk mahasiswa..."
                                class="min-h-[100px]"
                            />
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
                            @click="rejectSempro"
                            :disabled="approvalForm.processing"
                        >
                            Tolak Pengajuan
                        </Button>
                        <Button
                            @click="submitApproval"
                            :disabled="approvalForm.processing"
                            class="bg-green-600 hover:bg-green-700"
                        >
                            <CheckCircle class="h-4 w-4 mr-2" />
                            Setujui Pengajuan
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Schedule Approval Modal -->
        <Dialog v-model:open="showScheduleModal">
            <DialogContent class="!max-w-3xl">
                <DialogHeader>
                    <DialogTitle>Persetujuan Jadwal Seminar Proposal</DialogTitle>
                </DialogHeader>
                <div v-if="selectedSempro" class="space-y-6">
                    <!-- Schedule Summary -->
                    <div class="p-4 bg-muted/50 rounded-lg">
                        <h3 class="font-medium mb-2">Detail Jadwal yang Diajukan</h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Mahasiswa:</strong> {{ selectedSempro.tesis.mahasiswa.user.name }} ({{ selectedSempro.tesis.mahasiswa.nim }})</p>
                            <p><strong>Judul:</strong> {{ selectedSempro.tesis.us_judul }}</p>
                            <div class="grid grid-cols-2 gap-4 mt-3 p-3 bg-white rounded-lg border">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <Calendar class="h-4 w-4 text-blue-600" />
                                        <strong>Tanggal:</strong>
                                    </div>
                                    <p>{{ selectedSempro.formatted_date }}</p>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <Clock class="h-4 w-4 text-blue-600" />
                                        <strong>Waktu:</strong>
                                    </div>
                                    <p>{{ selectedSempro.formatted_time }}</p>
                                </div>
                                <div class="col-span-2">
                                    <div class="flex items-center gap-2 mb-1">
                                        <MapPin class="h-4 w-4 text-blue-600" />
                                        <strong>Tempat:</strong>
                                    </div>
                                    <p>{{ selectedSempro.tempat }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Separator />

                    <!-- Options -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2">
                            <input
                                id="blast_email"
                                v-model="scheduleForm.blast_email"
                                type="checkbox"
                                class="rounded border-gray-300"
                            />
                            <Label for="blast_email" class="text-sm">
                                Kirim undangan ke email pembimbing
                            </Label>
                        </div>

                        <div>
                            <Label for="catatan_jadwal" class="text-sm">Catatan Jadwal (Opsional)</Label>
                            <Textarea
                                id="catatan_jadwal"
                                v-model="scheduleForm.catatan_jadwal"
                                placeholder="Masukkan catatan untuk jadwal seminar..."
                                class="min-h-[80px]"
                            />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <Button
                            variant="outline"
                            @click="closeScheduleModal"
                            :disabled="scheduleForm.processing"
                        >
                            Batal
                        </Button>
                        <Button
                            @click="approveSchedule"
                            :disabled="scheduleForm.processing"
                            class="bg-blue-600 hover:bg-blue-700"
                        >
                            <Calendar class="h-4 w-4 mr-2" />
                            Setujui & Cetak Undangan
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
