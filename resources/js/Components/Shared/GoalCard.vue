<!-- resources/js/Components/GoalCard.vue -->
<template>
    <div class="bg-white shadow rounded-lg p-4 border-2 border-purple-500">
        <div class="flex justify-between items-center border-b pb-2 mb-2">
            <h3 class="text-lg font-semibold text-purple-700">{{ goal.name }}</h3>
            <span class="px-2 py-1 text-xs font-bold uppercase rounded-full" :class="{
                'bg-yellow-400 text-black': goal.status === 'in_progress',
                'bg-purple-400 text-white': goal.status === 'achieved',
                'bg-gray-400 text-black': goal.status === 'abandoned'
            }">
                {{ goal.status }}
            </span>
        </div>
        <div class="mb-2">
            <p class="text-gray-600">{{ goal.description }}</p>
            <p class="mt-1"><strong>Saved:</strong> {{ formatCurrency(goal.current_amount) }}</p>
            <p class="mt-1"><strong>Remaining:</strong> {{ formatCurrency(goal.target_amount - goal.current_amount) }}
            </p>
            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                <div class="h-2.5 rounded-full bg-yellow-400" :style="{ width: progressPercentage + '%' }"></div>
            </div>
        </div>
        <div class="text-right text-sm text-gray-500">
            Target Date: {{ formatDate(goal.target_date) }}
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatDate } from '@/Components/Composables/useDateFormat';

const props = defineProps({
    goal: Object,
});

// Compute the progress percentage based on current_amount vs. target_amount
const progressPercentage = computed(() => {
    if (props.goal.target_amount === 0) return 0;
    return Math.min(100, (props.goal.current_amount / props.goal.target_amount) * 100);
});

// Simple currency formatter
const formatCurrency = (value) => {
    return 'KES ' + Math.round(value).toLocaleString();
};
</script>
