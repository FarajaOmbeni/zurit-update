<!-- resources/js/Components/DebtsTable.vue -->
<template>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-purple-500 text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Debt Name</th>
                    <th class="px-4 py-2 text-right">Principal</th>
                    <th class="px-4 py-2 text-right">Amount Paid</th>
                    <th class="px-4 py-2 text-right">Minimum Payment</th>
                    <th class="px-4 py-2 text-right">Interest Rate</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <tr v-for="debt in activeDebts" :key="debt.id" class="border-t">
                    <td class="px-4 py-2">{{ debt.name }}</td>
                    <td class="px-4 py-2 text-right">{{ Math.round(debt.initial_amount).toLocaleString() }}</td>
                    <td class="px-4 py-2 text-right">{{ Math.round(debt.current_amount).toLocaleString() }}</td>
                    <td class="px-4 py-2 text-right">{{ Math.round(debt.minimum_payment).toLocaleString() }}</td>
                    <td class="px-4 py-2 text-right">{{ debt.interest_rate }}%</td>
                </tr>
            </tbody>
            <tfoot class="bg-gray-100 font-bold">
                <tr>
                    <td class="px-4 py-2">Totals</td>
                    <td class="px-4 py-2 text-right">KES {{ totalAmountDue.toLocaleString() }}</td>
                    <td class="px-4 py-2 text-right">KES {{ Math.round(totalAmountPaid).toLocaleString() }}</td>
                    <td class="px-4 py-2 text-right">KES {{ Math.round(totalMinimumPayment).toLocaleString() }}</td>
                    <td class="px-4 py-2 text-right">Avg. {{ averageInterestRate }}%</td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatCurrency } from '../Composables/useFormatCurrency';

const props = defineProps({
    debts: {
        type: Array,
        default: () => [],
    },
});

// Filter debts that are NOT "paid_off"
const activeDebts = computed(() => {
    return props.debts.filter(debt => debt.status !== 'paid_off');
});

// Sum of all initial_amount values for active debts
const totalAmountDue = computed(() => {
    return activeDebts.value.reduce((total, debt) => total + Number(debt.initial_amount || 0), 0);
});

// Sum of all current_amount values for active debts
const totalAmountPaid = computed(() => {
    return activeDebts.value.reduce((total, debt) => total + Number(debt.current_amount || 0), 0);
});

// Calculate total minimum payments for active debts from DB
const totalMinimumPayment = computed(() => {
    return activeDebts.value.reduce((total, debt) => total + Number(debt.minimum_payment || 0), 0);
});

// Calculate average interest rate for active debts
const averageInterestRate = computed(() => {
    if (activeDebts.value.length === 0) return 0;
    const totalInterest = activeDebts.value.reduce((total, debt) => total + Number(debt.interest_rate || 0), 0);
    return (totalInterest / activeDebts.value.length).toFixed(2);
});
</script>
