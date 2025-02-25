<template>
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-purple-700 mb-4">Expenses</h2>
        <div v-for="(item, index) in expenses" :key="index" class="mb-4">
            <p class="text-gray-700">{{ item.description }}: <span class="font-semibold text-yellow-600">${{
                item.actual_expense }}</span></p>
        </div>
        <div class="mt-6">
            <h3 class="text-xl font-bold text-purple-700 mb-2">Goals</h3>
            <p class="text-gray-700">Savings Goal: <span class="font-semibold text-yellow-600">${{ goals }}</span></p>
        </div>
        <div class="mt-6">
            <h3 class="text-xl font-bold text-purple-700 mb-2">Debts</h3>
            <p class="text-gray-700">Total Debt: <span class="font-semibold text-yellow-600">${{ debts }}</span></p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    expenses: {
        type: Array,
        required: true
    },
    goals: {
        type: Number,
        required: true
    },
    debts: {
        type: Number,
        required: true
    }
});
</script>