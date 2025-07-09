<template>
    <div class="flex items-center gap-2">
        <template v-for="action in actions" :key="action.key">
            <TooltipProvider>
                <Tooltip>
                    <TooltipTrigger as-child>
                        <Button 
                            size="sm" 
                            variant="ghost" 
                            @click="$emit('action', action.key, item)"
                            :class="action.class"
                        >
                            <component :is="action.icon" class="h-4 w-4" />
                        </Button>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>{{ action.tooltip }}</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
        </template>
    </div>
</template>

<script setup lang="ts">
import { Button, Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui';
import type { Component } from 'vue';

interface ActionConfig {
    key: string;
    icon: Component;
    tooltip: string;
    class?: string;
}

interface Props {
    actions: ActionConfig[];
    item: any;
}

interface Emits {
    (e: 'action', actionKey: string, item: any): void;
}

defineProps<Props>();
defineEmits<Emits>();
</script>