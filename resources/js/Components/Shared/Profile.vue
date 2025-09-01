<template>
    <Sidebar>
        <div class="flex flex-col justify-center items-center mb-20 h-96">
            <a :href="linkedin_link" target="_blank">
                <div class="relative">
                    <img class="rounded-full cursor-pointer w-80 h-80 sm:w-64 sm:h-64 object-cover border-8 border-purple-600 shadow-2xl transition-transform duration-300 hover:scale-110"
                        :src="image" alt="User profile" />
                </div>
            </a>
            <div class="text-center mt-6 flex-1 flex flex-col justify-center">
                <p class="font-bold text-4xl text-gray-900">{{ name }}</p>
                <p class="italic text-lg text-gray-600">{{ position }}</p>
            </div>
        </div>
    </Sidebar>
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
