<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { FileText, Clock, CheckCircle, Plus, Download, Upload, FileCheck } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';

const showPdfModal = ref(false);
const pdfUrl = ref('');
const pdfTitle = ref('');
const showUploadModal = ref(false);

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

interface Draft {
    id: number;
    us_judul: string;
    us_abstrak: string;
    status: string;
    tgl_pengajuan: string;
    tgl_upload_surat: string;
    file_sk_pembimbing: string;
    file_khs: string;
    file_krs: string;
    file_surat_permohonan: string;
    ket_dospem_satu: string;
    ket_dospem_dua: string;
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

interface Props {
    draft: Draft | null;
    currentStep: number;
}

const props = defineProps<Props>();

// Form for uploading surat permohonan
const uploadForm = useForm({
    file_surat_permohonan: null as File | null,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard Mahasiswa',
        href: route('mahasiswa.dashboard'),
    },
    {
        title: 'Draft Pratesis',
        href: route('mahasiswa.draft.index'),
    },
];

const steps = [
    { id: 1, title: 'Form Pengajuan', icon: FileText, description: 'Isi formulir pengajuan draft pratesis' },
    { id: 2, title: 'Upload Surat', icon: Upload, description: 'Upload surat permohonan bimbingan' },
    { id: 3, title: 'Persetujuan', icon: Clock, description: 'Menunggu persetujuan admin' },
    { id: 4, title: 'Selamat!', icon: CheckCircle, description: 'Draft pratesis telah disetujui' }
];

function getStatusBadge(status: string) {
    switch (status) {
        case 'pending':
            return { text: 'Menunggu Upload Surat', variant: 'secondary' };
        case 'surat_uploaded':
            return { text: 'Surat Telah Diupload', variant: 'default' };
        case 'approved':
            return { text: 'Disetujui', variant: 'default' };
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

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        uploadForm.file_surat_permohonan = target.files[0];
    }
}

function submitUploadSurat() {
    if (!props.draft) return;

    uploadForm.post(route('mahasiswa.draft.upload-surat', props.draft.id), {
        onSuccess: () => {
            closeUploadModal();
        },
        onError: (errors) => {
            console.error('Upload errors:', errors);
        }
    });
}

// function downloadTemplate() {
//     if (!props.draft) return;
//     window.open(route('mahasiswa.draft.template-surat', props.draft.id), '_blank');
// }
</script>

<template>
    <Head title="Draft Pratesis" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Draft Pratesis</h1>
                    <p class="text-muted-foreground">Kelola pengajuan draft pratesis Anda</p>
                </div>
                <Button v-if="!draft" as-child>
                    <Link :href="route('mahasiswa.draft.create')">
                        <Plus class="h-4 w-4" />
                        Ajukan Draft
                    </Link>
                </Button>
            </div>

            <!-- Content based on step -->
            <div v-if="currentStep === 1">
                <!-- Step 1: Form -->
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
                            <FileText class="h-16 w-16 mx-auto text-muted-foreground mb-4" />
                            <h3 class="text-lg font-semibold mb-2">Belum Ada Draft</h3>
                            <p class="text-muted-foreground mb-4">
                                Anda belum mengajukan draft pratesis. Klik tombol di bawah untuk memulai pengajuan.
                            </p>
                            <Button as-child>
                                <Link :href="route('mahasiswa.draft.create')">
                                    <Plus class="h-4 w-4" />
                                    Ajukan Draft Sekarang
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else-if="currentStep === 2 && draft">
                <!-- Step 2: Upload Surat -->
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
                            <h3 class="text-lg font-semibold mb-2">Upload Surat Permohonan Bimbingan</h3>
                            <p class="text-sm text-muted-foreground mb-4">
                                Unduh template surat, isi dan tanda tangani, kemudian upload kembali dalam format PDF.
                            </p>

                            <!-- Template dan Upload Actions -->
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <a
                                    href="https://docs.google.com/document/d/1M8FQsndmpfBeYzZzGgyn3K86payYDJf5YQjGQQoN2zU/edit?tab=t.0"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                <Button variant="outline">
                                    <Download class="h-4 w-4 mr-2" />
                                    Download Template Surat
                                </Button>
                                </a>
                                <Button @click="openUploadModal">
                                    <Upload class="h-4 w-4 mr-2" />
                                    Upload Surat Bertanda Tangan
                                </Button>
                            </div>
                        </div>

                        <Separator />

                        <!-- Draft Info -->
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-sm text-muted-foreground mb-1">Judul Penelitian</h4>
                                <p class="text-sm">{{ draft.us_judul }}</p>
                            </div>

                            <div v-if="draft.us_abstrak">
                                <h4 class="font-medium text-sm text-muted-foreground mb-1">Abstrak</h4>
                                <p class="text-sm">{{ draft.us_abstrak }}</p>
                            </div>
                        </div>

