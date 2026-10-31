<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import type { Category, Product } from '@/types';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { Search, Shirt, SlidersHorizontal, X, Sparkles, MessageCircle } from 'lucide-vue-next';

interface Props {
    category: Category;
    products: {
        data: Product[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
        total: number;
        current_page: number;
        last_page: number;
    };
    filters: {
        search?: string;
        sort?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const sort = ref(props.filters.sort || 'featured');

const applyFilters = () => {
    router.get(
        route('clothing.index'),
        {
            search: search.value || undefined,
            sort: sort.value,
        },
        { preserveState: true, replace: true }
    );
};

const clearSearch = () => {
    search.value = '';
    applyFilters();
};

// ── Dynamic Purply-Pinkish Palettes (Rotates on every refresh) ────────
interface PurplyPinkTheme {
    id: string;
    name: string;
    bgGradientLight: string;
    bgGradientDark: string;
    glowColor1: string;
    glowColor2: string;
    borderColorLight: string;
    borderColorDark: string;
    tagBgLight: string;
    tagBgDark: string;
    tagTextLight: string;
    tagTextDark: string;
    tagBorder: string;
    cardBgDark: string;
    cardBorderDark: string;
    brandPillBg: string;
}

const themes: PurplyPinkTheme[] = [
    {
        id: 'mulberry',
        name: 'Mulberry Velvet & Plum',
        bgGradientLight: 'linear-gradient(135deg, #FDF2FA 0%, #F7E4F2 50%, #EED3E8 100%)',
        bgGradientDark: 'linear-gradient(135deg, #2A0A26 0%, #1B0519 50%, #0E020D 100%)',
        glowColor1: 'rgba(184, 50, 128, 0.28)',
        glowColor2: 'rgba(139, 30, 106, 0.35)',
        borderColorLight: 'rgba(184, 50, 128, 0.18)',
        borderColorDark: 'rgba(184, 50, 128, 0.32)',
        tagBgLight: 'rgba(139, 30, 106, 0.1)',
        tagBgDark: 'rgba(184, 50, 128, 0.25)',
        tagTextLight: '#8B1E6A',
        tagTextDark: '#FBB6CE',
        tagBorder: 'rgba(184, 50, 128, 0.25)',
        cardBgDark: '#1C061A',
        cardBorderDark: 'rgba(184, 50, 128, 0.35)',
        brandPillBg: '#8B1E6A',
    },
    {
        id: 'orchid',
        name: 'Orchid Magenta & Wild Fuchsia',
        bgGradientLight: 'linear-gradient(135deg, #FDF4FF 0%, #FAE8FF 50%, #F5D0FE 100%)',
        bgGradientDark: 'linear-gradient(135deg, #32072B 0%, #1F031B 50%, #0E010C 100%)',
        glowColor1: 'rgba(217, 70, 239, 0.26)',
        glowColor2: 'rgba(162, 28, 175, 0.35)',
        borderColorLight: 'rgba(217, 70, 239, 0.18)',
        borderColorDark: 'rgba(217, 70, 239, 0.32)',
        tagBgLight: 'rgba(162, 28, 175, 0.1)',
        tagBgDark: 'rgba(217, 70, 239, 0.25)',
        tagTextLight: '#A21CAF',
        tagTextDark: '#F0ABFC',
        tagBorder: 'rgba(217, 70, 239, 0.25)',
        cardBgDark: '#1D031A',
        cardBorderDark: 'rgba(217, 70, 239, 0.35)',
        brandPillBg: '#A21CAF',
    },
    {
        id: 'amethyst',
        name: 'Amethyst Violet & Dusty Rose',
        bgGradientLight: 'linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 50%, #E9D5FF 100%)',
        bgGradientDark: 'linear-gradient(135deg, #28092E 0%, #18041D 50%, #0C010F 100%)',
        glowColor1: 'rgba(192, 38, 211, 0.25)',
        glowColor2: 'rgba(126, 34, 206, 0.35)',
        borderColorLight: 'rgba(192, 38, 211, 0.18)',
        borderColorDark: 'rgba(192, 38, 211, 0.32)',
        tagBgLight: 'rgba(126, 34, 206, 0.1)',
        tagBgDark: 'rgba(192, 38, 211, 0.25)',
        tagTextLight: '#7E22CE',
        tagTextDark: '#E9D5FF',
        tagBorder: 'rgba(192, 38, 211, 0.25)',
        cardBgDark: '#1A041F',
        cardBorderDark: 'rgba(192, 38, 211, 0.35)',
        brandPillBg: '#7E22CE',
    },
    {
        id: 'rose_noir',
        name: 'Dark Berry & Rose Quartz',
        bgGradientLight: 'linear-gradient(135deg, #FFF1F2 0%, #FCE7F3 50%, #FBCFE8 100%)',
        bgGradientDark: 'linear-gradient(135deg, #330823 0%, #200416 50%, #10010B 100%)',
        glowColor1: 'rgba(244, 63, 94, 0.22)',
        glowColor2: 'rgba(190, 24, 93, 0.35)',
        borderColorLight: 'rgba(190, 24, 93, 0.18)',
        borderColorDark: 'rgba(190, 24, 93, 0.32)',
        tagBgLight: 'rgba(190, 24, 93, 0.1)',
        tagBgDark: 'rgba(244, 63, 94, 0.22)',
        tagTextLight: '#BE185D',
        tagTextDark: '#FDA4AF',
        tagBorder: 'rgba(190, 24, 93, 0.25)',
        cardBgDark: '#210417',
        cardBorderDark: 'rgba(190, 24, 93, 0.35)',
        brandPillBg: '#BE185D',
    },
    {
        id: 'lilac_fuchsia',
        name: 'Wild Lilac & Pinkish Mauve',
        bgGradientLight: 'linear-gradient(135deg, #FDF2F8 0%, #FCE7F3 50%, #F5D0FE 100%)',
        bgGradientDark: 'linear-gradient(135deg, #26092B 0%, #17051B 50%, #0B010E 100%)',
        glowColor1: 'rgba(232, 121, 249, 0.25)',
        glowColor2: 'rgba(168, 85, 247, 0.35)',
        borderColorLight: 'rgba(168, 85, 247, 0.18)',
        borderColorDark: 'rgba(168, 85, 247, 0.32)',
        tagBgLight: 'rgba(168, 85, 247, 0.1)',
        tagBgDark: 'rgba(232, 121, 249, 0.22)',
        tagTextLight: '#A855F7',
        tagTextDark: '#F5D0FE',
        tagBorder: 'rgba(168, 85, 247, 0.25)',
        cardBgDark: '#18041D',
        cardBorderDark: 'rgba(168, 85, 247, 0.35)',
        brandPillBg: '#A855F7',
    },
];

const currentTheme = ref<PurplyPinkTheme>(themes[0]);
const isDark = ref(false);

const updateDarkMode = () => {
    isDark.value = document.documentElement.classList.contains('dark');
};

onMounted(() => {
    updateDarkMode();
    try {
        const lastIdx = Number(sessionStorage.getItem('clothing_theme_idx') ?? -1);
        const nextIdx = (lastIdx + 1) % themes.length;
        sessionStorage.setItem('clothing_theme_idx', String(nextIdx));
        currentTheme.value = themes[nextIdx];
    } catch {
        currentTheme.value = themes[Math.floor(Math.random() * themes.length)];
    }

    const observer = new MutationObserver(() => updateDarkMode());
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});
</script>

<template>
    <AppLayout 
        title="Biba Clothings — Curated Apparel" 
        description="Explore tailored linen sets, silk wrap dresses, power blazers, and luxury cotton essentials from Biba Clothings."
    >
        <!-- Category Hero Banner (Dynamic Purply-Pinkish Gradient on Refresh) -->
        <div 
            class="relative overflow-hidden border-b transition-colors duration-700 ease-out"
            :style="{
                background: isDark ? currentTheme.bgGradientDark : currentTheme.bgGradientLight,
                borderColor: isDark ? currentTheme.borderColorDark : currentTheme.borderColorLight,
            }"
        >
            <!-- Ambient Purply-Pink Glow (Reactive) -->
            <div 
                class="absolute -top-32 -left-32 w-96 h-96 rounded-full blur-3xl pointer-events-none transition-all duration-700"
                :style="{ backgroundColor: currentTheme.glowColor1 }"
            ></div>
            <div 
                class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full blur-3xl pointer-events-none transition-all duration-700"
                :style="{ backgroundColor: currentTheme.glowColor2 }"
            ></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Left: Brand Copy & Meta -->
                    <div class="lg:col-span-7 space-y-4 sm:space-y-5">
                        <div 
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide border transition-all duration-500"
                            :style="{
                                backgroundColor: isDark ? currentTheme.tagBgDark : currentTheme.tagBgLight,
                                color: isDark ? currentTheme.tagTextDark : currentTheme.tagTextLight,
                                borderColor: currentTheme.tagBorder,
                            }"
                        >
                            <Shirt class="w-3.5 h-3.5" />
                            <span>Fashion & Apparel Collection</span>
                        </div>

