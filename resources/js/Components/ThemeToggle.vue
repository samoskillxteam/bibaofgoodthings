<script setup lang="ts">
import { useTheme } from '@/composables/useTheme';
import { Sun, Moon } from 'lucide-vue-next';

const { isDark, toggleTheme } = useTheme();
</script>

<template>
    <button
        type="button"
        @click="toggleTheme"
        :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
        aria-label="Toggle colour theme"
        class="relative inline-flex items-center justify-center w-8 h-8 rounded-full text-zinc-500 hover:text-black dark:text-zinc-400 dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/10 transition-colors focus:outline-none"
    >
        <Transition
            enter-active-class="transition-all duration-200"
            enter-from-class="opacity-0 rotate-90 scale-50"
            enter-to-class="opacity-100 rotate-0 scale-100"
            leave-active-class="transition-all duration-200"
            leave-from-class="opacity-100 rotate-0 scale-100"
            leave-to-class="opacity-0 -rotate-90 scale-50"
            mode="out-in"
        >
            <Sun v-if="isDark" key="sun" class="w-4 h-4" />
            <Moon v-else key="moon" class="w-4 h-4" />
        </Transition>
    </button>
</template>
