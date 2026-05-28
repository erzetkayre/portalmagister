<script setup lang="ts">
import { Badge } from '@/components/ui/badge';

defineProps<{
    label: string
    items: string[]
    variant?: 'default' | 'primary-outline' | 'destructive-outline' | 'secondary' | 'outline'
}>()
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <p class="text-xs font-medium text-muted-foreground tracking-wide">{{ label }}</p>
        <div class="flex flex-wrap items-center gap-1.5">
            <template v-if="items.length">
                <Badge
                    v-for="item in items"
                    :key="item"
                    :variant="variant ?? 'primary-outline'"
                    class="capitalize">
                    {{ item }}
                </Badge>
            </template>
            <span v-else class="text-muted-foreground">-</span>
        </div>
    </div>
</template>
