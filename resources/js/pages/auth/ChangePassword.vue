<script setup lang="ts">

import { Head, useForm} from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Alert } from '@/components/ui/alert';
import ChangePasswordBase from '@/layouts/auth/AuthChangePassword.vue';
import { ref } from 'vue';
import { User, Eye, EyeOff} from 'lucide-vue-next';

// Define props
defineProps<Props>();

interface Props {
    user: {
        name: string;
        email: string;
    };
}

// Setup form to sumbit new password
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    password: '',
    password_confirmation: '',
    remember: false,
});

const submit = () => {
    form.post(route('post.first.login'), {
        onFinish: () => form.reset('password','password_confirmation'),
    });
};

</script>

<template>
    <ChangePasswordBase title="Ubah Password" description="Untuk keamanan akun, silakan ganti password default Anda">
    <Head title="Ubah Password" />
    <Alert variant="default" class="mb-6 p-4 bg-default/10 rounded-lg">
            <div class="flex items-center space-x-3">
                <div class="flex items-center self-center font-medium mr-4">
                    <User class="size-8 text-primary" />
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ user.name }}</p>
                    <p class="text-sm text-gray-600 dark:text-muted-foreground">{{ user.email }}</p>
                </div>
            </div>
    </Alert>
    <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="password">Password Baru</Label>
                    <div class="relative">
                    <Input
                        id="password"
                        v-model="form.password"
                        :type="showPassword? 'text' : 'password'"
                        placeholder="Masukkan password baru"
                        required
                        autofocus
                        :tabindex="1"
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
                    <InputError :message="form.errors.password" />
                </div>
                <div class="grid gap-2">
                    <Label for="password_confirmation">Konfirmasi Password Baru</Label>
                    <div class="relative">
                    <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        :type="showConfirmPassword? 'text' : 'password'"
                        placeholder="Masukkan password baru"
                        required
                        autofocus
                        :tabindex="1"
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
                </div>
                <Button type="submit" class=" w-full mt-2" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Submit
                </Button>
            </div>
        </form>
    </ChangePasswordBase>

</template>
