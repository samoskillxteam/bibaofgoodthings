<script setup lang="ts">
import { computed } from 'vue';
import { usePage, Head, Link } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import { MessageCircle, CheckCircle, AlertTriangle, AlertCircle, Info, X, Home, Shirt, Cake, ShoppingBag } from 'lucide-vue-next';

interface Props {
    title?: string;
    description?: string;
}

const props = defineProps<Props>();
const page = usePage<PageProps>();

const flash = computed(() => page.props.flash);
const settings = computed(() => page.props.settings);
const cartCount = computed(() => page.props.cartCount || 0);

const whatsappFloatUrl = computed(() => {
    const raw = settings.value?.whatsapp_number || '2348012345678';
    const clean = raw.replace(/[^\d]/g, '');
    return `https://wa.me/${clean}?text=${encodeURIComponent('Hello Biba! I am visiting your store and would like to ask a question.')}`;
});
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#FBFBFD] text-[#1D1D1F] dark:bg-[#000000] dark:text-[#F5F5F7] transition-colors duration-200">
        <Head>
            <title>{{ props.title ? `${props.title} | Biba of Good Things` : 'Biba of Good Things — Fashion & Confectioneries' }}</title>
            <meta v-if="props.description" name="description" :content="props.description" />
            <meta property="og:title" :content="props.title || 'Biba of Good Things'" />
            <meta property="og:description" :content="props.description || 'Curated fashion from Biba Clothings and handcrafted treats from Biba Treats.'" />
        </Head>

        <!-- Navbar -->
        <Navbar />

        <!-- Flash Messages Banner -->
        <div v-if="flash.success || flash.error || flash.warning || flash.info" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div 
                v-if="flash.success" 
                class="flex items-center gap-3 p-3.5 rounded-2xl bg-emerald-50/80 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 shadow-2xs"
            >
                <CheckCircle class="w-4 h-4 text-emerald-600 shrink-0" />
                <p class="text-xs sm:text-sm font-medium flex-1">{{ flash.success }}</p>
            </div>

            <div 
                v-if="flash.error" 
                class="flex items-center gap-3 p-3.5 rounded-2xl bg-rose-50/80 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/60 shadow-2xs mt-2"
            >
                <AlertCircle class="w-4 h-4 text-rose-600 shrink-0" />
                <p class="text-xs sm:text-sm font-medium flex-1">{{ flash.error }}</p>
            </div>

            <div 
                v-if="flash.warning" 
                class="flex items-center gap-3 p-3.5 rounded-2xl bg-amber-50/80 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60 shadow-2xs mt-2"
            >
                <AlertTriangle class="w-4 h-4 text-amber-600 shrink-0" />
                <p class="text-xs sm:text-sm font-medium flex-1">{{ flash.warning }}</p>
            </div>

            <div 
                v-if="flash.info" 
                class="flex items-center gap-3 p-3.5 rounded-2xl bg-blue-50/80 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60 shadow-2xs mt-2"
            >
                <Info class="w-4 h-4 text-blue-600 shrink-0" />
                <p class="text-xs sm:text-sm font-medium flex-1">{{ flash.info }}</p>
            </div>
        </div>

        <!-- Main Content -->
        <main class="flex-grow pb-16 sm:pb-0">
            <slot />
        </main>

        <!-- Desktop Floating WhatsApp Contact Pill -->
        <aside class="hidden sm:block fixed bottom-6 right-6 z-40">
            <a
                :href="whatsappFloatUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-2 px-4 py-2.5 bg-zinc-900/90 dark:bg-white/90 hover:bg-zinc-900 dark:hover:bg-white text-white dark:text-zinc-950 font-medium text-xs rounded-full shadow-lg backdrop-blur-md transition-all duration-200 hover:scale-105 active:scale-95 group border border-white/10 dark:border-black/10"
                aria-label="Contact us on WhatsApp"
            >
                <MessageCircle class="w-4 h-4 text-emerald-400 dark:text-emerald-600" />
                <span class="tracking-tight">Chat on WhatsApp</span>
            </a>
        </aside>

        <!-- Mobile Bottom Sticky Navigation Bar (Apple Frosted Dock) -->
        <nav class="sm:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/80 dark:bg-black/80 backdrop-blur-xl border-t border-black/[0.06] dark:border-white/[0.08] px-2 py-1.5 flex items-center justify-around shadow-lg">
            <Link :href="route('home')" class="flex flex-col items-center gap-0.5 text-[10px] font-normal py-1 px-2 rounded-lg transition-colors" :class="route().current('home') ? 'text-black dark:text-white font-medium' : 'text-zinc-400 dark:text-zinc-500'">
                <Home class="w-4 h-4" />
                <span>Store</span>
            </Link>
            <Link :href="route('clothing.index')" class="flex flex-col items-center gap-0.5 text-[10px] font-normal py-1 px-2 rounded-lg transition-colors" :class="route().current('clothing.*') ? 'text-black dark:text-white font-medium' : 'text-zinc-400 dark:text-zinc-500'">
                <Shirt class="w-4 h-4" />
                <span>Clothings</span>
            </Link>
            <Link :href="route('confectionery.index')" class="flex flex-col items-center gap-0.5 text-[10px] font-normal py-1 px-2 rounded-lg transition-colors" :class="route().current('confectionery.*') ? 'text-black dark:text-white font-medium' : 'text-zinc-400 dark:text-zinc-500'">
                <Cake class="w-4 h-4" />
                <span>Treats</span>
            </Link>
            <Link :href="route('cart.index')" class="relative flex flex-col items-center gap-0.5 text-[10px] font-normal py-1 px-2 rounded-lg transition-colors" :class="route().current('cart.*') ? 'text-black dark:text-white font-medium' : 'text-zinc-400 dark:text-zinc-500'">
                <ShoppingBag class="w-4 h-4" />
                <span v-if="cartCount > 0" class="absolute -top-0.5 right-1 px-1 min-w-[14px] h-[14px] bg-black text-white dark:bg-white dark:text-black text-[8px] font-semibold rounded-full flex items-center justify-center">
                    {{ cartCount }}
                </span>
                <span>Bag</span>
            </Link>
            <a :href="whatsappFloatUrl" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center gap-0.5 text-[10px] font-normal text-emerald-600 dark:text-emerald-400 py-1 px-2">
                <div class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center">
                    <MessageCircle class="w-3 h-3" />
                </div>
                <span>Chat</span>
            </a>
        </nav>

        <!-- Footer -->
        <Footer />
    </div>
</template>
