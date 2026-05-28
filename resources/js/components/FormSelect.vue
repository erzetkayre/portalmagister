<script setup lang="ts">
import { Label } from '@/components/ui'
import InputError from '@/components/InputError.vue'
import {
    Select, SelectTrigger, SelectValue, SelectContent, SelectGroup, SelectItem,
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
    disabled?: boolean
    modal?: boolean
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number): void
}>()

const id = `select-${Math.random().toString(36).slice(2, 9)}`

const selectClass = computed(() =>
    props.error ? 'border-destructive focus-visible:ring-destructive' : ''
)
</script>

<template>
    <div class="flex flex-col gap-2.5">
        <Label v-if="label" :for="id">{{ label }}</Label>
        <Select
            :model-value="modelValue"
            :disabled="disabled"
            :modal="modal ?? true"
            @update:model-value="emit('update:modelValue', $event)"
        >
            <SelectTrigger :id="id" class="w-full" :class="selectClass">
                <SelectValue :placeholder="placeholder ?? 'Pilih data'" />
            </SelectTrigger>
            <SelectContent>
                <SelectGroup>
                    <SelectItem
                        v-for="opt in options"
                        :key="opt.value"
                        :value="String(opt.value)"
                    >
                        {{ opt.label }}
                    </SelectItem>
                </SelectGroup>
            </SelectContent>
        </Select>
        <InputError v-if="error" :message="error" />
    </div>
</template>
