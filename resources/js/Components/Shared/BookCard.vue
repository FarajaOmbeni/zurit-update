<template>
    <div class="bg-white md:w-[20rem] p-4 rounded-lg shadow-md text-center max-w-sm border">
        <img :src="book.image" :alt="book.title" class="md:h-[25rem] object-cover rounded-md">
        <h2 class="mt-4 text-xl font-bold text-gray-900">{{ book.title }}</h2>
        <p class="text-gray-600 text-sm mt-2 line-clamp-2">{{ book.description }}</p>
        <p class="text-gray-900 font-semibold mt-2">Price: Kshs {{ book.price }}</p>
        <button @click="openModal"
            class="mt-4 bg-purple-600 text-white px-4 py-2 rounded-md text-sm hover:bg-purple-700 transition">
            Get a Copy
        </button>

        <!-- Modal Component -->
        <BookModal :show="isModalOpen" :book="book" @close="closeModal" :user="user"/>
    </div>
</template>

<script setup>
import { ref } from "vue";
import BookModal from "./BookModal.vue";

defineProps({
    book: Object,
    user: {
        type: Object,
        default: null
    }
});

const isModalOpen = ref(false);

const openModal = () => {
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};
</script>
