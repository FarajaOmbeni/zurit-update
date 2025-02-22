<template>
    <div class="bg-white p-8 rounded-2xl shadow-xl text-center w-72 sm:w-80 md:w-96">
        <div class="flex justify-center">
            <img :src="image" :alt="title"
                class="h-32 sm:h-40 object-contain transition-transform duration-300 hover:scale-110" />
        </div>
        <h3 class="mt-6 text-2xl font-bold text-gray-900">{{ title }}</h3>
        <p class="mt-4 text-gray-700 text-base">{{ description }}</p>
    </div>
</template>

<script setup>
defineProps({
    image: String,
    title: String,
    description: String
})
</script>
