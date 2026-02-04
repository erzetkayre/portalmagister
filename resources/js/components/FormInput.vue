<script setup lang="ts">
import { Input, Label } from '@/components/ui'
import InputError from '@/components/InputError.vue'
import { computed } from 'vue'

const props = defineProps<{
  modelValue: string | number
  label?: string
  type?: string
  error?: string
}>()

const emit = defineEmits(['update:modelValue'])

const inputClass = computed(() =>
  props.error
    ? 'border-destructive focus-visible:ring-destructive'
    : ''
)
</script>

<template>
  <div class="flex flex-col gap-3 my-2 md:col-span-2">
    <Label v-if="label">{{ label }}</Label>
    <Input
      :type="type ?? 'text'"
      :model-value="modelValue"
      @update:model-value="emit('update:modelValue', $event)"
      :class="inputClass"
    />
    <InputError v-if="error" :message="error" />
  </div>
</template>
