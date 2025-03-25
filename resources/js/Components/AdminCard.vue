<template>
    <div class="bg-purple-200 w-40 flex flex-col items-center gap-2 shadow rounded-lg p-6">
        <div class="mt-2 text-3xl font-bold text-gray-900">{{ number }}</div>
        <div class="text-2xl font-semibold text-gray-700">{{ title }}</div>
    </div>
</template>

<script setup>
const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    number: {
        type: Number,
        required: true,
    },
});
</script>