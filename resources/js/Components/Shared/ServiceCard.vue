<template>
    <div class="bg-white border-lg border-red-500 shadow-lg rounded-lg overflow-hidden shadow-xl">
        <img :src="imageUrl" :alt="title" class="w-full h-[300px] object-cover" />
        <div class="p-4">
            <h3 class="text-xl font-bold mb-2">{{ title }}</h3>
            <p class="text-gray-600 mb-4">{{ description }}</p>
            <div class="flex justify-between items-center">
                <span class="text-yellow-600 font-bold">{{ price }}</span>
                <button @click="$emit('learn-more')"
                    class="bg-purple-900 text-white px-4 py-2 rounded hover:bg-yellow-700">
                    Learn More
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    imageUrl: String,
    title: String,
    description: String,
    price: String,
});
</script>
