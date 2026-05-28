<script setup lang="ts">
defineProps<{
    label: string
    value?: string | number | null
}>()
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <p class="text-xs font-medium text-muted-foreground tracking-wide">{{ label }}</p>
        <div class="min-h-9 flex items-center rounded-md border border-input bg-muted/40 px-3 text-sm text-foreground">
            <slot>{{ value ?? '-' }}</slot>
        </div>
    </div>
</template>
