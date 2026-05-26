<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import FormInput from '@/components/FormInput.vue';
import FormSelect from '@/components/FormSelect.vue';
import FormCheckboxGroup from '@/components/FormCheckboxGroup.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { BreadcrumbItem, Role } from '@/types';
import { computed } from 'vue';
import { Button } from '@/components/ui';
import { TooltipProvider, Tooltip, TooltipTrigger, TooltipContent } from '@/components/ui/tooltip';
import { ArrowLeftToLine, CircleHelp } from 'lucide-vue-next';

interface Props {
    roles: Role[]
}
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/admin/users' },
    { title: 'Tambah User', href: '#' },
];

const form = useForm({
    name: '',
    email: '',
    nomor_induk: '',
    phone: '',
    is_active: true,
    roles: [] as number[],
});

const selectedRoleNames = computed(() =>
    props.roles.filter(r => form.roles.includes(r.id)).map(r => r.role_name)
);
const isMahasiswa = computed(() => selectedRoleNames.value.includes('mahasiswa'));
const isDosen = computed(() => selectedRoleNames.value.some(r => ['dosen', 'koordinator', 'admin', 'kaprodi'].includes(r)));

const statusValue = computed({
    get: () => form.is_active ? '1' : '0',
    set: (val: string) => form.is_active = val === '1',
});

const roleOptions = computed(() => props.roles.map(r => {
    const isMhs = r.role_name === 'mahasiswa'
    const isDisabled = isMhs ? isDosen.value : isMahasiswa.value
    return {
        id: r.id,
        label: r.role_name,
        disabled: isDisabled,
        disabledTooltip: isDisabled
            ? (isMhs ? 'Tidak dapat dipilih bersamaan dengan admin / dosen / koordinator' : 'Tidak dapat dipilih bersamaan dengan mahasiswa')
            : undefined,
    }
}));

const hasData = computed(() =>
    !!form.name &&
    !!form.email &&
    !!form.nomor_induk &&
    form.roles.length > 0
);

const submit = () => form.post(route('admin.users.store'), {
    preserveScroll: true,
    onError: () => toast.error('Gagal menyimpan user.', { description: 'Periksa kembali data yang diisi.' }),
});
</script>

<template>
    <Head title="Tambah User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="w-full max-w-2xl mx-auto flex flex-col gap-6">
                <PageHeader
                    title="Tambah Pengguna"
                    description="Masukkan informasi pengguna yang akan didaftarkan ke sistem. Detail profil dapat dilengkapi melalui manajemen mahasiswa atau dosen." />
                <!-- Form -->
                <form @submit.prevent="submit" novalidate class="flex flex-col gap-6">
                    <FormInput label="Nama Lengkap" v-model="form.name"
                        placeholder="Masukkan nama lengkap" :error="form.errors.name" />
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput label="Nomor Induk" v-model="form.nomor_induk"
                            placeholder="NIM / NIP" :error="form.errors.nomor_induk" />
                        <FormInput label="Email" type="email" v-model="form.email"
                            placeholder="contoh@email.com" :error="form.errors.email" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput label="No. Telepon" v-model="form.phone"
                            placeholder="(xxx) xxx-xxxx" :error="form.errors.phone" />
                        <FormSelect label="Status" v-model="statusValue"
                            :options="[{ label: 'Aktif', value: '1' }, { label: 'Tidak Aktif', value: '0' }]"
                            :error="form.errors.is_active" />
                    </div>
                    <FormCheckboxGroup label="Hak Akses" v-model="form.roles"
                        :options="roleOptions" :cols="2" :error="form.errors.roles">
                        <template #label>
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <CircleHelp class="w-3.5 h-3.5 text-muted-foreground cursor-help" />
                                    </TooltipTrigger>
                                    <TooltipContent side="right" class="max-w-96">
                                        <p>Mahasiswa tidak dapat dipilih bersamaan dengan admin, dosen, atau koordinator.</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </template>
                    </FormCheckboxGroup>
                    <hr />
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" as-child>
                            <Link :href="route('admin.users.index')" class="flex items-center gap-2">
                                <ArrowLeftToLine class="w-4 h-4" />
                                Kembali
                            </Link>
                        </Button>
                        <Button type="submit" size="sm" :disabled="form.processing || !hasData" class="flex items-center gap-2">
                            Simpan
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
