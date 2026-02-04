<script setup lang="ts">
import { Label } from '@/components/ui'
import InputError from '@/components/InputError.vue'
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectGroup,
  SelectItem,
} from '@/components/ui/select'
import { computed } from 'vue'

interface SelectOption {
  label: string
  value: string | number
}

const props = defineProps<{
  modelValue: string | number
  label?: string
  placeholder?: string
  options: SelectOption[]
  error?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | number): void
}>()

const selectClass = computed(() =>
  props.error ? 'border-destructive focus-visible:ring-destructive' : ''
)
</script>

<template>
  <div class="flex flex-col gap-3 my-2 md:col-span-2">
    <Label v-if="label">{{ label }}</Label>

    <Select
      :model-value="modelValue"
      @update:model-value="emit('update:modelValue', $event)"
    >
      <SelectTrigger class="w-full" :class="selectClass">
        <SelectValue :placeholder="placeholder ?? 'Pilih data'" />
      </SelectTrigger>

      <SelectContent>
        <SelectGroup>
          <SelectItem
            v-for="opt in options"
            :key="opt.value"
            :value="opt.value"
          >
            {{ opt.label }}
          </SelectItem>
        </SelectGroup>
      </SelectContent>
    </Select>

    <InputError v-if="error" :message="error" />
  </div>
</template>
