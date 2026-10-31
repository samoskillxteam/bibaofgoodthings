<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import type { Category } from '@/types';
import { Edit, Save, X, Plus, Shirt, Cake, FolderTree } from 'lucide-vue-next';

interface Props {
    categories: Category[];
}
const props = defineProps<Props>();

const editingId = ref<number | null>(null);
const editForm = reactive<{
    name: string;
    brand_name: string;
    subtitle: string;
    description: string;
    is_active: boolean;
    display_order: number;
}>({ name: '', brand_name: '', subtitle: '', description: '', is_active: true, display_order: 0 });

const createForm = useForm({
    name: '',
    brand_name: '',
    subtitle: '',
    description: '',
    is_active: true,
    display_order: props.categories.length,
});

const showCreate = ref(false);

function startEdit(cat: Category) {
    editingId.value = cat.id;
    editForm.name = cat.name;
    editForm.brand_name = cat.brand_name;
    editForm.subtitle = cat.subtitle ?? '';
    editForm.description = cat.description ?? '';
    editForm.is_active = cat.is_active;
    editForm.display_order = cat.display_order;
}

function cancelEdit() { editingId.value = null; }

function saveEdit(cat: Category) {
    router.put(`/admin/categories/${cat.id}`, { ...editForm }, {
        preserveScroll: true,
        onSuccess: () => { editingId.value = null; },
    });
}

function submitCreate() {
    createForm.post('/admin/categories', {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset();
            showCreate.value = false;
        },
    });
}

const categoryIcon = (slug: string) => slug === 'clothing' ? Shirt : Cake;
</script>

<template>
    <AdminLayout title="Categories">
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Categories</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Manage product categories and brand information</p>
                </div>
                <button @click="showCreate = !showCreate"
                    class="flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <Plus class="w-4 h-4" /> New Category
                </button>
            </div>

            <!-- Create form -->
            <div v-if="showCreate" class="bg-white dark:bg-zinc-900 rounded-xl border border-amber-200 dark:border-amber-700 p-6">
                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Create New Category</h3>
                <form @submit.prevent="submitCreate" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Slug / ID *</label>
                            <input v-model="createForm.name" type="text" placeholder="e.g. accessories"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                            <p v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Brand / Display Name *</label>
                            <input v-model="createForm.brand_name" type="text" placeholder="e.g. Biba Accessories"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                            <p v-if="createForm.errors.brand_name" class="text-red-500 text-xs mt-1">{{ createForm.errors.brand_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Subtitle</label>
                            <input v-model="createForm.subtitle" type="text" placeholder="One-line description"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Display Order</label>
                            <input v-model.number="createForm.display_order" type="number" min="0"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Description</label>
                            <textarea v-model="createForm.description" rows="2"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm resize-none"></textarea>
                        </div>
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" v-model="createForm.is_active" class="rounded" />
                                <span class="text-sm text-zinc-700 dark:text-zinc-300">Active</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit" :disabled="createForm.processing"
                            class="flex items-center gap-2 px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-60">
                            <Save class="w-4 h-4" /> Create
                        </button>
                        <button type="button" @click="showCreate = false"
                            class="px-5 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Category list -->
            <div class="space-y-4">
                <div v-for="cat in categories" :key="cat.id"
                    class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">

                    <!-- View mode -->
                    <div v-if="editingId !== cat.id" class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                                    <component :is="categoryIcon(cat.slug)" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ cat.brand_name }}</h3>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="cat.is_active
                                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                                : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400'">
                                            {{ cat.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                        Slug: <code class="bg-zinc-100 dark:bg-zinc-800 px-1 rounded text-xs">{{ cat.slug }}</code>
                                        · Order: {{ cat.display_order }}
                                        · {{ cat.products_count ?? 0 }} products
                                    </p>
                                    <p v-if="cat.subtitle" class="text-sm text-zinc-600 dark:text-zinc-300 mt-1">{{ cat.subtitle }}</p>
                                    <p v-if="cat.description" class="text-xs text-zinc-400 dark:text-zinc-500 mt-1 line-clamp-2">{{ cat.description }}</p>
                                </div>
                            </div>
                            <button @click="startEdit(cat)"
                                class="flex items-center gap-1.5 px-3 py-1.5 text-sm border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                <Edit class="w-3.5 h-3.5" /> Edit
                            </button>
                        </div>
                    </div>

                    <!-- Edit mode -->
                    <div v-else class="p-6 bg-amber-50/50 dark:bg-amber-900/10">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Editing: {{ cat.brand_name }}</h3>
                            <button @click="cancelEdit" class="p-1.5 text-zinc-400 hover:text-zinc-600 rounded-lg">
                                <X class="w-4 h-4" />
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Brand / Display Name *</label>
                                <input v-model="editForm.brand_name" type="text"
                                    class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Subtitle</label>
                                <input v-model="editForm.subtitle" type="text"
                                    class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Description</label>
                                <textarea v-model="editForm.description" rows="2"
                                    class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm resize-none"></textarea>
                            </div>
                            <div class="flex items-center gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Display Order</label>
                                    <input v-model.number="editForm.display_order" type="number" min="0"
                                        class="w-24 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                                </div>
                                <label class="flex items-center gap-2 cursor-pointer mt-5">
                                    <input type="checkbox" v-model="editForm.is_active" class="rounded" />
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">Active</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mt-4">
                            <button @click="saveEdit(cat)"
                                class="flex items-center gap-2 px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium transition-colors">
                                <Save class="w-4 h-4" /> Save Changes
                            </button>
                            <button @click="cancelEdit"
                                class="px-5 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-700 p-4 flex gap-3">
                <FolderTree class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                <p class="text-sm text-amber-800 dark:text-amber-300">
                    <strong>Note:</strong> The category slug is used in URLs (e.g. <code>/clothing</code>, <code>/confectionery</code>).
                    Changing a slug will break existing links. The <em>Clothing</em> and <em>Confectionery</em> categories
                    have special routes and should not be renamed.
                </p>
            </div>
        </div>
    </AdminLayout>
</template>
