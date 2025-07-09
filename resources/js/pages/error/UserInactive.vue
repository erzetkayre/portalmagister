<!-- resources/js/Pages/ErrorPage.vue -->
<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Link } from '@inertiajs/vue3'
import { LogOut, TentTree, Home } from 'lucide-vue-next'

</script>

<template>
    <div class="min-h-screen bg-background flex items-center justify-center p-4">
        <div class="max-w-md w-full space-y-8 text-center">
            <div class="space-y-4">
                <div class="flex justify-center">
                    <div class="rounded-full p-6 bg-destructive/10">
                        <component :is="TentTree" class="h-16 w-16 text-destructive" />
                    </div>
                </div>
                <div class="space-y-2">
                    <h1 class="text-9xl font-extrabold text-foreground">423</h1>
                    <h2 class="text-2xl font-semibold text-foreground">Inactive User</h2>
                    <p class="text-muted-foreground">
                        For reactivation, please contact your system administrator.
                    </p>
                </div>
            </div>
            <div class="space-y-4">
                <div class="space-x-2">
                    <Button as-child>
                        <Link :href="route('logout')" method="post" as="button">
                            <LogOut class="h-4 w-4" /> Logout
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
