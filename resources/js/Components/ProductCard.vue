<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import type { Product } from '@/types';
import { Eye, Sparkles, Video, Image as ImageIcon, Volume2, VolumeX, Play, Pause } from 'lucide-vue-next';

interface Props {
    product: Product;
}

const props = defineProps<Props>();

const cardRef = ref<HTMLElement | null>(null);
const videoRef = ref<HTMLVideoElement | null>(null);
const isVideoActive = ref(false);
const isMuted = ref(true);
const isPlaying = ref(false);
let observer: IntersectionObserver | null = null;

const detailRoute = computed(() => {
    const catSlug = props.product.category?.slug;
    if (catSlug === 'confectionery') {
        return route('confectionery.show', props.product.slug);
    }
    return route('clothing.show', props.product.slug);
});

const brandLabel = computed(() =>
    props.product.category?.brand_name ||
    (props.product.category?.slug === 'confectionery' ? 'Biba Treats' : 'Biba Clothings')
);

const isConfectionery = computed(() => props.product.category?.slug === 'confectionery');

const videoSrc = computed(() => {
    if (props.product.primary_video_url) return props.product.primary_video_url;
    if (props.product.videos && props.product.videos.length > 0) return props.product.videos[0].url;
    return null;
});

const hasVideo = computed(() => !!videoSrc.value);

onMounted(() => {
    if (hasVideo.value) {
        isVideoActive.value = true;
        if (cardRef.value && 'IntersectionObserver' in window) {
            observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting && isVideoActive.value) {
                            playVideo();
                        } else {
                            pauseVideo();
                        }
                    });
                },
                { threshold: 0.15 }
            );
            observer.observe(cardRef.value);
        } else {
            playVideo();
        }
    }
});

onUnmounted(() => {
    if (observer) {
        observer.disconnect();
    }
    pauseVideo();
});

function playVideo() {
    if (videoRef.value) {
        videoRef.value.play().then(() => {
            isPlaying.value = true;
        }).catch(() => {
            isPlaying.value = false;
        });
    }
}

function pauseVideo() {
    if (videoRef.value) {
        videoRef.value.pause();
        isPlaying.value = false;
    }
}

function toggleMediaMode(e: Event) {
    e.preventDefault();
    e.stopPropagation();
    isVideoActive.value = !isVideoActive.value;
    if (isVideoActive.value) {
        playVideo();
    } else {
        pauseVideo();
    }
}

function toggleAudio(e: Event) {
    e.preventDefault();
    e.stopPropagation();
    isMuted.value = !isMuted.value;
    if (videoRef.value) {
        videoRef.value.muted = isMuted.value;
    }
}

const formatPrice = (val: number | string) => {
    const num = Number(val);
    return isNaN(num) ? '₦0' : '₦' + num.toLocaleString('en-NG', { maximumFractionDigits: 0 });
};
</script>

