<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    FileText,
    Clock,
    CheckCircle,
    Plus,
    Download,
    Upload,
    FileCheck,
    Calendar,
    MapPin,
    BookOpen
} from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';

const showPdfModal = ref(false);
const pdfUrl = ref('');
const pdfTitle = ref('');
const showUploadModal = ref(false);
const showJadwalModal = ref(false);
const showKartuUploadModal = ref(false);

function openPdfModalVue(url: string, title: string) {
    pdfUrl.value = url;
    pdfTitle.value = title;
    showPdfModal.value = true;
}

function closePdfModal() {
    showPdfModal.value = false;
    pdfUrl.value = '';
    pdfTitle.value = '';
}

function openUploadModal() {
    showUploadModal.value = true;
}

function closeUploadModal() {
    showUploadModal.value = false;
    uploadForm.reset();
}

function openJadwalModal() {
    showJadwalModal.value = true;
}

function closeJadwalModal() {
    showJadwalModal.value = false;
    jadwalForm.reset();
}

function openKartuUploadModal() {
    showKartuUploadModal.value = true;
}

function closeKartuUploadModal() {
    showKartuUploadModal.value = false;
    kartuForm.reset();
}

interface Tesis {
    id: number;
    us_judul: string;
    us_abstrak: string;
    mahasiswa: {
        nim: string;
        nama_mhs: string;
        user: {
            nama: string;
        };
    };
    dosen_pembimbing_satu: {
        nama_dosen: string;
        user: {
            nama: string;
        };
    };
    dosen_pembimbing_dua: {
        nama_dosen: string;
        user: {
            nama: string;
        };
    };
}

interface UjianProposal {
    id: number;
    tanggal: string;
    tempat: string;
    jam_mulai: string;
    jam_selesai: string;
    status_seminar: string;
    kartu_bimbingan: string;
    draft_semhas: string;
    surat_kelayakan: string;
    summary: string;
    catatan: string;
    berita_acara: string;
}

interface Props {
    tesis: Tesis | null;
    ujianProposal: UjianProposal | null;
    currentStep: number;
}

const props = defineProps<Props>();

// Form for uploading kartu bimbingan
const kartuForm = useForm({
    kartu_bimbingan: null as File | null,
});

// Form for uploading surat kelayakan
const uploadForm = useForm({
    file_surat_kelayakan: null as File | null,
});

// Form for setting jadwal
const jadwalForm = useForm({
    tanggal: '',
    tempat: '',
    jam_mulai: '',
    jam_selesai: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard Mahasiswa',
        href: route('mahasiswa.dashboard'),
    },
    {
        title: 'Ujian Proposal',
        href: route('mahasiswa.sempro.index'),
    },
];

const steps = [
    { id: 1, title: 'Upload Kartu', icon: BookOpen, description: 'Upload kartu bimbingan' },
    { id: 2, title: 'Upload Surat', icon: Upload, description: 'Upload surat kelayakan ujian' },
    { id: 3, title: 'Persetujuan', icon: Clock, description: 'Menunggu persetujuan admin' },
    { id: 4, title: 'Atur Jadwal', icon: Calendar, description: 'Masukkan tanggal dan tempat ujian' },
    { id: 5, title: 'Selesai!', icon: CheckCircle, description: 'Ujian proposal siap dilaksanakan' }
];

function getStatusBadge(status: string) {
    switch (status) {
        case 'kartu_uploaded':
            return { text: 'Kartu Uploaded', variant: 'secondary' };
        case 'waiting':
            return { text: 'Menunggu Review', variant: 'secondary' };
        case 'approved':
            return { text: 'Disetujui - Atur Jadwal', variant: 'default' };
        case 'scheduled':
            return { text: 'Jadwal Diatur', variant: 'default' };
        case 'done':
            return { text: 'Selesai', variant: 'default' };
        case 'rejected':
            return { text: 'Ditolak', variant: 'destructive' };
        default:
            return { text: 'Draft', variant: 'outline' };
    }
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatTime(timeString: string) {
    return timeString.slice(0, 5); // Format HH:MM
}

function handleKartuFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        kartuForm.kartu_bimbingan = target.files[0];
    }
}

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        uploadForm.file_surat_kelayakan = target.files[0];
    }
}

