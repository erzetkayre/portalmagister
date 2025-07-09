
<script setup lang="ts">
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, Button } from '@/components/ui';
import type { Component } from 'vue';

interface WarningConfig {
    title: string;
    message: string;
}

interface Props {
    open: boolean;
    title: string;
    description: string;
    icon: Component;
    iconClass?: string;
    confirmText: string;
    confirmIcon: Component;
    confirmVariant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
    confirmClass?: string;
    cancelText?: string;
    warning?: WarningConfig;
    warningIcon?: Component;
    points?: string[];
}

interface Emits {
    (e: 'confirm'): void;
    (e: 'cancel'): void;
    (e: 'update:open', value: boolean): void;
}

withDefaults(defineProps<Props>(), {
    cancelText: 'Batal',
    confirmVariant: 'default',
    iconClass: 'h-5 w-5'
});

defineEmits<Emits>();
</script>
<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <component :is="icon" :class="iconClass" />
                    {{ title }}
                </DialogTitle>
            </DialogHeader>
            <div class="space-y-4">
                <p class="text-sm text-muted-foreground">
                    {{ description }}
                </p>
                <div v-if="warning" class="bg-destructive/10 border border-destructive/20 rounded-lg p-3">
                    <div class="flex items-center gap-2 text-destructive text-sm font-medium">
                        <component :is="warningIcon" class="h-4 w-4" />
                        {{ warning.title }}
                    </div>
                    <p class="text-destructive/80 text-sm mt-1">
                        {{ warning.message }}
                    </p>
                </div>
                <ul v-if="points && points.length > 0" class="text-sm text-muted-foreground space-y-1 ml-2">
                    <li v-for="point in points" :key="point">{{ point }}</li>
                </ul>
            </div>
            <DialogFooter class="flex-row justify-end space-x-2">
                <Button variant="outline" @click="$emit('cancel')">
                    {{ cancelText }}
                </Button>
                <Button
                    :variant="confirmVariant"
                    @click="$emit('confirm')"
                    :class="confirmClass"
                >
                    <component :is="confirmIcon" class="h-4 w-4 mr-2" />
                    {{ confirmText }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
