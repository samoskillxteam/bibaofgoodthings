<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { useTheme } from '@/composables/useTheme';
import { 
    ShoppingBag, 
    Menu, 
    X, 
    MessageCircle, 
    Sparkles,
    Shirt,
    Cake
} from 'lucide-vue-next';

const page = usePage<PageProps>();
const { isDark } = useTheme();

const mobileMenuOpen = ref(false);

const cartCount = computed(() => page.props.cartCount || 0);
const settings = computed(() => page.props.settings);

const logoSrc = computed(() => {
    if (isDark.value) {
        return settings.value?.logo_dark_url || '/images/logo_dark.png';
    }
    return settings.value?.logo_url || '/images/logo.png';
});

const whatsappUrl = computed(() => {
    const raw = settings.value?.whatsapp_number || '2348012345678';
    const clean = raw.replace(/[^\d]/g, '');
    return `https://wa.me/${clean}?text=${encodeURIComponent('Hello! I would like to make an enquiry with Biba of Good Things.')}`;
});
</script>

<template>
    <header class="sticky top-0 z-50 bg-white/80 dark:bg-black/80 backdrop-blur-xl border-b border-black/[0.06] dark:border-white/[0.08] transition-colors duration-200">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <Link :href="route('home')" class="flex items-center gap-2.5 group">
                        <img 
                            :src="logoSrc" 
                            alt="Biba of Good Things" 
                            class="h-8 w-8 object-contain rounded-full transition-opacity group-hover:opacity-80"
                        />
                        <div class="flex items-baseline gap-1.5">
                            <span class="font-semibold text-sm tracking-tight text-zinc-900 dark:text-white">
                                Biba
                            </span>
                            <span class="text-[10px] tracking-widest uppercase text-zinc-400 dark:text-zinc-500 font-medium">
                                Of Good Things
                            </span>
                        </div>
                    </Link>
                </div>

                <!-- Desktop Navigation Links (Apple Clean Minimalist Typography) -->
                <div class="hidden md:flex items-center space-x-1 lg:space-x-7 text-[13px] tracking-tight">
                    <Link 
                        :href="route('home')" 
                        class="px-2.5 py-1 rounded-full transition-colors duration-150"
                        :class="route().current('home') ? 'text-black dark:text-white font-medium' : 'text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                    >
                        Store
                    </Link>

                    <Link 
                        :href="route('clothing.index')" 
                        class="px-2.5 py-1 rounded-full transition-colors duration-150"
                        :class="route().current('clothing.*') ? 'text-black dark:text-white font-medium' : 'text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                    >
                        Clothings
                    </Link>

                    <Link 
                        :href="route('confectionery.index')" 
                        class="px-2.5 py-1 rounded-full transition-colors duration-150"
                        :class="route().current('confectionery.*') ? 'text-black dark:text-white font-medium' : 'text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                    >
                        Treats
                    </Link>

                    <Link 
                        :href="route('about')" 
                        class="px-2.5 py-1 rounded-full transition-colors duration-150"
                        :class="route().current('about') ? 'text-black dark:text-white font-medium' : 'text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                    >
                        About
                    </Link>

                    <Link 
                        :href="route('contact')" 
                        class="px-2.5 py-1 rounded-full transition-colors duration-150"
                        :class="route().current('contact') ? 'text-black dark:text-white font-medium' : 'text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                    >
                        Contact
                    </Link>
                </div>

                <!-- Right Side Actions (Theme, WhatsApp, Cart) -->
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <!-- Theme Toggle -->
                    <ThemeToggle />

                    <!-- WhatsApp Quick Chat (Clean Pill) -->
                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-all active:scale-95"
                        title="Chat with us on WhatsApp"
                    >
                        <MessageCircle class="w-3.5 h-3.5" />
                        <span>WhatsApp</span>
                    </a>

                    <!-- Cart Bag (Apple Style Icon Button) -->
                    <Link
                        :href="route('cart.index')"
                        class="relative p-2 rounded-full text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/10 transition-colors flex items-center justify-center focus:outline-none"
                        aria-label="View shopping bag"
                    >
                        <ShoppingBag class="w-4 h-4" />
                        <span 
                            v-if="cartCount > 0"
                            class="absolute top-1 right-1 bg-black text-white dark:bg-white dark:text-black text-[9px] font-semibold h-4 w-4 rounded-full flex items-center justify-center"
                        >
                            {{ cartCount }}
                        </span>
                    </Link>

                    <!-- Mobile Menu Button -->
                    <button
                        type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 rounded-full text-zinc-700 dark:text-zinc-300 hover:bg-black/5 dark:hover:bg-white/10 focus:outline-none"
                        aria-label="Toggle navigation menu"
                    >
                        <X v-if="mobileMenuOpen" class="w-5 h-5" />
                        <Menu v-else class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu Drawer -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div 
                v-if="mobileMenuOpen"
                class="md:hidden border-b border-black/[0.06] dark:border-white/[0.08] bg-white/95 dark:bg-black/95 backdrop-blur-2xl px-5 pt-3 pb-6 space-y-1"
            >
                <Link
                    :href="route('home')"
                    @click="mobileMenuOpen = false"
                    class="block px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="route().current('home') ? 'bg-zinc-100 dark:bg-zinc-800 text-black dark:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                >
                    Store
                </Link>

                <Link
                    :href="route('clothing.index')"
                    @click="mobileMenuOpen = false"
                    class="block px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="route().current('clothing.*') ? 'bg-zinc-100 dark:bg-zinc-800 text-black dark:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                >
                    Biba Clothings
                </Link>

                <Link
                    :href="route('confectionery.index')"
                    @click="mobileMenuOpen = false"
                    class="block px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="route().current('confectionery.*') ? 'bg-zinc-100 dark:bg-zinc-800 text-black dark:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                >
                    Biba Treats
                </Link>

                <Link
                    :href="route('about')"
                    @click="mobileMenuOpen = false"
                    class="block px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="route().current('about') ? 'bg-zinc-100 dark:bg-zinc-800 text-black dark:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                >
                    About Us
                </Link>

                <Link
                    :href="route('contact')"
                    @click="mobileMenuOpen = false"
                    class="block px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="route().current('contact') ? 'bg-zinc-100 dark:bg-zinc-800 text-black dark:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                >
                    Contact
                </Link>

                <div class="pt-3 border-t border-black/[0.06] dark:border-white/[0.08]">
                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 text-xs font-medium rounded-full bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs"
                    >
                        <MessageCircle class="w-4 h-4" />
                        <span>Chat on WhatsApp</span>
                    </a>
                </div>
            </div>
        </transition>
    </header>
</template>
