<template>
    <CoachSidebar :title="`Client: ${client.name}`">

        <Head :title="`${client.name} • Profile`" />
        <DashboardBackButton />
        <div class="space-y-6">
            <!-- Basic info -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-purple-800">{{ client.name }}</h2>
                        <p class="text-gray-600">{{ client.email }}</p>
                        <p class="text-gray-600" v-if="client.phone_number">{{ client.phone_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Net Worth</p>
                        <p :class="netWorth >= 0 ? 'text-green-600' : 'text-red-600'" class="text-2xl font-semibold">KES
                            {{ Math.round(netWorth).toLocaleString() }}</p>
                    </div>
                </div>
            </div>

            <!-- Budget Overview (by Month) -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                    <h3 class="text-xl font-bold text-purple-800">Budget Overview — {{ currentMonthLabel || 'N/A' }}
                    </h3>
                    <div class="w-full md:w-64">
                        <label for="monthSelect" class="block text-sm font-medium text-gray-700 mb-1">Select
                            Month</label>
                        <select id="monthSelect" v-model="currentMonthLabel" @change="selectMonth(currentMonthLabel)"
                            class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <option v-for="(monthName, index) in availableMonths" :key="index" :value="monthName">
                                {{ monthName }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-500">Total Income</div>
                        <div class="text-2xl font-semibold text-green-600">KES {{ monthlyIncome.toLocaleString() }}
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-500">Total Expenses</div>
                        <div class="text-2xl font-semibold text-red-600">KES {{ monthlyExpenses.toLocaleString() }}
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-500">Balance</div>
                        <div :class="monthlyBalance >= 0 ? 'text-green-600' : 'text-red-600'"
                            class="text-2xl font-semibold">KES {{ monthlyBalance.toLocaleString() }}</div>
                    </div>
                </div>
            </div>

            <!-- Net worth overview -->
            <div class="bg-white rounded-lg shadow p-6">
                <NetworthChart :assets="assetsBasic" :liabilities="liabilitiesBasic" />
            </div>

            <!-- Income vs Expense Trend (last 6 months) -->
            <div class="bg-white rounded-lg shadow p-6">
                <IncomeExpenseTrendChart :transactions="transactions" />
            </div>

            <!-- Two-column details: Goals and Debts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-bold text-purple-800 mb-4">Goals</h3>
                    <GoalTable v-if="goals && goals.length" :goals="goals" />
                    <div v-else class="text-gray-500">No goals yet.</div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-bold text-purple-800 mb-4">Debts</h3>
                    <DebtsTable :debts="debts" />
                </div>
            </div>

            <!-- Investments and Networth tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-bold text-purple-800 mb-4">Investments</h3>
                    <InvestmentChart :investments="investments" />
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <AssetsTable :assets="assetsBasic" />
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <LiabilitiesTable :liabilities="liabilitiesBasic" />
                    </div>
                </div>
            </div>
        </div>
    </CoachSidebar>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import CoachSidebar from '@/Components/CoachSidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import NetworthChart from '@/Components/Shared/NetworthChart.vue';
import DebtsTable from '@/Components/Shared/DebtsTable.vue';
import AssetsTable from '@/Components/Shared/AssetsTable.vue';
import LiabilitiesTable from '@/Components/Shared/LiabilitiesTable.vue';
import IncomeExpenseTrendChart from '@/Components/IncomeExpenseTrendChart.vue';
import GoalTable from '@/Components/Shared/GoalTable.vue';
import InvestmentChart from '@/Components/Shared/InvestmentChart.vue';

const props = defineProps({
    client: Object,
    incomes: Array,
    expenses: Array,
    transactions: Array,
    goals: Array,
    investments: Array,
    debts: Array,
    assetsBasic: Array,
    liabilitiesBasic: Array,
    netWorth: Number,
});

const { client, incomes, expenses, transactions, goals, investments, debts, assetsBasic, liabilitiesBasic, netWorth } = props;

// --- Month selection like in Budgets.vue ---
const currentMonthLabel = ref(null);
const availableMonths = ref([]);

function formatMonthYear(date) {
    const months = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    return `${months[date.getMonth()]} ${date.getFullYear()}`;
}

function getMonthIndex(monthName) {
    const months = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    return months.indexOf(monthName.split(' ')[0]);
}

function initializeMonths() {
    const months = {};
    (transactions || []).forEach(t => {
        if (!t.transaction_date) return;
        const d = new Date(t.transaction_date);
        if (isNaN(d.getTime())) return;
        months[formatMonthYear(d)] = true;
    });
    availableMonths.value = Object.keys(months).sort((a, b) => {
        const [ma, ya] = a.split(' ');
        const [mb, yb] = b.split(' ');
        return (parseInt(yb) - parseInt(ya)) || (getMonthIndex(mb) - getMonthIndex(ma));
    });
    if (availableMonths.value.length > 0) {
        selectMonth(availableMonths.value[0]);
    }
}

function selectMonth(label) {
    currentMonthLabel.value = label;
}

onMounted(() => {
    initializeMonths();
});

// Filtered transactions for selected month
const filteredTransactions = computed(() => {
    if (!currentMonthLabel.value) return [];
    return (transactions || []).filter(t => {
        const d = new Date(t.transaction_date);
        return formatMonthYear(d) === currentMonthLabel.value;
    });
});

// Monthly aggregates
const monthlyIncome = computed(() => filteredTransactions.value
    .filter(t => t.type === 'income')
    .reduce((sum, t) => sum + Number(t.amount || 0), 0));
const monthlyExpenses = computed(() => filteredTransactions.value
    .filter(t => t.type !== 'income')
    .reduce((sum, t) => sum + Number(t.amount || 0), 0));
const monthlyBalance = computed(() => monthlyIncome.value - monthlyExpenses.value);
</script>

<style scoped></style>
