<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import type { Category, Product } from '@/types';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { Search, Cake, SlidersHorizontal, X, Sparkles, MessageCircle } from 'lucide-vue-next';

interface Props {
    category: Category;
    products: {
        data: Product[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
        total: number;
        current_page: number;
        last_page: number;
    };
    productTypes: string[];
    filters: {
        search?: string;
        type?: string;
        sort?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const currentType = ref(props.filters.type || '');
const sort = ref(props.filters.sort || 'featured');

const filterByType = (type: string) => {
    currentType.value = type;
    applyFilters();
};

const applyFilters = () => {
    router.get(
        route('confectionery.index'),
        {
            search: search.value || undefined,
            type: currentType.value || undefined,
            sort: sort.value,
        },
        { preserveState: true, replace: true }
    );
};

const clearSearch = () => {
    search.value = '';
    applyFilters();
};

const formatTypeName = (type: string) => {
    switch (type) {
        case 'cupcake': return 'Gourmet Cupcakes';
        case 'cake': return 'Cakes & Bento';
        case 'snack': return 'Brownies & Slices';
        case 'pastry': return 'French Pastries';
        case 'drink': return 'Artisanal Drinks';
        default: return type.charAt(0).toUpperCase() + type.slice(1);
    }
};
</script>

<template>
    <AppLayout 
        title="Biba Treats — Artisanal Confectioneries" 
        description="Freshly baked celebration cakes, gourmet cupcakes, brownies, and refreshing botanical drinks from Biba Treats. Order on WhatsApp."
    >
        <!-- Category Hero Banner (Burgundy Theme) -->
        <div class="relative overflow-hidden bg-gradient-to-br from-[#FCF2F5] via-[#F8E7EC] to-[#F2D7E0] dark:from-[#2D0612] dark:via-[#1E040C] dark:to-[#0D0206] border-b border-[#80142F]/10 dark:border-[#80142F]/30">
            <!-- Ambient Burgundy Glow -->
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#80142F]/15 dark:bg-[#80142F]/25 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-[#5C0D22]/20 dark:bg-[#5C0D22]/35 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Left: Brand Copy & Meta -->
                    <div class="lg:col-span-7 space-y-4 sm:space-y-5">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#80142F]/10 dark:bg-[#80142F]/25 text-[#80142F] dark:text-[#FFA8BE] text-xs font-semibold tracking-wide border border-[#80142F]/20">
                            <Cake class="w-3.5 h-3.5" />
                            <span>Artisanal Bakery & Refreshments</span>
                        </div>

                        <div>
                            <h1 class="font-semibold text-3xl sm:text-5xl lg:text-6xl text-zinc-900 dark:text-white tracking-tight leading-tight">
                                {{ category.brand_name || 'Biba Treats' }}
                            </h1>
                            <p class="text-base sm:text-xl text-zinc-700 dark:text-zinc-300 font-normal mt-2 tracking-tight">
                                Freshly baked indulgences and botanical refreshers, handcrafted daily.
                            </p>
                        </div>

                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300/80 leading-relaxed max-w-xl">
                            {{ category.description || 'From celebration bento cakes and decadent gourmet cupcakes to handcrafted botanical hibiscus refreshers and chilled yogurt smoothies, every recipe is made to order with love and delivered fresh.' }}
                        </p>

                        <!-- Key Highlights / Badges -->
                        <div class="pt-2 flex flex-wrap items-center gap-2.5 sm:gap-3 text-xs text-zinc-700 dark:text-zinc-300">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/80 dark:bg-[#1E040C]/80 border border-[#80142F]/15 dark:border-[#80142F]/30 shadow-2xs font-medium backdrop-blur-md">
                                <Sparkles class="w-3.5 h-3.5 text-amber-500" />
                                <span>100% Fresh Daily Bakes</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/80 dark:bg-[#1E040C]/80 border border-[#80142F]/15 dark:border-[#80142F]/30 shadow-2xs font-medium backdrop-blur-md">
                                <MessageCircle class="w-3.5 h-3.5 text-emerald-500" />
                                <span>Instant WhatsApp Order</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/80 dark:bg-[#1E040C]/80 border border-[#80142F]/15 dark:border-[#80142F]/30 shadow-2xs font-medium backdrop-blur-md">
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ products.total }}</span>
                                <span>Fresh Treats Available</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Featured Visual Showcase Card -->
                    <div class="lg:col-span-5">
                        <div class="relative group rounded-3xl overflow-hidden bg-white/90 dark:bg-[#1E040C] border border-[#80142F]/15 dark:border-[#80142F]/35 shadow-xl sm:shadow-2xl">
                            <!-- Image Container -->
                            <div class="aspect-[4/3] sm:aspect-[16/10] lg:aspect-[4/3] overflow-hidden bg-zinc-950">
                                <img 
                                    src="/images/confectionery_hero.png" 
                                    alt="Biba Treats Botanical Refreshers & Drinks" 
                                    class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out"
                                />
                            </div>

                            <!-- Floating Apple-style pill badge on image -->
                            <div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 z-10 flex items-center justify-between pointer-events-none">
                                <div class="px-3 py-1.5 rounded-full bg-black/60 dark:bg-black/75 backdrop-blur-md text-white text-[11px] font-medium border border-white/10 shadow-sm flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Signature Botanical Refreshers</span>
                                </div>
                                <span class="px-2.5 py-1 rounded-full bg-[#80142F] text-white text-[10px] font-semibold backdrop-blur-md shadow-sm border border-white/20">
                                    Biba Treats
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Product Type Tabs (Apple Style Minimalist Filter Pills) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-4 scrollbar-thin">
                <button
                    type="button"
                    @click="filterByType('')"
                    class="px-4 py-1.5 text-xs font-medium rounded-full transition-all shrink-0 focus:outline-none"
                    :class="!currentType 
                        ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-950' 
                        : 'bg-black/5 dark:bg-white/10 text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                >
                    All Treats
                </button>

                <button
                    v-for="type in productTypes"
                    :key="type"
                    type="button"
                    @click="filterByType(type)"
                    class="px-4 py-1.5 text-xs font-medium rounded-full transition-all shrink-0 focus:outline-none"
                    :class="currentType === type 
                        ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-950' 
                        : 'bg-black/5 dark:bg-white/10 text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                >
                    {{ formatTypeName(type) }}
                </button>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-6 border-b border-black/[0.06] dark:border-white/[0.08]">
                <!-- Search Box -->
                <div class="relative w-full sm:w-80">
                    <Search class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input 
                        v-model="search"
                        @keydown.enter="applyFilters"
                        type="text" 
                        placeholder="Search cupcakes, cakes, drinks..." 
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
                        {{ products.total }} delicacies
                    </span>

                    <div class="flex items-center gap-2">
                        <SlidersHorizontal class="w-3.5 h-3.5 text-zinc-400" />
                        <select 
                            v-model="sort" 
                            @change="applyFilters"
                            class="text-xs sm:text-sm py-1.5 px-3 rounded-full border border-black/10 dark:border-white/15 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-black dark:focus:ring-white cursor-pointer"
                        >
                            <option value="featured">Featured First</option>
                            <option value="newest">Fresh Additions</option>
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
                <Cake class="w-10 h-10 mx-auto text-zinc-400 mb-3" />
                <h3 class="font-medium text-base text-zinc-900 dark:text-white">No confectionery found</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto mt-1">
                    Try clearing your search or browsing all sweet treat categories.
                </p>
                <button 
                    @click="clearSearch"
                    type="button" 
                    class="mt-4 px-4 py-2 text-xs font-medium rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 transition-all active:scale-95"
                >
                    View All Treats
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
