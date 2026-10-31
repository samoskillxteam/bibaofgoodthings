<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    Save, Globe, MessageCircle, Phone, Instagram, Facebook,
    Truck, Image, AlertCircle, CheckCircle
} from 'lucide-vue-next';

interface SettingItem {
    id: number;
    key: string;
    value: string | null;
    description: string;
    type: string;
    group: string;
}

interface Props {
    settings: SettingItem[];
}
const props = defineProps<Props>();

// Build keyed map from flat settings array
const settingMap = Object.fromEntries(props.settings.map(s => [s.key, s.value ?? '']));

const form = useForm({
    settings: { ...settingMap } as Record<string, string>,
});

function submit() {
    form.post('/admin/settings', { preserveScroll: true });
}

// Group definitions
const groups = [
    {
        key: 'branding',
        label: 'Branding',
        icon: Globe,
        description: 'Business name, tagline, and brand identity',
        keys: ['business_name', 'clothing_brand_name', 'confectionery_brand_name', 'tagline', 'currency_code', 'currency_symbol'],
    },
    {
        key: 'hero',
        label: 'Homepage Hero',
        icon: Globe,
        description: 'Main banner headline and subheadline',
        keys: ['hero_headline', 'hero_subheadline'],
    },
    {
        key: 'contact',
        label: 'Contact Info',
        icon: Phone,
        description: 'Phone, email, and physical address',
        keys: ['contact_phone', 'contact_email', 'whatsapp_number', 'address'],
    },
    {
        key: 'social',
        label: 'Social Media',
        icon: Instagram,
        description: 'Instagram and Facebook links',
        keys: ['instagram_url', 'facebook_url'],
    },
    {
        key: 'delivery',
        label: 'Delivery & Fulfilment',
        icon: Truck,
        description: 'Delivery information displayed to customers',
        keys: ['delivery_info', 'delivery_fee'],
    },
];

// Get setting metadata for a key
function getMeta(key: string): SettingItem | undefined {
    return props.settings.find(s => s.key === key);
}

function isTextarea(key: string) {
    const meta = getMeta(key);
    return meta?.type === 'textarea' || ['hero_subheadline', 'address', 'delivery_info'].includes(key);
}

function inputType(key: string) {
    const meta = getMeta(key);
    if (meta?.type === 'url') return 'url';
    if (meta?.type === 'email') return 'email';
    if (key.includes('phone') || key === 'whatsapp_number') return 'tel';
    return 'text';
}

function label(key: string) {
    const meta = getMeta(key);
    if (meta?.description) return meta.description;
    return key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

// Keys from settings that don't appear in groups — show as "Other"
const groupedKeys = groups.flatMap(g => g.keys);
const ungroupedSettings = computed(() => props.settings.filter(s => !groupedKeys.includes(s.key)));
</script>

<template>
    <AdminLayout title="Settings">
        <div class="max-w-3xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Settings</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Configure your business information, branding, and contact details</p>
            </div>

            <!-- Flash -->
            <div v-if="$page.props.flash?.success" class="flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-xl">
                <CheckCircle class="w-5 h-5 text-emerald-500 flex-shrink-0" />
                <p class="text-sm text-emerald-700 dark:text-emerald-400">{{ $page.props.flash.success }}</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div v-for="group in groups" :key="group.key"
                    class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <div class="flex items-center gap-3 pb-1 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                            <component :is="group.icon" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ group.label }}</h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ group.description }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div v-for="key in group.keys" :key="key" v-if="form.settings[key] !== undefined || getMeta(key)">
                            <label :for="`setting-${key}`" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                {{ label(key) }}
                            </label>
                            <textarea v-if="isTextarea(key)" :id="`setting-${key}`"
                                v-model="form.settings[key]" rows="3"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm resize-none">
                            </textarea>
                            <input v-else :id="`setting-${key}`" :type="inputType(key)"
                                v-model="form.settings[key]"
                                :placeholder="key === 'whatsapp_number' ? '234XXXXXXXXXX (with country code, no +)' : ''"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                            <p v-if="(form.errors as any)[`settings.${key}`]" class="text-red-500 text-xs mt-1">
                                {{ (form.errors as any)[`settings.${key}`] }}
                            </p>
                            <p v-if="key === 'whatsapp_number'" class="text-xs text-zinc-400 mt-1">
                                Enter the number with country code but without the + sign. E.g. 2348083271068
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Ungrouped settings -->
                <div v-if="ungroupedSettings.length > 0"
                    class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 pb-1 border-b border-zinc-100 dark:border-zinc-800">Other Settings</h2>
                    <div v-for="s in ungroupedSettings" :key="s.key">
                        <label :for="`setting-${s.key}`" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">{{ s.description || label(s.key) }}</label>
                        <textarea v-if="s.type === 'textarea'" :id="`setting-${s.key}`" v-model="form.settings[s.key]" rows="3"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm resize-none"></textarea>
                        <input v-else :id="`setting-${s.key}`" :type="s.type === 'url' ? 'url' : 'text'" v-model="form.settings[s.key]"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                    </div>
                </div>

                <!-- Error summary -->
                <div v-if="Object.keys(form.errors).length > 0"
                    class="flex gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl">
                    <AlertCircle class="w-5 h-5 text-red-500 flex-shrink-0" />
                    <div class="text-sm text-red-700 dark:text-red-400">
                        <p class="font-medium mb-1">Please fix the errors above.</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
                        </ul>
                    </div>
                </div>

                <div class="flex justify-end pb-8">
                    <button type="submit" :disabled="form.processing"
                        class="flex items-center gap-2 px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium text-sm transition-colors disabled:opacity-60">
                        <Save class="w-4 h-4" />
                        {{ form.processing ? 'Saving…' : 'Save Settings' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
