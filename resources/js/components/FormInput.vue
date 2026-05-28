<script setup lang="ts">
import { Input, Label } from '@/components/ui'
import InputError from '@/components/InputError.vue'
import { computed } from 'vue'

const props = defineProps<{
    modelValue: string | number
    label?: string
    type?: string
    placeholder?: string
    error?: string
    disabled?: boolean
    readonly?: boolean
}>()

const emit = defineEmits(['update:modelValue'])

const id = `field-${Math.random().toString(36).slice(2, 9)}`

const inputClass = computed(() =>
    props.error ? 'border-destructive focus-visible:ring-destructive' : ''
)
</script>

<template>
    <div class="flex flex-col gap-2.5">
        <Label v-if="label" :for="id">{{ label }}</Label>
        <Input
            :id="id"
            :type="type ?? 'text'"
            :model-value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :readonly="readonly"
            @update:model-value="emit('update:modelValue', $event)"
            :class="inputClass"
        />
        <InputError v-if="error" :message="error" />
    </div>
</template>
