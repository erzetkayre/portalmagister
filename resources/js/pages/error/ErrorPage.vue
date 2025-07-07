<!-- resources/js/Pages/ErrorPage.vue -->
<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Link } from '@inertiajs/vue3'
import {
    LogIn,
    Home,
    ShieldX,
    LayoutDashboard,
    Search,
    AlertTriangle,
    Shield,
    Clock,
    Wrench,
    XCircle
} from 'lucide-vue-next'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const propsStatus = defineProps<{
    status: number
}> ()

const { props } = usePage();

const isAuthenticated = computed(() => {
    return props.auth?.user !== null && props.auth?.user !== undefined
})

const getDashboardRoute = () => {
    const user = props.auth?.user as any
    if (!user) return '/'
    return route(`${user.role.nama_role}.dashboard`)
}

// Dynamic content berdasarkan status code
const errorConfig = computed(() => {
    const configs = {
        403: {
            icon: ShieldX,
            title: 'Access Forbidden',
            description: 'You don\'t have permission to access this resource.',
            bgColor: 'bg-destructive/10',
            iconColor: 'text-destructive'
        },
        404: {
            icon: Search,
            title: 'Page Not Found',
            description: 'The page you\'re looking for doesn\'t exist or has been moved.',
            bgColor: 'bg-destructive/10',
            iconColor: 'text-destructive'
        },
        500: {
            icon: AlertTriangle,
            title: 'Server Error',
            description: 'Something went wrong on our end. We\'re working to fix it.',
            bgColor: 'bg-destructive/10',
            iconColor: 'text-destructive'
        },
        503: {
            icon: Wrench,
            title: 'Under Maintenance',
            description: 'We\'re currently performing scheduled maintenance. We\'ll be back shortly.',
            bgColor: 'bg-destructive/10',
            iconColor: 'text-destructive'
        },
        419: {
            icon: Shield,
            title: 'Session Expired',
            description: 'Your session has expired. Please refresh the page and try again.',
            bgColor: 'bg-destructive/10',
            iconColor: 'text-destructive'
        },
        429: {
            icon: Clock,
            title: 'Too Many Requests',
            description: 'You\'ve made too many requests. Please wait a moment and try again.',
            bgColor: 'bg-destructive/10',
            iconColor: 'text-destructive'
        }
    };

    return configs[propsStatus.status] || {
        icon: XCircle,
        title: 'Something went wrong',
        description: 'An unexpected error occurred. Please try again.',
        bgColor: 'bg-destructive/10',
        iconColor: 'text-destructive'
    };
});

</script>

<template>
    <div class="min-h-screen bg-background flex items-center justify-center p-4">
        <div class="max-w-md w-full space-y-8 text-center">
            <div class="space-y-4">
                <div class="flex justify-center">
                    <div class="rounded-full p-6" :class="errorConfig.bgColor">
                        <component :is="errorConfig.icon" class="h-16 w-16" :class="errorConfig.iconColor" />
                    </div>
                </div>
                <div class="space-y-2">
                    <h1 class="text-9xl font-extrabold text-foreground">{{ status }}</h1>
                    <h2 class="text-2xl font-semibold text-foreground">{{ errorConfig.title }}</h2>
                    <p class="text-muted-foreground">
                        {{ errorConfig.description }}
                    </p>
                </div>
            </div>
            <div class="space-y-4">
                <div class="space-x-2">
                    <Button as-child v-if="isAuthenticated">
                        <Link :href="getDashboardRoute()">
                            <LayoutDashboard class="h-4 w-4" />
                            Back to Dashboard
                        </Link>
                    </Button>
                    <Button as-child v-else>
                        <Link :href="route('login')">
                            <LogIn class="h-4 w-4" />
                            Login
                        </Link>
                    </Button>
                    <Button variant="secondary" as-child class="">
                        <Link href="/">
                            <Home class="h-4 w-4" />
                            Home
                        </Link>
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
