<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import FormInput from '@/components/FormInput.vue';
import FormSelect from '@/components/FormSelect.vue';
import FormCheckboxGroup from '@/components/FormCheckboxGroup.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { BreadcrumbItem, User, Role } from '@/types';
import { computed } from 'vue';
import { Button } from '@/components/ui';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { TooltipProvider, Tooltip, TooltipTrigger, TooltipContent } from '@/components/ui/tooltip';
import { ArrowLeftToLine, CircleHelp } from 'lucide-vue-next';

interface Props {
    user: User
    roles: Role[]
    isSelf: boolean
}
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/admin/users' },
    { title: props.user.name, href: route('admin.users.show', props.user.id) },
    { title: 'Edit', href: '#' },
];

const { getInitials } = useInitials();
const showAvatar = computed(() => !!props.user.photo);
const avatarUrl = computed(() => props.user.photo_url ?? null);

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    nomor_induk: props.user.nomor_induk,
    phone: props.user.phone || '',
    is_active: props.user.is_active,
    roles: Array.isArray(props.user.roles) ? props.user.roles.map(r => r.id) : [],
});

const initial = {
    name: props.user.name,
    email: props.user.email,
    nomor_induk: props.user.nomor_induk,
    phone: props.user.phone || '',
    is_active: props.user.is_active,
    roles: Array.isArray(props.user.roles) ? props.user.roles.map(r => r.id).sort() : [],
};

const hasChanges = computed(() =>
    form.name !== initial.name        ||
    form.email !== initial.email       ||
    form.nomor_induk !== initial.nomor_induk ||
    form.phone !== initial.phone       ||
    form.is_active !== initial.is_active   ||
    JSON.stringify([...form.roles].sort()) !== JSON.stringify(initial.roles)
);

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

const submit = () => form.put(route('admin.users.update', props.user.id), {
    preserveScroll: true,
    onError: () => toast.error('Gagal memperbarui user.', { description: 'Periksa kembali data yang diisi.' }),
});
</script>

<template>
    <Head title="Edit User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="w-full max-w-2xl mx-auto flex flex-col gap-6">
                <PageHeader title="Edit Pengguna" description="Perbarui informasi dan hak akses pengguna." />
                <!-- Section: Identity -->
                <div class="flex flex-col items-center gap-2 px-5 py-5">
                    <Avatar class="h-25 w-25 shrink-0 overflow-hidden rounded-full">
                        <AvatarImage v-if="showAvatar" :src="avatarUrl!" :alt="user.name" />
                        <AvatarFallback class="text-2xl font-bold text-primary bg-primary/10">
                            {{ getInitials(user.name) }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="flex flex-col items-center gap-0.5">
                        <p class="text-lg font-bold leading-tight">{{ user.name }}</p>
                        <p class="text-sm font-mono text-muted-foreground">{{ user.nomor_induk }}</p>
                    </div>
                </div>
                <!-- Form -->
                <form @submit.prevent="submit" novalidate class="flex flex-col gap-6">
                    <FormInput label="Nama Lengkap" v-model="form.name" :error="form.errors.name" />
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput label="Nomor Induk" v-model="form.nomor_induk" :error="form.errors.nomor_induk" />
                        <FormInput label="Email" type="email" v-model="form.email" :error="form.errors.email" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput label="No. Telepon" v-model="form.phone" :error="form.errors.phone" placeholder="(xxx) xxx-xxxx"/>
                        <FormSelect label="Status" v-model="statusValue"
                            :options="[{ label: 'Aktif', value: '1' }, { label: 'Tidak Aktif', value: '0' }]"
                            :disabled="isSelf"
                            :error="form.errors.is_active" />
                    </div>
                    <FormCheckboxGroup label="Hak Akses" v-model="form.roles"
                        :options="roleOptions" :cols="2" :error="form.errors.roles">
                        <template #label>
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <CircleHelp class="w-3.5 h-3.5 cursor-help" />
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
                        <Button type="submit" size="sm" :disabled="form.processing || !hasChanges" class="flex items-center">
                            Simpan Perubahan
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
