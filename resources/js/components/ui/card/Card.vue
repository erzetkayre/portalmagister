<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <div
    data-slot="card"
    :class="
      cn(
        'text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-xs',
        props.class,
      )
    "
  >
    <slot />
  </div>
</template>
