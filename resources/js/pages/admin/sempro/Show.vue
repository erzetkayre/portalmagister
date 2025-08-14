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
    FileText, CheckCircle, ArrowLeft, Download, X, Calendar, Clock, MapPin, Mail, Upload
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

interface Props {
    sempro: Sempro;
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
    catatan: ''
});

function openApprovalModal() {
    showApprovalModal.value = true;
    approvalForm.reset();
}

function closeApprovalModal() {
    showApprovalModal.value = false;
    approvalForm.reset();
}

function submitApproval() {
    approvalForm.post(`/admin/sempro/${props.sempro.id}/approve`, {
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
    approvalForm.action = 'reject';
    approvalForm.post(`/admin/sempro/${props.sempro.id}/reject`, {
        onSuccess: () => {
            closeApprovalModal();
            showSuccessAlert('Sukses', 'Pengajuan seminar proposal berhasil ditolak');
        },
        onError: (errors) => {
            showErrorAlert('Error', 'Terjadi kesalahan saat menolak pengajuan');
        }
    });
}

// Schedule Approval Modal
const showScheduleModal = ref(false);
const scheduleForm = useForm({
    approve_schedule: true,
    blast_email: false,
    catatan_jadwal: ''
});

function openScheduleModal() {
    showScheduleModal.value = true;
    scheduleForm.reset();
}

function closeScheduleModal() {
    showScheduleModal.value = false;
    scheduleForm.reset();
}

function approveSchedule() {
    scheduleForm.post(`/admin/sempro/${props.sempro.id}/approve-schedule`, {
        onSuccess: () => {
            closeScheduleModal();
            showSuccessAlert('Sukses', 'Jadwal seminar proposal berhasil disetujui');
        },
        onError: (errors) => {
            showErrorAlert('Error', 'Terjadi kesalahan saat menyetujui jadwal');
        }
    });
}

function formatDate(dateString: string | null) {
    if (!dateString) return 'Belum diatur';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function getStatusBadge(status: string) {
    switch(status) {
        case 'kartu_uploaded':
            return { text: 'KARTU UPLOADED', class: 'bg-blue-100 text-blue-800 border-blue-300' };
        case 'waiting':
            return { text: 'MENUNGGU REVIEW', class: 'bg-yellow-100 text-yellow-800 border-yellow-300' };
        case 'approved':
            return { text: 'DISETUJUI', class: 'bg-green-100 text-green-800 border-green-300' };
        case 'scheduled':
            return { text: 'JADWAL DIATUR', class: 'bg-purple-100 text-purple-800 border-purple-300' };
        case 'done':
            return { text: 'SELESAI', class: 'bg-emerald-100 text-emerald-800 border-emerald-300' };
        case 'rejected':
            return { text: 'DITOLAK', class: 'bg-red-100 text-red-800 border-red-300' };
        default:
            return { text: 'DRAFT', class: 'bg-gray-100 text-gray-800 border-gray-300' };
    }
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Menu', href: '/admin/dashboard' },
    { title: 'Seminar Proposal', href: '/admin/sempro' },
    { title: 'Detail Seminar Proposal', href: `/admin/sempro/${props.sempro.id}` },
];
</script>

<template>
    <Head title="Detail Seminar Proposal" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Alert Notification -->
            <AlertNotification :show="showAlert" :type="alertType" :title="alertTitle" :description="alertDescription" @close="closeAlert"/>

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div>
                        <h1 class="text-2xl font-bold">Detail Seminar Proposal</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" size="sm" as-child>
                        <Link href="/admin/sempro">
                            <ArrowLeft class="h-4 w-4" />
                            Kembali
                        </Link>
                    </Button>
                    <!-- Approval Button -->
                    <Button
                        v-if="props.sempro.status_seminar === 'kartu_uploaded' || props.sempro.status_seminar === 'waiting'"
                        @click="openApprovalModal"
                        class="bg-green-600 hover:bg-green-700" size="sm"
                    >
                        <CheckCircle class="h-4 w-4" />
                        Kelola Persetujuan
                    </Button>
                    <!-- Schedule Approval Button -->
                    <Button
                        v-if="props.sempro.status_seminar === 'approved' && props.sempro.tanggal && props.sempro.tempat"
                        @click="openScheduleModal"
                        class="bg-blue-600 hover:bg-blue-700" size="sm"
                    >
                        <Calendar class="h-4 w-4" />
                        Setujui Jadwal
                    </Button>
                    <!-- Download Invitation Button -->
                    <Button
                        v-if="props.sempro.status_seminar === 'scheduled'"
                        as-child
                        variant="outline" size="sm"
                    >
                        <a :href="`/admin/sempro/${props.sempro.id}/download-invitation`">
                            <Download class="h-4 w-4" />
                            Undangan
                        </a>
                    </Button>
                </div>
            </div>

            <!-- Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Seminar Information -->
                    <Card class="!gap-2">
                        <CardHeader>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <CardTitle>Informasi Seminar Proposal</CardTitle>
                                <div class="flex justify-end">
                                    <Badge :class="getStatusBadge(props.sempro.status_seminar).class">
                                        {{ getStatusBadge(props.sempro.status_seminar).text }}
                                    </Badge>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div>
                                <Label class="text-sm font-medium text-muted-foreground">Judul Tesis</Label>
                                <p class="mt-1 text-lg font-medium">{{ props.sempro.tesis.us_judul }}</p>
                            </div>

                            <div v-if="props.sempro.tesis.us_abstrak">
                                <Label class="text-sm font-medium text-muted-foreground">Abstrak</Label>
                                <p class="mt-1 text-sm leading-relaxed">{{ props.sempro.tesis.us_abstrak }}</p>
                            </div>

                            <div v-if="props.sempro.tgl_upload_surat">
                                <Label class="text-sm font-medium text-muted-foreground">Tanggal Upload Surat</Label>
                                <p class="mt-1">{{ new Date(props.sempro.tgl_upload_surat).toLocaleString('id-ID') }}</p>
                            </div>

                            <div v-if="props.sempro.catatan">
                                <Label class="text-sm font-medium text-muted-foreground">Catatan</Label>
                                <div class="mt-1 p-3 bg-muted/50 rounded-md">
                                    <p class="text-sm">{{ props.sempro.catatan }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Schedule Information -->
                    <Card class="!gap-2" v-if="props.sempro.tanggal || props.sempro.tempat">
                        <CardHeader>
                            <CardTitle>Jadwal Seminar</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div v-if="props.sempro.formatted_date">
                                    <div class="flex items-center gap-2 mb-2">
                                        <Calendar class="h-4 w-4 text-blue-600" />
                                        <Label class="text-sm font-medium text-muted-foreground">Tanggal</Label>
                                    </div>
                                    <p class="font-medium">{{ props.sempro.formatted_date }}</p>
                                </div>
                                <div v-if="props.sempro.formatted_time">
                                    <div class="flex items-center gap-2 mb-2">
                                        <Clock class="h-4 w-4 text-blue-600" />
                                        <Label class="text-sm font-medium text-muted-foreground">Waktu</Label>
                                    </div>
                                    <p class="font-medium">{{ props.sempro.formatted_time }}</p>
                                </div>
                                <div v-if="props.sempro.tempat" class="md:col-span-2">
                                    <div class="flex items-center gap-2 mb-2">
                                        <MapPin class="h-4 w-4 text-blue-600" />
                                        <Label class="text-sm font-medium text-muted-foreground">Tempat</Label>
                                    </div>
                                    <p class="font-medium">{{ props.sempro.tempat }}</p>
                                </div>
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
                                    <p class="mt-1 font-medium">{{ props.sempro.tesis.dosen_pembimbing_satu.nama_dosen }}</p>
                                    <div class="mt-2 flex items-center gap-2 text-sm text-muted-foreground">
                                        <Mail class="h-3 w-3" />
                                        {{ props.sempro.tesis.dosen_pembimbing_satu.user.email }}
                                    </div>
                                </div>
                                <div>
                                    <Label class="text-sm font-medium text-muted-foreground">Pembimbing Pendamping</Label>
                                    <p class="mt-1 font-medium">{{ props.sempro.tesis.dosen_pembimbing_dua.nama_dosen }}</p>
                                    <div class="mt-2 flex items-center gap-2 text-sm text-muted-foreground">
                                        <Mail class="h-3 w-3" />
                                        {{ props.sempro.tesis.dosen_pembimbing_dua.user.email }}
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
                                <p class="mt-1 font-medium">{{ props.sempro.tesis.mahasiswa.nama_mhs }}</p>
                            </div>
                            <div>
                                <Label class="text-sm font-medium text-muted-foreground">NIM</Label>
                                <p class="mt-1 font-mono">{{ props.sempro.tesis.mahasiswa.nim }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Documents -->
                    <Card class="!gap-2">
                        <CardHeader>
                            <CardTitle>Dokumen Seminar</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <Button
                                v-if="props.sempro.kartu_bimbingan"
                                variant="outline"
                                size="sm"
                                class="w-full justify-start"
                                @click="openPdfModal(`/storage/kartu_bimbingan/${sempro.tesis.mahasiswa.nim}_kartu_bimbingan.pdf`, 'Kartu Bimbingan')"
                            >
                             <FileText class="h-4 w-4" />
                                Lihat Kartu Bimbingan
                            </Button>
                            <Button
                                v-if="props.sempro.surat_kelayakan"
                                variant="outline"
                                size="sm"
                                class="w-full justify-start"
                                @click="openPdfModal(`/storage/surat_kelayakan/${sempro.tesis.mahasiswa.nim}_surat_kelayakan_ujian.pdf`, 'Surat Kelayakan')"
                            >
                                <FileText class="h-4 w-4" />
                                Lihat Surat Kelayakan
                            </Button>
                            <Button
                                v-if="props.sempro.draft_semhas"
                                variant="outline"
                                size="sm"
                                class="w-full justify-start"
                                @click="openPdfModal(`/storage/draft_semhas/${props.sempro.draft_semhas}`, 'Draft Semhas')"
                            >
                                <FileText class="h-4 w-4" />
                                Lihat Draft Semhas
                            </Button>
                            <Button
                                v-if="props.sempro.berita_acara"
                                variant="outline"
                                size="sm"
                                class="w-full justify-start"
                                @click="openPdfModal(`/storage/berita_acara/${props.sempro.berita_acara}`, 'Berita Acara')"
                            >
                                <FileText class="h-4 w-4" />
                                Lihat Berita Acara
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
                    <DialogTitle>Kelola Persetujuan Seminar Proposal</DialogTitle>
                </DialogHeader>
                <div class="space-y-6">
                    <!-- Sempro Summary -->
                    <div class="p-4 bg-muted/50 rounded-lg">
                        <h3 class="font-medium mb-2">Ringkasan Pengajuan</h3>
                        <div class="space-y-1 text-sm">
                            <p><strong>Mahasiswa:</strong> {{ props.sempro.tesis.mahasiswa.user.name }} ({{ props.sempro.tesis.mahasiswa.nim }})</p>
                            <p><strong>Judul:</strong> {{ props.sempro.tesis.us_judul }}</p>
                            <p><strong>Pembimbing 1:</strong> {{ props.sempro.tesis.dosen_pembimbing_satu.nama_dosen }}</p>
                            <p><strong>Pembimbing 2:</strong> {{ props.sempro.tesis.dosen_pembimbing_dua.nama_dosen }}</p>
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
                            <p class="text-xs text-muted-foreground mt-1">
                                Maksimal 1000 karakter
                            </p>
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
                            <CheckCircle class="h-4 w-4" />
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
                <div class="space-y-6">
                    <!-- Schedule Summary -->
                    <div class="p-4 bg-muted/50 rounded-lg">
                        <h3 class="font-medium mb-2">Detail Jadwal yang Diajukan</h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Mahasiswa:</strong> {{ props.sempro.tesis.mahasiswa.user.name }} ({{ props.sempro.tesis.mahasiswa.nim }})</p>
                            <p><strong>Judul:</strong> {{ props.sempro.tesis.us_judul }}</p>
                            <div class="grid grid-cols-2 gap-4 mt-3 p-3 bg-white rounded-lg border">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <Calendar class="h-4 w-4 text-blue-600" />
                                        <strong>Tanggal:</strong>
                                    </div>
                                    <p>{{ props.sempro.formatted_date }}</p>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <Clock class="h-4 w-4 text-blue-600" />
                                        <strong>Waktu:</strong>
                                    </div>
                                    <p>{{ props.sempro.formatted_time }}</p>
                                </div>
                                <div class="col-span-2">
                                    <div class="flex items-center gap-2 mb-1">
                                        <MapPin class="h-4 w-4 text-blue-600" />
                                        <strong>Tempat:</strong>
                                    </div>
                                    <p>{{ props.sempro.tempat }}</p>
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
                                Kirim undangan ke email pembimbing dan mahasiswa
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
                            <Calendar class="h-4 w-4" />
                            Setujui & Cetak Undangan
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
