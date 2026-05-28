<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { AuthUser } from '@/types';
import { computed } from 'vue';

interface Props {
    user: AuthUser;
    showEmail?: boolean;
    showNomorInduk?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
    showNomorInduk: false,
});
const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(() => props.user.photo && props.user.photo !== '');
const userName = computed(() => props.user.name || props.user.email);
const avatarUrl = computed(() => props.user.photo ?? null);
</script>

<template>
    <Avatar class="h-8 w-8 overflow-hidden">
        <AvatarImage v-if="showAvatar" :src="avatarUrl!" :alt="userName" />
        <AvatarFallback class="text-xs text-black dark:text-white">
            {{ getInitials(userName) }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ userName }}</span>
        <span v-if="showNomorInduk" class="truncate text-xs text-muted-foreground">{{ user.nomor_induk }}</span>
        <span v-if="showEmail" class="truncate text-xs text-muted-foreground">{{ user.email }}</span>
    </div>
</template>
