<template>
    <button @click="goBack"
        class="block w-full inline-flex items-center justify-start gap-2 px-4 py-2 mb-6 text-gray-600 hover:text-gray-800 transition-colors duration-200 hover:bg-gray-100 rounded-lg"
        :class="customClass">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                clip-rule="evenodd" />
        </svg>
        <span class="font-medium">{{ text }}</span>
    </button>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
    text: {
        type: String,
        default: 'Back'
    },
    customClass: {
        type: String,
        default: ''
    }
})

const goBack = () => {
    // Check if there's a previous page in the browser history
    if (window.history.length > 1) {
        window.history.back()
    } else {
        // Fallback to home page if no history
        window.location.href = '/'
    }
}
</script>
