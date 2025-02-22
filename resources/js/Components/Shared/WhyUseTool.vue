<template>
    <div class="max-w-7xl mx-auto p-6">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden p-8 flex flex-col md:flex-row gap-8 items-center">
            <!-- Text Content Section -->
            <div class="flex-1">
                <h2 class="text-2xl text-center md:text-start font-bold text-gray-800 mb-4">{{ title }}</h2>
                <p class="text-gray-600 mb-4">{{ description }}</p>

                <!-- Bullet Points -->
                <ul class="space-y-2">
                    <li v-for="(point, index) in bulletPoints" :key="index"
                        class="flex items-start gap-2 text-gray-600">
                        <span class="text-orange-500 font-bold">•</span>
                        {{ point }}
                    </li>
                </ul>
            </div>

            <!-- Image Section -->
            <div class="flex-1 flex justify-center items-center bg-purple-50 rounded-xl p-6">
                <img :src="imageUrl" :alt="title" class="w-full h-auto max-w-md object-contain" />
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    bulletPoints: {
        type: Array,
        required: true,
    },
    imageUrl: {
        type: String,
        required: true
    }
})
</script>