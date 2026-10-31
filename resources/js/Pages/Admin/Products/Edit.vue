<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import type { Product, Category, ProductOption, ProductOptionValue, ProductVariant, ProductAddon, ProductMedia } from '@/types';
import {
    Plus, Trash2, Upload, Image, Video, X, AlertCircle, Save, Eye
} from 'lucide-vue-next';

interface Props {
    product: Product & { media: ProductMedia[] };
    categories: Category[];
}

const props = defineProps<Props>();

// ── Form ───────────────────────────────────────────────────────────────
const form = useForm({
    category_id: props.product.category_id,
    name: props.product.name,
    sku: props.product.sku ?? '',
    product_type: props.product.product_type ?? '',
    short_description: props.product.short_description ?? '',
    full_description: props.product.full_description ?? '',
    base_price: props.product.base_price,
    sale_price: props.product.sale_price ?? '',
    cost_price: props.product.cost_price ?? '',
    track_stock: props.product.track_stock,
    stock_quantity: props.product.stock_quantity ?? 0,
    is_available: props.product.is_available,
    is_featured: props.product.is_featured,
    display_order: props.product.display_order ?? 0,
    new_images: [] as File[],
    new_videos: [] as File[],
    delete_media_ids: [] as number[],
    options: [] as Array<{ id?: number; name: string; values: Array<{ id?: number; name: string; price_modifier: number; color_code: string }> }>,
    variants: [] as Array<{ id?: number; sku: string; title: string; price: number; sale_price: number | string; stock_quantity: number; is_available: boolean; option_combination: string }>,
    addons: [] as Array<{ id?: number; name: string; price: number; is_available: boolean; display_order: number }>,
    _method: 'PUT',
});

// Pre-populate options
form.options = (props.product.options ?? []).map(o => ({
    id: o.id,
    name: o.name,
    values: (o.values ?? []).map(v => ({
        id: v.id,
        name: v.name,
        price_modifier: Number(v.price_modifier),
        color_code: v.color_code ?? '',
    })),
}));

// Pre-populate variants
form.variants = (props.product.variants ?? []).map(v => ({
    id: v.id,
    sku: v.sku ?? '',
    title: v.title,
    price: v.price,
    sale_price: v.sale_price ?? '',
    stock_quantity: v.stock_quantity,
    is_available: v.is_available,
    option_combination: '',
}));

// Pre-populate addons
form.addons = (props.product.addons ?? []).map(a => ({
    id: a.id,
    name: a.name,
    price: a.price,
    is_available: a.is_available,
    display_order: a.display_order,
}));

// Existing media
const existingMedia = ref<ProductMedia[]>(props.product.media ?? []);
const mediaToDelete = ref<number[]>([]);

function toggleDeleteMedia(id: number) {
    const idx = mediaToDelete.value.indexOf(id);
    if (idx === -1) {
        mediaToDelete.value.push(id);
    } else {
        mediaToDelete.value.splice(idx, 1);
    }
    form.delete_media_ids = [...mediaToDelete.value];
}

// ── Image / Video upload ───────────────────────────────────────────────
const imageInput = ref<HTMLInputElement | null>(null);
const videoInput = ref<HTMLInputElement | null>(null);
const newImagePreviews = ref<string[]>([]);

function handleImages(e: Event) {
    const files = Array.from((e.target as HTMLInputElement).files ?? []);
    form.new_images = files;
    newImagePreviews.value = files.map(f => URL.createObjectURL(f));
}

function handleVideos(e: Event) {
    const files = Array.from((e.target as HTMLInputElement).files ?? []);
    form.new_videos = files;
}

// ── Options ───────────────────────────────────────────────────────────
function addOption() {
    form.options.push({ name: '', values: [{ name: '', price_modifier: 0, color_code: '' }] });
}
function removeOption(i: number) { form.options.splice(i, 1); }
function addOptionValue(oi: number) { form.options[oi].values.push({ name: '', price_modifier: 0, color_code: '' }); }
function removeOptionValue(oi: number, vi: number) { form.options[oi].values.splice(vi, 1); }

// ── Variants ──────────────────────────────────────────────────────────
function addVariant() {
    form.variants.push({ sku: '', title: '', price: 0, sale_price: '', stock_quantity: 0, is_available: true, option_combination: '' });
}
function removeVariant(i: number) { form.variants.splice(i, 1); }

// ── Addons ────────────────────────────────────────────────────────────
function addAddon() {
    form.addons.push({ name: '', price: 0, is_available: true, display_order: form.addons.length });
}
function removeAddon(i: number) { form.addons.splice(i, 1); }

