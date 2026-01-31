<script setup lang="ts">
import { Method } from '@inertiajs/core';
import { Link } from '@inertiajs/vue3';
import { TooltipProvider, Tooltip, TooltipTrigger, TooltipContent } from '@/components/ui/tooltip'

interface Props {
    href: string;
    tabindex?: number;
    method?: Method;
    as?: string;
    tooltip?: string;
}

defineProps<Props>();
</script>

<template>
    <TooltipProvider>
        <Tooltip>
        <TooltipTrigger as-child>
            <Link
                :href="href"
                :tabindex="tabindex"
                :method="method"
                :as="as"
                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
            >
                <slot />
            </Link>
        </TooltipTrigger>
        <TooltipContent>
            <p>{{ tooltip }}</p>
        </TooltipContent>
        </Tooltip>
    </TooltipProvider>
</template>
