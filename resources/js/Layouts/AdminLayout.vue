<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage, router, Head } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { 
    LayoutDashboard, 
    Package, 
    FolderTree, 
    MessageSquare, 
    Settings, 
    ExternalLink, 
    LogOut, 
    Menu, 
    X, 
    CheckCircle, 
    AlertCircle, 
    Info 
} from 'lucide-vue-next';

interface Props {
    title: string;
}

const props = defineProps<Props>();
const page = usePage<PageProps>();

const mobileSidebarOpen = ref(false);
const flash = computed(() => page.props.flash);
const user = computed(() => page.props.auth.user);

const logout = () => {
    router.post(route('admin.logout'));
};
</script>

<template>
    <div class="min-h-screen bg-zinc-100 dark:bg-[#0A0C10] text-zinc-900 dark:text-zinc-100 flex flex-col md:flex-row transition-colors duration-200">
        <Head :title="`${props.title} — Admin Portal`" />

        <!-- Mobile Header Bar -->
        <div class="md:hidden flex items-center justify-between p-4 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center gap-2">
                <span class="font-display font-bold text-lg text-zinc-900 dark:text-zinc-100">BIBA ADMIN</span>
            </div>
            <div class="flex items-center gap-2">
                <ThemeToggle />
                <button 
                    type="button" 
                    @click="mobileSidebarOpen = !mobileSidebarOpen" 
                    class="p-2 rounded-lg text-zinc-700 dark:text-zinc-300"
                >
                    <X v-if="mobileSidebarOpen" class="w-6 h-6" />
                    <Menu v-else class="w-6 h-6" />
                </button>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <aside 
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800 transform md:translate-x-0 transition-transform duration-200 ease-in-out flex flex-col justify-between shrink-0"
            :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:relative md:translate-x-0'"
        >
            <div class="p-6">
                <!-- Branding -->
                <div class="flex items-center justify-between pb-6 border-b border-zinc-200 dark:border-zinc-800">
                    <div>
                        <h2 class="font-display font-extrabold text-xl text-zinc-900 dark:text-zinc-100 tracking-wider">
                            BIBA
                        </h2>
                        <span class="text-[10px] uppercase font-bold tracking-widest text-[#A66E38] dark:text-[#C59A65]">
                            MANAGEMENT PORTAL
                        </span>
                    </div>
                    <ThemeToggle class="hidden md:inline-flex" />
                </div>

                <!-- Nav links -->
                <nav class="mt-6 space-y-1.5">
                    <Link
                        :href="route('admin.dashboard')"
                        @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-colors"
                        :class="route().current('admin.dashboard') ? 'bg-[#2D241E] text-white dark:bg-[#C59A65] dark:text-[#0F1115] shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                    >
                        <LayoutDashboard class="w-4 h-4" />
                        <span>Dashboard Overview</span>
                    </Link>

                    <Link
                        :href="route('admin.products.index')"
                        @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-colors"
                        :class="route().current('admin.products.*') ? 'bg-[#2D241E] text-white dark:bg-[#C59A65] dark:text-[#0F1115] shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                    >
                        <Package class="w-4 h-4" />
                        <span>Products & Variations</span>
                    </Link>

                    <Link
                        :href="route('admin.categories.index')"
                        @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-colors"
                        :class="route().current('admin.categories.*') ? 'bg-[#2D241E] text-white dark:bg-[#C59A65] dark:text-[#0F1115] shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                    >
                        <FolderTree class="w-4 h-4" />
                        <span>Categories</span>
                    </Link>

                    <Link
                        :href="route('admin.orders.index')"
                        @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-colors"
                        :class="route().current('admin.orders.*') ? 'bg-[#2D241E] text-white dark:bg-[#C59A65] dark:text-[#0F1115] shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                    >
                        <MessageSquare class="w-4 h-4" />
                        <span>Order Enquiries</span>
                    </Link>

                    <Link
                        :href="route('admin.settings.index')"
                        @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-colors"
                        :class="route().current('admin.settings.*') ? 'bg-[#2D241E] text-white dark:bg-[#C59A65] dark:text-[#0F1115] shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                    >
                        <Settings class="w-4 h-4" />
                        <span>Store Settings</span>
                    </Link>
                </nav>
            </div>

            <!-- Bottom User & Storefront Link -->
            <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 space-y-3">
                <a
                    :href="route('home')"
                    target="_blank"
                    class="flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                >
                    <span class="flex items-center gap-2">
                        <ExternalLink class="w-3.5 h-3.5" />
                        <span>View Storefront</span>
                    </span>
                    <span class="text-[10px] text-zinc-400">Live</span>
                </a>

                <div class="pt-2 flex items-center justify-between">
                    <div class="truncate max-w-[140px]">
                        <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 truncate">{{ user?.name || 'Admin' }}</p>
                        <p class="text-[10px] text-zinc-400 truncate">{{ user?.email }}</p>
                    </div>

                    <button
                        type="button"
                        @click="logout"
                        class="p-2 text-zinc-400 hover:text-rose-600 rounded-lg transition-colors"
                        title="Logout"
                    >
                        <LogOut class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Flash Message Banner in Admin -->
            <div v-if="flash.success || flash.error || flash.info" class="p-4 px-6">
                <div v-if="flash.success" class="p-3 rounded-xl bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
                    <CheckCircle class="w-4 h-4 text-emerald-600 shrink-0" />
                    <span>{{ flash.success }}</span>
                </div>
                <div v-if="flash.error" class="p-3 rounded-xl bg-rose-50 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 text-xs font-semibold flex items-center gap-2 mt-2">
                    <AlertCircle class="w-4 h-4 text-rose-600 shrink-0" />
                    <span>{{ flash.error }}</span>
                </div>
                <div v-if="flash.info" class="p-3 rounded-xl bg-blue-50 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300 text-xs font-semibold flex items-center gap-2 mt-2">
                    <Info class="w-4 h-4 text-blue-600 shrink-0" />
                    <span>{{ flash.info }}</span>
                </div>
            </div>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
