<script setup>
import { defineProps, ref, onMounted } from "vue";
import { Chart, registerables } from "chart.js";
import { Bar } from "vue-chartjs";

// Register required components
Chart.register(...registerables);

// Props for the chart
const props = defineProps({
    chartData: Object,
    chartOptions: Object
});

</script>

<template>
    <div>
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>
