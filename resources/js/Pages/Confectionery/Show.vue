<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import type { Product, PageProps } from '@/types';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductGallery from '@/Components/ProductGallery.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { 
    ShoppingBag, 
    MessageCircle, 
    Check, 
    Clock, 
    Cake, 
    Sparkles, 
    ChevronRight,
    Plus,
    Minus,
    Heart
} from 'lucide-vue-next';

interface Props {
    product: Product;
    relatedProducts: Product[];
}

const props = defineProps<Props>();
const page = usePage<PageProps>();

// Selected Options State
const selectedOptions = ref<Record<string, string>>({});
const selectedAddonIds = ref<number[]>([]);
const quantity = ref(1);
const customerNotes = ref('');
const isSubmitting = ref(false);

// Initialize default selected options
if (props.product.options && props.product.options.length > 0) {
    props.product.options.forEach(opt => {
        if (opt.values && opt.values.length > 0) {
            selectedOptions.value[opt.name] = opt.values[0].name;
        }
    });
}

// Find matched variant
const matchedVariant = computed(() => {
    if (!props.product.variants || props.product.variants.length === 0) return null;
    const selectedVals = Object.values(selectedOptions.value).map(v => String(v).toLowerCase().trim());

    // 1. First try matching by option_values relationship if present
    const byOptionValues = props.product.variants.find(v => {
        if (!v.option_values || v.option_values.length === 0) return false;
        const optValNames = v.option_values.map(ov => ov.name.toLowerCase().trim());
        return selectedVals.some(sv => optValNames.some(ovn => ovn.includes(sv) || sv.includes(ovn)));
    });
    if (byOptionValues) return byOptionValues;

    // 2. Try exact or partial title matching
    const exact = props.product.variants.find(v => {
        const titleLower = (v.title || '').toLowerCase();
        return selectedVals.every(val => {
            if (titleLower.includes(val)) return true;
            const words = val.split(/\s+/).filter(w => w.length > 2);
            return words.some(w => titleLower.includes(w));
        });
    });

    return exact || props.product.variants[0];
});

// Calculate Active Display Price
const activeBasePrice = computed(() => {
    if (matchedVariant.value) {
        const p = matchedVariant.value.effective_price ?? matchedVariant.value.price;
        const num = Number(p);
        if (!isNaN(num) && num > 0) return num;
    }
    const fallback = props.product.effective_price ?? props.product.base_price;
    const numFallback = Number(fallback);
    return isNaN(numFallback) ? 0 : numFallback;
});

const activeAddonsPrice = computed(() => {
    if (!props.product.addons) return 0;
    return props.product.addons
        .filter(a => selectedAddonIds.value.includes(a.id))
        .reduce((sum, a) => sum + (Number(a.price) || 0), 0);
});

const unitPrice = computed(() => {
    const base = Number(activeBasePrice.value) || 0;
    const addons = Number(activeAddonsPrice.value) || 0;
    return base + addons;
});

const totalPrice = computed(() => {
    const q = Number(quantity.value) || 1;
    return unitPrice.value * q;
});

const formatPrice = (val: number | string | undefined | null) => {
    const num = Number(val);
    if (isNaN(num)) return '₦0';
    return '₦' + num.toLocaleString('en-NG', { maximumFractionDigits: 0 });
};

const selectOption = (optionName: string, valueName: string) => {
    selectedOptions.value[optionName] = valueName;
};

const toggleAddon = (addonId: number) => {
    const idx = selectedAddonIds.value.indexOf(addonId);
    if (idx > -1) {
        selectedAddonIds.value.splice(idx, 1);
    } else {
        selectedAddonIds.value.push(addonId);
    }
};

const incrementQty = () => {
    quantity.value++;
};

const decrementQty = () => {
    if (quantity.value > 1) quantity.value--;
};

// Add to Cart Action
const addToCart = (redirectCart = false) => {
    isSubmitting.value = true;
    router.post(
        route('cart.store'),
        {
            product_id: props.product.id,
            quantity: quantity.value,
            variant_id: matchedVariant.value ? matchedVariant.value.id : null,
            addon_ids: selectedAddonIds.value,
            notes: customerNotes.value || null,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                isSubmitting.value = false;
                if (redirectCart) {
                    router.visit(route('cart.index'));
                }
            },
        }
    );
};
</script>

