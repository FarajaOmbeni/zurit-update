<template>
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-purple-700 mb-4">Monthly Overview</h2>
        <canvas ref="chart"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import {Chart} from 'chart.js';

const props = defineProps({
    monthlyIncomes: {
        type: Object,
        required: true
    },
    monthlyExpenses: {
        type: Object,
        required: true
    }
});

const chart = ref(null);

onMounted(() => {
    const ctx = chart.value.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(props.monthlyIncomes),
            datasets: [
                {
                    label: 'Income',
                    data: Object.values(props.monthlyIncomes),
                    backgroundColor: 'rgba(128, 0, 128, 0.5)',
                },
                {
                    label: 'Expenses',
                    data: Object.values(props.monthlyExpenses),
                    backgroundColor: 'rgba(255, 223, 0, 0.5)',
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>