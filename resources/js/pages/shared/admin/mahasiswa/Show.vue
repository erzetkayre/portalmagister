<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { computed } from 'vue';
import { Badge, Button } from '@/components/ui';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { ArrowLeftToLine, SquarePen, Hash, Users, Activity, CalendarDays, GraduationCap, BookOpen, Trophy } from 'lucide-vue-next';

interface MahasiswaDetail {
    id: number
    nama_mhs: string
    nim: string
    angkatan: number | null
    gender: string | null
    status_mhs: string
    ipk: number | null
    sks: number | null
    pem_akademik: string | null
    created_at: string
}

interface Props {
    mahasiswa: MahasiswaDetail
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Mahasiswa', href: route('admin.mahasiswa.index') },
    { title: props.mahasiswa.nama_mhs, href: '#' },
];

const { getInitials } = useInitials();

const genderLabel = (g: string | null) =>
    g === 'L' ? 'Laki-laki' : g === 'P' ? 'Perempuan' : '—';

const statusVariant = (s: string) => {
    if (s === 'aktif')  return 'default';
    if (s === 'lulus')  return 'success';
    return 'destructive';
};

const joinedDate = computed(() => {
    if (!props.mahasiswa.created_at) return '—';
    return new Date(props.mahasiswa.created_at).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
    });
});
</script>

<template>
    <Head title="Detail Mahasiswa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="w-full max-w-2xl mx-auto flex flex-col gap-6">
                <PageHeader title="Informasi Mahasiswa" description="Detail data mahasiswa program studi." />

                <div class="overflow-hidden divide-y">
                    <!-- Section: Identity -->
                    <div class="flex flex-col items-center gap-2 px-5 py-5 pb-8">
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
                    <div class="flex items-center gap-3 px-5 py-3">
                        <Hash class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-muted-foreground">Angkatan</span>
                            <span class="text-sm">{{ mahasiswa.angkatan ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <Users class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-muted-foreground">Jenis Kelamin</span>
                            <span class="text-sm">{{ genderLabel(mahasiswa.gender) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <Activity class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-1.5">
                            <span class="text-xs text-muted-foreground">Status</span>
                            <Badge :variant="statusVariant(mahasiswa.status_mhs)" class="text-xs py-0 self-start capitalize">
                                {{ mahasiswa.status_mhs }}
                            </Badge>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <Trophy class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-muted-foreground">IPK</span>
                            <span class="text-sm">{{ mahasiswa.ipk ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <BookOpen class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-muted-foreground">SKS Tempuh</span>
                            <span class="text-sm">{{ mahasiswa.sks ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <GraduationCap class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-muted-foreground">Pembimbing Akademik</span>
                            <span class="text-sm">{{ mahasiswa.pem_akademik ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <CalendarDays class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-muted-foreground">Terdaftar</span>
                            <span class="text-sm">{{ joinedDate }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="route('admin.mahasiswa.index')" class="flex items-center gap-2">
                            <ArrowLeftToLine class="w-4 h-4" />
                            Kembali
                        </Link>
                    </Button>
                    <Button size="sm" as-child>
                        <Link :href="route('admin.mahasiswa.edit', mahasiswa.id)" class="flex items-center gap-2">
                            <SquarePen class="w-4 h-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