                        <!-- Supervisors - 2 Columns -->
                        <div>
                            <h4 class="font-medium text-sm text-muted-foreground mb-3">Dosen Pembimbing</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium">{{ draft.dosen_pembimbing_satu.user.nama }}</p>
                                    <p class="text-xs text-muted-foreground">Pembimbing Utama</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ draft.dosen_pembimbing_dua.user.nama }}</p>
                                    <p class="text-xs text-muted-foreground">Pembimbing Pendamping</p>
                                </div>
                            </div>
                        </div>

                        <Separator />

                        <!-- File Actions -->
                        <div class="flex flex-col sm:flex-row gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                class="flex-1"
                                @click="openPdfModalVue(`/storage/file_lampiran_khs/${draft.mahasiswa.nim}_file_khs.pdf`, 'File KHS')"
                            >
                                <FileText class="h-4 w-4 mr-2" />
                                Lihat KHS
                            </Button>

                            <Button
                                variant="outline"
                                size="sm"
                                class="flex-1"
                                @click="openPdfModalVue(`/storage/file_lampiran_krs/${draft.mahasiswa.nim}_file_krs.pdf`, 'File KRS')"
                            >
                                <FileText class="h-4 w-4 mr-2" />
                                Lihat KRS
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else-if="currentStep === 3 && draft">
                <!-- Step 3: Waiting for final approval after surat uploaded -->
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
                            <FileCheck class="h-12 w-12 mx-auto text-green-500 mb-4" />
                            <h3 class="text-lg font-semibold mb-2">Surat Telah Diupload</h3>
                            <p class="text-sm text-muted-foreground">
                                Surat permohonan telah diupload pada {{ formatDate(draft.tgl_upload_surat) }}.
                                Menunggu persetujuan final dari koordinator tesis.
                            </p>
                        </div>

                        <Separator />

                        <!-- Uploaded Surat Info -->
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center gap-2 mb-2">
                                <FileCheck class="h-5 w-5 text-green-600" />
                                <span class="font-medium text-green-800">Surat Permohonan Berhasil Diupload</span>
                            </div>
                            <p class="text-sm text-green-700">
                                File: {{ draft.file_surat_permohonan ? draft.file_surat_permohonan.split('/').pop() : 'Surat Permohonan' }}
                            </p>
                            <Button
                                variant="outline"
                                size="sm"
                                class="mt-2"
                                @click="openPdfModalVue(`/storage/${draft.file_surat_permohonan}`, 'Surat Permohonan')"
                            >
                                <FileText class="h-4 w-4 mr-2" />
                                Lihat Surat
                            </Button>
                        </div>

                        <!-- Draft Info -->
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-sm text-muted-foreground mb-1">Judul Penelitian</h4>
                                <p class="text-sm">{{ draft.us_judul }}</p>
                            </div>
                        </div>

                        <!-- Supervisors -->
                        <div>
                            <h4 class="font-medium text-sm text-muted-foreground mb-3">Dosen Pembimbing</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium">{{ draft.dosen_pembimbing_satu.user.nama }}</p>
                                    <p class="text-xs text-muted-foreground">Pembimbing Utama</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ draft.dosen_pembimbing_dua.user.nama }}</p>
                                    <p class="text-xs text-muted-foreground">Pembimbing Pendamping</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else-if="currentStep === 4 && draft">
                <!-- Step 4: Approved -->
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
                                <Badge variant="default">
                                    {{ getStatusBadge(draft.status).text }}
                                </Badge>
                                <span class="text-sm text-muted-foreground">
                                    Disetujui pada {{ formatDate(draft.tgl_pengajuan) }}
                                </span>
                            </div>

                            <Separator />

                            <div class="text-center py-8">
                                <CheckCircle class="h-16 w-16 mx-auto text-green-500 mb-4" />
                                <h3 class="text-lg font-semibold mb-2">Draft Pratesis Disetujui</h3>
                                <p class="text-muted-foreground mb-6">
                                    Selamat! Draft pratesis Anda telah disetujui oleh admin.
                                    Anda dapat mengunduh SK Pembimbing di bawah ini.
                                </p>

                                <div v-if="draft.file_sk_pembimbing">
                                    <Button as-child>
                                        <a :href="`/storage/${draft.file_sk_pembimbing}`" download>
                                            <Download class="h-4 w-4 mr-2" />
                                            Download SK Pembimbing
                                        </a>
                                    </Button>
                                </div>
                                <div v-else>
                                    <p class="text-sm text-muted-foreground">
                                        SK Pembimbing belum tersedia. Silakan hubungi admin.
                                    </p>
                                </div>
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

        <!-- Upload Modal -->
        <teleport to="body">
            <div v-if="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
                <div class="relative bg-white rounded-lg shadow-xl w-[90%] max-w-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Upload Surat Permohonan</h2>
                    <form @submit.prevent="submitUploadSurat" class="space-y-4">
                        <div>
                            <Label for="file_surat_permohonan">File Surat (PDF)</Label>
                            <Input
                                id="file_surat_permohonan"
                                type="file"
                                accept=".pdf"
                                @change="handleFileChange"
                                class="mt-1"
                            />
                            <p class="text-xs text-muted-foreground mt-1">
                                Maksimal 5MB, format PDF
                            </p>
                            <div v-if="uploadForm.errors.file_surat_permohonan" class="text-red-500 text-xs mt-1">
                                {{ uploadForm.errors.file_surat_permohonan }}
                            </div>
                        </div>

                        <div class="flex gap-2 justify-end">
                            <Button type="button" variant="outline" @click="closeUploadModal">
                                Batal
                            </Button>
                            <Button
                                type="submit"
                                :disabled="uploadForm.processing || !uploadForm.file_surat_permohonan"
                            >
                                <Upload class="h-4 w-4 mr-2" />
                                {{ uploadForm.processing ? 'Mengupload...' : 'Upload' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </teleport>
    </AppLayout>
</template>
