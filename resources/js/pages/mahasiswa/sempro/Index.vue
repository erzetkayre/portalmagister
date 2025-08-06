<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { ArrowLeft, Upload, FileText, AlertCircle } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

interface Tempat {
  nama: string;
}

interface Sempro {
  status_seminar: string;
  draft_sempro?: string;
  tanggal?: string;
  jam_mulai?: string;
  jam_selesai?: string;
  tempat?: Tempat;
}

defineProps<{
  sempro: Sempro | null;
}>()
</script>


<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Seminar Proposal</h1>

    <div v-if="sempro">
      <p><strong>Status:</strong> {{ sempro.status_seminar }}</p>
      <p><strong>Draft:</strong> <a :href="`/storage/${sempro.draft_sempro}`" target="_blank" class="text-blue-500">Lihat Draft</a></p>
      <p><strong>Tanggal:</strong> {{ sempro.tanggal ?? '-' }}</p>
      <p><strong>Jam:</strong> {{ sempro.jam_mulai ?? '-' }} - {{ sempro.jam_selesai ?? '-' }}</p>
      <p><strong>Tempat:</strong> {{ sempro.tempat?.nama ?? '-' }}</p>
    </div>
    <div v-else>
      <p>Belum ada data seminar proposal.</p>
      <Link href="/mahasiswa/sempro/pengajuan" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded">Ajukan Seminar Proposal</Link>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({ sempro: Object })
</script>

