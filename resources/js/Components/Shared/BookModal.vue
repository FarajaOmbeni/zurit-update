<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg relative">
            <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">
                ✕
            </button>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <img :src="book.image" :alt="book.title" class="w-[15rem] h-[22rem] rounded-md">
                </div>
                <div class=" flex flex-col justify-between">
                    <div>
                        <h1 class="text-xl font-bold">{{ book.title }}</h1>
                        <p class="text-md mb-2">{{ book.description }}</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <p class="text-lg font-semibold text-yellow-600">Price: KES {{ book.price }}</p>
                        <img src="/images/books/mpesa.webp" alt="Mpesa Details">
                        <a class="bg-yellow-600 font-bold text-white p-1 rounded hover:opacity-85 transition" href="https://dashboard.mailerlite.com/forms/1042116/153897443613738343/share"
                            target="_blank">Complete Payment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    show: Boolean,
    book: Object
});
</script>
