<script setup lang="ts">
import { ref, computed } from 'vue';
import type { ProductMedia } from '@/types';
import { Play, Video, Volume2, VolumeX } from 'lucide-vue-next';

interface Props {
    media: ProductMedia[];
    productName: string;
}

const props = defineProps<Props>();

const activeIndex = ref(0);
const isMuted = ref(true);

const currentMedia = computed(() => {
    if (!props.media || props.media.length === 0) return null;
    return props.media[activeIndex.value] || props.media[0];
});

const selectMedia = (index: number) => {
    activeIndex.value = index;
};

const toggleMute = () => {
    isMuted.value = !isMuted.value;
};
</script>

<template>
    <div class="flex flex-col gap-4">
        <!-- Main Viewer -->
        <div class="relative aspect-[4/5] rounded-3xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 border border-[#EFE8DD] dark:border-zinc-800 shadow-md">
            <!-- Active is Video -->
            <div v-if="currentMedia?.media_type === 'video'" class="relative w-full h-full bg-black flex items-center justify-center">
                <video
                    :src="currentMedia.url"
                    controls
                    autoplay
                    loop
                    playsinline
                    :muted="isMuted"
                    class="w-full h-full object-cover"
                ></video>
                <!-- Mute toggle button -->
                <button
                    type="button"
                    @click="toggleMute"
                    class="absolute top-4 right-4 z-20 p-2 rounded-full bg-black/60 text-white hover:bg-black/80 transition-colors"
                    :title="isMuted ? 'Unmute' : 'Mute'"
                >
                    <VolumeX v-if="isMuted" class="w-4 h-4" />
                    <Volume2 v-else class="w-4 h-4" />
                </button>
            </div>

            <!-- Active is Image -->
            <img
                v-else-if="currentMedia?.media_type === 'image'"
                :src="currentMedia.url"
                :alt="currentMedia.alt_text || productName"
                class="w-full h-full object-cover object-center transition-all duration-300"
            />

            <!-- Empty State -->
            <div v-else class="w-full h-full flex items-center justify-center text-zinc-400">
                <span class="text-sm font-semibold uppercase tracking-wider">No media available</span>
            </div>
        </div>

        <!-- Thumbnails Strip -->
        <div v-if="media && media.length > 1" class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-thin">
            <button
                v-for="(item, idx) in media"
                :key="item.id || idx"
                type="button"
                @click="selectMedia(idx)"
                class="relative h-20 w-16 sm:h-24 sm:w-20 rounded-xl overflow-hidden shrink-0 border-2 transition-all focus:outline-none"
                :class="activeIndex === idx ? 'border-[#A66E38] dark:border-[#C59A65] ring-2 ring-[#C59A65]/30' : 'border-transparent opacity-70 hover:opacity-100'"
            >
                <!-- Image thumb -->
                <img
                    v-if="item.media_type === 'image'"
                    :src="item.url"
                    :alt="item.alt_text || productName"
                    class="w-full h-full object-cover"
                />

                <!-- Video thumb -->
                <div v-else class="w-full h-full bg-zinc-900 flex items-center justify-center relative">
                    <video :src="item.url" class="w-full h-full object-cover opacity-60"></video>
                    <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                        <Play class="w-5 h-5 text-white fill-white/80" />
                    </div>
                </div>

                <!-- Video Badge on Thumb -->
                <span
                    v-if="item.media_type === 'video'"
                    class="absolute bottom-1 right-1 p-0.5 rounded bg-black/80 text-white"
                >
                    <Video class="w-2.5 h-2.5" />
                </span>
            </button>
        </div>
    </div>
</template>
