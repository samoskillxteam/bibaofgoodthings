<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import type { Category, Product, PageProps } from '@/types';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { useTheme } from '@/composables/useTheme';
import { 
    Shirt, 
    Cake, 
    ArrowRight, 
    Sparkles, 
    MessageCircle, 
    CheckCircle2, 
    ShieldCheck, 
    Truck, 
    Star 
} from 'lucide-vue-next';

// ── Dynamic Clothing Purply-Pinkish Theme (mirrors Clothing/Index.vue) ────
interface PurplyPinkTheme {
    id: string;
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

const clothingThemes: PurplyPinkTheme[] = [
    {
        id: 'mulberry',
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

const currentClothingTheme = ref<PurplyPinkTheme>(clothingThemes[0]);
const isPageDark = ref(false);

const updatePageDark = () => {
    isPageDark.value = document.documentElement.classList.contains('dark');
};

onMounted(() => {
    updatePageDark();
    try {
        const lastIdx = Number(sessionStorage.getItem('clothing_theme_idx') ?? -1);
        // Sync with whatever Clothing/Index chose — read same key, don't re-advance
        const idx = ((lastIdx < 0 ? 0 : lastIdx)) % clothingThemes.length;
        currentClothingTheme.value = clothingThemes[idx];
    } catch {
        currentClothingTheme.value = clothingThemes[0];
    }
    const observer = new MutationObserver(() => updatePageDark());
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});

interface Props {
    categories: Category[];
    featuredClothing: Product[];
    featuredConfectionery: Product[];
    newArrivals: Product[];
    aboutStory?: string;
}

const props = defineProps<Props>();
const page = usePage<PageProps>();
const { isDark } = useTheme();
const settings = computed(() => page.props.settings);

const clothingCategory = computed(() => props.categories.find(c => c.slug === 'clothing'));
const confectioneryCategory = computed(() => props.categories.find(c => c.slug === 'confectionery'));

const whatsappUrl = computed(() => {
    const raw = settings.value?.whatsapp_number || '2348012345678';
    const clean = raw.replace(/[^\d]/g, '');
    return `https://wa.me/${clean}?text=${encodeURIComponent('Hello Biba! I would like to place an order.')}`;
});

const heroDesktopSrc = computed(() => isDark.value ? '/images/biba_hero_dark_mode.png' : '/images/biba_hero_light_mode.png');
const heroMobileSrc = computed(() => isDark.value ? '/images/hero_mobile_dark.png' : '/images/hero_mobile_light.png');
</script>

<template>
    <AppLayout 
        :title="settings?.business_name || 'Biba of Good Things'"
        :description="settings?.tagline || 'Exquisite Fashion & Handcrafted Confectioneries'"
    >
        <!-- HERO BANNER (Responsive Full Fill: Mobile vs Desktop) -->
        <section class="relative w-full overflow-hidden bg-[#FAF8F5] dark:bg-[#100E0C]">
            <picture class="block w-full">
                <!-- Mobile Screens (< 640px) -->
                <source media="(max-width: 639px)" :srcset="heroMobileSrc" />
                <!-- Desktop Screens (>= 640px) -->
                <img 
                    :src="heroDesktopSrc" 
                    alt="Biba of Good Things — Where Style Meets Sweet Perfection" 
                    class="w-full h-auto block object-cover object-center"
                    loading="eager"
                />
            </picture>
        </section>

        <!-- TWO MAIN CATEGORY HERO SECTIONS (side-by-side, hero-style design) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">

                <!-- 1. Biba Clothings (Dynamic Purply-Pinkish) -->
                <div
                    class="relative overflow-hidden rounded-3xl border transition-colors duration-700 ease-out"
                    :style="{
                        background: isPageDark ? currentClothingTheme.bgGradientDark : currentClothingTheme.bgGradientLight,
                        borderColor: isPageDark ? currentClothingTheme.borderColorDark : currentClothingTheme.borderColorLight,
                    }"
                >
                    <div
                        class="absolute -top-16 -left-16 w-56 h-56 rounded-full blur-3xl pointer-events-none transition-all duration-700"
                        :style="{ backgroundColor: currentClothingTheme.glowColor1 }"
                    ></div>
                    <div
                        class="absolute -bottom-16 -right-16 w-56 h-56 rounded-full blur-3xl pointer-events-none transition-all duration-700"
                        :style="{ backgroundColor: currentClothingTheme.glowColor2 }"
                    ></div>

                    <div class="relative p-5 sm:p-7 space-y-3">
                        <!-- Category pill -->
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide border transition-all duration-500"
                            :style="{
                                backgroundColor: isPageDark ? currentClothingTheme.tagBgDark : currentClothingTheme.tagBgLight,
                                color: isPageDark ? currentClothingTheme.tagTextDark : currentClothingTheme.tagTextLight,
                                borderColor: currentClothingTheme.tagBorder,
                            }"
                        >
                            <Shirt class="w-3 h-3" />
                            <span>Fashion &amp; Apparel Collection</span>
                        </div>

