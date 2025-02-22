<template>
    <div class="flex flex-col justify-center items-center mb-20">
        <div class="relative">
            <img class="rounded-full w-80 h-80 sm:w-64 sm:h-64 object-cover border-8 border-purple-600 shadow-2xl transition-transform duration-300 hover:scale-110"
                :src="image" alt="User profile" />
        </div>
        <div class="text-center mt-6">
            <p class="font-bold text-4xl text-gray-900">{{ name }}</p>
            <p class="italic text-lg text-gray-600">{{ position }}</p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    name: String,
    position: String,
    image: String,
})
</script>
