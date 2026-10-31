<script setup lang="ts">
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import type { Product, Category } from '@/types';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    Plus, 
    Search, 
    Filter, 
    Edit, 
    Trash2, 
    Eye, 
    Video, 
    Sparkles, 
    Check, 
    X,
    ExternalLink
} from 'lucide-vue-next';

interface Props {
    products: {
        data: Product[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
        total: number;
    };
    categories: Category[];
    filters: {
        search?: string;
        category_id?: string;
        is_available?: string;
        sort?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const categoryId = ref(props.filters.category_id || '');
const isAvailable = ref(props.filters.is_available ?? '');
const sort = ref(props.filters.sort || 'newest');

const applyFilters = () => {
    router.get(
        route('admin.products.index'),
        {
            search: search.value || undefined,
            category_id: categoryId.value || undefined,
            is_available: isAvailable.value !== '' ? isAvailable.value : undefined,
            sort: sort.value,
        },
        { preserveState: true, replace: true }
    );
};

const toggleAvailability = (product: Product) => {
    router.post(route('admin.products.toggle-availability', product.id), {}, { preserveScroll: true });
};

const toggleFeatured = (product: Product) => {
    router.post(route('admin.products.toggle-featured', product.id), {}, { preserveScroll: true });
};

const deleteProduct = (product: Product) => {
    if (confirm(`Are you sure you want to delete "${product.name}"?`)) {
        router.delete(route('admin.products.destroy', product.id));
    }
};

const formatPrice = (val: number | string) => {
    const num = Number(val);
    return isNaN(num) ? '₦0' : '₦' + num.toLocaleString('en-NG', { maximumFractionDigits: 0 });
};
</script>

<template>
    <AdminLayout title="Product Management">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-zinc-900 dark:text-zinc-100">
                        Products & Variations
                    </h1>
                    <p class="text-xs text-zinc-500 mt-1">
                        Manage clothing, confectioneries, media, prices, and customizable options.
                    </p>
                </div>

                <Link
                    :href="route('admin.products.create')"
                    class="px-4 py-2.5 rounded-xl bg-[#2D241E] hover:bg-[#43362C] dark:bg-[#C59A65] dark:hover:bg-[#B3804A] text-white dark:text-[#0F1115] font-semibold text-xs shadow flex items-center gap-1.5 transition-all"
                >
                    <Plus class="w-4 h-4" />
                    <span>Create New Product</span>
                </Link>
            </div>

            <!-- Filters Bar -->
            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                <!-- Search -->
                <div class="relative w-full md:w-72">
                    <Search class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input 
                        v-model="search"
                        @keydown.enter="applyFilters"
                        type="text" 
                        placeholder="Search product or SKU..." 
                        class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                    />
                </div>

                <!-- Select Filters -->
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <!-- Category Filter -->
                    <select
                        v-model="categoryId"
                        @change="applyFilters"
                        class="text-xs py-2 px-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 focus:outline-none"
                    >
                        <option value="">All Brands & Categories</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">
                            {{ c.brand_name || c.name }}
                        </option>
                    </select>

                    <!-- Availability Filter -->
                    <select
                        v-model="isAvailable"
                        @change="applyFilters"
                        class="text-xs py-2 px-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 focus:outline-none"
                    >
                        <option value="">All Statuses</option>
                        <option value="1">Available (Published)</option>
                        <option value="0">Unavailable (Hidden)</option>
                    </select>

                    <!-- Sort -->
                    <select
                        v-model="sort"
                        @change="applyFilters"
                        class="text-xs py-2 px-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 focus:outline-none"
                    >
                        <option value="newest">Newest First</option>
                        <option value="name_asc">Name A-Z</option>
                        <option value="price_asc">Price Low-High</option>
                        <option value="price_desc">Price High-Low</option>
                    </select>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60 text-zinc-500 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="p-4">Product</th>
                                <th class="p-4">Category</th>
                                <th class="p-4">Base Price</th>
                                <th class="p-4">Variations</th>
                                <th class="p-4">Stock</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-center">Featured</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            <tr v-for="product in products.data" :key="product.id" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40 transition-colors">
                                <!-- Media & Name -->
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-14 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-800 shrink-0 border border-zinc-200 dark:border-zinc-700 relative">
                                            <img v-if="product.primary_image_url" :src="product.primary_image_url" class="w-full h-full object-cover" />
                                            <span v-if="product.primary_video_url" class="absolute bottom-0.5 right-0.5 p-0.5 rounded bg-black/80 text-white">
                                                <Video class="w-2.5 h-2.5" />
                                            </span>
                                        </div>
                                        <div class="space-y-0.5">
                                            <span class="font-bold text-zinc-900 dark:text-zinc-100 text-sm block">
                                                {{ product.name }}
                                            </span>
                                            <span v-if="product.sku" class="text-[10px] text-zinc-400 font-mono">
                                                SKU: {{ product.sku }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="p-4">
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">
                                        {{ product.category?.brand_name || product.category?.name }}
                                    </span>
                                    <span class="text-[10px] text-zinc-400 block uppercase">
                                        {{ product.product_type || 'Standard' }}
                                    </span>
                                </td>

                                <!-- Price -->
                                <td class="p-4">
                                    <div class="font-display font-bold text-zinc-900 dark:text-zinc-100 text-sm">
                                        {{ formatPrice(product.effective_price) }}
                                    </div>
                                    <span v-if="product.sale_price" class="text-[10px] text-zinc-400 line-through">
                                        {{ formatPrice(product.base_price) }}
                                    </span>
                                </td>

                                <!-- Variations count -->
                                <td class="p-4 text-zinc-600 dark:text-zinc-400">
                                    <span v-if="product.variants && product.variants.length > 0">
                                        {{ product.variants.length }} options
                                    </span>
                                    <span v-else class="text-zinc-400 italic">None</span>
                                </td>

                                <!-- Stock -->
                                <td class="p-4">
                                    <span 
                                        v-if="product.track_stock"
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                        :class="product.stock_quantity <= 5 ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'"
                                    >
                                        {{ product.stock_quantity }} in stock
                                    </span>
                                    <span v-else class="text-[10px] text-zinc-400">Unlimited</span>
                                </td>

                                <!-- Availability Toggle -->
                                <td class="p-4 text-center">
                                    <button
                                        type="button"
                                        @click="toggleAvailability(product)"
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold transition-colors"
                                        :class="product.is_available ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400'"
                                    >
                                        {{ product.is_available ? 'Published' : 'Hidden' }}
                                    </button>
                                </td>

                                <!-- Featured Toggle -->
                                <td class="p-4 text-center">
                                    <button
                                        type="button"
                                        @click="toggleFeatured(product)"
                                        class="p-1 rounded-lg transition-colors"
                                        :class="product.is_featured ? 'text-amber-500 hover:text-amber-600' : 'text-zinc-300 dark:text-zinc-600 hover:text-zinc-500'"
                                        title="Toggle Featured"
                                    >
                                        <Sparkles class="w-4 h-4 fill-current" />
                                    </button>
                                </td>

                                <!-- Actions -->
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Live preview link -->
                                        <a
                                            :href="product.category?.slug === 'confectionery' ? route('confectionery.show', product.slug) : route('clothing.show', product.slug)"
                                            target="_blank"
                                            class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                            title="View on Storefront"
                                        >
                                            <ExternalLink class="w-4 h-4" />
                                        </a>

                                        <!-- Edit -->
                                        <Link
                                            :href="route('admin.products.edit', product.id)"
                                            class="p-1.5 rounded-lg text-zinc-400 hover:text-[#A66E38] dark:hover:text-[#C59A65] hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                            title="Edit Product"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </Link>

                                        <!-- Delete -->
                                        <button
                                            type="button"
                                            @click="deleteProduct(product)"
                                            class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                            title="Delete Product"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="products.links.length > 3" class="p-4 border-t border-zinc-200 dark:border-zinc-800 flex justify-center gap-1">
                    <component
                        :is="link.url ? 'Link' : 'span'"
                        v-for="(link, i) in products.links"
                        :key="i"
                        :href="link.url || undefined"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-colors"
                        :class="[
                            link.active 
                                ? 'bg-[#A66E38] text-white border-[#A66E38]' 
                                : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700',
                            !link.url ? 'opacity-40 cursor-not-allowed' : ''
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
