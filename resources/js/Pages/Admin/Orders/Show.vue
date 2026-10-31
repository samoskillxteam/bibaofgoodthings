<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import type { OrderEnquiry } from '@/types';
import { MessageCircle, ExternalLink, ArrowLeft, Clock, CheckCircle, XCircle, Phone } from 'lucide-vue-next';

interface Props {
    enquiry: OrderEnquiry;
}
const props = defineProps<Props>();

const statusForm = useForm({ status: props.enquiry.status });

function updateStatus() {
    statusForm.patch(`/admin/orders/${props.enquiry.id}/status`, { preserveScroll: true });
}

const statusConfig: Record<string, { label: string; icon: any; classes: string }> = {
    pending:   { label: 'Pending',   icon: Clock,         classes: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' },
    contacted: { label: 'Contacted', icon: Phone,         classes: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' },
    completed: { label: 'Completed', icon: CheckCircle,   classes: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
    cancelled: { label: 'Cancelled', icon: XCircle,       classes: 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400' },
};

function formatCurrency(amount: number) {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', minimumFractionDigits: 0 }).format(amount);
}

function formatDate(date: string) {
    return new Date(date).toLocaleString('en-GB', { dateStyle: 'medium', timeStyle: 'short' });
}

const currentStatus = statusConfig[props.enquiry.status] ?? statusConfig['pending'];
</script>

<template>
    <AdminLayout :title="`Enquiry — ${enquiry.reference}`">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-start justify-between">
                <div>
                    <a href="/admin/orders" class="flex items-center gap-1.5 text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 mb-2 transition-colors">
                        <ArrowLeft class="w-4 h-4" /> All Enquiries
                    </a>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 font-mono">{{ enquiry.reference }}</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Received {{ formatDate(enquiry.created_at) }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span :class="['flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium', currentStatus.classes]">
                        <component :is="currentStatus.icon" class="w-4 h-4" />
                        {{ currentStatus.label }}
                    </span>
                    <a :href="enquiry.whatsapp_url" target="_blank" rel="noopener noreferrer"
                        class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">
                        <MessageCircle class="w-4 h-4" /> Open WhatsApp
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Customer info -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
                    <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Customer</h2>
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-zinc-500 dark:text-zinc-400">Name</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ enquiry.customer_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-zinc-500 dark:text-zinc-400">Phone</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ enquiry.customer_phone ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-zinc-500 dark:text-zinc-400">Address</dt>
                            <dd class="text-zinc-700 dark:text-zinc-300">{{ enquiry.customer_address ?? '—' }}</dd>
                        </div>
                        <div v-if="enquiry.customer_notes">
                            <dt class="text-zinc-500 dark:text-zinc-400">Notes</dt>
                            <dd class="text-zinc-700 dark:text-zinc-300 italic">{{ enquiry.customer_notes }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Order summary -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
                    <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Order Summary</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-zinc-500 dark:text-zinc-400">Subtotal</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ formatCurrency(enquiry.subtotal) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-zinc-500 dark:text-zinc-400">Delivery</dt>
                            <dd class="text-zinc-700 dark:text-zinc-300">{{ enquiry.delivery_fee > 0 ? formatCurrency(enquiry.delivery_fee) : 'To be confirmed' }}</dd>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800">
                            <dt class="font-semibold text-zinc-900 dark:text-zinc-100">Total</dt>
                            <dd class="font-bold text-lg text-amber-600 dark:text-amber-400">{{ formatCurrency(enquiry.total) }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Status update -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
                    <h2 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Update Status</h2>
                    <form @submit.prevent="updateStatus" class="space-y-3">
                        <select v-model="statusForm.status"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm">
                            <option value="pending">Pending</option>
                            <option value="contacted">Contacted</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <button type="submit" :disabled="statusForm.processing"
                            class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-60">
                            {{ statusForm.processing ? 'Saving…' : 'Update Status' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Items table -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Order Items ({{ enquiry.items?.length ?? 0 }})</h2>
                </div>
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    <div v-for="item in enquiry.items" :key="item.key" class="px-5 py-4 flex gap-4 items-start">
                        <img v-if="item.image_url" :src="item.image_url" :alt="item.product_name"
                            class="w-14 h-14 object-cover rounded-lg border border-zinc-200 dark:border-zinc-700 flex-shrink-0" />
                        <div v-else class="w-14 h-14 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex-shrink-0 flex items-center justify-center text-zinc-400 text-xl">
                            🛍️
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ item.product_name }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ item.brand_name }}</p>
                            <div v-if="item.variant_title" class="text-xs text-zinc-600 dark:text-zinc-300 mt-0.5">
                                Variant: {{ item.variant_title }}
                            </div>
                            <div v-if="Object.keys(item.selected_options ?? {}).length > 0" class="flex flex-wrap gap-1.5 mt-1">
                                <span v-for="(val, key) in item.selected_options" :key="key"
                                    class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded text-xs">
                                    {{ key }}: {{ val }}
                                </span>
                            </div>
                            <div v-if="item.addons?.length > 0" class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                Extras: {{ item.addons.map(a => a.name).join(', ') }}
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ item.formatted_unit_price }} × {{ item.quantity }}</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ item.formatted_line_total }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WhatsApp message preview -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">WhatsApp Message Preview</h2>
                    <a :href="enquiry.whatsapp_url" target="_blank" rel="noopener noreferrer"
                        class="flex items-center gap-1.5 text-sm text-emerald-600 hover:text-emerald-700">
                        <ExternalLink class="w-4 h-4" /> Open Chat
                    </a>
                </div>
                <pre class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-lg p-4 text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap font-mono overflow-x-auto leading-relaxed">{{ enquiry.whatsapp_message }}</pre>
            </div>
        </div>
    </AdminLayout>
</template>