                        <!-- Heading + tagline -->
                        <div>
                            <h2 class="font-semibold text-2xl sm:text-3xl text-zinc-900 dark:text-white tracking-tight leading-tight">
                                Biba Clothings
                            </h2>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300 font-normal mt-1">
                                Effortless elegance, graceful cuts, and bespoke tailoring.
                            </p>
                        </div>

                        <p class="text-xs text-zinc-600 dark:text-zinc-300/80 leading-relaxed">
                            {{ clothingCategory?.description || 'Explore refined collections designed to inspire confidence. From luxurious linen sets to sweeping silk dresses and versatile everyday essentials.' }}
                        </p>

                        <!-- Badges -->
                        <div class="flex flex-wrap items-center gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                            <div
                                class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/80 dark:bg-black/40 border font-medium backdrop-blur-md"
                                :style="{ borderColor: isPageDark ? currentClothingTheme.borderColorDark : currentClothingTheme.borderColorLight }"
                            >
                                <Sparkles class="w-3 h-3 text-amber-500" />
                                <span>Curated Cuts</span>
                            </div>
                            <div
                                class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/80 dark:bg-black/40 border font-medium backdrop-blur-md"
                                :style="{ borderColor: isPageDark ? currentClothingTheme.borderColorDark : currentClothingTheme.borderColorLight }"
                            >
                                <MessageCircle class="w-3 h-3 text-emerald-500" />
                                <span>WhatsApp Order</span>
                            </div>
                        </div>

