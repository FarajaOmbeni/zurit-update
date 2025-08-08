<template>
    <Sidebar :title="`Client: ${client.name}`">

        <Head :title="`${client.name} • Profile`" />

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

            <!-- Budget Overview (This Month) -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-purple-800 mb-4">Budget Overview (This Month)</h3>
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
    </Sidebar>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
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

// Budget (current month) summary computed from transactions
const now = new Date();
const currentMonth = now.getMonth();
const currentYear = now.getFullYear();

function isCurrentMonth(dateStr) {
    if (!dateStr) return false;
    const d = new Date(dateStr);
    return d.getMonth() === currentMonth && d.getFullYear() === currentYear;
}

const monthlyTransactions = (transactions || []).filter(t => isCurrentMonth(t.transaction_date));
const monthlyIncome = monthlyTransactions
    .filter(t => t.type === 'income')
    .reduce((sum, t) => sum + Number(t.amount || 0), 0);
const monthlyExpenses = monthlyTransactions
    .filter(t => t.type !== 'income')
    .reduce((sum, t) => sum + Number(t.amount || 0), 0);
const monthlyBalance = monthlyIncome - monthlyExpenses;
</script>

<style scoped></style>
