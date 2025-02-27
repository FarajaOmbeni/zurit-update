<template>
    <div class="w-full max-w-4xl p-4 bg-white rounded-lg shadow-md border-2">
        <h2 class="text-xl font-bold text-center mb-4">Net Worth Overview</h2>

        <!-- Responsive layout: stack vertically on small screens -->
        <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4">
            <!-- Assets Bar -->
            <div class="w-full flex flex-col md:flex-row items-center">
                <div class="text-sm font-medium text-green-700 w-full md:w-1/4 text-center md:text-right pr-2">Assets
                </div>
                <div class="w-full md:w-1/2 bg-gray-200 rounded-sm h-8 md:h-8 relative my-2 md:my-0">
                    <div class="absolute left-0 h-full bg-green-500 rounded-sm transition-all duration-500"
                        :style="`width: ${calculatePercentage(totalAssets, maxValue)}%`"></div>
                </div>
                <div class="text-sm font-medium text-gray-700 pl-2 w-full md:w-1/4 text-center md:text-left">{{
                    formatCurrency(totalAssets) }}</div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4 mt-4">
            <!-- Liabilities Bar -->
            <div class="w-full flex flex-col md:flex-row items-center">
                <div class="text-sm font-medium text-red-700 w-full md:w-1/4 text-center md:text-right pr-2">Liabilities
                </div>
                <div class="w-full md:w-1/2 bg-gray-200 rounded-sm h-8 md:h-8 relative my-2 md:my-0">
                    <div class="absolute left-0 h-full bg-red-500 rounded-sm transition-all duration-500"
                        :style="`width: ${calculatePercentage(totalLiabilities, maxValue)}%`"></div>
                </div>
                <div class="text-sm font-medium text-gray-700 pl-2 w-full md:w-1/4 text-center md:text-left">{{
                    formatCurrency(totalLiabilities) }}</div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4 mt-4">
            <!-- Net Worth Bar -->
            <div class="w-full flex flex-col md:flex-row items-center">
                <div class="text-sm font-medium text-blue-700 w-full md:w-1/4 text-center md:text-right pr-2">Net Worth
                </div>
                <div class="w-full md:w-1/2 bg-gray-200 rounded-sm h-8 md:h-8 relative my-2 md:my-0">
                    <div class="absolute left-0 h-full transition-all duration-500 rounded-sm"
                        :class="{ 'bg-blue-500': netWorth > 0, 'bg-red-500': netWorth < 0 }"
                        :style="`width: ${calculatePercentage(Math.abs(netWorth), maxValue)}%`"></div>
                </div>
                <div class="text-sm font-medium text-gray-700 pl-2 w-full md:w-1/4 text-center md:text-left">{{
                    formatCurrency(netWorth) }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    assets: {
        type: Array,
        default: () => []
    },
    liabilities: {
        type: Array,
        default: () => []
    }
});

// Compute total assets and liabilities
const totalAssets = computed(() => {
    return props.assets.reduce((total, asset) => total + Number(asset.value || 0), 0);
});

const totalLiabilities = computed(() => {
    return props.liabilities.reduce((total, liability) => total + Number(liability.amount || 0), 0);
});

// Net worth calculation
const netWorth = computed(() => totalAssets.value - totalLiabilities.value);

// Calculate the max value for scaling the bars
const maxValue = computed(() => {
    return Math.max(totalAssets.value, totalLiabilities.value, Math.abs(netWorth.value));
});

// Calculate percentage for bar width
const calculatePercentage = (value, max) => {
    if (max === 0) return 0;
    return (value / max) * 100;
};

// Format currency
const formatCurrency = (amount) => {
    return `KES ${amount.toLocaleString()}`;
};
</script>