// ── Submit ────────────────────────────────────────────────────────────
function submit() {
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('category_id', String(form.category_id));
    formData.append('name', form.name);
    formData.append('sku', form.sku);
    formData.append('product_type', form.product_type);
    formData.append('short_description', form.short_description);
    formData.append('full_description', form.full_description);
    formData.append('base_price', String(form.base_price));
    formData.append('sale_price', String(form.sale_price));
    formData.append('cost_price', String(form.cost_price));
    formData.append('track_stock', form.track_stock ? '1' : '0');
    formData.append('stock_quantity', String(form.stock_quantity));
    formData.append('is_available', form.is_available ? '1' : '0');
    formData.append('is_featured', form.is_featured ? '1' : '0');
    formData.append('display_order', String(form.display_order));
    form.new_images.forEach(f => formData.append('images[]', f));
    form.new_videos.forEach(f => formData.append('videos[]', f));
    form.delete_media_ids.forEach(id => formData.append('remove_media_ids[]', String(id)));
    form.options.forEach((o, i) => {
        if (o.id) formData.append(`options[${i}][id]`, String(o.id));
        formData.append(`options[${i}][name]`, o.name);
        formData.append(`options[${i}][display_order]`, String(o.display_order ?? i));
        o.values.forEach((v, j) => {
            if (v.id) formData.append(`options[${i}][values][${j}][id]`, String(v.id));
            formData.append(`options[${i}][values][${j}][name]`, v.name);
            formData.append(`options[${i}][values][${j}][price_modifier]`, String(v.price_modifier ?? 0));
            formData.append(`options[${i}][values][${j}][color_code]`, v.color_code ?? '');
        });
    });
    form.variants.forEach((v, i) => {
        if (v.id) formData.append(`variants[${i}][id]`, String(v.id));
        formData.append(`variants[${i}][title]`, v.title);
        formData.append(`variants[${i}][sku]`, v.sku ?? '');
        formData.append(`variants[${i}][price]`, String(v.price));
        formData.append(`variants[${i}][sale_price]`, String(v.sale_price ?? ''));
        formData.append(`variants[${i}][stock_quantity]`, String(v.stock_quantity));
        formData.append(`variants[${i}][is_available]`, v.is_available ? '1' : '0');
        formData.append(`variants[${i}][option_combination]`, v.option_combination ?? '');
    });
    form.addons.forEach((a, i) => {
        if (a.id) formData.append(`addons[${i}][id]`, String(a.id));
        formData.append(`addons[${i}][name]`, a.name);
        formData.append(`addons[${i}][price]`, String(a.price));
        formData.append(`addons[${i}][is_available]`, a.is_available ? '1' : '0');
        formData.append(`addons[${i}][display_order]`, String(a.display_order ?? i));
    });

    router.post(`/admin/products/${props.product.id}`, formData, {
        forceFormData: true,
        onError: (errors) => { form.setError(errors as any); },
    });
}

const selectedCategory = computed(() =>
    props.categories.find(c => c.id === Number(form.category_id))
);
const isClothing = computed(() => selectedCategory.value?.slug === 'clothing');
const isConfectionery = computed(() => selectedCategory.value?.slug === 'confectionery');
</script>

