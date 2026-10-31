<script setup lang="ts">
import { ref } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import type { CartData } from '@/types';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
    ShoppingBag, 
    Trash2, 
    Plus, 
    Minus, 
    MessageCircle, 
    ArrowRight, 
    Shirt, 
    Cake, 
    AlertTriangle,
    ShieldCheck,
    CheckCircle2,
    Truck
} from 'lucide-vue-next';

interface Props {
    cart: CartData;
    whatsappNumber: string;
    deliveryInfo?: string;
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    phone: '',
    address: '',
    notes: '',
    clear_cart: true,
});

const isSubmitting = ref(false);

const updateQuantity = (itemKey: string, newQty: number) => {
    router.patch(
        route('cart.update', itemKey),
        { quantity: newQty },
        { preserveScroll: true }
    );
};

const removeItem = (itemKey: string) => {
    router.delete(route('cart.destroy', itemKey), { preserveScroll: true });
};

const clearCart = () => {
    if (confirm('Are you sure you want to clear your order bag?')) {
        router.post(route('cart.clear'));
    }
};

const submitWhatsAppCheckout = () => {
    isSubmitting.value = true;
    form.post(route('checkout.whatsapp'), {
        preserveScroll: true,
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};
</script>

<template>
    <AppLayout 
        title="Your Order Bag — WhatsApp Checkout"
        description="Review your selected items from Biba Clothings and Biba Treats and place your order directly via WhatsApp."
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-8 border-b border-[#EADFCF] dark:border-zinc-800 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-[#A66E38] dark:text-[#C59A65]">
                        Combined Order Bag
                    </span>
                    <h1 class="font-display font-extrabold text-3xl sm:text-4xl text-zinc-900 dark:text-zinc-100 mt-1">
                        Review Your Order
                    </h1>
                </div>

                <div v-if="!cart.is_empty" class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="clearCart"
                        class="text-xs text-zinc-500 hover:text-rose-600 transition-colors flex items-center gap-1"
                    >
                        <Trash2 class="w-3.5 h-3.5" />
                        <span>Clear Cart</span>
                    </button>

                    <Link
                        :href="route('home')"
                        class="text-xs font-semibold text-[#A66E38] dark:text-[#C59A65] hover:underline"
                    >
                        Continue Shopping →
                    </Link>
                </div>
            </div>

            <!-- Warnings / Price update notices -->
            <div v-if="cart.warnings && cart.warnings.length > 0" class="mt-6 space-y-2">
                <div 
                    v-for="(w, idx) in cart.warnings" 
                    :key="idx" 
                    class="p-4 rounded-xl bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-200 border border-amber-200 dark:border-amber-800 text-xs font-medium flex items-center gap-2"
                >
                    <AlertTriangle class="w-4 h-4 shrink-0 text-amber-600" />
                    <span>{{ w }}</span>
                </div>
            </div>

            <!-- Empty Cart State -->
            <div v-if="cart.is_empty" class="text-center py-24 bg-white dark:bg-zinc-900 rounded-3xl border border-dashed border-[#EADFCF] dark:border-zinc-800 mt-8 p-8 max-w-2xl mx-auto">
                <div class="w-16 h-16 rounded-full bg-[#EFE8DD] dark:bg-zinc-800 text-[#4A3525] dark:text-[#C59A65] flex items-center justify-center mx-auto mb-4">
                    <ShoppingBag class="w-8 h-8" />
                </div>
                <h2 class="font-serif font-bold text-2xl text-zinc-900 dark:text-zinc-100">Your order bag is empty</h2>
                <p class="text-sm text-zinc-500 max-w-md mx-auto mt-2 leading-relaxed">
                    You haven’t added any items yet. Discover graceful fashion from 
                    <strong>Biba Clothings</strong> or freshly baked artisanal sweets from 
                    <strong>Biba Treats</strong>.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link
                        :href="route('clothing.index')"
                        class="w-full sm:w-auto px-6 py-3 rounded-xl bg-[#2D241E] hover:bg-[#43362C] dark:bg-[#C59A65] dark:hover:bg-[#B3804A] text-white dark:text-[#0F1115] font-semibold text-xs shadow flex items-center justify-center gap-2"
                    >
                        <Shirt class="w-4 h-4" />
                        <span>Explore Biba Clothings</span>
                    </Link>

                    <Link
                        :href="route('confectionery.index')"
                        class="w-full sm:w-auto px-6 py-3 rounded-xl bg-[#5C1D24] hover:bg-[#732730] text-white font-semibold text-xs shadow flex items-center justify-center gap-2"
                    >
                        <Cake class="w-4 h-4" />
                        <span>Explore Biba Treats</span>
                    </Link>
                </div>
            </div>

            <!-- Active Cart with Items -->
            <div v-else class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Cart Items List (Col 7) -->
                <div class="lg:col-span-7 space-y-4">
                    <div 
                        v-for="item in cart.items" 
                        :key="item.key"
                        class="p-5 rounded-2xl bg-white dark:bg-zinc-900 border border-[#EFE8DD] dark:border-zinc-800 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 transition-all"
                    >
                        <!-- Image and Info -->
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-24 rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 shrink-0 border border-zinc-200 dark:border-zinc-700">
                                <img 
                                    v-if="item.image_url" 
                                    :src="item.image_url" 
                                    :alt="item.product_name"
                                    class="w-full h-full object-cover" 
                                />
                                <div v-else class="w-full h-full flex items-center justify-center text-[9px] text-zinc-400">
                                    No Image
                                </div>
                            </div>

                            <div class="space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#A66E38] dark:text-[#C59A65]">
                                    {{ item.brand_name }}
                                </span>
                                <h3 class="font-serif font-bold text-base text-zinc-900 dark:text-zinc-100">
                                    {{ item.product_name }}
                                </h3>

                                <div v-if="item.variant_title" class="text-xs text-zinc-500 dark:text-zinc-400">
                                    <span>Selected: <strong>{{ item.variant_title }}</strong></span>
                                </div>

                                <div v-if="item.addons && item.addons.length > 0" class="text-[11px] text-zinc-500">
                                    <span>Add-ons: {{ item.addons.map(a => a.name).join(', ') }}</span>
                                </div>

                                <div v-if="item.notes" class="text-[11px] text-zinc-400 italic">
                                    <span>Note: {{ item.notes }}</span>
                                </div>

                                <div class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 pt-1 sm:hidden">
                                    {{ item.formatted_unit_price }} × {{ item.quantity }} = <strong>{{ item.formatted_line_total }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Modifiers & Line Total -->
                        <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-6 pt-3 sm:pt-0 border-t sm:border-t-0 border-zinc-100 dark:border-zinc-800">
                            <!-- Qty buttons -->
                            <div class="flex items-center border border-[#E0D5C3] dark:border-zinc-700 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 p-1">
                                <button
                                    type="button"
                                    @click="updateQuantity(item.key, item.quantity - 1)"
                                    class="p-1 rounded text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200"
                                    aria-label="Decrease quantity"
                                >
                                    <Minus class="w-3.5 h-3.5" />
                                </button>
                                <span class="px-3 text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ item.quantity }}</span>
                                <button
                                    type="button"
                                    @click="updateQuantity(item.key, item.quantity + 1)"
                                    class="p-1 rounded text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200"
                                    aria-label="Increase quantity"
                                >
                                    <Plus class="w-3.5 h-3.5" />
                                </button>
                            </div>

                            <!-- Line total -->
                            <div class="text-right min-w-[90px] hidden sm:block">
                                <span class="text-xs text-zinc-400 block">{{ item.formatted_unit_price }} each</span>
                                <span class="font-display font-bold text-base text-zinc-900 dark:text-zinc-100">{{ item.formatted_line_total }}</span>
                            </div>

                            <!-- Remove button -->
                            <button
                                type="button"
                                @click="removeItem(item.key)"
                                class="p-2 text-zinc-400 hover:text-rose-600 transition-colors"
                                title="Remove item"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Checkout Summary & Customer Details (Col 5) -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-[#EFE8DD] dark:border-zinc-800 shadow-md space-y-6">
                        <h2 class="font-serif font-bold text-xl text-zinc-900 dark:text-zinc-100 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                            Order Summary & WhatsApp Checkout
                        </h2>

                        <!-- Price Breakdown -->
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                                <span>Items Subtotal ({{ cart.total_quantity }} items)</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ cart.formatted_subtotal }}</span>
                            </div>

                            <div class="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                                <span>Delivery Fee</span>
                                <span class="text-xs italic text-[#A66E38] dark:text-[#C59A65]">Calculated on WhatsApp</span>
                            </div>

                            <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex items-baseline justify-between">
                                <span class="font-serif font-bold text-base text-zinc-900 dark:text-zinc-100">Estimated Order Total</span>
                                <span class="font-display font-extrabold text-2xl sm:text-3xl text-[#2D241E] dark:text-[#F3EDE2]">
                                    {{ cart.formatted_subtotal }}
                                </span>
                            </div>

                            <!-- Disclaimer Note -->
                            <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 text-[11px] text-zinc-500 leading-relaxed">
                                ℹ️ <em>WhatsApp checkout creates an instant order enquiry. Final item confirmation, delivery options, and payment arrangements will be finalized directly with Biba on WhatsApp.</em>
                            </div>
                        </div>

                        <!-- Customer Details Form -->
                        <form @submit.prevent="submitWhatsAppCheckout" class="space-y-4 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                            <span class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 block">
                                Your Details (Included in Message)
                            </span>

                            <div class="space-y-1">
                                <label class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Full Name</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="e.g. Zainab Ibrahim"
                                    class="w-full text-xs p-3 rounded-xl border border-[#E0D5C3] dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Phone Number</label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    placeholder="e.g. 08012345678"
                                    class="w-full text-xs p-3 rounded-xl border border-[#E0D5C3] dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Delivery Address / City</label>
                                <textarea
                                    v-model="form.address"
                                    rows="2"
                                    placeholder="e.g. 15 Admiralty Way, Lekki Phase 1, Lagos"
                                    class="w-full text-xs p-3 rounded-xl border border-[#E0D5C3] dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                ></textarea>
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Order Notes / Custom Requests</label>
                                <input
                                    v-model="form.notes"
                                    type="text"
                                    placeholder="e.g. Please deliver before Friday 2pm or gift wrap with card"
                                    class="w-full text-xs p-3 rounded-xl border border-[#E0D5C3] dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>

                            <!-- WhatsApp Checkout Button -->
                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="w-full py-4 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 mt-4 group"
                            >
                                <MessageCircle class="w-5 h-5 text-white" />
                                <span>Complete Order on WhatsApp</span>
                                <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                            </button>
                        </form>

                        <div class="pt-2 text-center text-xs text-zinc-400 flex items-center justify-center gap-1.5">
                            <ShieldCheck class="w-4 h-4 text-emerald-600" />
                            <span>100% Secure WhatsApp Ordering • No card needed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
