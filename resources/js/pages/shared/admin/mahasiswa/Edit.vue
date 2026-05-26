<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import FormInput from '@/components/FormInput.vue';
import FormSelect from '@/components/FormSelect.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { type BreadcrumbItem } from '@/types';
import { type DosenOption } from '@/types/user';
import { computed } from 'vue';
import { Button } from '@/components/ui';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { ArrowLeftToLine } from 'lucide-vue-next';

interface MahasiswaEditData {
    id: number
    nama_mhs: string
    nim: string
    angkatan: number | null
    gender: string | null
    status_mhs: string
    pem_akademik: number | null
}

interface Props {
    mahasiswa: MahasiswaEditData
    dosenOptions: DosenOption[]
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Mahasiswa', href: route('admin.mahasiswa.index') },
    { title: props.mahasiswa.nama_mhs, href: route('admin.mahasiswa.show', props.mahasiswa.id) },
    { title: 'Edit', href: '#' },
];

const { getInitials } = useInitials();

const form = useForm({
    angkatan: props.mahasiswa.angkatan ? String(props.mahasiswa.angkatan) : '',
    gender: props.mahasiswa.gender || '',
    status_mhs: props.mahasiswa.status_mhs,
    pem_akademik: props.mahasiswa.pem_akademik ? String(props.mahasiswa.pem_akademik) : '',
});

const initial = {
    angkatan: props.mahasiswa.angkatan ? String(props.mahasiswa.angkatan) : '',
    gender: props.mahasiswa.gender || '',
    status_mhs: props.mahasiswa.status_mhs,
    pem_akademik: props.mahasiswa.pem_akademik ? String(props.mahasiswa.pem_akademik) : '',
};

const hasChanges = computed(() =>
    form.angkatan !== initial.angkatan ||
    form.gender !== initial.gender ||
    form.status_mhs !== initial.status_mhs ||
    form.pem_akademik !== initial.pem_akademik
);

const submit = () => form.put(route('admin.mahasiswa.update', props.mahasiswa.id), {
    preserveScroll: true,
    onError: () => toast.error('Gagal memperbarui data mahasiswa.', { description: 'Periksa kembali data yang diisi.' }),
});
</script>

<template>
    <Head title="Edit Mahasiswa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="w-full max-w-2xl mx-auto flex flex-col gap-6">
                <PageHeader title="Edit Mahasiswa" description="Perbarui data mahasiswa program studi." />

                <!-- Section: Identity -->
                <div class="flex flex-col items-center gap-2 px-5 py-5">
                    <Avatar class="h-25 w-25 shrink-0 overflow-hidden rounded-full">
                        <AvatarFallback class="text-2xl font-bold text-primary bg-primary/10">
                            {{ getInitials(mahasiswa.nama_mhs) }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="flex flex-col items-center gap-0.5">
                        <p class="text-lg font-bold leading-tight">{{ mahasiswa.nama_mhs }}</p>
                        <p class="text-sm font-mono text-muted-foreground">{{ mahasiswa.nim }}</p>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" novalidate class="flex flex-col gap-6">
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            label="Angkatan"
                            type="number"
                            v-model="form.angkatan"
                            placeholder="2024"
                            :error="form.errors.angkatan" />
                        <FormSelect
                            label="Jenis Kelamin"
                            v-model="form.gender"
                            placeholder="Pilih jenis kelamin"
                            :options="[{ label: 'Laki-laki', value: 'L' }, { label: 'Perempuan', value: 'P' }]"
                            :error="form.errors.gender" />
                    </div>
                    <FormSelect
                        label="Status Mahasiswa"
                        v-model="form.status_mhs"
                        :options="[
                            { label: 'Aktif',   value: 'aktif' },
                            { label: 'Lulus',   value: 'lulus' },
                            { label: 'Dropout', value: 'dropout' },
                        ]"
                        :error="form.errors.status_mhs" />
                    <FormSelect
                        label="Pembimbing Akademik"
                        v-model="form.pem_akademik"
                        placeholder="Pilih dosen (opsional)"
                        :options="dosenOptions.map(d => ({ label: d.label, value: String(d.id) }))"
                        :error="form.errors.pem_akademik" />
                    <hr />
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" as-child>
                            <Link :href="route('admin.mahasiswa.index')" class="flex items-center gap-2">
                                <ArrowLeftToLine class="w-4 h-4" />
                                Batal
                            </Link>
                        </Button>
                        <Button type="submit" size="sm" :disabled="form.processing || !hasChanges" class="flex items-center gap-2">
                            Simpan Perubahan
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