<template>
    <AdminLayout :title="`Edit: ${product.name}`">
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Edit Product</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ product.name }}</p>
                </div>
                <a :href="`/${selectedCategory?.slug ?? 'clothing'}/${product.slug}`"
                   target="_blank"
                   class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                    <Eye class="w-4 h-4" /> View Live
                </a>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Info -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Category *</label>
                            <select v-model="form.category_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm">
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.brand_name }} ({{ cat.name }})</option>
                            </select>
                            <p v-if="form.errors.category_id" class="text-red-500 text-xs mt-1">{{ form.errors.category_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Product Name *</label>
                            <input v-model="form.name" type="text" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">SKU / Reference</label>
                            <input v-model="form.sku" type="text" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                {{ isClothing ? 'Collection / Style' : 'Product Type' }}
                            </label>
                            <input v-model="form.product_type" type="text" :placeholder="isClothing ? 'e.g. Summer Collection' : 'e.g. cupcake, cake, drink'" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Short Description</label>
                        <textarea v-model="form.short_description" rows="2" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Full Description</label>
                        <textarea v-model="form.full_description" rows="4" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm resize-none"></textarea>
                    </div>
                </div>

                <!-- Pricing & Stock -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Pricing & Stock</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Base Price (₦) *</label>
                            <input v-model="form.base_price" type="number" min="0" step="0.01" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                            <p v-if="form.errors.base_price" class="text-red-500 text-xs mt-1">{{ form.errors.base_price }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Sale Price (₦)</label>
                            <input v-model="form.sale_price" type="number" min="0" step="0.01" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Cost Price (₦)</label>
                            <input v-model="form.cost_price" type="number" min="0" step="0.01" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Display Order</label>
                            <input v-model="form.display_order" type="number" min="0" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" v-model="form.track_stock" class="rounded border-zinc-300" />
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">Track Stock</span>
                        </label>
                        <div v-if="form.track_stock" class="flex items-center gap-2">
                            <label class="text-sm text-zinc-700 dark:text-zinc-300">Stock Qty:</label>
                            <input v-model="form.stock_quantity" type="number" min="0" class="w-24 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2 py-1 text-sm" />
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" v-model="form.is_available" class="rounded border-zinc-300" />
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">Available</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" v-model="form.is_featured" class="rounded border-zinc-300" />
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">Featured</span>
                        </label>
                    </div>
                </div>

                <!-- Media -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Media</h2>

                    <!-- Existing media -->
                    <div v-if="existingMedia.length > 0">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-3">Existing media — click the trash icon to mark for deletion:</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                            <div v-for="m in existingMedia" :key="m.id" class="relative group">
                                <div :class="[
                                    'rounded-xl overflow-hidden border-2 transition-all relative',
                                    mediaToDelete.includes(m.id)
                                        ? 'border-red-500 opacity-40 scale-95'
                                        : 'border-zinc-200 dark:border-zinc-700'
                                ]">
                                    <video v-if="m.media_type === 'video'" :src="m.url" class="w-full aspect-square object-cover" muted></video>
                                    <img v-else :src="m.url" :alt="m.alt_text ?? ''" class="w-full aspect-square object-cover" />
                                </div>
                                <button
                                    type="button"
                                    @click="toggleDeleteMedia(m.id)"
                                    :class="[
                                        'absolute top-1.5 right-1.5 p-1.5 rounded-lg text-xs font-bold transition-all shadow-md',
                                        mediaToDelete.includes(m.id)
                                            ? 'bg-red-600 text-white'
                                            : 'bg-zinc-900/80 hover:bg-red-600 text-white'
                                    ]"
                                    :title="mediaToDelete.includes(m.id) ? 'Undo delete' : 'Delete media'"
                                >
                                    <Trash2 v-if="!mediaToDelete.includes(m.id)" class="w-3.5 h-3.5" />
                                    <X v-else class="w-3.5 h-3.5" />
                                </button>
                                <span class="absolute bottom-1.5 left-1.5 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-zinc-900/80 text-white backdrop-blur-sm">
                                    {{ m.media_type.toUpperCase() }}{{ m.is_primary ? ' ★' : '' }}
                                </span>
                            </div>
                        </div>
                        <p v-if="mediaToDelete.length > 0" class="text-red-500 text-xs font-semibold mt-2">
                            {{ mediaToDelete.length }} media item(s) will be permanently deleted upon saving.
                        </p>
                    </div>

                    <!-- New images -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Add New Images</label>
                        <div @click="imageInput?.click()" class="border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-4 text-center cursor-pointer hover:border-amber-400 transition-colors">
                            <Image class="w-6 h-6 mx-auto text-zinc-400 mb-1" />
                            <p class="text-xs text-zinc-500">Click to add images</p>
                        </div>
                        <input ref="imageInput" type="file" multiple accept="image/*" class="hidden" @change="handleImages" />
                        <div v-if="newImagePreviews.length > 0" class="grid grid-cols-4 gap-2 mt-2">
                            <img v-for="(src, i) in newImagePreviews" :key="i" :src="src" class="aspect-square object-cover rounded-lg" />
                        </div>
                    </div>

                    <!-- New videos -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Add New Videos</label>
                        <div @click="videoInput?.click()" class="border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-4 text-center cursor-pointer hover:border-amber-400 transition-colors">
                            <Video class="w-6 h-6 mx-auto text-zinc-400 mb-1" />
                            <p class="text-xs text-zinc-500">Click to add videos (MP4)</p>
                        </div>
                        <input ref="videoInput" type="file" multiple accept="video/*" class="hidden" @change="handleVideos" />
                        <p v-if="form.new_videos.length > 0" class="text-sm text-zinc-500 mt-1">{{ form.new_videos.length }} video(s) selected</p>
                    </div>
                </div>

                <!-- Options -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ isClothing ? 'Size & Colour Options' : 'Product Options' }}
                        </h2>
                        <button type="button" @click="addOption" class="flex items-center gap-1 px-3 py-1.5 text-sm bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg border border-amber-200 dark:border-amber-700 hover:bg-amber-100 transition-colors">
                            <Plus class="w-4 h-4" /> Add Option
                        </button>
                    </div>
                    <div v-if="form.options.length === 0" class="text-sm text-zinc-400 py-4 text-center">
                        No options configured. Add options like Size, Colour, Flavour, etc.
                    </div>
                    <div v-for="(opt, oi) in form.options" :key="oi" class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 space-y-3">
                        <div class="flex items-center gap-2">
                            <input v-model="opt.name" type="text" :placeholder="isClothing ? 'e.g. Size, Colour' : 'e.g. Flavour, Pack Size'" class="flex-1 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-1.5 text-sm" />
                            <button type="button" @click="removeOption(oi)" class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg">
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                        <div class="space-y-2 pl-2">
                            <div v-for="(val, vi) in opt.values" :key="vi" class="flex items-center gap-2">
                                <input v-model="val.name" type="text" :placeholder="isClothing ? 'e.g. XS, Red' : 'e.g. Vanilla, Box of 6'" class="flex-1 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm" />
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-zinc-500">₦</span>
                                    <input v-model.number="val.price_modifier" type="number" step="0.01" placeholder="+/- price" class="w-24 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm" />
                                </div>
                                <input v-if="isClothing" v-model="val.color_code" type="color" class="w-8 h-8 rounded cursor-pointer border border-zinc-300" title="Colour swatch" />
                                <button type="button" @click="removeOptionValue(oi, vi)" class="p-1 text-red-400 hover:text-red-600">
                                    <X class="w-3.5 h-3.5" />
                                </button>
                            </div>
                            <button type="button" @click="addOptionValue(oi)" class="text-xs text-amber-600 hover:text-amber-700 flex items-center gap-1 pl-1">
                                <Plus class="w-3.5 h-3.5" /> Add value
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Variants -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Variants / Combinations</h2>
                        <button type="button" @click="addVariant" class="flex items-center gap-1 px-3 py-1.5 text-sm bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg border border-amber-200 dark:border-amber-700 hover:bg-amber-100 transition-colors">
                            <Plus class="w-4 h-4" /> Add Variant
                        </button>
                    </div>
                    <div v-if="form.variants.length === 0" class="text-sm text-zinc-400 py-4 text-center">
                        No variants — product uses base price. Add variants for size/colour/flavour combos with individual prices.
                    </div>
                    <div class="space-y-3">
                        <div v-for="(v, vi) in form.variants" :key="vi" class="grid grid-cols-2 md:grid-cols-6 gap-2 items-end p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                            <div class="md:col-span-2">
                                <label class="text-xs text-zinc-500 mb-1 block">Title *</label>
                                <input v-model="v.title" type="text" placeholder="e.g. Medium / Black" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs text-zinc-500 mb-1 block">SKU</label>
                                <input v-model="v.sku" type="text" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs text-zinc-500 mb-1 block">Price (₦) *</label>
                                <input v-model.number="v.price" type="number" min="0" step="0.01" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs text-zinc-500 mb-1 block">Sale Price</label>
                                <input v-model="v.sale_price" type="number" min="0" step="0.01" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm" />
                            </div>
                            <div class="flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="text-xs text-zinc-500 mb-1 block">Stock</label>
                                    <input v-model.number="v.stock_quantity" type="number" min="0" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm" />
                                </div>
                                <button type="button" @click="removeVariant(vi)" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg mb-0.5">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add-ons -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Add-ons / Extras</h2>
                        <button type="button" @click="addAddon" class="flex items-center gap-1 px-3 py-1.5 text-sm bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg border border-amber-200 dark:border-amber-700 hover:bg-amber-100 transition-colors">
                            <Plus class="w-4 h-4" /> Add Extra
                        </button>
                    </div>
                    <div v-if="form.addons.length === 0" class="text-sm text-zinc-400 py-4 text-center">
                        No add-ons. Add optional extras like toppings, accessories, or customisations.
                    </div>
                    <div class="space-y-2">
                        <div v-for="(a, ai) in form.addons" :key="ai" class="flex items-center gap-3 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                            <input v-model="a.name" type="text" placeholder="e.g. Sprinkles, Extra Frosting" class="flex-1 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm" />
                            <div class="flex items-center gap-1">
                                <span class="text-xs text-zinc-500">₦</span>
                                <input v-model.number="a.price" type="number" min="0" step="0.01" class="w-24 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm" />
                            </div>
                            <label class="flex items-center gap-1 text-sm text-zinc-600 dark:text-zinc-300">
                                <input type="checkbox" v-model="a.is_available" class="rounded" />
                                On
                            </label>
                            <button type="button" @click="removeAddon(ai)" class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg">
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Error Summary -->
                <div v-if="Object.keys(form.errors).length > 0" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-4 flex gap-3">
                    <AlertCircle class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
                    <div>
                        <p class="font-medium text-red-800 dark:text-red-300 text-sm">Please fix the following errors:</p>
                        <ul class="mt-1 text-sm text-red-700 dark:text-red-400 list-disc list-inside space-y-0.5">
                            <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pb-8">
                    <a href="/admin/products" class="px-5 py-2.5 text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" :disabled="form.processing"
                        class="flex items-center gap-2 px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium text-sm transition-colors disabled:opacity-60">
                        <Save class="w-4 h-4" />
                        {{ form.processing ? 'Saving…' : 'Save Changes' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