<template>
    <AppLayout 
        :title="`${product.name} — Biba Treats`"
        :description="product.short_description || product.name"
    >
        <!-- Breadcrumb -->
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                <Link :href="route('home')" class="hover:text-zinc-800 dark:hover:text-zinc-200">Home</Link>
                <ChevronRight class="w-3.5 h-3.5" />
                <Link :href="route('confectionery.index')" class="hover:text-zinc-800 dark:hover:text-zinc-200">Biba Treats</Link>
                <ChevronRight class="w-3.5 h-3.5" />
                <span class="text-zinc-800 dark:text-zinc-200 font-medium truncate max-w-xs">{{ product.name }}</span>
            </div>
        </nav>

        <!-- Product Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14">
                <!-- Gallery Column (Images + Real Product Videos) -->
                <div class="lg:col-span-7">
                    <ProductGallery 
                        :media="product.media || []" 
                        :productName="product.name" 
                    />
                </div>

                <!-- Product Details Column -->
                <div class="lg:col-span-5 flex flex-col justify-between">
                    <div class="space-y-6">
                        <!-- Category & Status -->
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 text-xs font-bold uppercase tracking-wider">
                                <Cake class="w-3.5 h-3.5" />
                                <span>{{ product.category?.brand_name || 'Biba Treats' }}</span>
                            </span>

                            <span 
                                class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 dark:bg-amber-950/80 dark:text-amber-200 flex items-center gap-1"
                            >
                                <Sparkles class="w-3 h-3 text-amber-600" />
                                <span>Baked Fresh to Order</span>
                            </span>
                        </div>

                        <!-- Product Title -->
                        <div>
                            <h1 class="font-serif font-bold text-2xl sm:text-3xl text-zinc-900 dark:text-zinc-100 leading-tight">
                                {{ product.name }}
                            </h1>
                            <p v-if="product.sku" class="text-xs text-zinc-400 mt-1">
                                Item Ref: {{ matchedVariant?.sku || product.sku }}
                            </p>
                        </div>

                        <!-- Dynamic Price Display -->
                        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-[#EADFCF] dark:border-zinc-800 flex items-baseline justify-between shadow-sm">
                            <div>
                                <span class="text-xs text-zinc-500 uppercase tracking-wider block">Price</span>
                                <div class="flex items-baseline gap-2 mt-0.5">
                                    <span class="font-display font-extrabold text-2xl sm:text-3xl text-[#5C1D24] dark:text-[#F7CAD0]">
                                        {{ formatPrice(unitPrice) }}
                                    </span>
                                    <span 
                                        v-if="product.sale_price && product.sale_price < product.base_price" 
                                        class="text-sm text-zinc-400 line-through"
                                    >
                                        {{ formatPrice(Number(product.base_price)) }}
                                    </span>
                                </div>
                            </div>
                            <span v-if="quantity > 1" class="text-xs text-zinc-500 text-right">
                                Total: <strong class="text-zinc-900 dark:text-zinc-100">{{ formatPrice(totalPrice) }}</strong>
                            </span>
                        </div>

                        <!-- Short Description -->
                        <p v-if="product.short_description" class="text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            {{ product.short_description }}
                        </p>

                        <!-- Configurable Product Options (Pack Size, Flavours, etc.) -->
                        <div v-if="product.options && product.options.length > 0" class="space-y-5 pt-2">
                            <div 
                                v-for="option in product.options" 
                                :key="option.id" 
                                class="space-y-2"
                            >
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                        {{ option.name }}: <span class="font-semibold text-[#5C1D24] dark:text-[#F4A8A8]">{{ selectedOptions[option.name] }}</span>
                                    </span>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="val in option.values"
                                        :key="val.id"
                                        type="button"
                                        @click="selectOption(option.name, val.name)"
                                        class="px-4 py-2 text-xs font-semibold rounded-xl border transition-all focus:outline-none"
                                        :class="selectedOptions[option.name] === val.name 
                                            ? 'bg-[#5C1D24] text-white border-[#5C1D24] dark:bg-rose-900 dark:text-white dark:border-rose-800 shadow-sm' 
                                            : 'bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border-[#E0D5C3] dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                                    >
                                        {{ val.name }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Add-ons (e.g. Custom Fondant Text, Candles, Cake Topper, etc.) -->
                        <div v-if="product.addons && product.addons.length > 0" class="space-y-3 pt-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 block">
                                Toppings, Inscriptions & Extras
                            </span>
                            <div class="space-y-2">
                                <label
                                    v-for="addon in product.addons"
                                    :key="addon.id"
                                    class="flex items-center justify-between p-3 rounded-xl border border-[#E0D5C3] dark:border-zinc-800 bg-white dark:bg-zinc-900 cursor-pointer hover:border-rose-400 transition-colors"
                                >
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="checkbox"
                                            :checked="selectedAddonIds.includes(addon.id)"
                                            @change="toggleAddon(addon.id)"
                                            class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500"
                                        />
                                        <span class="text-xs font-medium text-zinc-800 dark:text-zinc-200">{{ addon.name }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-rose-700 dark:text-rose-400">
                                        +{{ formatPrice(Number(addon.price)) }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Custom Inscription / Celebration Notes -->
                        <div class="space-y-1 pt-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 block">
                                Custom Piping Message / Cake Inscription (Optional)
                            </label>
                            <input
                                v-model="customerNotes"
                                type="text"
                                placeholder="e.g. 'Happy Birthday Chidi!', preferred delivery date or dietary notes"
                                class="w-full text-xs p-3 rounded-xl border border-[#E0D5C3] dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-rose-400"
                            />
                        </div>

                        <!-- Quantity Selector & Action Buttons -->
                        <div class="space-y-3 pt-4 border-t border-[#EADFCF] dark:border-zinc-800">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center border border-[#E0D5C3] dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-900 p-1">
                                    <button
                                        type="button"
                                        @click="decrementQty"
                                        :disabled="quantity <= 1"
                                        class="p-2 rounded-lg text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 disabled:opacity-40"
                                    >
                                        <Minus class="w-4 h-4" />
                                    </button>
                                    <span class="px-4 text-sm font-bold text-zinc-800 dark:text-zinc-200">{{ quantity }}</span>
                                    <button
                                        type="button"
                                        @click="incrementQty"
                                        class="p-2 rounded-lg text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200"
                                    >
                                        <Plus class="w-4 h-4" />
                                    </button>
                                </div>

                                <!-- Add to Bag -->
                                <button
                                    type="button"
                                    @click="addToCart(false)"
                                    :disabled="!product.is_available || isSubmitting"
                                    class="flex-1 py-3.5 px-6 rounded-xl bg-[#5C1D24] hover:bg-[#732730] text-white font-semibold text-sm shadow transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50"
                                >
                                    <ShoppingBag class="w-4 h-4" />
                                    <span>Add to Order Bag</span>
                                </button>
                            </div>

                            <!-- Buy & Checkout via WhatsApp Directly -->
                            <button
                                type="button"
                                @click="addToCart(true)"
                                :disabled="!product.is_available || isSubmitting"
                                class="w-full py-3.5 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                <MessageCircle class="w-4 h-4" />
                                <span>Order Directly on WhatsApp</span>
                            </button>
                        </div>
                    </div>

                    <!-- Trust Signals -->
                    <div class="mt-8 pt-6 border-t border-[#EADFCF] dark:border-zinc-800 grid grid-cols-2 gap-4 text-xs text-zinc-500 dark:text-zinc-400">
                        <div class="flex items-center gap-2">
                            <Clock class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                            <span>Freshly Baked Every Day</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Heart class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                            <span>Premium Pure Ingredients</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Description Tabs / Details -->
            <div class="mt-16 pt-10 border-t border-[#EADFCF] dark:border-zinc-800">
                <div class="max-w-3xl">
                    <h3 class="font-serif font-bold text-xl text-zinc-900 dark:text-zinc-100 mb-4">
                        Ingredients & Flavour Notes
                    </h3>
                    <div class="prose dark:prose-invert text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed whitespace-pre-line">
                        {{ product.full_description || product.short_description }}
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <div v-if="relatedProducts && relatedProducts.length > 0" class="mt-20 pt-10 border-t border-[#EADFCF] dark:border-zinc-800">
                <h3 class="font-display font-bold text-2xl text-zinc-900 dark:text-zinc-100 mb-8">
                    More Sweet Temptations
                </h3>
                <div class="grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <ProductCard 
                        v-for="rel in relatedProducts" 
                        :key="rel.id" 
                        :product="rel" 
                    />
                </div>
            </div>
        </section>

        <!-- Mobile Sticky Bottom Bar -->
        <div class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-[#0F1115]/95 backdrop-blur-md border-t border-[#EADFCF] dark:border-zinc-800 p-3 flex items-center justify-between gap-3 shadow-2xl safe-area-inset-bottom">
            <div>
                <span class="text-[10px] text-zinc-500 uppercase tracking-wider block">Price</span>
                <span class="font-bold text-base text-[#2D241E] dark:text-[#F3EDE2]">{{ formatPrice(totalPrice) }}</span>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="addToCart(false)"
                    :disabled="!product.is_available || isSubmitting"
                    class="py-2.5 px-3.5 rounded-xl bg-[#2D241E] dark:bg-zinc-800 text-white text-xs font-semibold flex items-center gap-1.5 shadow active:scale-95 disabled:opacity-50"
                >
                    <ShoppingBag class="w-4 h-4" />
                    <span>Bag</span>
                </button>
                <button
                    type="button"
                    @click="addToCart(true)"
                    :disabled="!product.is_available || isSubmitting"
                    class="py-2.5 px-3.5 rounded-xl bg-emerald-600 text-white text-xs font-semibold flex items-center gap-1.5 shadow active:scale-95 disabled:opacity-50"
                >
                    <MessageCircle class="w-4 h-4" />
                    <span>WhatsApp</span>
                </button>
            </div>
        </div>
    </AppLayout>
</template>
