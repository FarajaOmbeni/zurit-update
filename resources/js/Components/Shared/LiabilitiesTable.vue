<!-- LiabilitiesTable.vue -->
<template>
    <div class="w-full">
        <h2 class="text-xl font-bold mb-4">{{ title }}</h2>
        <table class="w-full border-collapse">
            <thead class="bg-purple-500 text-white">
                <tr>
                    <th class="py-2 px-4 border border-gray-300 text-left">Liability Name</th>
                    <th class="py-2 px-4 border border-gray-300 text-right"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(liability, index) in liabilities" :key="index" class="hover:bg-gray-50">
                    <td class="py-2 px-4 border border-gray-300">{{ liability.name }}</td>
                    <td class="py-2 px-4 border border-gray-300 text-right">{{ formatCurrency(liability.amount) }}</td>
                </tr>
                <tr v-if="liabilities.length === 0">
                    <td class="py-2 px-4 border border-gray-300" colspan="2">No liabilities found</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="bg-gray-100 font-bold">
                    <td class="py-2 px-4 border border-gray-300">Total Liabilities</td>
                    <td class="py-2 px-4 border border-gray-300 text-right">{{ formatCurrency(totalAmount) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    liabilities: {
        type: Array,
        default: () => []
    },
    currencyCode: {
        type: String,
        default: 'KES'
    },
    title: {
        type: String,
        default: 'Liabilities'
    }
});

const totalAmount = computed(() => {
    return props.liabilities.reduce((total, liability) => total + Number(liability.amount || 0), 0);
});

const formatCurrency = (amount) => {
    return `${props.currencyCode} ${Number(amount).toLocaleString()}`;
};

// Expose total for parent components
const emit = defineEmits(['update:totalLiabilities']);
emit('update:totalLiabilities', totalAmount.value);
</script>