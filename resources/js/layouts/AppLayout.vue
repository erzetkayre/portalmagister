<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { useFlashSonner } from '@/composables/useFlashSonner';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

useFlashSonner()
withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
