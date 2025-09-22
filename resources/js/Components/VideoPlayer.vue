<template>
    <div class="video-player-container">
        <div v-if="isVideo" class="w-full">
            <video
                :src="videoUrl"
                controls
                preload="metadata"
                class="w-full max-w-full rounded-lg shadow-lg"
                :poster="posterUrl"
                @loadstart="onLoadStart"
                @loadeddata="onLoadedData"
                @error="onError"
            >
                <source :src="videoUrl" :type="videoType" />
                Your browser does not support the video tag.
            </video>

            <div v-if="showTitle" class="mt-2 text-center">
                <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
                <p v-if="description" class="text-sm text-gray-600 mt-1">
                    {{ description }}
                </p>
            </div>
        </div>

        <div
            v-else-if="loading"
            class="flex items-center justify-center h-64 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg border border-purple-100"
        >
            <div class="text-center">
                <div
                    class="animate-spin mx-auto mb-4 w-10 h-10 text-purple-600"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                </div>
                <div class="space-y-2">
                    <p class="text-purple-700 font-medium">Loading video...</p>
                    <p class="text-purple-500 text-sm">
                        Please wait while we prepare your content
                    </p>
                </div>
            </div>
        </div>

        <div
            v-else-if="error"
            class="flex items-center justify-center h-64 bg-red-50 rounded-lg border border-red-200"
        >
            <div class="text-center">
                <div class="text-red-600 mb-2">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8 mx-auto"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
                <p class="text-red-800 font-medium">Error loading video</p>
                <p class="text-red-600 text-sm mt-1">{{ error }}</p>
                <button
                    @click="retry"
                    class="mt-2 px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700"
                >
                    Retry
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, defineProps } from "vue";

const props = defineProps({
    videoUrl: { type: String, required: true },
    title: { type: String, default: "" },
    description: { type: String, default: "" },
    posterUrl: { type: String, default: "" },
    showTitle: { type: Boolean, default: true },
    autoplay: { type: Boolean, default: false },
    muted: { type: Boolean, default: false },
});

const loading = ref(true);
const error = ref(null);

const isVideo = computed(() => {
    return props.videoUrl && !loading.value && !error.value;
});

const videoType = computed(() => {
    const url = props.videoUrl.toLowerCase();
    if (url.includes(".mp4")) return "video/mp4";
    if (url.includes(".webm")) return "video/webm";
    if (url.includes(".ogg")) return "video/ogg";
    return "video/mp4"; // default
});

const onLoadStart = () => {
    loading.value = true;
    error.value = null;
};

const onLoadedData = () => {
    loading.value = false;
    error.value = null;
};

const onError = (event) => {
    loading.value = false;
    error.value =
        "Failed to load video. Please check your internet connection and try again.";
    console.error("Video loading error:", event);
};

const retry = () => {
    loading.value = true;
    error.value = null;
    // Force reload by adding timestamp
    const video = document.querySelector("video");
    if (video) {
        video.load();
    }
};

onMounted(() => {
    if (!props.videoUrl) {
        loading.value = false;
        error.value = "No video URL provided";
    }
});
</script>

<style scoped>
.video-player-container {
    width: 100%;
}

video {
    max-height: 70vh;
    object-fit: contain;
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
