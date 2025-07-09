<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppearanceButton from '@/components/AppearanceButtons.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Eye, EyeOff, Lock, User } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Props {
    user: {
        nama: string;
        email: string;
        role: string;
    };
}

defineProps<Props>();

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    password: '',
    password_confirmation: '',
});

// Validasi
const passwordError = computed(() => {
    if (!form.password) return '';
    if (form.password.length < 8) return 'Password minimal 8 karakter';
    return '';
});

const confirmPasswordError = computed(() => {
    if (!form.password_confirmation) return '';
    if (form.password_confirmation !== form.password) return 'Konfirmasi password tidak sama';
    return '';
});

const isFormValid = computed(() => {
    return form.password.length >= 8 &&
           form.password_confirmation === form.password &&
           form.password_confirmation.length > 0;
});

const submit = () => {
    // Validasi sebelum submit
    if (!isFormValid.value) {
        return;
    }

    form.post(route('post.first.login'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="absolute right-3 top-3 sm:right-4 sm:top-4 md:fixed md:right-6 md:top-6 z-50">
        <AppearanceButton />
    </div>
    <Head title="Ubah Password" />
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-muted p-6 md:p-10">
        <div class="flex w-full max-w-md flex-col gap-6">
            <div class="flex flex-col gap-6">
                <Card class="w-full max-w-md">
                    <CardHeader class="text-center">
                        <div class="flex justify-center mb-4">
                            <div class="p-3 bg-destructive/10 rounded-full">
                                <Lock class="h-6 w-6 text-destructive" />
                            </div>
                        </div>
                        <CardTitle class="text-2xl font-bold">Ubah Password</CardTitle>
                        <CardDescription>
                            Untuk keamanan akun, silakan ganti password default Anda
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <!-- User Info -->
                        <div class="mb-6 p-4 bg-destructive/10 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <User class="h-5 w-5 text-destructive" />
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ user.nama }}</p>
                                    <p class="text-sm text-gray-600 dark:text-muted-foreground">{{ user.email }} • {{ user.role }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submit" class="space-y-4 grid gap-2">
                            <!-- Password Baru -->
                            <div class="grid gap-2">
                                <Label for="password">Password Baru</Label>
                                <div class="relative">
                                    <Input
                                        id="password"
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        placeholder="Masukkan password baru"
                                        class="pr-10"
                                        :class="{ 'border-red-500': form.errors.password || passwordError }"
                                        required
                                    />
                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    >
                                        <Eye v-if="showPassword" class="h-4 w-4 text-gray-400" />
                                        <EyeOff v-else class="h-4 w-4 text-gray-400" />
                                    </button>
                                </div>
                                <p v-if="passwordError" class="mt-1 text-sm text-red-600">
                                    {{ passwordError }}
                                </p>
                                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <!-- Konfirmasi Password -->
                            <div class="grid gap-2">
                                <Label for="password_confirmation">Konfirmasi Password</Label>
                                <div class="relative mt-1">
                                    <Input
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        :type="showConfirmPassword ? 'text' : 'password'"
                                        placeholder="Ulangi password baru"
                                        class="pr-10"
                                        :class="{ 'border-red-500': confirmPasswordError }"
                                        required
                                    />
                                    <button
                                        type="button"
                                        @click="showConfirmPassword = !showConfirmPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    >
                                        <Eye v-if="showConfirmPassword" class="h-4 w-4 text-gray-400" />
                                        <EyeOff v-else class="h-4 w-4 text-gray-400" />
                                    </button>
                                </div>
                                <p v-if="confirmPasswordError" class="mt-1 text-sm text-red-600">
                                    {{ confirmPasswordError }}
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <Button
                                type="submit"
                                class="w-full"
                                :disabled="form.processing || !isFormValid"
                            >
                                <span v-if="form.processing">Memproses...</span>
                                <span v-else-if="!isFormValid">Lengkapi Form</span>
                                <span v-else>Ganti Password</span>
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
