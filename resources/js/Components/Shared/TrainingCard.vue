<!-- TrainingCard.vue -->
<template>
    <div class="relative group">
        <!-- Image -->
        <img :src="imageUrl" :alt="title" class="w-full h-[30rem] object-cover rounded-lg" />

        <!-- Overlay Content -->
        <div class="absolute h-[10rem] bottom-0 left-0 right-0 p-4 bg-gray-900 rounded-b-lg">
            <h3 class="text-xl font-bold text-white mb-2">{{ title }}</h3>
            <p class="text-gray-200 text-sm mb-4 line-clamp-2">
                {{ description }}
            </p>
            <button @click="$emit('learn-more')"
                class="bg-amber-400 text-gray-900 px-4 py-2 rounded text-sm font-medium hover:bg-amber-500 transition-colors duration-200">
                Learn more
            </button>
        </div>
    </div>
</template>

<script setup>
defineProps({
    imageUrl: {
        type: String,
        required: true
    },
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    }
})

defineEmits(['learn-more'])
</script>