import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import type { AppPageProps } from '@/types'

export function useAuth() {
    const page = usePage<AppPageProps>()

    const auth = computed(() => page.props.auth)
    const user = computed(() => auth.value.user)
    const program = computed(() => auth.value.program)
    const can = computed(() => auth.value.can)

    return {
        auth,
        user,
        program,
        can,

        isAuthenticated: computed(() => !!user.value),
        isAdmin: computed(() => can.value.admin),
        isKoordinator: computed(() => can.value.koordinator),
        isDosen: computed(() => can.value.dosen),
        isMahasiswa: computed(() => can.value.mahasiswa),

        isPWK: computed(() => program.value === 'pwk'),
        isElektro: computed(() => program.value === 'elektro'),
    }
}
