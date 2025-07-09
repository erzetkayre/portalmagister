<!-- resources/js/Pages/ErrorPage.vue -->
<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Link } from '@inertiajs/vue3'
import {
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

const page = computed(() => usePage().props)

const dashboardRoute = computed(() => {
    const user = page.value.auth?.user as any;
    if (!user) return '/login';

    const role = user.role?.nama_role;
    // console.log(user);

    return route(`${role}.dashboard`);
});

// Dynamic content berdasarkan status code
const errorConfig = computed(() => {
    const configs = {
        403: {
            icon: ShieldX,
            title: 'Access Forbidden',
            description: 'You don\'t have permission to access this resource.',
        },
        404: {
            icon: Search,
            title: 'Page Not Found',
            description: 'The page you\'re looking for doesn\'t exist or has been moved.',
        },
        500: {
            icon: AlertTriangle,
            title: 'Server Error',
            description: 'Something went wrong on our end. We\'re working to fix it.',
        },
        503: {
            icon: Wrench,
            title: 'Under Maintenance',
            description: 'We\'re currently performing scheduled maintenance. We\'ll be back shortly.',
        },
        419: {
            icon: Shield,
            title: 'Session Expired',
            description: 'Your session has expired. Please refresh the page and try again.',
        },
        429: {
            icon: Clock,
            title: 'Too Many Requests',
            description: 'You\'ve made too many requests. Please wait a moment and try again.',
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
                    <div class="rounded-full p-6 bg-destructive/10">
                        <component :is="errorConfig.icon" class="h-16 w-16 text-destructive" />
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
                <div v-if="page.auth?.user" class="space-x-2">
                    <Button as-child>
                        <Link :href="dashboardRoute">
                            <LayoutDashboard class="h-4 w-4" />
                            Back to Dashboard
                        </Link>
                    </Button>
                    <Button variant="secondary" as-child class="">
                        <Link href="/">
                            <Home class="h-4 w-4" />
                            Home
                        </Link>
                    </Button>
                </div>
                <div v-else class="space-x-2">
                    <Button as-child class="">
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
