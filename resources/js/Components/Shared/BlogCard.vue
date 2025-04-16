<template>
    <div class="bg-white rounded-2xl shadow-lg p-4 border border-purple-200 text-center md:text-start">
        <div class="flex justify-center">
            <img :src="image" alt="Blog Image" :class="displaying ? 'w-[25rem] h-[16rem]' : ''" class="object-cover rounded-lg">
        </div>
        <p class="text-gray-500 text-sm mt-2">Posted On: {{ date }}</p>
        <h2 class="text-xl font-semibold text-gray-800 mt-1 line-clamp-2">{{ title }}</h2>
        <div v-html="content" :class="clamped ? 'line-clamp-3' : ''" class="text-gray-600 mt-1 leading-loose"></div>
        <a :href="link" class="inline-block mt-3">
            <button v-if="readmore"
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
    date: String,
    clamped: Boolean,
    displaying: Boolean,
    readmore: {
        typr: Boolean,
        default: true
    },
});

</script>