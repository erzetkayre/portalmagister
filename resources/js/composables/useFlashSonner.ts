import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import 'vue-sonner/style.css';

function showToast(
  type: 'success' | 'error' | 'warning' | 'info',
  payload: string | { title: string; description?: string }
) {
  if (typeof payload === 'string') {
    toast[type](payload)
  } else {
    toast[type](payload.title, {
      description: payload.description,
    })
  }
}

export function useFlashSonner() {
  const page = usePage()

  watch(
    () => page.props.flash as any,
    (flash) => {
      if (!flash) return

      if (flash.success) showToast('success', flash.success)
      if (flash.error) showToast('error', flash.error)
      if (flash.warning) showToast('warning', flash.warning)
      if (flash.info) showToast('info', flash.info)
    },
    { immediate: true }
  )
}
