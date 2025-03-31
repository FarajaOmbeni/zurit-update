<template>
    <div class="w-full p-4 bg-white rounded-lg shadow-md border-2">
        <h2 class="text-xl font-bold text-center mb-4">Add your first investment</h2>

        <div v-if="investmentItems"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <div v-for="(item, index) in investmentItems" :key="index" class="flex flex-col items-center justify-end">
                <div class="relative w-full mb-2 flex justify-center">
                    <div class="text-sm font-medium text-blue-600">
                        {{ item.currency }} {{ item.amount.toLocaleString() }}
                    </div>
                </div>
                <div class="w-1/2 bg-gray-200 rounded-sm">
                    <div class="w-full mx-auto rounded-sm flex justify-center items-end pt-1 bg-blue-500"
                        :style="`height: ${calculateHeight(item.amount)}px`"></div>
                </div>
                <div class="mt-2 text-sm font-medium text-gray-700">{{ item.label }}</div>
            </div>
        </div>
    </div>
</template>


<script setup>
import { computed } from 'vue';

const props = defineProps({
    investments: {
        type: Array,
        required: true
    },
    maxHeight: {
        type: Number,
        default: 200
    }
});

console.log(props.investments)

// Transform investments into chart items
const investmentItems = computed(() => {
    return props.investments.map(investment => ({
        label: investment.details_of_investment,
        amount: Math.round(investment.current_amount),
        currency: 'KES'
    }));
});

const maxAmount = computed(() => {
    if (investmentItems.value.length === 0) return 1;
    return Math.max(...investmentItems.value.map(item => item.amount));
});

const calculateHeight = (amount) => {
    const height = (amount / maxAmount.value) * props.maxHeight;
    return Math.max(height, 20); // Minimum height of 20px
};
</script>
