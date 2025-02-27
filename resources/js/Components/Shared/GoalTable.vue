<!-- resources/js/Components/GoalsTable.vue -->
<template>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-purple-500 text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Goal Name</th>
                    <th class="px-4 py-2 text-left">Target Date</th>
                    <th class="px-4 py-2 text-right">Target Amount</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <tr v-for="goal in goals" :key="goal.goal_id" class="border-t">
                    <td class="px-4 py-2">{{ goal.name }}</td>
                    <td class="px-4 py-2">{{ formatDate(goal.target_date) }}</td>
                    <td class="px-4 py-2 text-right">{{ formatCurrency(goal.target_amount) }}</td>
                </tr>
            </tbody>
            <tfoot class="bg-gray-100 font-bold">
                <tr>
                    <td class="px-4 py-2">Totals</td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2 text-right">{{ formatCurrency(totalTargetAmount) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatDate } from '@/composables/useDateFormat'; // Your date helper
import { formatCurrency } from '@/composables/useCurrencyFormat'; // Your currency helper

const props = defineProps({
    goals: {
        type: Array,
        default: () => [],
    },
});

// Sum of all target_amount values
const totalTargetAmount = computed(() => {
    return props.goals.reduce((total, goal) => total + Number(goal.target_amount || 0), 0);
});
</script>