<template>
    <div
        ref="cardRef"
        class="group relative flex flex-col bg-[#F5F5F7] dark:bg-[#161617] rounded-2xl overflow-hidden border border-black/[0.04] dark:border-white/[0.08] hover:shadow-xl transition-all duration-300"
    >
        <!-- Media Area (Tappable Link to Details) -->
        <div class="relative aspect-[3/4] overflow-hidden bg-zinc-100 dark:bg-zinc-800/50 block">
            <Link :href="detailRoute" class="absolute inset-0 z-0">
                <!-- Static Image (Shown when photo mode or video loading) -->
                <img
                    v-if="product.primary_image_url"
                    :src="product.primary_image_url"
                    :alt="product.name"
                    class="w-full h-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-[1.03]"
                    :class="{ 'opacity-0': isVideoActive && isPlaying }"
                    loading="lazy"
                />
                <div v-else class="w-full h-full flex items-center justify-center bg-zinc-100 dark:bg-[#121214] p-4">
                    <img src="/images/logo.png" alt="Biba of Good Things" class="w-48 h-48 object-contain opacity-95 transition-transform duration-500 group-hover:scale-105" />
                </div>

                <!-- Video (Autoplays and loops when available) -->
                <video
                    v-if="hasVideo"
                    ref="videoRef"
                    :src="videoSrc"
                    autoplay
                    muted
                    loop
                    playsinline
                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                    :class="(isVideoActive && isPlaying) ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                    @playing="isPlaying = true"
                    @pause="isPlaying = false"
                ></video>
            </Link>

            <!-- Brand Badge (Apple Frosted Pill) -->
            <div class="absolute top-2.5 left-2.5 z-10 flex flex-col gap-1 pointer-events-none">
                <span
                    class="px-2.5 py-0.5 text-[9px] tracking-wider uppercase font-medium rounded-full backdrop-blur-md border border-white/10"
                    :class="isConfectionery
                        ? 'bg-black/60 text-white dark:bg-white/15 dark:text-zinc-200'
                        : 'bg-black/60 text-white dark:bg-white/15 dark:text-zinc-200'"
                >
                    {{ brandLabel }}
                </span>
                <span
                    v-if="product.is_featured"
                    class="px-2 py-0.5 text-[8px] font-medium uppercase tracking-wider rounded-full bg-white/90 text-black dark:bg-black/90 dark:text-white backdrop-blur-md flex items-center gap-1 w-fit"
                >
                    <Sparkles class="w-2.5 h-2.5 text-amber-500" /> Featured
                </span>
            </div>

            <!-- Sale Badge -->
            <div
                v-if="product.sale_price && product.sale_price < product.base_price"
                class="absolute top-2.5 right-2.5 z-10 px-2 py-0.5 bg-rose-600 text-white text-[9px] font-medium uppercase tracking-wider rounded-full pointer-events-none"
            >
                Sale
            </div>

            <!-- Video / Photo Switcher & Audio Controls (Directly Interactive) -->
            <div
                v-if="hasVideo"
                class="absolute bottom-2.5 left-2.5 right-2.5 z-10 flex items-center justify-between pointer-events-auto"
            >
                <!-- Video / Photo toggle button -->
                <button
                    type="button"
                    @click="toggleMediaMode"
                    class="px-2.5 py-1 rounded-full bg-black/60 hover:bg-black/80 text-white text-[10px] font-medium flex items-center gap-1.5 backdrop-blur-md transition-all active:scale-95 shadow-sm"
                    :title="isVideoActive ? 'Switch to photo' : 'Play video'"
                >
                    <template v-if="isVideoActive">
                        <ImageIcon class="w-3 h-3 text-zinc-300" />
                        <span>Photo</span>
                    </template>
                    <template v-else>
                        <Play class="w-3 h-3 text-white fill-white" />
                        <span>Video</span>
                    </template>
                </button>

                <!-- Audio toggle if video is active -->
                <button
                    v-if="isVideoActive && isPlaying"
                    type="button"
                    @click="toggleAudio"
                    class="p-1.5 rounded-full bg-black/60 hover:bg-black/80 text-white text-[10px] backdrop-blur-md transition-transform active:scale-95 shadow-sm"
                    :title="isMuted ? 'Unmute video' : 'Mute video'"
                >
                    <VolumeX v-if="isMuted" class="w-3.5 h-3.5 text-zinc-300" />
                    <Volume2 v-else class="w-3.5 h-3.5 text-white" />
                </button>
            </div>
        </div>

        <!-- Product Info (Apple Clean Layout) -->
        <div class="p-3.5 sm:p-4 flex flex-col flex-1 justify-between bg-white dark:bg-[#161617]">
            <div>
                <span class="text-[10px] font-medium uppercase tracking-widest text-zinc-400 dark:text-zinc-500 block">
                    {{ product.product_type || product.category?.name }}
                </span>
                <h3 class="font-medium text-xs sm:text-sm text-zinc-900 dark:text-white mt-1 line-clamp-2 leading-snug group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors">
                    <Link :href="detailRoute">
                        {{ product.name }}
                    </Link>
                </h3>
            </div>

            <!-- Price & Action -->
            <div class="mt-3 pt-2.5 border-t border-black/[0.04] dark:border-white/[0.06] flex items-center justify-between gap-1.5">
                <div class="min-w-0">
                    <div class="flex items-baseline gap-1.5 flex-wrap">
                        <span class="font-semibold text-xs sm:text-sm tabular-nums text-zinc-900 dark:text-white">
                            {{ formatPrice(product.effective_price) }}
                        </span>
                        <span
                            v-if="product.sale_price && product.sale_price < product.base_price"
                            class="text-[10px] text-zinc-400 line-through tabular-nums"
                        >
                            {{ formatPrice(product.base_price) }}
                        </span>
                    </div>
                </div>

                <Link
                    :href="detailRoute"
                    class="shrink-0 px-3 py-1 text-[11px] font-medium rounded-full bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200 transition-all active:scale-95"
                >
                    <span>View</span>
                </Link>
            </div>
        </div>
    </div>
</template>
