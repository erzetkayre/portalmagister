<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { ArrowLeft, Upload, FileText, AlertCircle } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

interface Dosen {
    id: number;
    nama_dosen: string;
    user: {
        nama: string;
    };
}

interface Props {
    dosens: Dosen[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard Mahasiswa',
        href: route('mahasiswa.dashboard'),
    },
    {
        title: 'Draft Pratesis',
        href: route('mahasiswa.draft.index'),
    },
    {
        title: 'Buat Draft',
        href: route('mahasiswa.draft.create'),
    },
];

const form = useForm({
    us_judul: '',
    us_abstrak: '',
    us_dospem_satu: '',
    us_dospem_dua: '',
    ket_dospem_satu: '',
    ket_dospem_dua: '',
    file_khs: null as File | null,
    file_krs: null as File | null,
});

function handleFileUpload(event: Event, field: 'file_khs' | 'file_krs') {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form[field] = target.files[0];
    }
}

function onSubmit() {
    form.post(route('mahasiswa.draft.store'), {
        forceFormData: true,
        onSuccess: () => {
            // Will redirect to index page
        },
    });
}
</script>

<template>
    <Head title="Buat Draft Pratesis" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Buat Draft Pratesis</h1>
                    <p class="text-muted-foreground">Lengkapi formulir pengajuan draft pratesis</p>
                </div>
                <Button variant="outline" as-child>
                    <Link :href="route('mahasiswa.draft.index')">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Kembali
                    </Link>
                </Button>
            </div>

            <!-- Form -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <FileText class="h-5 w-5" />
                        Formulir Pengajuan Draft Pratesis
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="onSubmit" class="space-y-6">
                        <!-- Judul Penelitian -->
                        <div class="space-y-2">
                            <Label for="us_judul">Judul Penelitian <span class="text-primary">*</span></Label>
                            <Textarea
                                id="us_judul"
                                v-model="form.us_judul"
                                placeholder="Masukkan judul penelitian Anda"
                                rows="3"
                                :class="{ 'border-red-500': form.errors.us_judul }"
                            />
                            <p v-if="form.errors.us_judul" class="text-sm text-red-500">
                                {{ form.errors.us_judul }}
                            </p>
                        </div>

                        <!-- Abstrak -->
                        <div class="space-y-2">
                            <Label for="us_abstrak">Abstrak (Opsional)</Label>
                            <Textarea
                                id="us_abstrak"
                                v-model="form.us_abstrak"
                                placeholder="Masukkan abstrak penelitian Anda"
                                rows="5"
                                :class="{ 'border-red-500': form.errors.us_abstrak }"
                            />
                            <p v-if="form.errors.us_abstrak" class="text-sm text-red-500">
                                {{ form.errors.us_abstrak }}
                            </p>
                        </div>

                        <!-- Dosen Pembimbing -->
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2 ">
                                    <Label for="us_dospem_satu">Dosen Pembimbing 1 <span class="text-primary">*</span></Label>
                                    <Select v-model="form.us_dospem_satu" class="w-full">
                                        <SelectTrigger class="w-full h-auto min-h-[2.5rem]" :class="{ 'border-red-500': form.errors.us_dospem_satu }">
                                            <SelectValue placeholder="Pilih dosen pembimbing 1" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="dosen in dosens"
                                                :key="dosen.id"
                                                :value="dosen.id.toString()"
                                            >
                                                {{ dosen.user.nama }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.us_dospem_satu" class="text-sm text-red-500">
                                        {{ form.errors.us_dospem_satu }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="us_dospem_dua">Dosen Pembimbing 2 <span class="text-primary">*</span></Label>
                                    <Select v-model="form.us_dospem_dua" class="w-full">
                                        <SelectTrigger class="w-full h-auto min-h-[2.5rem]" :class="{ 'border-red-500': form.errors.us_dospem_dua }">
                                            <SelectValue placeholder="Pilih dosen pembimbing 2" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="dosen in dosens"
                                                :key="dosen.id"
                                                :value="dosen.id.toString()"
                                                :disabled="dosen.id.toString() === form.us_dospem_satu"
                                            >
                                                {{ dosen.user.nama }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.us_dospem_dua" class="text-sm text-red-500">
                                        {{ form.errors.us_dospem_dua }}
                                    </p>
                                </div>
                            </div>

                            <!-- Alasan Pemilihan Dosen -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="ket_dospem_satu">Alasan Memilih Dosen Pembimbing 1 <span class="text-primary">*</span></Label>
                                    <Textarea
                                        id="ket_dospem_satu"
                                        v-model="form.ket_dospem_satu"
                                        placeholder="Jelaskan alasan memilih dosen pembimbing 1"
                                        rows="3"
                                        :class="{ 'border-red-500': form.errors.ket_dospem_satu }"
                                    />
                                    <p v-if="form.errors.ket_dospem_satu" class="text-sm text-red-500">
                                        {{ form.errors.ket_dospem_satu }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="ket_dospem_dua">Alasan Memilih Dosen Pembimbing 2 <span class="text-primary">*</span></Label>
                                    <Textarea
                                        id="ket_dospem_dua"
                                        v-model="form.ket_dospem_dua"
                                        placeholder="Jelaskan alasan memilih dosen pembimbing 2"
                                        rows="3"
                                        :class="{ 'border-red-500': form.errors.ket_dospem_dua }"
                                    />
                                    <p v-if="form.errors.ket_dospem_dua" class="text-sm text-red-500">
                                        {{ form.errors.ket_dospem_dua }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="file_khs">File KHS (PDF) <span class="text-primary">*</span></Label>
                                <div class="flex items-center gap-2">
                                    <Input
                                        id="file_khs"
                                        type="file"
                                        accept=".pdf"
                                        @change="handleFileUpload($event, 'file_khs')"
                                        :class="{ 'border-red-500': form.errors.file_khs }"
                                    />
                                    <Upload class="h-4 w-4 text-muted-foreground" />
                                </div>
                                <p v-if="form.errors.file_khs" class="text-sm text-red-500">
                                    {{ form.errors.file_khs }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    Format: PDF, Maksimal 2MB
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="file_krs">File KRS (PDF) <span class="text-primary">*</span></Label>
                                <div class="flex items-center gap-2">
                                    <Input
                                        id="file_krs"
                                        type="file"
                                        accept=".pdf"
                                        @change="handleFileUpload($event, 'file_krs')"
                                        :class="{ 'border-red-500': form.errors.file_krs }"
                                    />
                                    <Upload class="h-4 w-4 text-muted-foreground" />
                                </div>
                                <p v-if="form.errors.file_krs" class="text-sm text-red-500">
                                    {{ form.errors.file_krs }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    Format: PDF, Maksimal 2MB
                                </p>
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <Alert class="text-warning">
                            <AlertCircle class="h-4 w-4" />
                            <AlertDescription class="text-warning text-sm">
                                Pastikan semua data yang Anda masukkan sudah benar. Setelah submit,
                                draft pratesis akan menunggu persetujuan dari admin.
                            </AlertDescription>
                        </Alert>

                        <!-- Submit Button -->
                        <div class="flex justify-end gap-2">
                            <Button variant="outline" type="button" as-child>
                                <Link :href="route('mahasiswa.draft.index')">
                                    Batal
                                </Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Mengirim...' : 'Ajukan Draft' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