                        <div>
                            <h1 class="font-semibold text-3xl sm:text-5xl lg:text-6xl text-zinc-900 dark:text-white tracking-tight leading-tight">
                                {{ category.brand_name || 'Biba Clothings' }}
                            </h1>
                            <p class="text-base sm:text-xl text-zinc-700 dark:text-zinc-300 font-normal mt-2 tracking-tight">
                                Effortless elegance, graceful cuts, and bespoke tailoring.
                            </p>
                        </div>

                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300/80 leading-relaxed max-w-xl">
                            {{ category.description || 'Explore curated linen two-pieces, silky satin co-ords, fluid wrap dresses, and luxury everyday essentials tailored to make an understated, confident statement.' }}
                        </p>

                        <!-- Key Highlights / Badges -->
                        <div class="pt-2 flex flex-wrap items-center gap-2.5 sm:gap-3 text-xs text-zinc-700 dark:text-zinc-300">
                            <div 
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/80 dark:bg-black/40 border shadow-2xs font-medium backdrop-blur-md"
                                :style="{ borderColor: isDark ? currentTheme.borderColorDark : currentTheme.borderColorLight }"
                            >
                                <Sparkles class="w-3.5 h-3.5 text-amber-500" />
                                <span>Curated Designer Cuts</span>
                            </div>
                            <div 
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/80 dark:bg-black/40 border shadow-2xs font-medium backdrop-blur-md"
                                :style="{ borderColor: isDark ? currentTheme.borderColorDark : currentTheme.borderColorLight }"
                            >
                                <MessageCircle class="w-3.5 h-3.5 text-emerald-500" />
                                <span>Instant WhatsApp Order</span>
                            </div>
                            <div 
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/80 dark:bg-black/40 border shadow-2xs font-medium backdrop-blur-md"
                                :style="{ borderColor: isDark ? currentTheme.borderColorDark : currentTheme.borderColorLight }"
                            >
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ products.total }}</span>
                                <span>Styles Available</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Featured Visual Showcase Card -->
                    <div class="lg:col-span-5">
                        <div 
                            class="relative group rounded-3xl overflow-hidden bg-white/90 shadow-xl sm:shadow-2xl max-w-md mx-auto lg:max-w-none border transition-all duration-700"
                            :style="{
                                backgroundColor: isDark ? currentTheme.cardBgDark : 'rgba(255, 255, 255, 0.9)',
                                borderColor: isDark ? currentTheme.cardBorderDark : currentTheme.borderColorLight,
                            }"
                        >
                            <!-- Image Container -->
                            <div class="aspect-square sm:aspect-[4/3] lg:aspect-square overflow-hidden bg-zinc-950">
                                <img 
                                    src="/images/clothing_hero.png" 
                                    alt="Biba Clothings Satin & Silk Co-ord Sets" 
                                    class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out"
                                />
                            </div>

                            <!-- Floating Apple-style pill badge on image -->
                            <div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 z-10 flex items-center justify-between pointer-events-none">
                                <div class="px-3 py-1.5 rounded-full bg-black/60 dark:bg-black/75 backdrop-blur-md text-white text-[11px] font-medium border border-white/10 shadow-sm flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Signature Co-Ord Sets</span>
                                </div>
                                <span 
                                    class="px-2.5 py-1 rounded-full text-white text-[10px] font-semibold backdrop-blur-md shadow-sm border border-white/20 transition-colors duration-500"
                                    :style="{ backgroundColor: currentTheme.brandPillBg }"
                                >
                                    Biba Clothings
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 border-b border-black/[0.06] dark:border-white/[0.08]">
                <!-- Search Box (Apple Style Pill Input) -->
                <div class="relative w-full sm:w-80">
                    <Search class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input 
                        v-model="search"
                        @keydown.enter="applyFilters"
                        type="text" 
                        placeholder="Search collection..." 
                        class="w-full pl-10 pr-9 py-2 text-xs sm:text-sm rounded-full border border-black/10 dark:border-white/15 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-black dark:focus:ring-white transition-all"
                    />
                    <button 
                        v-if="search" 
                        type="button" 
                        @click="clearSearch"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>

                <!-- Sort & Meta -->
                <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-4">
                    <span class="text-xs text-zinc-400 dark:text-zinc-500">
                        {{ products.total }} items
                    </span>

                    <div class="flex items-center gap-2">
                        <SlidersHorizontal class="w-3.5 h-3.5 text-zinc-400" />
                        <select 
                            v-model="sort" 
                            @change="applyFilters"
                            class="text-xs sm:text-sm py-1.5 px-3 rounded-full border border-black/10 dark:border-white/15 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-black dark:focus:ring-white cursor-pointer"
                        >
                            <option value="featured">Featured First</option>
                            <option value="newest">Newest Arrivals</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div v-if="products.data.length > 0" class="grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4 pt-6 sm:pt-8">
                <ProductCard 
                    v-for="product in products.data" 
                    :key="product.id" 
                    :product="product" 
                />
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20 bg-[#F5F5F7] dark:bg-[#161617] rounded-3xl border border-black/[0.04] dark:border-white/[0.08] mt-8 p-8">
                <Shirt class="w-10 h-10 mx-auto text-zinc-400 mb-3" />
                <h3 class="font-medium text-base text-zinc-900 dark:text-white">No apparel pieces found</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto mt-1">
                    Try adjusting your search criteria or explore our newest featured arrivals.
                </p>
                <button 
                    @click="clearSearch"
                    type="button" 
                    class="mt-4 px-4 py-2 text-xs font-medium rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 transition-all active:scale-95"
                >
                    Clear Filters
                </button>
            </div>

            <!-- Pagination -->
            <div v-if="products.links.length > 3" class="mt-12 flex justify-center gap-1.5">
                <component
                    :is="link.url ? 'Link' : 'span'"
                    v-for="(link, i) in products.links"
                    :key="i"
                    :href="link.url || undefined"
                    class="px-3.5 py-1.5 text-xs font-medium rounded-full border transition-colors"
                    :class="[
                        link.active 
                            ? 'bg-zinc-900 text-white border-zinc-900 dark:bg-white dark:text-zinc-950 dark:border-white' 
                            : 'bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border-black/10 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-zinc-800',
                        !link.url ? 'opacity-40 cursor-not-allowed' : ''
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </AppLayout>
</template>
