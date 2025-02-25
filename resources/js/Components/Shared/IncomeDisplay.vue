<template>
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-purple-700 mb-4">Income</h2>
        <div v-for="(item, index) in income" :key="index" class="mb-4">
            <p class="text-gray-700">{{ item.description }}: <span class="font-semibold text-yellow-600">${{
                item.actual_income }}</span></p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    income: {
        type: Array,
        required: true
    }
});
</script>