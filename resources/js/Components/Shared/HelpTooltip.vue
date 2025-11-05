<template>
    <div class="relative inline-block">
        <button
            @click="showTooltip = !showTooltip"
            class="text-gray-400 hover:text-gray-600 transition-colors"
            :title="title"
        >
            <svg
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
        </button>

        <div
            v-if="showTooltip"
            class="absolute z-50 w-64 p-3 mt-2 text-sm text-white bg-gray-900 rounded-lg shadow-lg"
            :class="positionClass"
        >
            <div class="relative">
                <div class="font-medium text-white mb-1">{{ title }}</div>
                <div class="text-gray-300">{{ content }}</div>
                <div
                    class="absolute -top-1 left-4 w-2 h-2 bg-gray-900 transform rotate-45"
                ></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    content: {
        type: String,
        required: true,
    },
    position: {
        type: String,
        default: "bottom",
    },
});

const showTooltip = ref(false);

const positionClass = computed(() => {
    switch (props.position) {
        case "top":
            return "bottom-full mb-2";
        case "left":
            return "right-full mr-2 top-0";
        case "right":
            return "left-full ml-2 top-0";
        default:
            return "top-full mt-2";
    }
});
</script>
