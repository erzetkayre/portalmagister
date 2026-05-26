<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Head, Link } from '@inertiajs/vue3';
import { BreadcrumbItem, User } from '@/types';
import { computed } from 'vue';
import { Badge, Button } from '@/components/ui';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { ArrowLeftToLine, SquarePen, Mail, Phone, CalendarDays, ShieldCheck, Activity } from 'lucide-vue-next';

interface Props { user: User }
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/admin/users' },
    { title: props.user.name, href: '#' },
];

const { getInitials } = useInitials();
const showAvatar = computed(() => !!props.user.photo);
const avatarUrl = computed(() => props.user.photo_url ?? null);
const joinedDate = computed(() => {
    if (!props.user.created_at) return '—';
    return new Date(props.user.created_at).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
    });
});
</script>

<template>
    <Head title="Detail User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="w-full max-w-2xl mx-auto flex flex-col gap-6">
                <PageHeader title="Informasi Pengguna" description="Detail informasi akun pengguna." />
                <div class="overflow-hidden divide-y">
                    <!-- Section: Identity -->
                    <div class="flex flex-col items-center gap-2 px-5 py-5 pb-8">
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
                    <div class="flex items-center gap-3 px-5 py-3">
                        <Mail class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5 min-w-0">
                            <span class="text-xs text-muted-foreground">Email</span>
                            <span class="text-sm truncate">{{ user.email }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <Phone class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-muted-foreground">No. Telepon</span>
                            <span class="text-sm">{{ user.phone || '—' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <CalendarDays class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-muted-foreground">Terdaftar</span>
                            <span class="text-sm">{{ joinedDate }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <ShieldCheck class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-1.5">
                            <span class="text-xs text-muted-foreground">Hak Akses</span>
                            <div class="flex flex-wrap gap-1.5">
                                <Badge
                                    v-for="role in user.roles" :key="role.id"
                                    variant="primary-outline"
                                    class="capitalize text-xs py-0">
                                    {{ role.role_name }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <Activity class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-1.5">
                            <span class="text-xs text-muted-foreground">Status</span>
                            <Badge :variant="user.is_active ? 'default' : 'destructive'" class="text-xs py-0 self-start">
                                {{ user.is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </Badge>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="route('admin.users.index')" class="flex items-center gap-2">
                            <ArrowLeftToLine class="w-4 h-4" />
                            Kembali
                        </Link>
                    </Button>
                    <Button size="sm" as-child>
                        <Link :href="route('admin.users.edit', user.id)" class="flex items-center gap-2">
                            <SquarePen class="w-4 h-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
