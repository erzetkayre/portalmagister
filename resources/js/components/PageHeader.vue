<script setup lang="ts">
defineProps<{
    title: string
    description?: string
}>()
</script>

<template>
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div class="min-w-0 space-y-0.5">
            <h2 class="text-xl font-semibold tracking-tight text-foreground">{{ title }}</h2>
            <p v-if="description" class="text-sm text-muted-foreground">{{ description }}</p>
        </div>
        <div v-if="$slots.actions" class="flex shrink-0 flex-wrap items-center gap-2">
            <slot name="actions" />
        </div>
    </div>
</template>
