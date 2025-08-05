<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { useAlert } from '@/composables/UseAlert';
import { type BreadcrumbItem } from '@/types';

import {
    Card, CardContent, CardHeader, CardTitle, Badge, Button, AlertNotification,
    Dialog, DialogContent, DialogHeader, DialogTitle, Input, Label, Textarea, Separator
} from '@/components/ui';

import {
    FileText, CheckCircle, ArrowLeft, Download, X, Upload
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
}

interface Props {
    draft: Draft;
}

const props = defineProps<Props>();
const { showAlert, alertType, alertTitle, alertDescription, showSuccessAlert, showErrorAlert, closeAlert } = useAlert();

// PDF Modal
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

// Approval Modal
const showApprovalModal = ref(false);
const approvalForm = useForm({
    action: 'approve',
    file_sk_pembimbing: null as File | null,
    alasan_penolakan: ''
});

function openApprovalModal() {
    showApprovalModal.value = true;
    approvalForm.reset();
}

function closeApprovalModal() {
    showApprovalModal.value = false;
    approvalForm.reset();
}

function handleFileUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        approvalForm.file_sk_pembimbing = target.files[0];
    }
}

function submitApproval() {
    approvalForm.post(`/admin/draft/${props.draft.id}`, {
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
    approvalForm.action = 'reject';
    approvalForm.post(`/admin/draft/${props.draft.id}`, {
        onSuccess: () => {
            closeApprovalModal();
            showSuccessAlert('Sukses', 'Draft pratesis berhasil ditolak');
        },
        onError: (errors) => {
            showErrorAlert('Error', 'Terjadi kesalahan saat menolak draft');
        }
    });
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function getStatusBadge(status: string) {
    switch(status) {
        case 'pending':
            return { text: 'WAITING', class: 'bg-yellow-100 text-yellow-800 border-yellow-300' };
        case 'approved':
            return { text: 'SETUJU', class: 'bg-green-100 text-green-800 border-green-300' };
        case 'rejected':
            return { text: 'TOLAK', class: 'bg-red-100 text-red-800 border-red-300' };
        default:
            return { text: 'DRAFT', class: 'bg-gray-100 text-gray-800 border-gray-300' };
    }
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Draft Pratesis', href: '/admin/draft' },
    { title: 'Detail Draft', href: `/admin/draft/${props.draft.id}` },
];
</script>

<template>
    <Head title="Detail Draft Pratesis" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Alert Notification -->
            <AlertNotification :show="showAlert" :type="alertType" :title="alertTitle" :description="alertDescription" @close="closeAlert"/>

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div>
                        <h1 class="text-2xl font-bold">Detail Draft Pratesis</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="route('admin.draft.index')">
                            <ArrowLeft class="h-4 w-4" />
                            Kembali
                        </Link>
                    </Button>
                    <Button
                        v-if="props.draft.status === 'surat_uploaded'"
                        @click="openApprovalModal"
                        class="bg-green-600 hover:bg-green-700" size="sm"
                    >
                        <CheckCircle class="h-4 w-4" />
                        Kelola Persetujuan
                    </Button>
                </div>
            </div>

            <!-- Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Draft Information -->
                    <Card class="!gap-2">
                        <CardHeader>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <CardTitle>Informasi Draft</CardTitle>
                                <div class="flex justify-end">
                                    <Badge :class="getStatusBadge(props.draft.status).class">
                                        {{ getStatusBadge(props.draft.status).text }}
                                    </Badge>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div>
                                <Label class="text-sm font-medium text-muted-foreground">Usulan Judul</Label>
                                <p class="mt-1 text-lg font-medium">{{ props.draft.us_judul }}</p>
                            </div>

                            <div v-if="props.draft.us_abstrak">
                                <Label class="text-sm font-medium text-muted-foreground">Abstrak</Label>
                                <p class="mt-1 text-sm leading-relaxed">{{ props.draft.us_abstrak }}</p>
                            </div>

                            <div>
                                <Label class="text-sm font-medium text-muted-foreground">Tanggal Pengajuan</Label>
                                <p class="mt-1">{{ formatDate(props.draft.tgl_pengajuan) }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Supervisors Information -->
                    <Card class="!gap-2">
                        <CardHeader>
                            <CardTitle>Dosen Pembimbing</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <Label class="text-sm font-medium text-muted-foreground">Pembimbing Utama</Label>
                                    <p class="mt-1 font-medium">{{ props.draft.dosen_pembimbing_satu.nama_dosen }}</p>
                                    <div class="mt-2 p-3 bg-muted/50 rounded-md">
                                        <p class="text-xs font-medium text-muted-foreground mb-1">Alasan Pemilihan:</p>
                                        <p class="text-sm">{{ props.draft.ket_dospem_satu }}</p>
                                    </div>
                                </div>
                                <div>
                                    <Label class="text-sm font-medium text-muted-foreground">Pembimbing Pendamping</Label>
                                    <p class="mt-1 font-medium">{{ props.draft.dosen_pembimbing_dua.nama_dosen }}</p>
                                    <div class="mt-2 p-3 bg-muted/50 rounded-md">
                                        <p class="text-xs font-medium text-muted-foreground mb-1">Alasan Pemilihan:</p>
                                        <p class="text-sm">{{ props.draft.ket_dospem_dua }}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Student Information -->
                    <Card class="!gap-2">
                        <CardHeader>
                            <CardTitle>Informasi Mahasiswa</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div>
                                <Label class="text-sm font-medium text-muted-foreground">Nama</Label>
                                <p class="mt-1 font-medium">{{ props.draft.mahasiswa.nama_mhs }}</p>
                            </div>
                            <div>
                                <Label class="text-sm font-medium text-muted-foreground">NIM</Label>
                                <p class="mt-1 font-mono">{{ props.draft.mahasiswa.nim }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Documents -->
                    <Card class="!gap-2">
                        <CardHeader>
                            <CardTitle>Dokumen Pendukung</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <Button
                                variant="outline"
                                size="sm"
                                class="w-full justify-start"
                                @click="openPdfModal(`/storage/file_lampiran_khs/${props.draft.mahasiswa.nim}_file_khs.pdf`, 'File KHS')"
                            >
                                <FileText class="h-4 w-4" />
                                Lihat KHS
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                class="w-full justify-start"
                                @click="openPdfModal(`/storage/file_lampiran_krs/${props.draft.mahasiswa.nim}_file_krs.pdf`, 'File KRS')"
                            >
                                <FileText class="h-4 w-4" />
                                Lihat KRS
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                class="w-full justify-start"
                                @click="openPdfModal(`/storage/surat_permohonan/${props.draft.mahasiswa.nim}_surat_permohonan_bimbingan.pdf`, 'File Surat Permohonan Pembimbing')"
                            >
                                <FileText class="h-4 w-4" />
                                Lihat Surat Permohonan Pembimbing
                            </Button>
                        </CardContent>
                    </Card>
                    <!-- SK Pembimbing (if approved) -->
                    <Card class="!gap-2" v-if="props.draft.status === 'approved' && props.draft.file_sk_pembimbing">
                        <CardHeader>
                            <CardTitle>SK Pembimbing</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Button as-child variant="outline">
                                <a :href="`/storage/${props.draft.file_sk_pembimbing}`" download>
                                    <Download class="h-4 w-4" />
                                    Download SK Pembimbing
                                </a>
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Large PDF Modal -->
        <teleport to="body">
            <div v-if="showPdfModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80">
                <div class="relative bg-white rounded-lg shadow-xl w-[95vw] max-w-6xl h-[90vh] flex flex-col">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-4 border-b bg-white rounded-t-lg">
                        <h2 class="text-lg font-semibold">{{ pdfTitle }}</h2>
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
                <div class="space-y-6">
                    <!-- Draft Summary -->
                    <div class="p-4 bg-muted/50 rounded-lg">
                        <h3 class="font-medium mb-2">Ringkasan Draft</h3>
                        <div class="space-y-1 text-sm">
                            <p><strong>Mahasiswa:</strong> {{ props.draft.mahasiswa.user.name }} ({{ props.draft.mahasiswa.nim }})</p>
                            <p><strong>Judul:</strong> {{ props.draft.us_judul }}</p>
                            <p><strong>Pembimbing 1:</strong> {{ props.draft.dosen_pembimbing_satu.nama_dosen }}</p>
                            <p><strong>Pembimbing 2:</strong> {{ props.draft.dosen_pembimbing_dua.nama_dosen }}</p>
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
