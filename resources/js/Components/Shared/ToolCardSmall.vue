<template>
    <div class="bg-white p-4 rounded-xl shadow-lg text-center w-56 sm:w-64 md:w-72">
        <div class="flex justify-center">
            <img :src="image" :alt="title"
                class="h-20 sm:h-24 object-contain transition-transform duration-300 hover:scale-110" />
        </div>
        <h3 class="mt-3 text-lg font-bold text-gray-900">{{ title }}</h3>
        <p class="mt-2 text-gray-700 text-sm">{{ description }}</p>
    </div>
</template>

<script setup>
defineProps({
    image: String,
    title: String,
    description: String
})
</script>
