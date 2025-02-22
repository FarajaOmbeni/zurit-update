<template>
    <div class="max-w-7xl mx-auto p-6">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden p-8 flex flex-col md:flex-row gap-8 items-center">
            <!-- Text Content Section -->
            <div class="flex-1">
                <h2 class="text-2xl text-center md:text-start font-bold text-gray-800 mb-4">{{ title }}</h2>
                <p class="text-gray-600 text-lg leading-relaxed">
                    {{ description }}
                </p>
            </div>

            <!-- Image Section -->
            <div class="flex-1 flex justify-center items-center bg-purple-100 rounded-xl p-6">
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
        required: true,    },
    imageUrl: {
        type: String,
        required: true
    }
})
</script>