function submitKartuBimbingan() {
    kartuForm.post(route('mahasiswa.sempro.store'), {
        onSuccess: () => {
            closeKartuUploadModal();
        },
        onError: (errors) => {
            console.error('Upload errors:', errors);
        }
    });
}

function submitUploadSurat() {
    if (!props.ujianProposal) return;

    uploadForm.post(route('mahasiswa.sempro.upload-surat', props.ujianProposal.id), {
        onSuccess: () => {
            closeUploadModal();
        },
        onError: (errors) => {
            console.error('Upload errors:', errors);
        }
    });
}

function submitJadwal() {
    if (!props.ujianProposal) return;

    jadwalForm.post(route('mahasiswa.sempro.update-jadwal', props.ujianProposal.id), {
        onSuccess: () => {
            closeJadwalModal();
        },
        onError: (errors) => {
            console.error('Jadwal errors:', errors);
        }
    });
}

function downloadTemplate() {
    if (!props.ujianProposal) return;
    window.open(route('mahasiswa.sempro.template-surat', props.ujianProposal.id), '_blank');
}
</script>

<template>
    <Head title="Ujian Proposal" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Ujian Proposal</h1>
                    <p class="text-muted-foreground">Kelola pengajuan ujian proposal tesis Anda</p>
                </div>
            </div>

            <!-- No Tesis -->
            <div v-if="!tesis">
                <Card>
                    <CardContent>
                        <div class="text-center py-8">
                            <FileText class="h-16 w-16 mx-auto text-muted-foreground mb-4" />
                            <h3 class="text-lg font-semibold mb-2">Belum Ada Tesis yang Disetujui</h3>
                            <p class="text-muted-foreground mb-4">
                                Anda belum memiliki tesis yang disetujui. Silakan ajukan proposal tesis terlebih dahulu.
                            </p>
                            <Button as-child>
                                <Link :href="route('mahasiswa.dashboard')">
                                    Kembali ke Dashboard
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Content based on step -->
            <div v-else-if="currentStep === 1 && tesis">
                <!-- Step 1: Form Upload Kartu Bimbingan -->
                <Card>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <div
                                v-for="(step, index) in steps"
                                :key="step.id"
                                class="flex items-center"
                                :class="{ 'flex-1': index < steps.length - 1 }"
                            >
                                <!-- Step circle -->
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-colors"
                                        :class="[
                                            step.id <= currentStep
                                                ? 'bg-primary border-primary text-primary-foreground'
                                                : 'bg-muted border-muted-foreground/25 text-muted-foreground'
                                        ]"
                                    >
                                        <component :is="step.icon" class="h-5 w-5" />
                                    </div>
                                    <div class="mt-2 text-center max-w-[120px]">
                                        <p class="text-sm font-medium">{{ step.title }}</p>
                                        <p class="text-xs text-muted-foreground mt-1">{{ step.description }}</p>
                                    </div>
                                </div>

                                <!-- Connector line -->
                                <div
                                    v-if="index < steps.length - 1"
                                    class="flex-1 h-0.5 mx-4 transition-colors"
                                    :class="[
                                        step.id < currentStep
                                            ? 'bg-primary'
                                            : 'bg-muted-foreground/25'
                                    ]"
                                />
                            </div>
                        </div>
                    </CardContent>
                    <CardContent>
                        <div class="text-center py-8 border-t-1">
                            <BookOpen class="h-16 w-16 mx-auto text-muted-foreground mb-4" />
                            <h3 class="text-lg font-semibold mb-2">Upload Kartu Bimbingan</h3>
                            <p class="text-muted-foreground mb-4">
                                Mulai proses ujian proposal dengan mengupload kartu bimbingan dalam format PDF.
                            </p>
                            <Button @click="openKartuUploadModal">
                                <Plus class="h-4 w-4" />
                                Upload Kartu Bimbingan
                            </Button>
                        </div>

                        <Separator />

                        <!-- Tesis Info -->
                        <div class="space-y-4 pt-6">
                            <div>
                                <h4 class="font-medium text-sm text-muted-foreground mb-1">Judul Tesis</h4>
                                <p class="text-sm">{{ tesis.us_judul }}</p>
                            </div>

                            <div v-if="tesis.us_abstrak">
                                <h4 class="font-medium text-sm text-muted-foreground mb-1">Abstrak</h4>
                                <p class="text-sm text-justify">{{ tesis.us_abstrak }}</p>
                            </div>

                            <!-- Supervisors -->
                            <div>
                                <h4 class="font-medium text-sm text-muted-foreground mb-3">Dosen Pembimbing</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium">{{ tesis.dosen_pembimbing_satu.user.nama }}</p>
                                        <p class="text-xs text-muted-foreground">Pembimbing Utama</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">{{ tesis.dosen_pembimbing_dua.user.nama }}</p>
                                        <p class="text-xs text-muted-foreground">Pembimbing Pendamping</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else-if="currentStep === 2 && ujianProposal && tesis">
                <!-- Step 2: Upload Surat Kelayakan -->
                <Card>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <div
                                v-for="(step, index) in steps"
                                :key="step.id"
                                class="flex items-center"
                                :class="{ 'flex-1': index < steps.length - 1 }"
                            >
                                <!-- Step circle -->
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-colors"
                                        :class="[
                                            step.id <= currentStep
                                                ? 'bg-primary border-primary text-primary-foreground'
                                                : 'bg-muted border-muted-foreground/25 text-muted-foreground'
                                        ]"
                                    >
                                        <component :is="step.icon" class="h-5 w-5" />
                                    </div>
                                    <div class="mt-2 text-center max-w-[120px]">
                                        <p class="text-sm font-medium">{{ step.title }}</p>
                                        <p class="text-xs text-muted-foreground mt-1">{{ step.description }}</p>
                                    </div>
                                </div>

                                <!-- Connector line -->
                                <div
                                    v-if="index < steps.length - 1"
                                    class="flex-1 h-0.5 mx-4 transition-colors"
                                    :class="[
                                        step.id < currentStep
                                            ? 'bg-primary'
                                            : 'bg-muted-foreground/25'
                                    ]"
                                />
                            </div>
                        </div>
                    </CardContent>
                    <CardContent class="space-y-6">
                        <!-- Upload Surat Status -->
                        <div class="text-center py-4 border-t-1">
                            <Upload class="h-12 w-12 mx-auto text-blue-500 mb-4" />
                            <h3 class="text-lg font-semibold mb-2">Upload Surat Kelayakan Ujian</h3>
                            <p class="text-sm text-muted-foreground mb-4">
                                Unduh template surat kelayakan, isi dan tanda tangani, kemudian upload kembali dalam format PDF.
                            </p>

                            <!-- Template dan Upload Actions -->
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <Button variant="outline" @click="downloadTemplate">
                                    <Download class="h-4 w-4" />
                                    Download Template Surat
                                </Button>
                                <Button @click="openUploadModal">
                                    <Upload class="h-4 w-4" />
                                    Upload Surat Bertanda Tangan
                                </Button>
                            </div>
                        </div>

                        <Separator />

                        <!-- Kartu Info -->
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center gap-2 mb-2">
                                <FileCheck class="h-5 w-5 text-green-600" />
                                <span class="font-medium text-green-800">Kartu Bimbingan Berhasil Diupload</span>
                            </div>
                            <Button
                                variant="outline"
                                size="sm"
                                class="mt-2"
                                @click="openPdfModalVue(`/storage/${ujianProposal.kartu_bimbingan}`, 'Kartu Bimbingan')"
                            >
                                <BookOpen class="h-4 w-4 mr-2" />
                                Lihat Kartu Bimbingan
                            </Button>
                        </div>

                        <!-- Tesis Info -->
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-sm text-muted-foreground mb-1">Judul Tesis</h4>
                                <p class="text-sm">{{ tesis.us_judul }}</p>
                            </div>
                        </div>

                        <!-- Supervisors -->
                        <div>
                            <h4 class="font-medium text-sm text-muted-foreground mb-3">Dosen Pembimbing</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium">{{ tesis.dosen_pembimbing_satu.user.nama }}</p>
                                    <p class="text-xs text-muted-foreground">Pembimbing Utama</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ tesis.dosen_pembimbing_dua.user.nama }}</p>
                                    <p class="text-xs text-muted-foreground">Pembimbing Pendamping</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else-if="currentStep === 3 && ujianProposal && tesis">
                <!-- Step 3: Waiting for admin approval after surat uploaded -->
                <Card>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <div
                                v-for="(step, index) in steps"
                                :key="step.id"
                                class="flex items-center"
                                :class="{ 'flex-1': index < steps.length - 1 }"
                            >
                                <!-- Step circle -->
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-colors"
                                        :class="[
                                            step.id <= currentStep
                                                ? 'bg-primary border-primary text-primary-foreground'
                                                : 'bg-muted border-muted-foreground/25 text-muted-foreground'
                                        ]"
                                    >
                                        <component :is="step.icon" class="h-5 w-5" />
                                    </div>
                                    <div class="mt-2 text-center max-w-[120px]">
                                        <p class="text-sm font-medium">{{ step.title }}</p>
                                        <p class="text-xs text-muted-foreground mt-1">{{ step.description }}</p>
                                    </div>
                                </div>

                                <!-- Connector line -->
                                <div
                                    v-if="index < steps.length - 1"
                                    class="flex-1 h-0.5 mx-4 transition-colors"
                                    :class="[
                                        step.id < currentStep
                                            ? 'bg-primary'
                                            : 'bg-muted-foreground/25'
                                    ]"
                                />
                            </div>
                        </div>
                    </CardContent>
                    <CardContent class="space-y-6">
                        <!-- Processing Status - Centered -->
                        <div class="text-center py-4 border-t-1">
                            <Clock class="h-12 w-12 mx-auto text-orange-500 mb-4" />
                            <h3 class="text-lg font-semibold mb-2">Menunggu Persetujuan Admin</h3>
                            <p class="text-sm text-muted-foreground">
                                Surat kelayakan telah diupload. Menunggu persetujuan dari koordinator untuk melanjutkan ke tahap pengaturan jadwal ujian.
                            </p>
                        </div>

                        <Separator />

                        <!-- Uploaded Files Info -->
                        <div class="space-y-4">
                            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex items-center gap-2 mb-2">
                                    <FileCheck class="h-5 w-5 text-green-600" />
                                    <span class="font-medium text-green-800">Kartu Bimbingan Berhasil Diupload</span>
                                </div>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    class="mt-2"
                                    @click="openPdfModalVue(`/storage/${ujianProposal.kartu_bimbingan}`, 'Kartu Bimbingan')"
                                >
                                    <BookOpen class="h-4 w-4 mr-2" />
                                    Lihat Kartu Bimbingan
                                </Button>
                            </div>

                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-center gap-2 mb-2">
                                    <FileCheck class="h-5 w-5 text-blue-600" />
                                    <span class="font-medium text-blue-800">Surat Kelayakan Berhasil Diupload</span>
                                </div>
                                <p class="text-sm text-blue-700 mb-2">
                                    File: {{ ujianProposal.surat_kelayakan ? ujianProposal.surat_kelayakan.split('/').pop() : 'Surat Kelayakan' }}
                                </p>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="openPdfModalVue(`/storage/${ujianProposal.surat_kelayakan}`, 'Surat Kelayakan')"
                                >
                                    <FileText class="h-4 w-4 mr-2" />
                                    Lihat Surat
                                </Button>
                            </div>
                        </div>

                        <!-- Tesis Info -->
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-sm text-muted-foreground mb-1">Judul Tesis</h4>
                                <p class="text-sm">{{ tesis.us_judul }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else-if="currentStep === 4 && ujianProposal && tesis">
                <!-- Step 4: Fill date and place -->
                <Card>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <div
                                v-for="(step, index) in steps"
                                :key="step.id"
                                class="flex items-center"
                                :class="{ 'flex-1': index < steps.length - 1 }"
                            >
                                <!-- Step circle -->
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-colors"
                                        :class="[
                                            step.id <= currentStep
                                                ? 'bg-primary border-primary text-primary-foreground'
                                                : 'bg-muted border-muted-foreground/25 text-muted-foreground'
                                        ]"
                                    >
                                        <component :is="step.icon" class="h-5 w-5" />
                                    </div>
                                    <div class="mt-2 text-center max-w-[120px]">
                                        <p class="text-sm font-medium">{{ step.title }}</p>
                                        <p class="text-xs text-muted-foreground mt-1">{{ step.description }}</p>
                                    </div>
                                </div>

                                <!-- Connector line -->
                                <div
                                    v-if="index < steps.length - 1"
                                    class="flex-1 h-0.5 mx-4 transition-colors"
                                    :class="[
                                        step.id < currentStep
                                            ? 'bg-primary'
                                            : 'bg-muted-foreground/25'
                                    ]"
                                />
                            </div>
                        </div>
                    </CardContent>
                    <CardContent class="space-y-6">
                        <!-- Schedule Setting Status -->
                        <div class="text-center py-4 border-t-1">
                            <Calendar class="h-12 w-12 mx-auto text-green-500 mb-4" />
                            <h3 class="text-lg font-semibold mb-2">Atur Jadwal Ujian Proposal</h3>
                            <p class="text-sm text-muted-foreground mb-4">
                                Proposal Anda telah disetujui! Silakan tentukan tanggal, tempat, dan waktu pelaksanaan ujian proposal.
                            </p>

                            <Button @click="openJadwalModal">
                                <Calendar class="h-4 w-4" />
                                Atur Jadwal Ujian
                            </Button>
                        </div>

                        <Separator />

                        <!-- Status Info -->
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center gap-2 mb-2">
                                <CheckCircle class="h-5 w-5 text-green-600" />
                                <span class="font-medium text-green-800">Proposal Disetujui Admin</span>
                            </div>
                            <p class="text-sm text-green-700">
                                Selamat! Proposal ujian Anda telah disetujui. Silakan mengatur jadwal ujian.
                            </p>
                        </div>

                        <!-- Tesis Info -->
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-sm text-muted-foreground mb-1">Judul Tesis</h4>
                                <p class="text-sm">{{ tesis.us_judul }}</p>
                            </div>
                        </div>

                        <!-- Supervisors -->
                        <div>
                            <h4 class="font-medium text-sm text-muted-foreground mb-3">Dosen Pembimbing</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium">{{ tesis.dosen_pembimbing_satu.user.nama }}</p>
                                    <p class="text-xs text-muted-foreground">Pembimbing Utama</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ tesis.dosen_pembimbing_dua.user.nama }}</p>
                                    <p class="text-xs text-muted-foreground">Pembimbing Pendamping</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else-if="currentStep === 5 && ujianProposal && tesis">
                <!-- Step 5: Completed -->
                <Card>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <div
                                v-for="(step, index) in steps"
                                :key="step.id"
                                class="flex items-center"
                                :class="{ 'flex-1': index < steps.length - 1 }"
                            >
                                <!-- Step circle -->
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-colors"
                                        :class="[
                                            step.id <= currentStep
                                                ? 'bg-primary border-primary text-primary-foreground'
                                                : 'bg-muted border-muted-foreground/25 text-muted-foreground'
                                        ]"
                                    >
                                        <component :is="step.icon" class="h-5 w-5" />
                                    </div>
                                    <div class="mt-2 text-center max-w-[120px]">
                                        <p class="text-sm font-medium">{{ step.title }}</p>
                                        <p class="text-xs text-muted-foreground mt-1">{{ step.description }}</p>
                                    </div>
                                </div>

                                <!-- Connector line -->
                                <div
                                    v-if="index < steps.length - 1"
                                    class="flex-1 h-0.5 mx-4 transition-colors"
                                    :class="[
                                        step.id < currentStep
                                            ? 'bg-primary'
                                            : 'bg-muted-foreground/25'
                                    ]"
                                />
                            </div>
                        </div>
                    </CardContent>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <Badge :variant="getStatusBadge(ujianProposal.status_seminar).variant">
                                    {{ getStatusBadge(ujianProposal.status_seminar).text }}
                                </Badge>
                            </div>

                            <Separator />

                            <div class="text-center py-8">
                                <CheckCircle class="h-16 w-16 mx-auto text-green-500 mb-4" />
                                <h3 class="text-lg font-semibold mb-2">Ujian Proposal Siap Dilaksanakan</h3>
                                <p class="text-muted-foreground mb-6">
                                    Selamat! Semua persyaratan ujian proposal telah lengkap dan jadwal telah ditentukan.
                                </p>
                            </div>

                            <!-- Jadwal Info -->
                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <h4 class="font-medium text-blue-800 mb-3 flex items-center gap-2">
                                    <Calendar class="h-5 w-5" />
                                    Jadwal Ujian Proposal
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-blue-700">Tanggal:</span>
                                        <p class="text-blue-600">{{ formatDate(ujianProposal.tanggal) }}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-blue-700">Waktu:</span>
                                        <p class="text-blue-600">{{ formatTime(ujianProposal.jam_mulai) }} - {{ formatTime(ujianProposal.jam_selesai) }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <span class="font-medium text-blue-700">Tempat:</span>
                                        <p class="text-blue-600 flex items-center gap-1">
                                            <MapPin class="h-4 w-4" />
                                            {{ ujianProposal.tempat }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Files -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <Button
                                    variant="outline"
                                    @click="openPdfModalVue(`/storage/${ujianProposal.kartu_bimbingan}`, 'Kartu Bimbingan')"
                                >
                                    <BookOpen class="h-4 w-4 mr-2" />
                                    Lihat Kartu Bimbingan
                                </Button>

                                <Button
                                    variant="outline"
                                    @click="openPdfModalVue(`/storage/${ujianProposal.surat_kelayakan}`, 'Surat Kelayakan')"
                                >
                                    <FileText class="h-4 w-4 mr-2" />
                                    Lihat Surat Kelayakan
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- PDF Modal -->
        <teleport to="body">
            <div v-if="showPdfModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
                <div class="relative bg-white rounded-lg shadow-xl w-[90%] max-w-3xl h-[80%] flex flex-col">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-4 border-b">
                        <h2 class="text-lg font-semibold">{{ pdfTitle }}</h2>
                        <button @click="closePdfModal" class="text-gray-500 hover:text-gray-700">
                            ✕
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

        <!-- Upload Kartu Bimbingan Modal -->
        <teleport to="body">
            <div v-if="showKartuUploadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
                <div class="relative bg-white rounded-lg shadow-xl w-[90%] max-w-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Upload Kartu Bimbingan</h2>
                    <form @submit.prevent="submitKartuBimbingan" class="space-y-4">
                        <div>
                            <Label for="kartu_bimbingan">File Kartu Bimbingan (PDF)</Label>
                            <Input
                                id="kartu_bimbingan"
                                type="file"
                                accept=".pdf"
                                @change="handleKartuFileChange"
                                class="mt-1"
                            />
                            <p class="text-xs text-muted-foreground mt-1">
                                Maksimal 10MB, format PDF
                            </p>
                            <div v-if="kartuForm.errors.kartu_bimbingan" class="text-red-500 text-xs mt-1">
                                {{ kartuForm.errors.kartu_bimbingan }}
                            </div>
                        </div>

                        <div class="flex gap-2 justify-end">
                            <Button type="button" variant="outline" @click="closeKartuUploadModal">
                                Batal
                            </Button>
                            <Button
                                type="submit"
                                :disabled="kartuForm.processing || !kartuForm.kartu_bimbingan"
                            >
                                <Upload class="h-4 w-4 mr-2" />
                                {{ kartuForm.processing ? 'Mengupload...' : 'Upload' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </teleport>

        <!-- Upload Surat Modal -->
        <teleport to="body">
            <div v-if="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
                <div class="relative bg-white rounded-lg shadow-xl w-[90%] max-w-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Upload Surat Kelayakan</h2>
                    <form @submit.prevent="submitUploadSurat" class="space-y-4">
                        <div>
                            <Label for="file_surat_kelayakan">File Surat (PDF)</Label>
                            <Input
                                id="file_surat_kelayakan"
                                type="file"
                                accept=".pdf"
                                @change="handleFileChange"
                                class="mt-1"
                            />
                            <p class="text-xs text-muted-foreground mt-1">
                                Maksimal 5MB, format PDF
                            </p>
                            <div v-if="uploadForm.errors.file_surat_kelayakan" class="text-red-500 text-xs mt-1">
                                {{ uploadForm.errors.file_surat_kelayakan }}
                            </div>
                        </div>

                        <div class="flex gap-2 justify-end">
                            <Button type="button" variant="outline" @click="closeUploadModal">
                                Batal
                            </Button>
                            <Button
                                type="submit"
                                :disabled="uploadForm.processing || !uploadForm.file_surat_kelayakan"
                            >
                                <Upload class="h-4 w-4 mr-2" />
                                {{ uploadForm.processing ? 'Mengupload...' : 'Upload' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </teleport>

        <!-- Jadwal Modal -->
        <teleport to="body">
            <div v-if="showJadwalModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
                <div class="relative bg-white rounded-lg shadow-xl w-[90%] max-w-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Atur Jadwal Ujian Proposal</h2>
                    <form @submit.prevent="submitJadwal" class="space-y-4">
                        <div>
                            <Label for="tanggal">Tanggal Ujian</Label>
                            <Input
                                id="tanggal"
                                type="date"
                                v-model="jadwalForm.tanggal"
                                class="mt-1"
                                required
                            />
                            <div v-if="jadwalForm.errors.tanggal" class="text-red-500 text-xs mt-1">
                                {{ jadwalForm.errors.tanggal }}
                            </div>
                        </div>

                        <div>
                            <Label for="tempat">Tempat Ujian</Label>
                            <Input
                                id="tempat"
                                type="text"
                                v-model="jadwalForm.tempat"
                                placeholder="Masukkan tempat ujian"
                                class="mt-1"
                                required
                            />
                            <div v-if="jadwalForm.errors.tempat" class="text-red-500 text-xs mt-1">
                                {{ jadwalForm.errors.tempat }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <Label for="jam_mulai">Jam Mulai</Label>
                                <Input
                                    id="jam_mulai"
                                    type="time"
                                    v-model="jadwalForm.jam_mulai"
                                    class="mt-1"
                                    required
                                />
                                <div v-if="jadwalForm.errors.jam_mulai" class="text-red-500 text-xs mt-1">
                                    {{ jadwalForm.errors.jam_mulai }}
                                </div>
                            </div>

                            <div>
                                <Label for="jam_selesai">Jam Selesai</Label>
                                <Input
                                    id="jam_selesai"
                                    type="time"
                                    v-model="jadwalForm.jam_selesai"
                                    class="mt-1"
                                    required
                                />
                                <div v-if="jadwalForm.errors.jam_selesai" class="text-red-500 text-xs mt-1">
                                    {{ jadwalForm.errors.jam_selesai }}
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 justify-end">
                            <Button type="button" variant="outline" @click="closeJadwalModal">
                                Batal
                            </Button>
                            <Button
                                type="submit"
                                :disabled="jadwalForm.processing"
                            >
                                <Calendar class="h-4 w-4 mr-2" />
                                {{ jadwalForm.processing ? 'Menyimpan...' : 'Simpan Jadwal' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </teleport>
    </AppLayout>
</template>
