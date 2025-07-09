
<script setup lang="ts">
import { Card, CardHeader, CardContent, CardTitle, Badge } from '@/components/ui';
import type { Component } from 'vue';

interface Props {
    title: string;
    value: string | number;
    description: string;
    icon: Component;
    badge?: string;
}

defineProps<Props>();
</script>
<template>
    <Card class="gap-2">
        <CardHeader class="flex flex-row justify-between items-center">
            <CardTitle class="text-lg font-medium">
                {{ title }}
            </CardTitle>
            <component :is="icon" class="h-6 w-6 text-destructive" />
        </CardHeader>
        <CardContent>
            <div class="text-3xl font-bold">
                {{ value }}
            </div>
            <div class="flex items-center pt-2 justify-between">
                <p class="text-sm">
                    {{ description }}
                </p>
                <Badge v-if="badge" class="h-fit align-middle">
                    {{ badge }}
                </Badge>
            </div>
        </CardContent>
    </Card>
</template>
