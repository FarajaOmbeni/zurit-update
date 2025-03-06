<template>
    <div class="w-full max-w-lg p-4 bg-white rounded-lg shadow-md border-2" :class="{ 'hidden': !props }">
        <h2 class="text-xl font-bold text-center mb-4">{{ title }}</h2>
        <!-- Modify how the bars should display -->
        <div v-if="items" class="grid grid-cols-3  gap-4">
            <div v-for="(item, index) in items" :key="index" class="flex flex-col items-center justify-end">
                <div class="relative w-full mb-2 flex justify-center">
                    <div class="text-sm font-medium text-blue-600">
                        {{ item.currency }} {{ item.amount.toLocaleString() }}
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-sm">
                    <div class="rounded-sm flex justify-center items-end pt-1"
                        :class="type === 'income' ? 'bg-green-500' : 'bg-red-500'"
                        :style="`height: ${calculateHeight(item.amount)}px`"></div>
                </div>
                <div class="mt-2 line-clamp-1 text-sm font-medium text-gray-700">{{ item.label }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Budget Data'
    },
    items: {
        type: Array,
        required: true,
        validator: (items) => {
            return items.every(item =>
                typeof item.amount === 'number' &&
                typeof item.label === 'string' &&
                typeof item.currency === 'string'
            );
        }
    },
    type: {
        type: String,
        default: 'expense',
        validator: (value) => ['income', 'expense'].includes(value)
    },
    maxHeight: {
        type: Number,
        default: 200
    }
});

const maxAmount = computed(() => {
    if (props.items.length === 0) return 1;
    return Math.max(...props.items.map(item => item.amount));
});

const calculateHeight = (amount) => {
    const height = (amount / maxAmount.value) * props.maxHeight;
    return Math.max(height, 20); // Minimum height of 20px
};
</script>