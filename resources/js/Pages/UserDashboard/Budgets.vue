<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import { ref, computed, onMounted, watch } from 'vue';
import BudgetBarChart from '@/Components/Shared/BudgetBarChart.vue';
import { formatDate } from '@/Components/Composables/useDateFormat';
import IncomeExpenseTrendChart from '@/Components/IncomeExpenseTrendChart.vue';
//GETTING PROPS FROM CONTROLLER LOGIC
const props = defineProps({
    data: Object,
    today: String,
});

// State for month selection
const currentMonth = ref(null);
const availableMonths = ref([]);

// Computed property for transactions filtered by the selected month
const filteredTransactions = computed(() => {
    if (!currentMonth.value) {
        // If no month is selected (e.g., initial state with no transactions), return empty array.
        // initializeMonths ensures currentMonth is set if transactions exist.
        return [];
    }
    return (props.data.transactions || []).filter(transaction => {
        const transactionDate = new Date(transaction.transaction_date);
        const monthYear = formatMonthYear(transactionDate); // Ensure formatMonthYear is robust
        return monthYear === currentMonth.value;
    });
});

// Corrected spelling: currentMonthlyIncomes
// Computed property for incomes of the selected month
const currentMonthlyIncomes = computed(() => {
    return filteredTransactions.value.filter(t => t.type === 'income');
});

// Corrected spelling: currentMonthlyExpenses
// Computed property for expenses of the selected month
const currentMonthlyExpenses = computed(() => {
    // Assuming expenses are transactions that are not of type 'income'
    // Adjust if you have a specific type for expenses, e.g., t.type === 'expense'
    return filteredTransactions.value.filter(t => t.type !== 'income');
});

// Computed properties for totals (these will replace the old refs)
const monthlyIncome = computed(() => {
    return currentMonthlyIncomes.value.reduce((sum, t) => sum + parseFloat(t.amount), 0);
});

const monthlyExpenses = computed(() => {
    return currentMonthlyExpenses.value.reduce((sum, t) => sum + parseFloat(t.amount), 0);
});

const monthlyBalance = computed(() => {
    return monthlyIncome.value - monthlyExpenses.value;
});

//GETTING THE TOP INCOME AND EXPENSES (These will now use the reactive currentMonthlyIncomes/Expenses)
const topIncomes = computed(() => {
    const TOP_N = 3;
    const monthlyIncomesData = currentMonthlyIncomes.value; // Uses the reactive computed property

    const groupedIncomes = monthlyIncomesData.reduce((acc, income) => {
        const category = income.category;
        if (!acc[category]) {
            acc[category] = { totalAmount: 0, incomes: [] };
        }
        acc[category].totalAmount += parseFloat(income.amount);
        acc[category].incomes.push(income);
        return acc;
    }, {});

    return Object.entries(groupedIncomes)
        .map(([category, { totalAmount }]) => ({
            label: category,
            amount: Math.round(totalAmount),
        }))
        .sort((a, b) => b.amount - a.amount)
        .slice(0, TOP_N);
});

const topExpenses = computed(() => {
    const TOP_N = 3;
    const monthlyExpensesData = currentMonthlyExpenses.value; // Uses the reactive computed property

    const groupedExpenses = monthlyExpensesData.reduce((acc, expense) => {
        const category = expense.category;
        if (!acc[category]) {
            acc[category] = [];
        }
        acc[category].push(expense);
        return acc;
    }, {});

    const sortedGroupedExpenses = Object.entries(groupedExpenses)
        .map(([category, expenses]) => ({
            category,
            totalAmount: expenses.reduce((sum, expense) => sum + parseFloat(expense.amount), 0),
            expenses
        }))
        .sort((a, b) => b.totalAmount - a.totalAmount)
        .slice(0, TOP_N);

    return sortedGroupedExpenses.map(group => ({
        label: group.category,
        amount: Math.round(group.totalAmount),
        expenses: group.expenses
    }));
});

const categorizedExpenses = computed(() => {
    // Ensure currentMonthlyExpenses.value exists and has items before reducing
    if (!currentMonthlyExpenses.value || currentMonthlyExpenses.value.length === 0) return {};

    return currentMonthlyExpenses.value
        .reduce((acc, expense) => {
            const category = expense.category;
            if (!acc[category]) {
                acc[category] = 0;
            }
            acc[category] += parseFloat(expense.amount);
            return acc;
        }, {});
});

//CHECK IF THERE IS DATA (This remains the same, based on initial props)
const hasData = computed(() => {
    return props.data && (
        (props.data.incomes && props.data.incomes.length) ||
        (props.data.expenses && props.data.expenses.length) ||
        (props.data.goals && props.data.goals.length) ||
        (props.data.debts && props.data.debts.length) ||
        (props.data.investments && props.data.investments.length)
    );
});

// Lifecycle hooks
onMounted(() => {
    initializeMonths();
});

// Methods
function initializeMonths() {
    const months = {};
    (props.data.transactions || []).forEach(transaction => {
        // Ensure transaction_date is valid before creating a Date object
        if (transaction.transaction_date) {
            const date = new Date(transaction.transaction_date);
            // Check if date is valid
            if (!isNaN(date.getTime())) {
                const monthYear = formatMonthYear(date);
                months[monthYear] = true;
            } else {
                console.warn('Invalid transaction_date:', transaction.transaction_date);
            }
        }
    });

    availableMonths.value = Object.keys(months).sort((a, b) => {
        const [monthA, yearA] = a.split(' ');
        const [monthB, yearB] = b.split(' ');
        // Ensure getMonthIndex handles potential issues if month names are unexpected
        return (parseInt(yearB) - parseInt(yearA)) || (getMonthIndex(monthB) - getMonthIndex(monthA));
    });

    if (availableMonths.value.length > 0) {
        selectMonth(availableMonths.value[0]);
    }
}

