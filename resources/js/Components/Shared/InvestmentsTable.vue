<template>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-purple-500 text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Investment Name</th>
                    <th class="px-4 py-2 text-left">Type</th>
                    <th class="px-4 py-2 text-right">Current Amount</th>
                    <th class="px-4 py-2 text-right">Rate of Return</th>
                    <th class="px-4 py-2 text-right">Date of Maturity</th>
                    <th class="px-4 py-2 text-right">Net Income</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <tr v-for="investment in activeInvestments" :key="investment.id" class="border-t">
                    <td class="px-4 py-2">{{ formatDate(investment.start_date) }}</td>
                    <td class="px-4 py-2">{{ investment.name }}</td>
                    <td class="px-4 py-2">{{ investment.type }}</td>
                    <td class="px-4 py-2 text-right">{{ formatCurrency(investment.current_amount) }}</td>
                    <td class="px-4 py-2 text-right">{{ investment.expected_return_rate }}%</td>
                    <td class="px-4 py-2 text-right">{{ formatDate(investment.target_date) }}</td>
                    <td class="px-4 py-2 text-right">{{ formatCurrency(calculateNetIncome(investment)) }}</td>
                </tr>
            </tbody>
            <tfoot class="bg-gray-100 font-bold">
                <tr>
                    <td class="px-4 py-2">Totals</td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2 text-right">{{ formatCurrency(totalCurrentAmount) }}</td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2 text-right">{{ formatCurrency(totalNetIncome) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatCurrency } from '@/Components/Composables/useFormatCurrency'; // Helper function for currency formatting
import { formatDate } from '@/Components/Composables/useDateformat'; 

const props = defineProps({
    investments: {
        type: Array,
        default: () => [],
    },
});

// Filter investments that are NOT "sold" or "abandoned"
const activeInvestments = computed(() => {
    return props.investments.filter(investment => investment.status === 'active');
});

// Calculate net income (Current Amount * Rate of Return / 100)
const calculateNetIncome = (investment) => {
    return investment.current_amount * (investment.expected_return_rate / 100);
};

// Sum of all current_amount values for active investments
const totalCurrentAmount = computed(() => {
    return activeInvestments.value.reduce((total, investment) => total + Number(investment.current_amount || 0), 0);
});

// Sum of all net income values for active investments
const totalNetIncome = computed(() => {
    return activeInvestments.value.reduce((total, investment) => total + calculateNetIncome(investment), 0);
});
</script>
