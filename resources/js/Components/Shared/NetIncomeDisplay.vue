<template>
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-purple-700 mb-4">Net Income</h2>
        <p class="text-gray-700">Net Income: <span class="font-semibold text-yellow-600">${{ netIncome }}</span></p>
    </div>
</template>

<script setup>
defineProps({
    netIncome: {
        type: Number,
        required: true
    }
});
</script>