<script setup lang="ts">
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import { Moon, Sun } from 'lucide-vue-next';

const { appearance, updateAppearance } = useAppearance();

const isDark = computed(() => {
    if (appearance.value === 'system') {
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    return appearance.value === 'dark';
});

const toggleAppearance = () => {
    if (appearance.value === 'system') {
        updateAppearance(isDark.value ? 'light' : 'dark');
    } else {
        updateAppearance(isDark.value ? 'light' : 'dark');
    }
};
</script>

<template>
    <button
        @click="toggleAppearance"
        class="rounded-full p-2 transition hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50"
        aria-label="Toggle dark mode"
    >
        <component :is="isDark ? Sun : Moon" class="h-5 w-5" />
    </button>
</template>
