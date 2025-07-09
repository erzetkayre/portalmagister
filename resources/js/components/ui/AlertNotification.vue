<script setup lang="ts">
import { computed } from 'vue';
import { Alert, AlertTitle, AlertDescription, Button } from '@/components/ui';
import { CheckCircle, XCircle, X } from 'lucide-vue-next';

interface Props {
    show: boolean;
    type: 'success' | 'error';
    title: string;
    description: string;
}

interface Emits {
    (e: 'close'): void;
}

const props = defineProps<Props>();
defineEmits<Emits>();

const variant = computed(() => props.type === 'error' ? 'destructive' : 'default');

const alertClasses = computed(() => [
    'mb-4',
    props.type === 'success' ? 'text-success bg-card [&>svg]:text-current *:data-[slot=alert-description]:text-success/90' : ''
]);
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform translate-y-2"
    >
        <Alert v-if="show" :variant="variant" :class="alertClasses">
            <CheckCircle v-if="type === 'success'" class="h-4 w-4" />
            <XCircle v-if="type === 'error'" class="h-4 w-4" />
            <AlertTitle>{{ title }}</AlertTitle>
            <AlertDescription>{{ description }}</AlertDescription>
            <Button
                variant="ghost"
                size="sm"
                @click="$emit('close')"
                class="absolute top-2 right-2 h-6 w-6 p-0"
            >
                <X class="h-3 w-3" />
            </Button>
        </Alert>
    </Transition>
</template>
