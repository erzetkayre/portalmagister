<script setup lang="ts">
import { Label } from '@/components/ui'
import InputError from '@/components/InputError.vue'
import { Check } from 'lucide-vue-next'

interface Option {
    id: number | string
    label: string
    description?: string
    disabled?: boolean
}

const props = defineProps<{
    modelValue: (number | string)[]
    options: Option[]
    label?: string
    error?: string
    cols?: 1 | 2 | 3
}>()

const emit = defineEmits<{
    'update:modelValue': [(number | string)[]]
}>()

const isChecked  = (id: number | string) => props.modelValue.includes(id)
const isDisabled = (opt: Option) => !!opt.disabled

const toggle = (opt: Option) => {
    if (isDisabled(opt)) return
    const next = isChecked(opt.id)
        ? props.modelValue.filter(v => v !== opt.id)
        : [...props.modelValue, opt.id]
    emit('update:modelValue', next)
}
</script>

<template>
    <div class="flex flex-col gap-2.5">
        <div v-if="label || $slots.label" class="flex items-center gap-1.5">
            <Label v-if="label">{{ label }}</Label>
            <slot name="label" />
        </div>
        <div :class="[
            'grid gap-2',
            cols === 1 ? 'grid-cols-1' : cols === 3 ? 'sm:grid-cols-3' : 'grid-cols-1 sm:grid-cols-2'
        ]">
            <label
                v-for="opt in options"
                :key="opt.id"
                :class="[
                    'flex items-center gap-3 rounded-lg border border-input p-3 select-none transition-colors',
                    isDisabled(opt)
                        ? 'opacity-40 cursor-not-allowed'
                        : 'cursor-pointer hover:bg-accent/50',
                ]">
                <input
                    type="checkbox"
                    :checked="isChecked(opt.id)"
                    :disabled="isDisabled(opt)"
                    @change="toggle(opt)"
                    class="peer sr-only" />
                <div class="peer-checked:bg-primary peer-checked:border-primary peer-focus-visible:ring-ring/50 peer-focus-visible:ring-[3px] size-4 shrink-0 rounded-[4px] border border-input shadow-xs transition-all outline-none flex items-center justify-center">
                    <Check :class="['size-3.5 transition-all text-primary-foreground', isChecked(opt.id) ? 'opacity-100 scale-100' : 'opacity-0 scale-0']" />
                </div>
                <div class="grid gap-0.5">
                    <p class="text-sm font-medium capitalize leading-none">{{ opt.label }}</p>
                    <p v-if="opt.description" class="text-xs text-muted-foreground">{{ opt.description }}</p>
                </div>
            </label>
        </div>
        <InputError v-if="error" :message="error" />
    </div>
</template>
