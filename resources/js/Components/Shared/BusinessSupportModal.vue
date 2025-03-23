<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-1/2 lg:w-1/3 p-6 relative">
            <button @click="$emit('close')"
                class="absolute top-2 right-2 text-gray-600 hover:text-gray-800 text-2xl leading-none">
                &times;
            </button>
            <img v-if="service.imageUrl" :src="service.imageUrl" :alt="service.title"
                class="w-full h-48 object-cover rounded-t-lg mb-4" />
            <h2 class="text-2xl font-bold mb-2">{{ service.title }}</h2>
            <p class="text-gray-700 mb-4">{{ service.description }}</p>
            <p class="text-yellow-600 font-semibold mb-4">{{ service.price }}</p>
            <button @click="$emit('enroll')" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                Contact for Support
            </button>
        </div>
    </div>
</template>

<script setup>
defineProps({
    isOpen: Boolean,
    service: Object,
});
</script>
