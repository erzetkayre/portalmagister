<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import FormInput from '@/components/FormInput.vue';
import FormSelect from '@/components/FormSelect.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { BreadcrumbItem, Role } from '@/types';
import { computed } from 'vue';
import { Button, Label } from '@/components/ui';
import { ArrowLeftToLine, SquarePen, Check } from 'lucide-vue-next';

interface Props {
    roles: Role[]
}
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/admin/users' },
    { title: 'Tambah User', href: '#' },
];

// Form Data (kosong)
const form = useForm({
    name: '',
    email: '',
    nomor_induk: '',
    phone: '',
    is_active: true as boolean,
    roles: [] as number[],
});

const submit = () => {
    form.post(route('admin.users.store'), {
        preserveScroll: true,
    });
};

// Status Select
const statusValue = computed({
    get: () => form.is_active ? '1' : '0',
    set: (val: string) => form.is_active = val === '1'
});

// Role Checkbox Helpers
const isRoleChecked = (roleId: number) => {
    return form.roles.includes(roleId);
};

const toggleRole = (roleId: number, checked: boolean) => {
    if (checked) {
        if (!form.roles.includes(roleId)) {
            form.roles.push(roleId);
        }
    } else {
        const index = form.roles.indexOf(roleId);
        if (index > -1) {
            form.roles.splice(index, 1);
        }
    }
};
</script>

<template>
    <Head title="Tambah User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading title="Tambah Pengguna" description="Masukkan informasi pengguna yang akan didaftarkan ke sistem."/>
            <form @submit.prevent="submit" novalidate class="flex flex-col gap-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <FormInput
                        label="Nama User"
                        v-model="form.name"
                        :error="form.errors.name"
                    />
                    <FormInput
                        label="Email"
                        type="email"
                        v-model="form.email"
                        :error="form.errors.email"
                    />
                    <FormInput
                        label="Nomor Induk"
                        v-model="form.nomor_induk"
                        :error="form.errors.nomor_induk"
                    />
                    <FormInput
                        label="No. Telp"
                        v-model="form.phone"
                        :error="form.errors.phone"
                    />
                    <!-- Roles -->
                    <div class="flex flex-col gap-3 my-2 md:col-span-2">
                        <Label>Hak Akses</Label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-3">
                            <label
                                v-for="role in props.roles"
                                :key="role.id"
                                class="hover:bg-accent/50 flex items-center gap-3 rounded-lg border p-3 cursor-pointer transition-colors relative">
                                <input
                                    type="checkbox"
                                    :checked="isRoleChecked(role.id)"
                                    @change="(e) => toggleRole(role.id, (e.target as HTMLInputElement).checked)"
                                    class="peer sr-only"/>
                                <div class="peer-checked:bg-primary peer-checked:text-primary-foreground peer-checked:border-primary peer-focus-visible:border-ring peer-focus-visible:ring-ring/50 peer-focus-visible:ring-[3px] size-4 shrink-0 rounded-[4px] border border-input shadow-xs transition-all outline-none flex items-center justify-center">
                                    <Check
                                        :class="[
                                            'size-3.5 transition-all',
                                            isRoleChecked(role.id) ? 'opacity-100 scale-100' : 'opacity-0 scale-0'
                                        ]"/>
                                </div>
                                <div class="grid gap-1">
                                    <p class="text-sm font-medium capitalize">
                                        {{ role.role_name }}
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>
                    <!-- Status -->
                    <FormSelect
                        label="Status Aktif"
                        v-model="statusValue"
                        :options="[
                            { label: 'Aktif', value: '1' },
                            { label: 'Tidak Aktif', value: '0' }
                        ]"
                        :error="form.errors.is_active"
                    />
                </div>
                <hr />

                <div class="inline-flex gap-2 mt-4">
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="route('admin.users.index')" class="flex items-center gap-2">
                            <ArrowLeftToLine class="w-4 h-4" />
                            Back
                        </Link>
                    </Button>
                    <Button
                        type="submit"
                        size="sm"
                        :disabled="form.processing"
                        class="flex items-center gap-2">
                        <SquarePen class="w-4 h-4" />
                        Save
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
