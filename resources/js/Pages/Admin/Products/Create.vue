<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import type { Category } from '@/types';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    Plus, 
    Trash2, 
    Upload, 
    Video, 
    Image, 
    ArrowLeft, 
    Shirt, 
    Cake, 
    Check, 
    Sparkles 
} from 'lucide-vue-next';

interface Props {
    categories: Category[];
}

const props = defineProps<Props>();

const form = useForm({
    category_id: props.categories[0]?.id || 1,
    name: '',
    sku: '',
    product_type: '',
    short_description: '',
    full_description: '',
    base_price: '',
    sale_price: '',
    cost_price: '',
    stock_quantity: 0,
    track_stock: false,
    is_available: true,
    is_featured: false,
    display_order: 0,
    images: [] as File[],
    videos: [] as File[],
    variants: [] as Array<{ title: string; sku: string; price: number | string; sale_price?: number | string | null; stock_quantity: number }>,
    addons: [] as Array<{ name: string; price: number | string }>,
});

const selectedCategory = computed(() => {
    return props.categories.find(c => c.id === Number(form.category_id));
});

const isClothing = computed(() => {
    return selectedCategory.value?.slug === 'clothing';
});

const isConfectionery = computed(() => {
    return selectedCategory.value?.slug === 'confectionery';
});

// Image file selection
const onImagesChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        form.images = Array.from(target.files);
    }
};

// Video file selection
const onVideosChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        form.videos = Array.from(target.files);
    }
};

// Variant Management
const addVariant = () => {
    form.variants.push({
        title: '',
        sku: '',
        price: form.base_price || 0,
        sale_price: null,
        stock_quantity: 20,
    });
};

const removeVariant = (index: number) => {
    form.variants.splice(index, 1);
};

// Quick Templates for Clothing vs Confectionery
const applyClothingTemplate = () => {
    form.variants = [
        { title: 'Small / Black', sku: '', price: form.base_price || 25000, stock_quantity: 10 },
        { title: 'Medium / Black', sku: '', price: form.base_price || 25000, stock_quantity: 15 },
        { title: 'Large / Black', sku: '', price: form.base_price ? Number(form.base_price) + 2000 : 27000, stock_quantity: 10 },
        { title: 'Medium / White', sku: '', price: form.base_price ? Number(form.base_price) + 1000 : 26000, stock_quantity: 10 },
    ];
};

const applyConfectioneryTemplate = () => {
    form.variants = [
        { title: 'Box of 6 / Vanilla', sku: '', price: form.base_price || 12000, stock_quantity: 20 },
        { title: 'Box of 6 / Chocolate', sku: '', price: form.base_price || 12000, stock_quantity: 20 },
        { title: 'Box of 12 / Assorted', sku: '', price: form.base_price ? Number(form.base_price) * 1.8 : 22000, stock_quantity: 15 },
    ];
    form.addons = [
        { name: 'Custom Fondant Inscription', price: 1500 },
        { name: 'Gold Leaf & Topper', price: 2500 },
    ];
};

// Addon Management
const addAddon = () => {
    form.addons.push({
        name: '',
        price: 1000,
    });
};

const removeAddon = (index: number) => {
    form.addons.splice(index, 1);
};

const submit = () => {
    form.post(route('admin.products.store'));
};
</script>

