<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Lock } from 'lucide-vue-next';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-muted p-6 md:p-10">
        <div class="flex w-full max-w-md flex-col gap-6">
            <div class="flex flex-col gap-6">
                <Card class="rounded-xl">
                    <div class="flex items-center gap-2 pt-4 self-center font-medium">
                        <div class="flex h-24 w-24 items-center justify-center bg-primary/10 rounded-full">
                            <Lock class="size-9 text-primary" />
                        </div>
                    </div>
                    <CardHeader class="px-10 text-center">
                        <CardTitle class="text-xl font-bold">{{ title }}</CardTitle>
                        <CardDescription>
                            {{ description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="px-10 py-2">
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
