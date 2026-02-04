<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '../../../../components/HeadingSmall.vue';
import Heading from '../../../../components/Heading.vue';
import { Head, Link } from '@inertiajs/vue3';
import { BreadcrumbItem, User } from '@/types';
import { computed } from 'vue';
import { Input, Badge } from '@/components/ui';
import { Label } from '@/components/ui/label';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { Button } from '@/components/ui';
import { ArrowLeftToLine, SquarePen } from 'lucide-vue-next';

// Define Interface and Props
interface Props {
    user: User
}
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/admin/users' },
    { title: props.user.name, href: '#' },
];

// Avatar Logic
const { getInitials } = useInitials();
const showAvatar = computed(() => props.user.photo && props.user.photo !== '');
const avatarUrl = computed(() => {
    return props.user.photo ? route('profile.photo.current') : null;
});

const userType = computed(() => {
    const hasMahasiswa = props.user.roles?.some(
        role => role.role_name === 'mahasiswa'
    )
    return hasMahasiswa ? 'mahasiswa' : 'dosen'
})

</script>

<template>
    <Head title="Detail User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading title="Informasi Pengguna" :description="`Detail informasi ${userType} pengguna sistem`" />
            <div class="flex items-center gap-4">
                <Avatar class="h-16 w-16 overflow-hidden rounded-full">
                    <AvatarImage v-if="showAvatar" :src="avatarUrl!" :alt="props.user.name" />
                    <AvatarFallback class="rounded-lg font-semibold text-primary dark:text-white">
                        {{ getInitials(props.user.name) }}
                    </AvatarFallback>
                </Avatar>
                <div class="flex flex-col text-left text-sm leading-tight">
                    <HeadingSmall class="truncate font-medium" :title="`${user.name}`">
                        {{ props.user.name }}
                    </HeadingSmall>
                    <p class="text-xs text-primary">
                        {{ props.user.nomor_induk }}
                    </p>
                </div>
            </div>
            <hr>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-3 my-2">
                    <Label>Nama User</Label>
                    <Input readonly :model-value="props.user.name" />
                </div>
                <div class="flex flex-col gap-3 my-2">
                    <Label>Email</Label>
                    <Input readonly :model-value="props.user.email" />
                </div>
                <div class="flex flex-col gap-3 my-2">
                    <Label>Nomor Induk</Label>
                    <Input readonly :model-value="props.user.nomor_induk" />
                </div>
                <div class="flex flex-col gap-3 my-2">
                    <Label>No. Telp</Label>
                    <Input readonly :model-value="props.user.phone || '-'" />
                </div>
                <div class="flex flex-col gap-3 my-2">
                    <Label>Hak Akses</Label>
                    <div class="flex gap-2 flex-wrap">
                        <Badge
                        v-for="role in props.user.roles"
                        :key="role.id"
                        variant="default"
                        class="capitalize">
                        {{ role.role_name }}
                    </Badge>
                </div>
            </div>
            <div class="flex flex-col gap-3 my-2">
                <Label>Status</Label>
                <Badge :variant="props.user.is_active ? 'primary-outline' : 'destructive-outline'">
                    {{ props.user.is_active ? 'Active' : 'Inactive' }}
                </Badge>
            </div>
            </div>
            <hr>
            <div class="inline-flex gap-2">
                <Button variant="outline" size="sm" as-child class="w-auto">
                    <Link :href="route('admin.users.index')" class="flex items-center">
                        <ArrowLeftToLine class="w-4 h-4" />
                        Kembali
                    </Link>
                </Button>
                <Button variant="secondary" size="sm" as-child class="w-auto">
                    <Link :href="route('admin.users.edit', props.user.id)" class="flex items-center">
                        <SquarePen class="w-4 h-4" />
                        Edit
                    </Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
