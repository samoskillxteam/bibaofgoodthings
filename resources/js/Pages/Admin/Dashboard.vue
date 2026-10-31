<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { OrderEnquiry, Product } from '@/types';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    Package, 
    Shirt, 
    Cake, 
    CheckCircle, 
    AlertTriangle, 
    MessageSquare, 
    Plus, 
    ArrowRight, 
    Settings,
    Eye,
    TrendingUp
} from 'lucide-vue-next';

interface Props {
    stats: {
        total_products: number;
        clothing_count: number;
        confectionery_count: number;
        published_count: number;
        unavailable_count: number;
        low_stock_count: number;
        total_enquiries: number;
        pending_enquiries: number;
    };
    recentEnquiries: OrderEnquiry[];
    recentProducts: Product[];
}

const props = defineProps<Props>();

const formatPrice = (val: number | string) => {
    const num = Number(val);
    return isNaN(num) ? '₦0' : '₦' + num.toLocaleString('en-NG', { maximumFractionDigits: 0 });
};
</script>

<template>
    <AdminLayout title="Operational Dashboard">
        <div class="space-y-8">
            <!-- Header with Quick Action -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-zinc-900 dark:text-zinc-100">
                        Operational Dashboard
                    </h1>
                    <p class="text-xs text-zinc-500 mt-1">
                        Live catalogue overview and customer WhatsApp order enquiry activity.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.products.create')"
                        class="px-4 py-2.5 rounded-xl bg-[#2D241E] hover:bg-[#43362C] dark:bg-[#C59A65] dark:hover:bg-[#B3804A] text-white dark:text-[#0F1115] font-semibold text-xs shadow flex items-center gap-1.5 transition-all"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Add New Product</span>
                    </Link>

                    <Link
                        :href="route('admin.settings.index')"
                        class="p-2.5 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors shadow-sm"
                        title="Store Settings"
                    >
                        <Settings class="w-4 h-4" />
                    </Link>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Total Products -->
                <div class="p-5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-zinc-500 uppercase tracking-wider font-semibold">Total Products</span>
                        <div class="text-2xl sm:text-3xl font-display font-bold text-zinc-900 dark:text-zinc-100 mt-1">
                            {{ stats.total_products }}
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <Package class="w-5 h-5" />
                    </div>
                </div>

                <!-- Biba Clothings Count -->
                <div class="p-5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-zinc-500 uppercase tracking-wider font-semibold">Biba Clothings</span>
                        <div class="text-2xl sm:text-3xl font-display font-bold text-[#A66E38] dark:text-[#C59A65] mt-1">
                            {{ stats.clothing_count }}
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-[#EFE8DD] dark:bg-zinc-800 text-[#4A3525] dark:text-[#C59A65]">
                        <Shirt class="w-5 h-5" />
                    </div>
                </div>

                <!-- Biba Treats Count -->
                <div class="p-5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-zinc-500 uppercase tracking-wider font-semibold">Biba Treats</span>
                        <div class="text-2xl sm:text-3xl font-display font-bold text-rose-600 dark:text-rose-400 mt-1">
                            {{ stats.confectionery_count }}
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400">
                        <Cake class="w-5 h-5" />
                    </div>
                </div>

                <!-- Order Enquiries -->
                <div class="p-5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-zinc-500 uppercase tracking-wider font-semibold">Order Enquiries</span>
                        <div class="text-2xl sm:text-3xl font-display font-bold text-emerald-600 mt-1">
                            {{ stats.total_enquiries }}
                        </div>
                        <span class="text-[10px] text-emerald-600 font-semibold">{{ stats.pending_enquiries }} pending</span>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600">
                        <MessageSquare class="w-5 h-5" />
                    </div>
                </div>
            </div>

            <!-- Two-Column Operational Table Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Recent WhatsApp Enquiries (7 cols) -->
                <div class="lg:col-span-7 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div>
                            <h2 class="font-serif font-bold text-lg text-zinc-900 dark:text-zinc-100">
                                Recent Order Enquiries
                            </h2>
                            <p class="text-xs text-zinc-500">Incoming WhatsApp order enquiries initiated by customers.</p>
                        </div>
                        <Link 
                            :href="route('admin.orders.index')" 
                            class="text-xs font-semibold text-[#A66E38] dark:text-[#C59A65] hover:underline"
                        >
                            View all →
                        </Link>
                    </div>

                    <div v-if="recentEnquiries.length > 0" class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                        <div 
                            v-for="enquiry in recentEnquiries" 
                            :key="enquiry.id" 
                            class="py-3.5 flex items-center justify-between gap-4"
                        >
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ enquiry.reference }}</span>
                                    <span 
                                        class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full"
                                        :class="{
                                            'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300': enquiry.status === 'pending',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300': enquiry.status === 'contacted',
                                            'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300': enquiry.status === 'completed',
                                            'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400': enquiry.status === 'cancelled',
                                        }"
                                    >
                                        {{ enquiry.status }}
                                    </span>
                                </div>
                                <p class="text-xs text-zinc-500">
                                    {{ enquiry.customer_name || 'Guest Customer' }} • {{ Array.isArray(enquiry.items) ? enquiry.items.length : 0 }} items
                                </p>
                            </div>

                            <div class="flex items-center gap-4">
                                <span class="font-display font-bold text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ formatPrice(enquiry.total) }}
                                </span>
                                <Link 
                                    :href="route('admin.orders.show', enquiry.id)"
                                    class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                >
                                    <Eye class="w-4 h-4" />
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-10 text-xs text-zinc-400">
                        No order enquiries recorded yet.
                    </div>
                </div>

                <!-- Recent Products (5 cols) -->
                <div class="lg:col-span-5 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div>
                            <h2 class="font-serif font-bold text-lg text-zinc-900 dark:text-zinc-100">
                                Catalogue Products
                            </h2>
                            <p class="text-xs text-zinc-500">Recently updated items in catalogue.</p>
                        </div>
                        <Link 
                            :href="route('admin.products.index')" 
                            class="text-xs font-semibold text-[#A66E38] dark:text-[#C59A65] hover:underline"
                        >
                            All products →
                        </Link>
                    </div>

                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                        <div 
                            v-for="p in recentProducts" 
                            :key="p.id" 
                            class="py-3 flex items-center justify-between gap-3"
                        >
                            <div class="flex items-center gap-3 truncate">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-800 shrink-0 border border-zinc-200 dark:border-zinc-700">
                                    <img v-if="p.primary_image_url" :src="p.primary_image_url" class="w-full h-full object-cover" />
                                </div>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ p.name }}</p>
                                    <span class="text-[10px] text-zinc-400">{{ p.category?.brand_name }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <span class="font-display font-bold text-xs text-zinc-800 dark:text-zinc-200">
                                    {{ formatPrice(p.effective_price) }}
                                </span>
                                <Link 
                                    :href="route('admin.products.edit', p.id)"
                                    class="text-xs text-[#A66E38] dark:text-[#C59A65] hover:underline"
                                >
                                    Edit
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
