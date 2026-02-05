<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import Heading from '@/components/Heading.vue';
import FormInput from '@/components/FormInput.vue';
import FormSelect from '@/components/FormSelect.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { BreadcrumbItem, User, Role } from '@/types';
import { computed } from 'vue';
import { Button, Label } from '@/components/ui';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { ArrowLeftToLine, SquarePen, Check } from 'lucide-vue-next';

interface Props {
    user: User
    roles: Role[]
}
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/admin/users' },
    { title: `Edit ${props.user.name}`, href: '#' },
];

// Avatar Logic
const { getInitials } = useInitials();
const showAvatar = computed(() => props.user.photo && props.user.photo !== '');
const avatarUrl = computed(() => props.user.photo ? route('profile.photo.current') : null);

// Form Data
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    nomor_induk: props.user.nomor_induk,
    phone: props.user.phone || '',
    is_active: props.user.is_active,
    roles: Array.isArray(props.user.roles) ? props.user.roles.map(r => r.id) : []
});
const submit = () => {
    form.put(route('admin.users.update', props.user.id),{
        preserveScroll: true,
    });
};

// Select and Checkbox
const statusValue = computed({
    get: () => form.is_active ? '1' : '0',
    set: (val: string) => form.is_active = val === '1'
});
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
    <Head title="Edit User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading title="Edit Pengguna" description="Perbarui informasi dan hak akses pengguna" />
            <div class="flex items-center gap-4">
                <Avatar class="h-16 w-16 overflow-hidden rounded-full">
                    <AvatarImage v-if="showAvatar" :src="avatarUrl!" :alt="props.user.name" />
                    <AvatarFallback class="rounded-lg font-semibold text-primary dark:text-white">
                        {{ getInitials(props.user.name) }}
                    </AvatarFallback>
                </Avatar>
                <div class="flex flex-col text-left text-sm leading-tight">
                    <HeadingSmall class="truncate font-medium" :title="props.user.name" />
                    <p class="text-xs text-primary">{{ props.user.nomor_induk }}</p>
                </div>
            </div>
            <hr>
            <form @submit.prevent="submit" novalidate class="flex flex-col gap-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <FormInput
                        label="Nama User"
                        v-model="form.name"
                        :error="form.errors.name"
                        />
                    <FormInput
                        label="Email"
                        v-model="form.email"
                        type="email"
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
                <hr>
                <div class="inline-flex gap-2 mt-4">
                    <Button variant="outline" size="sm" as-child class="w-auto">
                        <Link :href="route('admin.users.index')" class="flex items-center">
                            <ArrowLeftToLine class="w-4 h-4" />
                            Back
                        </Link>
                    </Button>
                    <Button type="submit" variant="default" size="sm" class="w-auto flex items-center gap-2" :disabled="form.processing">
                        <SquarePen class="w-4 h-4" />
                        Save
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
