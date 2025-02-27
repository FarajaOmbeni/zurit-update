<!-- AssetsTable.vue -->
<template>
    <div class="w-full">
        <h2 class="text-xl font-bold mb-4">{{ title }}</h2>
        <table class="w-full border-collapse">
            <thead class="bg-purple-500 text-white">
                <tr>
                    <th class="py-2 px-4 border border-gray-300 text-left">Asset Name</th>
                    <th class="py-2 px-4 border border-gray-300 text-right">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(asset, index) in assets" :key="index" class="hover:bg-gray-50">
                    <td class="py-2 px-4 border border-gray-300">{{ asset.name }}</td>
                    <td class="py-2 px-4 border border-gray-300 text-right">{{ formatCurrency(asset.value) }}</td>
                </tr>
                <tr v-if="assets.length === 0">
                    <td class="py-2 px-4 border border-gray-300" colspan="2">No assets found</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="bg-gray-100 font-bold">
                    <td class="py-2 px-4 border border-gray-300">Total Assets</td>
                    <td class="py-2 px-4 border border-gray-300 text-right">{{ formatCurrency(totalValue) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    assets: {
        type: Array,
        default: () => []
    },
    currencyCode: {
        type: String,
        default: 'KES'
    },
    title: {
        type: String,
        default: 'Assets'
    }
});

const totalValue = computed(() => {
    return props.assets.reduce((total, asset) => total + Number(asset.value || 0), 0);
});

const formatCurrency = (amount) => {
    return `${props.currencyCode} ${Number(amount).toLocaleString()}`;
};

// Expose total for parent components
const emit = defineEmits(['update:totalAssets']);
emit('update:totalAssets', totalValue.value);
</script>