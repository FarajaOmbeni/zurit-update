<template>
    <div class="">
        <div
            class="w-[500px] h-[300px] bg-white rounded-3xl shadow-lg overflow-hidden py-12 flex flex-col justify-center">
            <!-- Text Content -->
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-4">{{ title }}</h1>
            <p class="text-lg text-center text-gray-600 mb-6">{{ description }}</p>

            <!-- Button with gradient -->
            <a :href="buttonLink"
                class="w-fit mx-auto px-6 py-3 rounded-full bg-gradient-to-r from-purple-600 to-orange-400 text-white font-medium 
        transform transition-transform duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                {{ buttonText }}
            </a>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
        default: 'Goal Setting Made Simple'
    },
    description: {
        type: String,
        required: true,
        default: 'At Zurit, we believe in goal setting. Our tool is designed for easy navigation and user-friendly interfaces.'
    },
    buttonText: {
        type: String,
        required: true,
        default: 'Go to Goal Setting'
    },
    buttonLink: {
        type: String,
        required: true,
        default: '/'
    }
})

defineEmits(['click'])
</script>