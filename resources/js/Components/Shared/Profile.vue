<template>
        <div class="flex flex-col justify-center items-center mb-8 h-64">
            <a :href="linkedin_link" target="_blank">
                <div class="relative">
                    <img class="rounded-full cursor-pointer w-32 h-32 sm:w-40 sm:h-40 object-cover border-4 border-purple-600 shadow-lg transition-transform duration-300 hover:scale-110"
                        :src="image" alt="User profile" />
                </div>
            </a>
            <div class="text-center mt-3 flex-1 flex flex-col justify-center">
                <p class="font-bold text-lg text-gray-900">{{ name }}</p>
                <p class="italic text-sm text-gray-600">{{ position }}</p>
            </div>
        </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue';
defineProps({
    name: String,
    position: String,
    image: String,
    linkedin_link: String,
})
</script>
