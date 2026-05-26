<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import FormInput from '@/components/FormInput.vue';
import FormSelect from '@/components/FormSelect.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { type BreadcrumbItem } from '@/types';
import { computed } from 'vue';
import { Button } from '@/components/ui';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { ArrowLeftToLine } from 'lucide-vue-next';
import type { Dosen } from '@/types/user';

interface Props {
    dosen: Dosen
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dosen', href: route('admin.dosen.index') },
    { title: props.dosen.nama_dsn, href: route('admin.dosen.show', props.dosen.id) },
    { title: 'Edit', href: '#' },
];

const { getInitials } = useInitials();

const form = useForm({
    nama_dsn: props.dosen.nama_dsn,
    nip: props.dosen.nip,
    kode_dsn: props.dosen.kode_dsn || '',
    bidang_keahlian: props.dosen.bidang_keahlian || '',
    gender: props.dosen.gender || '',
    status_dsn: props.dosen.status_dsn,
});

const initial = {
    nama_dsn: props.dosen.nama_dsn,
    nip: props.dosen.nip,
    kode_dsn: props.dosen.kode_dsn || '',
    bidang_keahlian: props.dosen.bidang_keahlian || '',
    gender: props.dosen.gender || '',
    status_dsn: props.dosen.status_dsn,
};

const hasChanges = computed(() =>
    form.nama_dsn !== initial.nama_dsn ||
    form.nip !== initial.nip ||
    form.kode_dsn !== initial.kode_dsn ||
    form.bidang_keahlian !== initial.bidang_keahlian ||
    form.gender !== initial.gender ||
    form.status_dsn !== initial.status_dsn
);

const submit = () => form.put(route('admin.dosen.update', props.dosen.id), {
    preserveScroll: true,
    onError: () => toast.error('Gagal memperbarui data dosen.', { description: 'Periksa kembali data yang diisi.' }),
});
</script>

<template>
    <Head title="Edit Dosen" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="w-full max-w-2xl mx-auto flex flex-col gap-6">
                <PageHeader title="Edit Dosen" description="Perbarui data dosen program studi." />
                <!-- Section: Identity -->
                <div class="flex flex-col items-center gap-2 px-5 py-5">
                    <Avatar class="h-25 w-25 shrink-0 overflow-hidden rounded-full">
                        <AvatarFallback class="text-2xl font-bold text-primary bg-primary/10">
                            {{ getInitials(dosen.nama_dsn) }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="flex flex-col items-center gap-0.5">
                        <p class="text-lg font-bold leading-tight">{{ dosen.nama_dsn }}</p>
                        <p class="text-sm font-mono text-muted-foreground">{{ dosen.nip }}</p>
                    </div>
                </div>
                <!-- Form -->
                <form @submit.prevent="submit" novalidate class="flex flex-col gap-6">
                    <FormInput label="Nama Dosen" v-model="form.nama_dsn" :error="form.errors.nama_dsn" />
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput label="NIP" v-model="form.nip" placeholder="18 digit NIP" :error="form.errors.nip" />
                        <FormInput label="Kode Dosen" v-model="form.kode_dsn" placeholder="Contoh: BWD" :error="form.errors.kode_dsn" />
                    </div>
                    <FormInput label="Bidang Keahlian" v-model="form.bidang_keahlian" placeholder="Contoh: Sistem Tenaga Listrik" :error="form.errors.bidang_keahlian" />
                    <div class="grid grid-cols-2 gap-4">
                        <FormSelect
                            label="Jenis Kelamin"
                            v-model="form.gender"
                            placeholder="Pilih jenis kelamin"
                            :options="[{ label: 'Laki-laki', value: 'L' }, { label: 'Perempuan', value: 'P' }]"
                            :error="form.errors.gender" />
                        <FormSelect
                            label="Status"
                            v-model="form.status_dsn"
                            :options="[{ label: 'Aktif', value: 'aktif' }, { label: 'Tidak Aktif', value: 'tidak aktif' }]"
                            :error="form.errors.status_dsn" />
                    </div>
                    <hr />
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" as-child>
                            <Link :href="route('admin.dosen.index')" class="flex items-center gap-2">
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
