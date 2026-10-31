<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Lock, Mail, ArrowRight, ShieldCheck } from 'lucide-vue-next';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('admin.login.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen bg-[#FAF8F5] dark:bg-[#0F1115] text-zinc-900 dark:text-zinc-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative">
        <Head title="Admin Portal Login" />

        <!-- Theme Toggle in top right -->
        <div class="absolute top-6 right-6">
            <ThemeToggle />
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center space-y-3">
            <div class="w-16 h-16 rounded-3xl bg-[#2D241E] dark:bg-[#C59A65] text-white dark:text-[#0F1115] flex items-center justify-center mx-auto shadow-md">
                <span class="font-display font-bold text-3xl">B</span>
            </div>
            <h2 class="font-display font-bold text-2xl sm:text-3xl tracking-tight text-zinc-900 dark:text-zinc-100">
                Biba Administrator
            </h2>
            <p class="text-xs text-zinc-500 uppercase tracking-widest font-semibold">
                Secure Storefront Management
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
            <div class="bg-white dark:bg-zinc-900 py-8 px-6 sm:px-10 rounded-3xl border border-[#EADFCF] dark:border-zinc-800 shadow-xl space-y-6">
                <!-- Errors -->
                <div v-if="form.errors.email" class="p-3 rounded-xl bg-rose-50 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 text-xs font-semibold">
                    {{ form.errors.email }}
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                            Email Address
                        </label>
                        <div class="relative">
                            <Mail class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                autofocus
                                placeholder="admin@biba.com"
                                class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                            />
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                            Password
                        </label>
                        <div class="relative">
                            <Lock class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                            <input
                                v-model="form.password"
                                type="password"
                                required
                                placeholder="••••••••"
                                class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-[#C59A65]"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="w-4 h-4 rounded text-[#A66E38] focus:ring-[#C59A65]"
                            />
                            <span class="text-zinc-600 dark:text-zinc-400">Remember session</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full mt-4 py-3 px-4 rounded-xl bg-[#2D241E] hover:bg-[#43362C] dark:bg-[#C59A65] dark:hover:bg-[#B3804A] text-white dark:text-[#0F1115] font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                    >
                        <span>Access Dashboard</span>
                        <ArrowRight class="w-4 h-4" />
                    </button>
                </form>

                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 text-center">
                    <p class="text-[11px] text-zinc-400 flex items-center justify-center gap-1">
                        <ShieldCheck class="w-3.5 h-3.5 text-emerald-600" />
                        <span>Authorized Biba staff access only</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
