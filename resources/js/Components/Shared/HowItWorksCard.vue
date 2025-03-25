<template>
    <div class="p-6 flex">
        <!-- Fixed width and height container -->
        <div
            class="w-[500px] h-[600px] lg:w-[300px] lg:h-[500px] xl:w-[400px] bg-white rounded-3xl shadow-lg overflow-hidden p-8 flex flex-col">
            <!-- Text content container with fixed height -->
            <div class="h-[200px] flex flex-col justify-center">
                <h2 class="text-2xl md:text-xl font-bold text-center text-gray-800 mb-3">{{ title }}</h2>
                <p class="text-lg px-8 sm:px-0 text-center text-gray-600">{{ description }}</p>
            </div>

            <!-- Image container with fixed height and centered content -->
            <div class="flex-1 flex justify-center items-center bg-gray-50 rounded-xl p-6 mt-12">
                <img :src="imageUrl" :alt="title" class="max-w-full max-h-full object-contain" />
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
        default: 'Step 1: Sign Up'
    },
    description: {
        type: String,
        required: true,
        default: 'Get started by signing up for goal setting. It\'s quick and easy!'
    },
    imageUrl: {
        type: String,
        required: true
    }
})
</script>