function formatMonthYear(date) {
    const months = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    return `${months[date.getMonth()]} ${date.getFullYear()}`;
}

function getMonthIndex(monthName) {
    const months = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    // Robustly get month name, e.g., "January" from "January 2023"
    return months.indexOf(monthName.split(' ')[0]);
}

function selectMonth(monthName) {
    currentMonth.value = monthName;
}
</script>

<template>

    <Head title="Budgets" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div v-show="hasData" class="flex justify-between">
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900"> {{ currentMonth }}'s Budget</h1>
                            </div>
                        </div>
                        <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                            :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />

                        <div v-if="hasData" class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Income</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-green-600">KES {{
                                        monthlyIncome.toLocaleString() }}</dd>
                                </div>
                            </div>

                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Expenses</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-red-500">KES {{
                                        monthlyExpenses.toLocaleString() }}</dd>
                                </div>
                            </div>

                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <dt class="text-sm font-medium text-gray-500 truncate">Balance</dt>
                                    <dd class="mt-1 text-3xl font-semibold"
                                        :class="monthlyBalance >= 0 ? 'text-green-600' : 'text-red-600'">
                                        KES {{ monthlyBalance.toLocaleString() }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                        <div class="my-4 w-[50%] flex flex-col items-center justify-center mx-auto">
                            <label for="monthSelect" class="block text-sm font-medium text-gray-700 mb-1">Select
                                Month</label>
                            <select id="monthSelect" v-model="currentMonth" @change="selectMonth(currentMonth)"
                                class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option v-for="(monthName, index) in availableMonths" :key="index" :value="monthName">
                                    {{ monthName }}
                                </option>
                            </select>
                        </div>

                        <div v-if="hasData" class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                            <BudgetBarChart title="Top Income Sources" :items="topIncomes" type="income"
                                class="bg-white shadow rounded-lg" />

                            <BudgetBarChart title="Top Expenses" :items="topExpenses" type="expense"
                                class="bg-white shadow rounded-lg" />
                        </div>

                        <div v-if="hasData" class="mt-8">
                            <IncomeExpenseTrendChart :transactions="props.data.transactions" />
                        </div>

                        <div v-show="hasData" class="mt-12">
                            <h2 class="text-xl font-bold text-gray-900 text-center">Your Income - {{ currentMonth }}
                            </h2>
                            <div class="mt-4 bg-white shadow rounded-lg">
                                <ul class="divide-y divide-gray-200">
                                    <li v-for="transaction in currentMonthlyIncomes" :key="transaction.id"
                                        class="px-4 py-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ transaction.category }} - {{ transaction.description }}
                                                </p>
                                            </div>
                                            <div
                                                class="flex flex-col gap-2 md:flex-row md:gap-0 text-sm items-center space-x-2">
                                                <div class="text-green-600 font-medium">
                                                    + KES {{ Math.round(parseFloat(transaction.amount)).toLocaleString()
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="px-4 py-3 bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">
                                                    TOTAL INCOME
                                                </p>
                                            </div>
                                            <div class="text-sm font-bold text-green-600">
                                                KES {{ monthlyIncome.toLocaleString() }}
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Montly Budget Section -->
                        <div v-show="hasData" class="mt-12">
                            <h2 class="text-xl font-bold text-gray-900 text-center">Your Budget - {{
                                currentMonth }}</h2>
                            <div class="mt-4 bg-white shadow rounded-lg">
                                <ul class="divide-y divide-gray-200">
                                    <li v-for="(amount, category) in categorizedExpenses" :key="category"
                                        class="px-4 py-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ category }}
                                                </p>
                                            </div>
                                            <div
                                                class="flex flex-col gap-2 md:flex-row md:gap-0 text-sm items-center space-x-2">
                                                <div class="text-red-600 font-medium">
                                                    - KES {{ Math.round(parseFloat(amount)).toLocaleString()
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="px-4 py-3 bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">
                                                    TOTAL MONTHLY BUDGET
                                                </p>
                                            </div>
                                            <div class="text-sm font-bold text-red-600">
                                                KES {{ monthlyExpenses.toLocaleString() }}
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div v-show="hasData" class="mt-12">
                            <h2 class="text-xl font-bold text-center text-gray-900">All Transactions - {{ currentMonth
                                }}</h2>
                            <div class="mt-4 bg-white shadow rounded-lg">
                                <!-- Transaction List -->
                                <ul class="divide-y divide-gray-200">
                                    <li v-if="filteredTransactions.length === 0"
                                        class="px-4 py-6 text-center text-gray-500">
                                        No transactions right now. Add the first transaction.
                                    </li>
                                    <li v-for="transaction in filteredTransactions" :key="transaction.id"
                                        class="px-4 py-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ transaction.description }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ formatDate(transaction.transaction_date) }}
                                                </p>
                                            </div>
                                            <div
                                                class="flex flex-col gap-2 md:flex-row md:gap-0 text-sm items-center space-x-2">
                                                <div :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'"
                                                    class="font-medium">
                                                    {{ transaction.type === 'income' ? '+' : '-' }} KES {{
                                                    Math.round(transaction.amount).toLocaleString() }}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>

                                <!-- Monthly Summary -->
                                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-700">{{ currentMonth }}
                                            Summary:</span>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-green-600">Income: KES {{
                                                monthlyIncome.toLocaleString() }}</p>
                                            <p class="text-sm font-medium text-red-600">Expenses: KES {{
                                                monthlyExpenses.toLocaleString() }}</p>
                                            <p class="text-sm font-bold"
                                                :class="monthlyBalance >= 0 ? 'text-green-600' : 'text-red-600'">
                                                Balance: KES {{ monthlyBalance.toLocaleString() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>