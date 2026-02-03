<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import Heading from '@/components/Heading.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { BreadcrumbItem, User, Role } from '@/types';
import { ref, computed } from 'vue';
import { Input, Badge, Button, Checkbox, Label } from '@/components/ui';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { ArrowLeftToLine, SquarePen } from 'lucide-vue-next';

// Props
interface Props {
    user: User
    roles: Role[]
}
const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/admin/users' },
    { title: props.user.name, href: '#' },
];

// Avatar Logic
const { getInitials } = useInitials();
const showAvatar = computed(() => props.user.photo && props.user.photo !== '');
const avatarUrl = computed(() => props.user.photo ? route('profile.photo.current') : null);

// Form Data
const form = ref({
    name: props.user.name,
    email: props.user.email,
    nomor_induk: props.user.nomor_induk,
    phone: props.user.phone || '',
    is_active: props.user.is_active,
    roles: props.user.roles?.map(r => r.id) // id roles yang dipilih
});

// Submit Form
const submit = () => {
    router.post(route('admin.users.update', props.user.id), form.value);
};

// Toggle Role
const toggleRole = (roleId: number) => {
    const index = form.value.roles?.indexOf(roleId);
    if (index > -1) {
        form.value.roles?.splice(index, 1);
    } else {
        form.value.roles?.push(roleId);
    }
};

// Check if role selected
const isRoleSelected = (roleId: number) => {
    return form.value.roles?.includes(roleId);
};
</script>

<template>
    <Head title="Edit User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading title="Edit Pengguna" description="Perbarui informasi dan hak akses pengguna" />

            <!-- Avatar + Basic Info -->
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

            <!-- Form Inputs -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-3 my-2">
                    <Label>Nama User</Label>
                    <Input v-model="form.name" />
                </div>
                <div class="flex flex-col gap-3 my-2">
                    <Label>Email</Label>
                    <Input v-model="form.email" type="email" />
                </div>
                <div class="flex flex-col gap-3 my-2">
                    <Label>Nomor Induk</Label>
                    <Input v-model="form.nomor_induk" />
                </div>
                <div class="flex flex-col gap-3 my-2">
                    <Label>No. Telp</Label>
                    <Input v-model="form.phone" />
                </div>
                <div class="flex flex-col gap-3 my-2">
                    <Label>Status Aktif</Label>
                    <Checkbox v-model="form.is_active" />
                </div>
            </div>

            <hr>

            <!-- Roles -->
            <div class="flex flex-col gap-3">
                <Label>Hak Akses</Label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    <Label
                        v-for="role in props.roles"
                        :key="role.id"
                        class="hover:bg-accent/50 flex items-center gap-3 rounded-lg border p-3 cursor-pointer
                               has-[[aria-checked=true]]:border-blue-600 has-[[aria-checked=true]]:bg-blue-50 dark:has-[[aria-checked=true]]:border-blue-900 dark:has-[[aria-checked=true]]:bg-blue-950"
                    >
                        <Checkbox
                            :id="'role-' + role.id"
                            :checked="isRoleSelected(role.id)"
                            @change="toggleRole(role.id)"
                            class="data-[state=checked]:border-blue-600 data-[state=checked]:bg-blue-600 data-[state=checked]:text-white dark:data-[state=checked]:border-blue-700 dark:data-[state=checked]:bg-blue-700"
                        />
                        <div class="grid gap-1">
                            <p class="text-sm font-medium capitalize">{{ role.role_name }}</p>
                        </div>
                    </Label>
                </div>
            </div>

            <hr>

            <!-- Actions -->
            <div class="inline-flex gap-2 mt-4">
                <Button variant="outline" size="sm" as-child class="w-auto">
                    <Link :href="route('admin.users.index')" class="flex items-center">
                        <ArrowLeftToLine class="w-4 h-4" />
                        Kembali
                    </Link>
                </Button>
                <Button @click="submit" variant="secondary" size="sm" class="w-auto flex items-center gap-2">
                    <SquarePen class="w-4 h-4" />
                    Simpan
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
