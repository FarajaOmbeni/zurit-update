<template>
    <div class="bg-white rounded-2xl shadow-lg p-4 border border-purple-200 text-center md:text-start">
        <div class="flex justify-center">
            <img :src="image" alt="Blog Image" class="object-cover rounded-lg w-[25rem] h-[16rem]">
        </div>
        <p class="text-gray-500 text-sm mt-2">Posted On: {{ date }}</p>
        <h2 class="text-xl font-semibold text-gray-800 mt-1 line-clamp-2">{{ title }}</h2>
        <p class="text-gray-600 mt-1 line-clamp-3">{{ content }}</p>
        <a :href="link" class="inline-block mt-3">
            <button
                class="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-yellow-400 text-white font-medium transition-transform duration-300 ease-in-out transform hover:scale-105 hover:shadow-lg">
                Read More
            </button>
        </a>
    </div>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
    image: String,
    title: String,
    content: String,
    link: String,
    date: String
});

</script>
