<!-- resources/js/Components/DebtCard.vue -->
<template>
    <div class="bg-white shadow rounded-lg p-4 border-2 border-purple-500" :class="{'border-green-500': debt.status === 'paid_off',
        'text-green-500': debt.status === 'paid_off'
    }">
        <div class="flex justify-between items-center border-b pb-2 mb-2">
            <h3 class="text-lg font-semibold text-purple-700" :class="{
                'text-green-500': debt.status === 'paid_off'
            }">{{ debt.name }}</h3>
            <span class="px-2 py-1 text-xs font-bold uppercase rounded-full" :class="{
                'bg-yellow-400 text-black': debt.status === 'active',
                'bg-green-500 text-gray-100': debt.status === 'paid_off'
            }">
                {{ debt.status }}
            </span>
        </div>
        <div class="mb-2">
            <p class="text-gray-600" :class="{
                'text-green-500': debt.status === 'paid_off'
            }">{{ debt.description }}</p>
            <p class="mt-1"><strong>Paid:</strong> {{ formatCurrency(debt.current_amount) }}
            </p>
            <p class="mt-1"><strong>Balance:</strong> {{ formatCurrency(debt.initial_amount - debt.current_amount) }}
            </p>
            <div class="w-full bg-gray-300 rounded-full h-2.5 mt-2">
                <div class="h-2.5 rounded-full bg-green-400" :style="{ width: progressPercentage + '%' }"></div>
            </div>
        </div>
        <div class="text-right text-red-500 font-bold text-sm" :class="{'hidden':debt.status === 'paid_off'}">
            Due: {{ formatDate(debt.due_date) }}
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatDate } from '@/Components/Composables/useDateFormat';

const props = defineProps({
    debt: Object,
});

// Compute the repayment progress percentage based on initial and current amounts
const progressPercentage = computed(() => {
    if (props.debt.current_amount === props.debt.initial_amount) return 100;
    return Math.min(
        100,
        ((props.debt.current_amount * 100) / props.debt.initial_amount)
    );
});

// Simple currency formatter
const formatCurrency = (value) => {
    return 'KES ' + Math.round(value).toLocaleString();
};
</script>
