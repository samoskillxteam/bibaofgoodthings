<script setup lang="ts">
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Shirt, Cake, Sparkles, Heart, CheckCircle2, MessageCircle } from 'lucide-vue-next';

interface Props {
    story?: string;
    clothingBrand: string;
    treatsBrand: string;
}

const props = defineProps<Props>();
const page = usePage<PageProps>();
const settings = computed(() => page.props.settings);

const whatsappUrl = computed(() => {
    const raw = settings.value?.whatsapp_number || '2348012345678';
    const clean = raw.replace(/[^\d]/g, '');
    return `https://wa.me/${clean}?text=${encodeURIComponent('Hello Biba! I would like to learn more about your products.')}`;
});
</script>

<template>
    <AppLayout 
        title="About Biba of Good Things"
        description="The story and philosophy behind Biba Clothings and Biba Treats."
    >
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <span class="text-xs font-bold uppercase tracking-widest text-[#A66E38] dark:text-[#C59A65]">
                    Our Story & Craft
                </span>
                <h1 class="font-display font-extrabold text-4xl sm:text-5xl text-zinc-900 dark:text-zinc-100">
                    A Union of Style and Sweet Indulgence
                </h1>
                <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-300 leading-relaxed font-light">
                    Founded on the belief that everyday life deserves beauty, sophistication, and sweet joy.
                </p>
            </div>

            <!-- Main Narrative -->
            <div class="mt-16 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6 text-zinc-700 dark:text-zinc-300 leading-relaxed text-sm sm:text-base">
                    <p>
                        {{ story || 'Biba of Good Things is an integrated lifestyle brand bringing together two distinct creative pursuits: exceptional contemporary fashion and artisanal, fresh-baked confectioneries.' }}
                    </p>
                    <p>
                        We envisioned a single home where our community can effortlessly explore tailored apparel that inspires inner confidence, alongside handcrafted delicacies made to celebrate life's sweetest moments.
                    </p>
                    <p>
                        To ensure every customer receives personal attention, custom sizing guidance, and tailored delivery schedules, we deliberately conduct all checkouts directly through WhatsApp. No cold automated checkout lines—just warm, direct, attentive human care.
                    </p>

                    <div class="pt-4 flex items-center gap-4">
                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow flex items-center gap-2"
                        >
                            <MessageCircle class="w-4 h-4" />
                            <span>Say Hello on WhatsApp</span>
                        </a>
                    </div>
                </div>

                <!-- Two Pillars -->
                <div class="space-y-6">
                    <!-- Pillar 1: Biba Clothings -->
                    <div class="p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-[#EADFCF] dark:border-zinc-800 shadow-sm flex items-start gap-5">
                        <div class="p-3.5 rounded-2xl bg-[#EFE8DD] dark:bg-zinc-800 text-[#4A3525] dark:text-[#C59A65] shrink-0">
                            <Shirt class="w-7 h-7" />
                        </div>
                        <div class="space-y-2">
                            <h3 class="font-serif font-bold text-xl text-zinc-900 dark:text-zinc-100">{{ clothingBrand }}</h3>
                            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Pure woven linens, fluttery silk wrap silhouettes, structured suiting, and dense luxury combed cotton. Crafted with precision for enduring wear.
                            </p>
                            <Link :href="route('clothing.index')" class="inline-block text-xs font-bold text-[#A66E38] dark:text-[#C59A65] hover:underline pt-1">
                                View Clothing Catalogue →
                            </Link>
                        </div>
                    </div>

                    <!-- Pillar 2: Biba Treats -->
                    <div class="p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-[#EADFCF] dark:border-zinc-800 shadow-sm flex items-start gap-5">
                        <div class="p-3.5 rounded-2xl bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 shrink-0">
                            <Cake class="w-7 h-7" />
                        </div>
                        <div class="space-y-2">
                            <h3 class="font-serif font-bold text-xl text-zinc-900 dark:text-zinc-100">{{ treatsBrand }}</h3>
                            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Fresh daily bakes, Korean-style celebration bento cakes, gourmet fudge brownies, and chilled natural hibiscus refreshers crafted with natural extracts.
                            </p>
                            <Link :href="route('confectionery.index')" class="inline-block text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline pt-1">
                                View Treats Catalogue →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
