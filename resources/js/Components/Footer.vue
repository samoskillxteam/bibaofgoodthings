<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import { useTheme } from '@/composables/useTheme';
import { MessageCircle, MapPin, Mail, Phone, Heart } from 'lucide-vue-next';

const page = usePage<PageProps>();
const { isDark } = useTheme();
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
    <footer class="bg-[#F5F5F7] dark:bg-[#111112] text-zinc-600 dark:text-zinc-400 border-t border-black/[0.06] dark:border-white/[0.08] transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">
                <!-- Col 1: Brand Info -->
                <div class="lg:col-span-2 space-y-3.5">
                    <div class="flex items-center gap-2.5">
                        <img 
                            :src="logoSrc" 
                            alt="Biba of Good Things" 
                            class="h-9 w-9 object-contain rounded-full"
                        />
                        <div class="flex items-baseline gap-1.5">
                            <span class="font-semibold text-sm tracking-tight text-zinc-900 dark:text-white">
                                Biba
                            </span>
                            <span class="text-[10px] tracking-widest uppercase text-zinc-400 dark:text-zinc-500 font-medium">
                                Of Good Things
                            </span>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 max-w-sm leading-relaxed">
                        Curated apparel from <span class="text-zinc-900 dark:text-white font-medium">Biba Clothings</span> and artisanal delicacies from <span class="text-zinc-900 dark:text-white font-medium">Biba Treats</span>. Human-guided checkout directly on WhatsApp.
                    </p>

                    <div class="pt-1">
                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-full bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-all active:scale-95"
                        >
                            <MessageCircle class="w-3.5 h-3.5" />
                            <span>Chat on WhatsApp</span>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Biba Clothings -->
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-zinc-900 dark:text-white">
                        Biba Clothings
                    </h4>
                    <ul class="space-y-2 text-xs text-zinc-500 dark:text-zinc-400">
                        <li>
                            <Link :href="route('clothing.index')" class="hover:text-black dark:hover:text-white transition-colors">All Apparel</Link>
                        </li>
                        <li>
                            <Link :href="route('clothing.index', { search: 'co-ord' })" class="hover:text-black dark:hover:text-white transition-colors">Linen Co-ords</Link>
                        </li>
                        <li>
                            <Link :href="route('clothing.index', { search: 'dress' })" class="hover:text-black dark:hover:text-white transition-colors">Silk & Wrap Dresses</Link>
                        </li>
                        <li>
                            <Link :href="route('clothing.index', { search: 'blazer' })" class="hover:text-black dark:hover:text-white transition-colors">Tailored Blazers</Link>
                        </li>
                        <li>
                            <Link :href="route('clothing.index', { search: 'tee' })" class="hover:text-black dark:hover:text-white transition-colors">Luxury Heavyweight Tees</Link>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Biba Treats -->
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-zinc-900 dark:text-white">
                        Biba Treats
                    </h4>
                    <ul class="space-y-2 text-xs text-zinc-500 dark:text-zinc-400">
                        <li>
                            <Link :href="route('confectionery.index')" class="hover:text-black dark:hover:text-white transition-colors">All Delicacies</Link>
                        </li>
                        <li>
                            <Link :href="route('confectionery.index', { type: 'cupcake' })" class="hover:text-black dark:hover:text-white transition-colors">Gourmet Cupcakes</Link>
                        </li>
                        <li>
                            <Link :href="route('confectionery.index', { type: 'cake' })" class="hover:text-black dark:hover:text-white transition-colors">Celebration Bento Cakes</Link>
                        </li>
                        <li>
                            <Link :href="route('confectionery.index', { type: 'snack' })" class="hover:text-black dark:hover:text-white transition-colors">Chocolate Brownies</Link>
                        </li>
                        <li>
                            <Link :href="route('confectionery.index', { type: 'drink' })" class="hover:text-black dark:hover:text-white transition-colors">Botanical Drinks</Link>
                        </li>
                    </ul>
                </div>

                <!-- Col 4: Quick Links & Contact -->
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-zinc-900 dark:text-white">
                        Information
                    </h4>
                    <div class="space-y-2 text-xs text-zinc-500 dark:text-zinc-400">
                        <div class="flex items-start gap-2">
                            <MapPin class="w-3.5 h-3.5 shrink-0 mt-0.5 text-zinc-400" />
                            <span>{{ settings?.address || 'Lagos, Nigeria' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Phone class="w-3.5 h-3.5 shrink-0 text-zinc-400" />
                            <span>{{ settings?.contact_phone || '+234 801 234 5678' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Mail class="w-3.5 h-3.5 shrink-0 text-zinc-400" />
                            <span>{{ settings?.contact_email || 'hello@biba.com' }}</span>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col space-y-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                        <Link :href="route('privacy')" class="hover:text-black dark:hover:text-white">Privacy Policy</Link>
                        <Link :href="route('terms')" class="hover:text-black dark:hover:text-white">Terms of Service</Link>
                        <Link :href="route('admin.login')" class="hover:text-black dark:hover:text-white pt-1">Admin Portal</Link>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="mt-12 pt-6 border-t border-black/[0.06] dark:border-white/[0.08] flex flex-col sm:flex-row items-center justify-between text-[11px] text-zinc-400 dark:text-zinc-500 gap-3">
                <p>© {{ new Date().getFullYear() }} {{ settings?.business_name || 'Biba of Good Things' }}. All rights reserved.</p>
                <p>
                    Curated fashion & artisanal sweet moments.
                </p>
            </div>
        </div>
    </footer>
</template>
