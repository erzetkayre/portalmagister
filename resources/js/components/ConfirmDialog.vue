<script setup lang="ts">
import {
  AlertDialog,
  AlertDialogTrigger,
  AlertDialogContent,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogCancel,
  AlertDialogAction,
} from '@/components/ui/alert-dialog'

defineProps<{
  title: string
  confirmText?: string
  confirmClass?: string
  contentClass?: string
  onConfirm: () => void
}>()
</script>

<template>
  <AlertDialog>
    <AlertDialogTrigger as-child>
      <slot name="trigger" />
    </AlertDialogTrigger>

    <AlertDialogContent :class="contentClass">
      <AlertDialogHeader>
        <div class="flex items-center gap-3">
          <slot name="icon" />
          <AlertDialogTitle>{{ title }}</AlertDialogTitle>
        </div>
      </AlertDialogHeader>

      <slot name="description" />

      <AlertDialogFooter>
        <AlertDialogCancel>Batal</AlertDialogCancel>
        <AlertDialogAction
          type="button"
          :class="confirmClass"
          @click="onConfirm"
        >
          {{ confirmText ?? 'Ya, Lanjutkan' }}
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>
