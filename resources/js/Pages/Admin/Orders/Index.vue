<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import type { OrderEnquiry } from '@/types';
import { Eye, Search, Filter, MessageSquare, RefreshCw } from 'lucide-vue-next';

interface PaginatedEnquiries {
    data: OrderEnquiry[];
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

interface Props {
    enquiries: PaginatedEnquiries;
    filters: { search?: string; status?: string };
}
const props = defineProps<Props>();

const search = ref(props.filters.search ?? '');
const statusFilter = ref(props.filters.status ?? '');

function applyFilters() {
    router.get('/admin/orders', { search: search.value, status: statusFilter.value }, { preserveState: true, replace: true });
}

function clearFilters() {
    search.value = '';
    statusFilter.value = '';
    applyFilters();
}

const statusConfig: Record<string, { label: string; classes: string }> = {
    pending:   { label: 'Pending',   classes: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' },
    contacted: { label: 'Contacted', classes: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' },
    completed: { label: 'Completed', classes: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
    cancelled: { label: 'Cancelled', classes: 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400' },
};

function formatCurrency(amount: number) {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', minimumFractionDigits: 0 }).format(amount);
}

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
</script>

<template>
    <AdminLayout title="Order Enquiries">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Order Enquiries</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ enquiries.total }} total enquiries via WhatsApp</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex flex-wrap gap-3">
                    <div class="relative flex-1 min-w-48">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" />
                        <input v-model="search" type="text" placeholder="Search by reference, customer…"
                            class="w-full pl-9 pr-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm"
                            @keyup.enter="applyFilters" />
                    </div>
                    <select v-model="statusFilter" @change="applyFilters"
                        class="rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="contacted">Contacted</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <button @click="applyFilters"
                        class="flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium transition-colors">
                        <Filter class="w-4 h-4" /> Filter
                    </button>
                    <button v-if="search || statusFilter" @click="clearFilters"
                        class="flex items-center gap-2 px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <RefreshCw class="w-4 h-4" /> Clear
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div v-if="enquiries.data.length === 0" class="py-16 text-center">
                    <MessageSquare class="w-12 h-12 text-zinc-300 dark:text-zinc-600 mx-auto mb-3" />
                    <p class="text-zinc-500 dark:text-zinc-400">No enquiries yet.</p>
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Reference</th>
                                <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Customer</th>
                                <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Items</th>
                                <th class="text-right px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Total</th>
                                <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Date</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <tr v-for="enquiry in enquiries.data" :key="enquiry.id"
                                class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3 font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ enquiry.reference }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ enquiry.customer_name ?? '—' }}</div>
                                    <div v-if="enquiry.customer_phone" class="text-xs text-zinc-400">{{ enquiry.customer_phone }}</div>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                    {{ enquiry.items?.length ?? 0 }} item(s)
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ formatCurrency(enquiry.total) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span :class="['px-2 py-1 rounded-full text-xs font-medium', statusConfig[enquiry.status]?.classes ?? '']">
                                        {{ statusConfig[enquiry.status]?.label ?? enquiry.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-zinc-400">{{ formatDate(enquiry.created_at) }}</td>
                                <td class="px-4 py-3">
                                    <a :href="`/admin/orders/${enquiry.id}`"
                                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                        <Eye class="w-3.5 h-3.5" /> View
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="enquiries.last_page > 1" class="border-t border-zinc-200 dark:border-zinc-700 px-4 py-3 flex items-center justify-between">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Page {{ enquiries.current_page }} of {{ enquiries.last_page }} ({{ enquiries.total }} total)
                    </p>
                    <div class="flex gap-1">
                        <template v-for="link in enquiries.links" :key="link.label">
                            <a v-if="link.url" :href="link.url"
                                :class="['px-3 py-1.5 rounded-lg text-sm transition-colors', link.active
                                    ? 'bg-amber-600 text-white'
                                    : 'border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400']"
                                v-html="link.label">
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