<template>
    <AdminLayout title="Create Product">
        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Header -->
            <div class="flex items-center justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.products.index')"
                        class="p-2 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50"
                    >
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <h1 class="font-display font-extrabold text-2xl text-zinc-900 dark:text-zinc-100">
                            Create New Product
                        </h1>
                        <p class="text-xs text-zinc-500">
                            Add a new apparel or confectionery product with pricing and variations.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="submit"
                    :disabled="form.processing"
                    class="px-6 py-2.5 rounded-xl bg-[#2D241E] hover:bg-[#43362C] dark:bg-[#C59A65] dark:hover:bg-[#B3804A] text-white dark:text-[#0F1115] font-bold text-xs shadow disabled:opacity-50 transition-all"
                >
                    Save & Publish
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Section 1: Category & Basic Information -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                    <h2 class="font-serif font-bold text-lg text-zinc-900 dark:text-zinc-100 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                        Category & Basic Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Category Choice -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Product Category *
                            </label>
                            <select
                                v-model="form.category_id"
                                required
                                class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                            >
                                <option v-for="c in categories" :key="c.id" :value="c.id">
                                    {{ c.brand_name || c.name }} ({{ c.name }})
                                </option>
                            </select>
                        </div>

                        <!-- Product Type (e.g. Linen Co-ord, Wrap Dress, Cupcake, Cake, Drink) -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Product Type / Tag
                            </label>
                            <input
                                v-model="form.product_type"
                                type="text"
                                :placeholder="isClothing ? 'e.g. co-ord, dress, blazer, tee' : 'e.g. cupcake, cake, drink, snack'"
                                class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                            />
                        </div>

                        <!-- Product Name -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Product Title *
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="e.g. Silk Wrap Dress or Velvet Chocolate Cupcakes"
                                class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                            />
                            <div v-if="form.errors.name" class="text-xs text-rose-500">{{ form.errors.name }}</div>
                        </div>

                        <!-- SKU -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                SKU / Item Reference
                            </label>
                            <input
                                v-model="form.sku"
                                type="text"
                                placeholder="e.g. BC-001 or BT-001"
                                class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                            />
                        </div>

                        <!-- Display Order -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Display Priority Order
                            </label>
                            <input
                                v-model="form.display_order"
                                type="number"
                                placeholder="0"
                                class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                            />
                        </div>

                        <!-- Short Description -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Short Summary Description (Catalogue Cards)
                            </label>
                            <textarea
                                v-model="form.short_description"
                                rows="2"
                                placeholder="Brief summary of fabrics, flavours, or key highlights..."
                                class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                            ></textarea>
                        </div>

                        <!-- Full Description -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Full Product Description & Specifications
                            </label>
                            <textarea
                                v-model="form.full_description"
                                rows="5"
                                placeholder="Detailed sizing info, care instructions, ingredients, or allergens..."
                                class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Pricing & Inventory -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                    <h2 class="font-serif font-bold text-lg text-zinc-900 dark:text-zinc-100 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                        Pricing & Stock
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <!-- Base Price -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Base Price (₦ NGN) *
                            </label>
                            <input
                                v-model="form.base_price"
                                type="number"
                                required
                                step="100"
                                placeholder="25000"
                                class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                            />
                        </div>

                        <!-- Sale Price -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Optional Sale Price (₦ NGN)
                            </label>
                            <input
                                v-model="form.sale_price"
                                type="number"
                                step="100"
                                placeholder="22000"
                                class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                            />
                        </div>

                        <!-- Cost Price (Admin only) -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 flex items-center justify-between">
                                <span>Cost Price (₦ NGN)</span>
                                <span class="text-[10px] text-zinc-400 font-normal">Private to Admin</span>
                            </label>
                            <input
                                v-model="form.cost_price"
                                type="number"
                                step="100"
                                placeholder="12000"
                                class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                            />
                        </div>
                    </div>

                    <!-- Stock tracking & availability flags -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 cursor-pointer">
                            <input
                                v-model="form.is_available"
                                type="checkbox"
                                class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500"
                            />
                            <div>
                                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 block">Available (Published)</span>
                                <span class="text-[10px] text-zinc-400">Visible on public store</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 cursor-pointer">
                            <input
                                v-model="form.is_featured"
                                type="checkbox"
                                class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400"
                            />
                            <div>
                                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 block">Featured Spotlight</span>
                                <span class="text-[10px] text-zinc-400">Highlighted on Homepage</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 cursor-pointer">
                            <input
                                v-model="form.track_stock"
                                type="checkbox"
                                class="w-4 h-4 rounded text-[#A66E38] focus:ring-[#C59A65]"
                            />
                            <div>
                                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 block">Track Stock Count</span>
                                <span class="text-[10px] text-zinc-400">Monitor quantity balance</span>
                            </div>
                        </label>
                    </div>

                    <div v-if="form.track_stock" class="max-w-xs space-y-1.5 pt-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                            Available Stock Quantity
                        </label>
                        <input
                            v-model="form.stock_quantity"
                            type="number"
                            min="0"
                            class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100"
                        />
                    </div>
                </div>

                <!-- Section 3: Media Uploads (Images and Videos) -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                    <h2 class="font-serif font-bold text-lg text-zinc-900 dark:text-zinc-100 border-b border-zinc-100 dark:border-zinc-800 pb-3 flex items-center justify-between">
                        <span>Product Visuals (Photos & Videos)</span>
                        <span class="text-xs text-zinc-400 font-normal">Supports JPEG, PNG, WEBP, SVG & MP4</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Image Upload Input -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                                <Image class="w-4 h-4 text-[#A66E38] dark:text-[#C59A65]" />
                                <span>Upload Photos (Multiple)</span>
                            </label>
                            <input
                                type="file"
                                multiple
                                accept="image/*"
                                @change="onImagesChange"
                                class="w-full text-xs p-3 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 text-zinc-700 dark:text-zinc-300 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#EFE8DD] file:text-[#4A3525] dark:file:bg-zinc-700 dark:file:text-zinc-200 cursor-pointer"
                            />
                            <p class="text-[10px] text-zinc-400">First uploaded image will serve as primary display thumbnail.</p>
                        </div>

                        <!-- Video Upload Input -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                                <Video class="w-4 h-4 text-rose-500" />
                                <span>Upload Product Video (.MP4)</span>
                            </label>
                            <input
                                type="file"
                                multiple
                                accept="video/mp4,video/mov,video/webm"
                                @change="onVideosChange"
                                class="w-full text-xs p-3 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 text-zinc-700 dark:text-zinc-300 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-rose-100 file:text-rose-800 dark:file:bg-zinc-700 dark:file:text-zinc-200 cursor-pointer"
                            />
                            <p class="text-[10px] text-zinc-400">Videos will play interactively on card hover and on detail pages.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Configurable Variations (Sizes, Colours, Flavours, Packs) -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800 gap-3">
                        <div>
                            <h2 class="font-serif font-bold text-lg text-zinc-900 dark:text-zinc-100">
                                Product Variations & Pricing
                            </h2>
                            <p class="text-xs text-zinc-500">
                                Set specific prices and inventory for different sizes, colours, flavours, or bundle packs.
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                v-if="isClothing"
                                type="button"
                                @click="applyClothingTemplate"
                                class="px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-[#A66E38] hover:bg-zinc-50 dark:hover:bg-zinc-800"
                            >
                                + Clothing Template (S/M/L)
                            </button>

                            <button
                                v-if="isConfectionery"
                                type="button"
                                @click="applyConfectioneryTemplate"
                                class="px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-rose-600 hover:bg-zinc-50 dark:hover:bg-zinc-800"
                            >
                                + Confectionery Template (Box of 6/12)
                            </button>

                            <button
                                type="button"
                                @click="addVariant"
                                class="px-3 py-1.5 rounded-lg bg-[#2D241E] text-white dark:bg-[#C59A65] dark:text-[#0F1115] text-xs font-semibold flex items-center gap-1"
                            >
                                <Plus class="w-3.5 h-3.5" />
                                <span>Add Variation</span>
                            </button>
                        </div>
                    </div>

                    <div v-if="form.variants.length > 0" class="space-y-3">
                        <div 
                            v-for="(v, idx) in form.variants" 
                            :key="idx" 
                            class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-800 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center"
                        >
                            <div class="sm:col-span-4">
                                <label class="text-[10px] text-zinc-400 uppercase font-semibold">Title (e.g. Medium / Black or Box of 6)</label>
                                <input
                                    v-model="v.title"
                                    type="text"
                                    placeholder="Variant Title"
                                    required
                                    class="w-full text-xs p-2 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800"
                                />
                            </div>

                            <div class="sm:col-span-3">
                                <label class="text-[10px] text-zinc-400 uppercase font-semibold">Variant Price (₦)</label>
                                <input
                                    v-model="v.price"
                                    type="number"
                                    placeholder="25000"
                                    required
                                    class="w-full text-xs p-2 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-[10px] text-zinc-400 uppercase font-semibold">Sale Price (₦)</label>
                                <input
                                    v-model="v.sale_price"
                                    type="number"
                                    placeholder="Optional"
                                    class="w-full text-xs p-2 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-[10px] text-zinc-400 uppercase font-semibold">Stock</label>
                                <input
                                    v-model="v.stock_quantity"
                                    type="number"
                                    class="w-full text-xs p-2 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800"
                                />
                            </div>

                            <div class="sm:col-span-1 flex justify-end pt-3 sm:pt-0">
                                <button
                                    type="button"
                                    @click="removeVariant(idx)"
                                    class="p-2 text-zinc-400 hover:text-rose-600 rounded-lg"
                                    title="Remove Variant"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-6 text-xs text-zinc-400 italic">
                        No variations configured. The product will use its base price of {{ form.base_price ? '₦' + form.base_price : '₦0' }}.
                    </div>
                </div>

                <!-- Section 5: Optional Add-ons -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div>
                            <h2 class="font-serif font-bold text-lg text-zinc-900 dark:text-zinc-100">
                                Optional Add-ons & Extras
                            </h2>
                            <p class="text-xs text-zinc-500">
                                Extra toppings, custom fondant inscriptions, gift wrapping, ribbons, or greeting cards.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="addAddon"
                            class="px-3 py-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-xs font-semibold flex items-center gap-1"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Extra</span>
                        </button>
                    </div>

                    <div v-if="form.addons.length > 0" class="space-y-3">
                        <div 
                            v-for="(a, idx) in form.addons" 
                            :key="idx" 
                            class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-800 flex items-center gap-4"
                        >
                            <div class="flex-1">
                                <input
                                    v-model="a.name"
                                    type="text"
                                    placeholder="Add-on Name (e.g. Gift Wrapping or Gold Topper)"
                                    required
                                    class="w-full text-xs p-2 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800"
                                />
                            </div>

                            <div class="w-36">
                                <input
                                    v-model="a.price"
                                    type="number"
                                    placeholder="Price (+₦)"
                                    required
                                    class="w-full text-xs p-2 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800"
                                />
                            </div>

                            <button
                                type="button"
                                @click="removeAddon(idx)"
                                class="p-2 text-zinc-400 hover:text-rose-600 rounded-lg"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Error Summary -->
                <div v-if="Object.keys(form.errors).length > 0" class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-2xl p-4 flex gap-3 text-xs text-rose-700 dark:text-rose-300">
                    <div>
                        <p class="font-bold mb-1">Please correct the following errors:</p>
                        <ul class="list-disc pl-4 space-y-1">
                            <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="flex items-center justify-end gap-4 pb-12">
                    <Link
                        :href="route('admin.products.index')"
                        class="px-5 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                    >
                        Cancel
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-7 py-3 rounded-xl bg-[#2D241E] hover:bg-[#43362C] dark:bg-[#C59A65] dark:hover:bg-[#B3804A] text-white dark:text-[#0F1115] font-bold text-xs shadow-md transition-all flex items-center gap-2 disabled:opacity-50"
                    >
                        <span>Publish Product</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
