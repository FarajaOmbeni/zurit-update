<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from 'chart.js';

// Register Chart.js components
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

// Props
const props = defineProps({
    transactions: {
        type: Array,
        default: () => [],
    },
    // To determine the 'current' month to go back from.
    // Could also be derived from transactions if not passed.
    currentReferenceDate: {
        type: String, // Expecting YYYY-MM-DD or a parsable date string
        default: () => new Date().toISOString().split('T')[0], // Defaults to today
    }
});

// Chart instance ref
const chartRef = ref(null); // This will hold the chart instance itself
const chartData = ref({
    labels: [],
    datasets: [],
});

// Helper to format month-year string
function formatMonthYear(date) {
    const months = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    return `${months[date.getMonth()]} ${date.getFullYear()}`;
}

// Computed property to process transactions for the chart
const processedChartData = computed(() => {
    const endDate = new Date(props.currentReferenceDate);
    const monthlyData = {}; // Store income/expense totals for each of the last 6 months

    // Initialize last 6 months labels and data structures
    const labels = [];
    for (let i = 5; i >= 0; i--) { // Iterate from 5 months ago to current month
        const date = new Date(endDate);
        date.setMonth(endDate.getMonth() - i);
        date.setDate(1); // Set to first day of month to avoid day-of-month issues
        const monthYear = formatMonthYear(date);
        labels.push(monthYear);
        monthlyData[monthYear] = { income: 0, expense: 0 };
    }

    // Process transactions
    (props.transactions || []).forEach(transaction => {
        if (!transaction.transaction_date || !transaction.amount) return;

        const transactionDate = new Date(transaction.transaction_date);
        const monthYear = formatMonthYear(transactionDate);

        if (monthlyData.hasOwnProperty(monthYear)) {
            const amount = parseFloat(transaction.amount);
            if (transaction.type === 'income') {
                monthlyData[monthYear].income += amount;
            } else { // Assuming anything not 'income' is 'expense'
                monthlyData[monthYear].expense += amount;
            }
        }
    });

    const incomeValues = labels.map(label => Math.round(monthlyData[label]?.income || 0));
    const expenseValues = labels.map(label => Math.round(monthlyData[label]?.expense || 0));

    return {
        labels: labels,
        datasets: [
            {
                label: 'Income',
                backgroundColor: 'rgba(75, 192, 192, 0.5)', // Greenish
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1,
                data: incomeValues,
            },
            {
                label: 'Expenses',
                backgroundColor: 'rgba(255, 99, 132, 0.5)', // Reddish
                borderColor: 'rgb(255, 99, 132)',
                borderWidth: 1,
                data: expenseValues,
            },
        ],
    };
});

// Chart options
const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'Income vs Expenses (Last 6 Months)',
            font: {
                size: 16,
            }
        },
        tooltip: {
            callbacks: {
                label: function (context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'KES' }).format(context.parsed.y);
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function (value) {
                    return 'KES ' + value.toLocaleString();
                }
            }
        },
        x: {
            title: {
                display: true,
                text: 'Month'
            }
        }
    },
});


// Watch for changes in processed data and update chart
watch(processedChartData, (newData) => {
    chartData.value = newData;
    // If using vue-chartjs <Bar> component directly, it should react to chartData changes.
    // If managing ChartJS instance manually, you'd do:
    // if (chartRef.value) {
    //   chartRef.value.data = newData;
    //   chartRef.value.update();
    // }
}, { deep: true });

// Initial data load
onMounted(() => {
    chartData.value = processedChartData.value;
});

</script>

<template>
    <div class="p-4 bg-white shadow rounded-lg">
        <div v-if="transactions && transactions.length > 0" style="height: 400px;">
            <Bar :data="chartData" :options="chartOptions" ref="chartRef" />
        </div>
        <div v-else class="text-center text-gray-500 py-10">
            <p>No transaction data available to display the trend.</p>
        </div>
    </div>
</template>