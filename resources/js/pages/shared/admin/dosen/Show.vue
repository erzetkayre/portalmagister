<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { computed } from 'vue';
import { Badge, Button } from '@/components/ui';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { ArrowLeftToLine, SquarePen, Hash, BadgeCheck, Lightbulb, Users, Activity, CalendarDays } from 'lucide-vue-next';
import type { Dosen, Jabatan } from '@/types/user';

interface Props {
    dosen: Dosen & { jabatan: Jabatan[] }
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dosen', href: route('admin.dosen.index') },
    { title: props.dosen.nama_dsn, href: '#' },
];

const { getInitials } = useInitials();

const genderLabel = (g: string | null | undefined) =>
    g === 'L' ? 'Laki-laki' : g === 'P' ? 'Perempuan' : '—';

const joinedDate = computed(() => {
    if (!props.dosen.created_at) return '—';
    return new Date(props.dosen.created_at).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
    });
});
</script>

<template>
    <Head title="Detail Dosen" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="w-full max-w-2xl mx-auto flex flex-col gap-6">
                <PageHeader title="Informasi Dosen" description="Detail data dosen program studi." />
                <div class="overflow-hidden divide-y">
                    <!-- Section: Identity -->
                    <div class="flex flex-col items-center gap-2 px-5 py-5 pb-8">
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
                    <div class="flex items-center gap-3 px-5 py-3">
                        <Hash class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-muted-foreground">Kode Dosen</span>
                            <span class="text-sm font-mono">{{ dosen.kode_dsn || '—' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <Lightbulb class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-muted-foreground">Bidang Keahlian</span>
                            <span class="text-sm">{{ dosen.bidang_keahlian || '—' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <Users class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-muted-foreground">Jenis Kelamin</span>
                            <span class="text-sm">{{ genderLabel(dosen.gender) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <Activity class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-1.5">
                            <span class="text-xs text-muted-foreground">Status</span>
                            <Badge
                                :variant="dosen.status_dsn === 'aktif' ? 'default' : 'destructive'"
                                class="text-xs py-0 self-start capitalize">
                                {{ dosen.status_dsn }}
                            </Badge>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3">
                        <CalendarDays class="w-4 h-4 text-muted-foreground shrink-0" />
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-muted-foreground">Terdaftar</span>
                            <span class="text-sm">{{ joinedDate }}</span>
                        </div>
                    </div>
                    <template v-if="dosen.jabatan?.length">
                        <div class="flex items-start gap-3 px-5 py-3">
                            <BadgeCheck class="w-4 h-4 text-muted-foreground shrink-0 mt-0.5" />
                            <div class="flex flex-col gap-1.5">
                                <span class="text-xs text-muted-foreground">Jabatan</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <Badge
                                        v-for="j in dosen.jabatan"
                                        :key="j.id"
                                        variant="secondary"
                                        class="text-xs py-0">
                                        {{ j.nama_jabatan }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="route('admin.dosen.index')" class="flex items-center gap-2">
                            <ArrowLeftToLine class="w-4 h-4" />
                            Kembali
                        </Link>
                    </Button>
                    <Button size="sm" as-child>
                        <Link :href="route('admin.dosen.edit', dosen.id)" class="flex items-center gap-2">
                            <SquarePen class="w-4 h-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