                        <!-- Hero image card -->
                        <div
                            class="relative group rounded-2xl overflow-hidden border transition-all duration-700"
                            :style="{
                                backgroundColor: isPageDark ? currentClothingTheme.cardBgDark : 'rgba(255,255,255,0.9)',
                                borderColor: isPageDark ? currentClothingTheme.cardBorderDark : currentClothingTheme.borderColorLight,
                            }"
                        >
                            <div class="aspect-[16/9] overflow-hidden bg-zinc-950">
                                <img
                                    src="/images/clothing_hero.png"
                                    alt="Biba Clothings Satin & Silk Co-ord Sets"
                                    class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out"
                                />
                            </div>
                            <div class="absolute bottom-2.5 left-2.5 right-2.5 z-10 flex items-center justify-between pointer-events-none">
                                <div class="px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[10px] font-medium border border-white/10 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Signature Co-Ord Sets</span>
                                </div>
                                <span
                                    class="px-2 py-0.5 rounded-full text-white text-[9px] font-semibold border border-white/20 transition-colors duration-500"
                                    :style="{ backgroundColor: currentClothingTheme.brandPillBg }"
                                >Biba Clothings</span>
                            </div>
                        </div>

                        <!-- CTA -->
                        <Link
                            :href="route('clothing.index')"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-100 font-medium text-xs transition-all duration-200 active:scale-95 shadow-sm"
                        >
                            <span>Explore Clothings</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>

                <!-- 2. Biba Treats (Fixed Burgundy) -->
                <div class="relative overflow-hidden rounded-3xl border border-[#80142F]/10 dark:border-[#80142F]/30 bg-gradient-to-br from-[#FCF2F5] via-[#F8E7EC] to-[#F2D7E0] dark:from-[#2D0612] dark:via-[#1E040C] dark:to-[#0D0206]">
                    <div class="absolute -top-16 -left-16 w-56 h-56 bg-[#80142F]/15 dark:bg-[#80142F]/25 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-16 -right-16 w-56 h-56 bg-[#5C0D22]/20 dark:bg-[#5C0D22]/35 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative p-5 sm:p-7 space-y-3">
                        <!-- Category pill -->
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#80142F]/10 dark:bg-[#80142F]/25 text-[#80142F] dark:text-[#FFA8BE] text-xs font-semibold tracking-wide border border-[#80142F]/20">
                            <Cake class="w-3 h-3" />
                            <span>Artisanal Bakery &amp; Refreshments</span>
                        </div>

                        <!-- Heading + tagline -->
                        <div>
                            <h2 class="font-semibold text-2xl sm:text-3xl text-zinc-900 dark:text-white tracking-tight leading-tight">
                                Biba Treats
                            </h2>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300 font-normal mt-1">
                                Freshly baked indulgences and botanical refreshers, handcrafted daily.
                            </p>
                        </div>

                        <p class="text-xs text-zinc-600 dark:text-zinc-300/80 leading-relaxed">
                            {{ confectioneryCategory?.description || 'Freshly baked indulgences crafted with premium ingredients. Perfect for celebrations, casual afternoon cravings, and memorable gift boxes.' }}
                        </p>

                        <!-- Badges -->
                        <div class="flex flex-wrap items-center gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/80 dark:bg-[#1E040C]/80 border border-[#80142F]/15 dark:border-[#80142F]/30 font-medium backdrop-blur-md">
                                <Sparkles class="w-3 h-3 text-amber-500" />
                                <span>Fresh Daily Bakes</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/80 dark:bg-[#1E040C]/80 border border-[#80142F]/15 dark:border-[#80142F]/30 font-medium backdrop-blur-md">
                                <MessageCircle class="w-3 h-3 text-emerald-500" />
                                <span>WhatsApp Order</span>
                            </div>
                        </div>

                        <!-- Hero image card -->
                        <div class="relative group rounded-2xl overflow-hidden bg-white/90 dark:bg-[#1E040C] border border-[#80142F]/15 dark:border-[#80142F]/35">
                            <div class="aspect-[16/9] overflow-hidden bg-zinc-950">
                                <img
                                    src="/images/confectionery_hero.png"
                                    alt="Biba Treats Botanical Refreshers & Drinks"
                                    class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out"
                                />
                            </div>
                            <div class="absolute bottom-2.5 left-2.5 right-2.5 z-10 flex items-center justify-between pointer-events-none">
                                <div class="px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[10px] font-medium border border-white/10 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Signature Botanical Refreshers</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-[#80142F] text-white text-[9px] font-semibold border border-white/20">Biba Treats</span>
                            </div>
                        </div>

                        <!-- CTA -->
                        <Link
                            :href="route('confectionery.index')"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-100 font-medium text-xs transition-all duration-200 active:scale-95 shadow-sm"
                        >
                            <span>Explore Treats</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>

            </div>
        </section>
                <!-- FEATURED CLOTHING SECTION -->
        <section v-if="featuredClothing.length > 0" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between mb-8 gap-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 block mb-1">
                        Biba Clothings
                    </span>
                    <h2 class="font-semibold text-2xl sm:text-3xl tracking-tight text-zinc-900 dark:text-white">
                        Featured Fashion & Apparel
                    </h2>
                </div>
                <Link 
                    :href="route('clothing.index')" 
                    class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline"
                >
                    <span>View collection</span>
                    <ArrowRight class="w-3.5 h-3.5" />
                </Link>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard 
                    v-for="product in featuredClothing" 
                    :key="product.id" 
                    :product="product" 
                />
            </div>
        </section>

        <!-- FEATURED CONFECTIONERY SECTION -->
        <section v-if="featuredConfectionery.length > 0" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between mb-8 gap-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 block mb-1">
                        Biba Treats
                    </span>
                    <h2 class="font-semibold text-2xl sm:text-3xl tracking-tight text-zinc-900 dark:text-white">
                        Artisanal Pastries & Delicacies
                    </h2>
                </div>
                <Link 
                    :href="route('confectionery.index')" 
                    class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline"
                >
                    <span>View collection</span>
                    <ArrowRight class="w-3.5 h-3.5" />
                </Link>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard 
                    v-for="product in featuredConfectionery" 
                    :key="product.id" 
                    :product="product" 
                />
            </div>
        </section>

        <!-- VALUE PROPOSITIONS (Apple Clean Minimalist Bar) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 border-t border-b border-black/[0.06] dark:border-white/[0.08] my-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="flex items-start gap-3.5">
                    <div class="p-2.5 rounded-full bg-black/5 dark:bg-white/10 text-zinc-900 dark:text-zinc-100 shrink-0">
                        <Sparkles class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100 tracking-tight">Curated Quality</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">Bespoke apparel cuts and freshly prepared confectionery using select ingredients.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3.5">
                    <div class="p-2.5 rounded-full bg-black/5 dark:bg-white/10 text-zinc-900 dark:text-zinc-100 shrink-0">
                        <MessageCircle class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100 tracking-tight">WhatsApp Checkout</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">Instant, direct checkout on WhatsApp with human confirmation of every detail.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3.5">
                    <div class="p-2.5 rounded-full bg-black/5 dark:bg-white/10 text-zinc-900 dark:text-zinc-100 shrink-0">
                        <Truck class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100 tracking-tight">Reliable Delivery</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">Nationwide clothing shipping and priority fresh delivery for treats.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3.5">
                    <div class="p-2.5 rounded-full bg-black/5 dark:bg-white/10 text-zinc-900 dark:text-zinc-100 shrink-0">
                        <ShieldCheck class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100 tracking-tight">Personalized Touch</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">Custom cake messages, tailored fit guidance, and responsive assistance.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ABOUT BRIEF SECTION (Clean Apple Editorial) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="rounded-3xl bg-[#F5F5F7] dark:bg-[#161617] border border-black/[0.04] dark:border-white/[0.08] p-8 sm:p-12 lg:p-16 flex flex-col lg:flex-row items-center gap-10">
                <div class="lg:w-1/2 space-y-4">
                    <span class="text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                        Philosophy
                    </span>
                    <h2 class="font-semibold text-3xl sm:text-4xl text-zinc-900 dark:text-white tracking-tight leading-tight">
                        Two Passions, One Heartfelt Vision.
                    </h2>
                    <p class="text-sm sm:text-base text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        {{ aboutStory || 'Biba of Good Things was born from a desire to bring joy into daily living. Whether you are dressing for life’s memorable milestones with Biba Clothings or savouring a box of artisanal cupcakes with friends from Biba Treats, every offering is created with love, taste, and precision.' }}
                    </p>
                    <div class="pt-2">
                        <Link 
                            :href="route('about')" 
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline"
                        >
                            <span>Read our story</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>

                <div class="lg:w-1/2 grid grid-cols-2 gap-4 w-full">
                    <div class="rounded-2xl bg-white dark:bg-[#1D1D1F] p-6 flex flex-col justify-center items-center text-center border border-black/[0.04] dark:border-white/[0.06] shadow-2xs">
                        <Shirt class="w-8 h-8 text-zinc-800 dark:text-zinc-200 mb-3" />
                        <h4 class="font-semibold text-sm text-zinc-900 dark:text-white">Biba Clothings</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Breathable fabrics and effortless contemporary tailoring.</p>
                    </div>
                    <div class="rounded-2xl bg-white dark:bg-[#1D1D1F] p-6 flex flex-col justify-center items-center text-center border border-black/[0.04] dark:border-white/[0.06] shadow-2xs">
                        <Cake class="w-8 h-8 text-zinc-800 dark:text-zinc-200 mb-3" />
                        <h4 class="font-semibold text-sm text-zinc-900 dark:text-white">Biba Treats</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Fresh daily bakes made with pure butter and premium botanicals.</p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
