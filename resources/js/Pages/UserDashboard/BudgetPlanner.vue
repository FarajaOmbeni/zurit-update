<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import { ref, computed, onMounted, watch } from 'vue';
import BudgetBarChart from '@/Components/Shared/BudgetBarChart.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import { formatDate } from '@/Components/Composables/useDateFormat';
import { expenseCategories } from '@/Components/Variables/expenseCategories';
import { incomeCategories } from '@/Components/Variables/incomeCategories';

// ALERT USAGE LOGIC, FROM COMPOSABLE
const { alertState, openAlert, clearAlert } = useAlert();

// GETTING PROPS FROM CONTROLLER LOGIC
const props = defineProps({
    data: Object, // Contains all initial data: transactions, incomes, expenses, goals, debts, investments
    today: String,
});

// --- PRIMARY REACTIVE DATA SOURCE ---
// This will hold ALL transactions, and will be updated after CUD operations
const allTransactions = ref([]); // Initialize as empty, will be filled in onMounted

// --- MODAL LOGIC ---
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedTransaction = ref(null);

// Form for editing/deleting (can be unified)
const transactionForm = useForm({ // Renamed from updateTransaction for clarity
    category: '',
    description: '',
    amount: '',
    transaction_date: '',
    is_recurring: false,
});


// --- COMPUTED PROPERTIES ---
// These will automatically react to changes in `allTransactions.value`
const currentMonth = ref(null); // Will be set in initializeMonths
const availableMonths = ref([]);

const filteredTransactions = computed(() => {
    if (!currentMonth.value) return allTransactions.value; // Filter based on allTransactions

    return allTransactions.value.filter(transaction => {
        const transactionDate = new Date(transaction.transaction_date);
        const monthYear = formatMonthYear(transactionDate);
        return monthYear === currentMonth.value;
    });
});

const currentMontlyIncomes = computed(() => {
    // Filter filteredTransactions for income type for the selected month
    return filteredTransactions.value.filter(t => t.type === 'income');
});

const currentMontlyExpenses = computed(() => {
    // Filter filteredTransactions for non-income type for the selected month
    return filteredTransactions.value.filter(t => t.type !== 'income');
});

const monthlyIncome = computed(() => {
    return currentMontlyIncomes.value.reduce((sum, t) => sum + parseFloat(t.amount), 0);
});

const monthlyExpenses = computed(() => {
    return currentMontlyExpenses.value.reduce((sum, t) => sum + parseFloat(t.amount), 0);
});

const monthlyBalance = computed(() => monthlyIncome.value - monthlyExpenses.value);

const hasData = computed(() => {
    // Check if there's any data to display (e.g., any transactions exist)
    return allTransactions.value.length > 0;
});

//GETTING THE TOP INCOME AND EXPENSES (these also implicitly use currentMontlyIncomes/Expenses)
const topIncomes = computed(() => {
    const TOP_N = 3;
    const groupedIncomes = currentMontlyIncomes.value.reduce((acc, income) => {
        const category = income.category;
        if (!acc[category]) {
            acc[category] = { totalAmount: 0 };
        }
        acc[category].totalAmount += parseFloat(income.amount);
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
    const groupedExpenses = currentMontlyExpenses.value.reduce((acc, expense) => {
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
    if (!currentMontlyExpenses.value) return {};
    return currentMontlyExpenses.value
        .reduce((acc, expense) => {
            const category = expense.category;
            if (!acc[category]) {
                acc[category] = 0;
            }
            acc[category] += parseFloat(expense.amount);
            return acc;
        }, {});
});


// --- METHODS ---

// New function to re-fetch all transactions from the backend
const fetchAndInitializeData = async () => {
    try {
        // You'll need an endpoint that returns ALL transactions (or the initial data as you structured it)
        // This might mean making a separate Inertia request or ensuring your initial props.data is always fresh
        // For now, let's assume `props.data.transactions` is passed fresh on every page load.
        // If you need to re-fetch *after* a modal operation without a full page reload:
        // You'll need to define a route/API endpoint in your backend that returns the updated transactions.
        // For example: const response = await axios.get(route('transactions.all'));
        // allTransactions.value = response.data;

        // For now, just initialize `allTransactions` from `props.data.transactions` on mount
        // and ensure your update/delete operations cause a refresh to this props.data.transactions.
        // The *real* fix for full AJAX updates would be to use axios.get() here.
        allTransactions.value = props.data.transactions || [];

        initializeMonths(); // Re-initialize available months and select the current one
        // calculateMonthlySummary(); // This is implicitly called via computed properties now if currentMonth is set
    } catch (error) {
        console.error('Error fetching or initializing data:', error);
        openAlert('danger', 'Failed to load data.', 5000);
    }
};

// Modals Open
const openEditModal = (transaction) => {
    selectedTransaction.value = {
        ...transaction,
        transaction_date: transaction.transaction_date
            ? new Date(transaction.transaction_date).toISOString().split('T')[0]
            : '',
    };
    // No need for separate incomes/expenses refs here, use props.data directly for select options
    // incomes.value = props.data.incomeCategories; // Remove this line
    // expenses.value = props.data.expenseCategories; // Remove this line
    showEditModal.value = true;
};

const openDeleteModal = (transaction) => {
    selectedTransaction.value = transaction;
    showDeleteModal.value = true;
};


// Save Edit & Confirm Delete (Crucial Changes Here)
const saveEdit = () => {
    const transaction = selectedTransaction.value;

    transactionForm.category = transaction.category;
    transactionForm.description = transaction.description;
    transactionForm.amount = transaction.amount;
    transactionForm.is_recurring = transaction.is_recurring;
    transactionForm.transaction_date = transaction.transaction_date;

    const routeName = transaction.type === 'income' ? 'income.edit' : 'expense.edit';

    transactionForm.put(route(routeName, transaction.id), {
        onSuccess: () => {
            showEditModal.value = false;
            openAlert('success', 'Transaction Updated Successfully', 5000);
            // Instead of window.location.reload(), re-fetch data or update locally
            // This is the ideal place to update your `allTransactions` array
            // If `props.data.transactions` is truly reactive from Inertia, it might update.
            // If not, you need to explicitly re-fetch all data:
            fetchAndInitializeData(); // Call this to refresh all data after CUD operation
        },
        onError: (errors) => {
            openAlert('danger', Object.values(errors).flat().join(' '), 5000);
            console.error('Error editing transaction:', errors);
        }
    });
};

const confirmDelete = () => {
    const transaction = selectedTransaction.value;
    const routeName = transaction.type === 'income' ? 'income.destroy' : 'expense.destroy';

    transactionForm.delete(route(routeName, transaction.id), { // Use transactionForm here to send delete request
        onSuccess: () => {
            openAlert('warning', 'Transaction Deleted Successfully', 5000);
            showDeleteModal.value = false;
            fetchAndInitializeData();
        },
        onError: (errors) => {
            openAlert('danger', 'Error Deleting Transaction', 5000);
            console.error('Error deleting transaction:', errors);
        },
    });
};

// ADD INCOME AND EXPENSE LOGIC
const showIncomeModal = ref(false);
const showExpenseModal = ref(false);
const showBudgetModal = ref(false);

const isContribution = computed(() =>
    newExpense.category === 'debt' || newExpense.category === 'investment' || newExpense.category === 'goal');

const newIncome = useForm({
    category: '',
    otherCategory: '',
    amount: '',
    description: '',
    income_date: '',
    is_recurring: true,
});

const newExpense = useForm({
    category: '',
    otherCategory: '',
    amount: '',
    description: '',
    expense_date: '',
    is_recurring: true,
});

watch(
    () => [
        newExpense.category,
        newExpense.debtId,
        newExpense.goalId,
        newExpense.investmentId
    ],
    () => {
        if (newExpense.category === 'debt') {
            const d = props.data.debts.find(x => x.id == newExpense.debtId);
            newExpense.description = d ? d.description : '';
        } else if (newExpense.category === 'goal') {
            const g = props.data.goals.find(x => x.id == newExpense.goalId);
            newExpense.description = g ? g.description : '';
        } else if (newExpense.category === 'investment') {
            const i = props.data.investments.find(x => x.id == newExpense.investmentId);
            newExpense.description = i ? i.details_of_investment : '';
        } else {
            newExpense.description = ''; // regular expense, user will type
        }
    },
    { immediate: true } // Run immediately on component mount too
);

const submitIncome = () => {
    newIncome.post(route('income.store'), {
        onSuccess: () => {
            showIncomeModal.value = false;
            newIncome.reset();
            openAlert('success', 'Income added successfully', 5000);
            // Refresh data after successful creation
            fetchAndInitializeData(); // This will update all derived lists
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages, 5000);
        }
    });
};

const submitExpense = () => {
    if (newExpense.category === 'debt') {
        submitDebtContribution();
    } else if (newExpense.category === 'investment') {
        submitInvestmentContribution();
    } else if (newExpense.category === 'goal') {
        submitGoalContribution();
    } else {
        submitNewExpense();
    }
}

const submitNewExpense = () => {
    newExpense.post(route('expense.store'), {
        onSuccess: () => {
            showExpenseModal.value = false;
            newExpense.reset();
            openAlert('success', 'Expense added successfully', 5000);
            // Refresh data after successful creation
            fetchAndInitializeData(); // This will update all derived lists
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages, 5000);
        }
    });
}

// Consolidate contribution submissions if they mostly share the same onSuccess/onError
const handleContributionSuccess = () => {
    showExpenseModal.value = false;
    newExpense.reset();
    openAlert('success', 'Contribution Made Successfully.', 5000);
    fetchAndInitializeData(); // Refresh data
};

const handleContributionError = (errors) => {
    const errorMessages = Object.values(errors).flat().join(' ');
    openAlert('danger', errorMessages, 5000);
};

const submitDebtContribution = () => {
    newExpense.put(route('debt.contribute', newExpense.debtId), {
        onSuccess: handleContributionSuccess,
        onError: handleContributionError
    });
}

const submitInvestmentContribution = () => {
    newExpense.put(route('invest.contribute', newExpense.investmentId), {
        onSuccess: handleContributionSuccess,
        onError: handleContributionError
    });
}

const submitGoalContribution = () => {
    newExpense.put(route('goal.contribute', newExpense.goalId), {
        onSuccess: handleContributionSuccess,
        onError: handleContributionError
    });
}


// --- Helper Functions (keep as is) ---
function initializeMonths() {
    const months = {};
    allTransactions.value.forEach(transaction => { // Use allTransactions here
        const date = new Date(transaction.transaction_date);
        const monthYear = formatMonthYear(date);
        months[monthYear] = true;
    });

    availableMonths.value = Object.keys(months).sort((a, b) => {
        const [monthA, yearA] = a.split(' ');
        const [monthB, yearB] = b.split(' ');
        return yearB - yearA || getMonthIndex(monthB) - getMonthIndex(monthA);
    });

    if (availableMonths.value.length > 0) {
        selectMonth(availableMonths.value[0]);
    } else {
        // Handle case where there are no transactions yet
        currentMonth.value = new Date().toLocaleString('en-US', { month: 'long', year: 'numeric' });
        monthlyIncome.value = 0;
        monthlyExpenses.value = 0;
        monthlyBalance.value = 0;
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
    return months.indexOf(monthName.split(' ')[0]);
}

function selectMonth(monthName) {
    currentMonth.value = monthName;
}

// --- Lifecycle hooks ---
onMounted(() => {
    fetchAndInitializeData(); // Call this on mount to populate allTransactions
});

// IMPORTANT: Any modal component will need to be imported and placed in your <template> section
// e.g., <EditTransactionModal v-if="showEditModal" :transaction="selectedTransaction" @close="showEditModal = false" @saved="fetchAndInitializeData" />
// <DeleteConfirmationModal v-if="showDeleteModal" :transaction="selectedTransaction" @close="showDeleteModal = false" @confirmed="confirmDelete" />

monthlyBalance.value = monthlyIncome.value - monthlyExpenses.value;


const showBalances = ref(false)

// Pricing alert state
const showPricingAlert = ref(true)
</script>

<template>

    <Head title="Budget Planner" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <DashboardBackButton />
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div v-show="hasData" class="flex justify-between">
                            <div class="flex items-center justify-between mb-4 gap-2">
                                <h1 class="text-2xl font-semibold text-gray-900">
                                    {{ today }}'s Budget
                                </h1>
                                <!-- Toggle Button -->
                                <button @click="showBalances = !showBalances" class="text-gray-600 hover:text-gray-800">
                                    <svg v-if="!showBalances" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
           c4.477 0 8.268 2.943 9.542 7
           -1.274 4.057-5.065 7-9.542 7
           -4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19
           c-4.477 0-8.268-2.943-9.542-7
           a9.956 9.956 0 012.442-3.993" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6.1 6.1l11.8 11.8" />
                                    </svg>
                                </button>
                            </div>

                            <div class="px-6">
                                <button @click="showBudgetModal = true"
                                    class="w-full py-2 px-4 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition duration-150">
                                    View Budget
                                </button>
                            </div>
                        </div>
                        <!-- Pricing Alert -->
                        <Alert v-if="showPricingAlert" type="info" message="✨ Great news! You're on a 3-month free trial of our budgeting tools — no charges until December 1st.
After that, you can continue your journey for just KES 500/month or KES 4,500/year.

Start using the tools today and enjoy stress-free budgeting! 💡" :dismissible="true"
                            @close="showPricingAlert = false" />

                        <!-- Regular alerts from useAlert composable -->
                        <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                            :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />

                        <div v-if="hasData" class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

                            <!-- Total Income -->
                            <dd class="mt-1 text-3xl font-semibold text-green-600">
                                <span :class="{ 'blur-md': !showBalances }">
                                    KES {{ monthlyIncome.toLocaleString() }}
                                </span>
                            </dd>

                            <!-- Total Expenses -->
                            <dd class="mt-1 text-3xl font-semibold text-red-500">
                                <span :class="{ 'blur-md': !showBalances }">
                                    KES {{ monthlyExpenses.toLocaleString() }}
                                </span>
                            </dd>

                            <!-- Balance -->
                            <dd class="mt-1 text-3xl font-semibold"
                                :class="monthlyBalance >= 0 ? 'text-green-600' : 'text-red-600'">
                                <span :class="{ 'blur-md': !showBalances }">
                                    KES {{ monthlyBalance.toLocaleString() }}
                                </span>
                            </dd>


                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6 flex flex-col space-y-2">
                                    <button @click="showIncomeModal = true"
                                        class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition duration-150">
                                        Add Income
                                    </button>
                                    <button @click="showExpenseModal = true"
                                        class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition duration-150">
                                        Add Expense
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="!hasData" class="mt-6 text-center">
                            <p class="text-lg text-gray-600">You have no data available for this month.</p>
                            <div class="mt-4 flex justify-center space-x-2">
                                <button @click="showIncomeModal = true"
                                    class="py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition duration-150">
                                    Add Income
                                </button>
                                <button @click="showExpenseModal = true"
                                    class="py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition duration-150">
                                    Add Expense
                                </button>
                            </div>
                        </div>

                        <div class="my-4 w-full flex justify-center">
                            <a target="_blank" :href="route('budget.budgets')"
                                class="bg-purple-500 px-4 py-2 rounded-md text-white hover:bg-purple-600">View
                                Previous
                                Budgets</a>
                        </div>

                        <div v-if="hasData" class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                            <BudgetBarChart title="Top Income Sources" :items="topIncomes" type="income"
                                class="bg-white shadow rounded-lg" :showBalances="showBalances" />

                            <BudgetBarChart title="Top Expenses" :items="topExpenses" type="expense"
                                class="bg-white shadow rounded-lg" :showBalances="showBalances" />
                        </div>

                        <div v-show="hasData" class="mt-12">
                            <h2 class="text-xl font-bold text-gray-900 text-center">Your Income - {{ currentMonth }}
                            </h2>
                            <div class="mt-4 bg-white shadow rounded-lg">
                                <ul class="divide-y divide-gray-200">
                                    <li v-for="transaction in currentMontlyIncomes" :key="transaction.id"
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
                                                <button @click="openEditModal(transaction)"
                                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                                                    Edit
                                                </button>
                                                <button @click="openDeleteModal(transaction)"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                                    Delete
                                                </button>
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
                                                <!-- <p v-if="transaction.is_recurring === 'yes'"
                                                    class="text-sm text-gray-500">Monthly recurring expense</p> -->
                                            </div>
                                            <div
                                                class="flex flex-col gap-2 md:flex-row md:gap-0 text-sm items-center space-x-2">
                                                <div class="text-red-600 font-medium">
                                                    - KES {{ Math.round(parseFloat(amount)).toLocaleString()
                                                    }}
                                                </div>
                                                <!-- EDIT/DELETE BUTTONS FOR BUDGET/EXPENSES HERE -->
                                                <!-- You'll need to pass the full transaction object if you want to edit/delete from this view -->
                                                <!-- Since categorizedExpenses just has amount and category, you'd need to find the underlying transactions.
                                                    A better approach might be to iterate over currentMontlyExpenses directly and group for display.
                                                    Or, if 'categorizedExpenses' needs edit/delete, it should contain the 'transaction' itself, not just amount.
                                                    For now, I'm just enabling the buttons, but remember `transaction` won't be available here directly.
                                                    If you want to edit/delete individual expenses, you should iterate `currentMontlyExpenses` here too,
                                                    and maybe group them in the UI with a sub-list.
                                                    For simplicity of making it "like all transactions", I'm adding buttons here, assuming `transaction` can be derived.
                                                    However, `categorizedExpenses` doesn't provide individual transactions.
                                                    Therefore, I will only add the buttons in a comment to signify where they would go if you change how `categorizedExpenses` is structured.
                                                -->
                                                <!-- <button @click="openEditModal(transaction_from_this_category)"
                                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                                                    Edit
                                                </button>
                                                <button @click="openDeleteModal(transaction_from_this_category)"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                                    Delete
                                                </button> -->
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
                            <h2 class="text-xl font-bold text-center text-gray-900">All Transactions</h2>
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
                                                <!-- Edit Button -->
                                                <button @click="openEditModal(transaction)"
                                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                                                    Edit
                                                </button>
                                                <!-- Delete Button -->
                                                <button @click="openDeleteModal(transaction)"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                                    Delete
                                                </button>
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

                <!-- ADD INCOME MODAL -->
                <div v-if="showIncomeModal" class="fixed inset-0 overflow-y-auto z-10 flex items-center justify-center">
                    <div class="fixed inset-0 bg-black bg-opacity-50" @click="showIncomeModal = false"></div>
                    <div class="relative bg-white rounded-lg max-w-sm w-full mx-2 p-4 shadow-lg">
                        <h3 class="text-base font-semibold text-gray-900 mb-3">Add Income</h3>
                        <form @submit.prevent="submitIncome">
                            <div class="mb-3">
                                <label for="incomeCategory" class="block text-sm font-medium text-gray-700 mb-1">Income
                                    Type</label>
                                <select id="incomeCategory" v-model="newIncome.category"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">Select Income Category</option>
                                    <option v-for="category in incomeCategories" :key="category.value"
                                        :value="category.label">
                                        {{ category.label }}
                                    </option>
                                </select>
                            </div>

                            <div v-show="newIncome.category === 'Other'" class="mb-3">
                                <label for="incomeCategory" class="block text-sm font-medium text-gray-700 mb-1">Specify
                                    Income</label>
                                <input type="text" id="incomeCategory" v-model="newIncome.otherCategory"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :required="newIncome.category === 'Other'" />
                                <!-- Fix: newIncome.value was incorrect -->
                            </div>

                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Is Recurrent?</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center text-sm">
                                        <input type="radio" id="yes" name="is_recurring" :value="true"
                                            v-model="newIncome.is_recurring" />
                                        <!-- Removed checked, v-model handles it -->
                                        <span class="ml-1">Yes</span>
                                    </label>
                                    <label class="flex items-center text-sm">
                                        <input type="radio" id="no" name="is_recurring" :value="false"
                                            v-model="newIncome.is_recurring" />
                                        <!-- Removed checked, v-model handles it -->
                                        <span class="ml-1">No</span>
                                    </label>
                                </div>
                            </div>

                            <!-- <div v-show="newIncome.is_recurring === 'yes'" class="mb-3">
                                <label for="incomeCategory"
                                    class="block text-sm font-medium text-gray-700 mb-1">Recurrence
                                    Pattern</label>
                                <select type="text" id="incomeCategory" v-model="newIncome.recurrence_pattern"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :required="newIncome.is_recurring === 'yes'">
                                    <option value="daily">Daily</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quaterly">Quaterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div> -->

                            <div class="mb-3">
                                <label for="incomeDescription"
                                    class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="incomeDescription" v-model="newIncome.description"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="incomeAmount" class="block text-sm font-medium text-gray-700 mb-1">Amount
                                    (KES)</label>
                                <input type="number" id="incomeAmount" v-model="newIncome.amount"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    min="0" step="0.01" required />
                            </div>

                            <div class="mb-4">
                                <label for="incomeDate"
                                    class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" id="incomeDate" v-model="newIncome.income_date"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required />
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="showIncomeModal = false"
                                    class="px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </button>
                                <button type="submit" :disabled="newIncome.processing"
                                    class="px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                    :class="{ 'opacity-50 cursor-not-allowed': newIncome.processing }">
                                    {{ newIncome.processing ? 'Saving...' : 'Save Income' }}
                                </button>

                            </div>
                        </form>
                    </div>
                </div>


                <!-- ADD EXPENSE MODAL -->
                <div v-if="showExpenseModal"
                    class="fixed inset-0 overflow-y-auto z-10 flex items-center justify-center">
                    <div class="fixed inset-0 bg-black bg-opacity-50" @click="showExpenseModal = false"></div>
                    <div class="relative bg-white rounded-lg max-w-sm w-full mx-2 p-4 shadow-lg">
                        <h3 class="text-base font-semibold text-gray-900 mb-3">Add Expense</h3>
                        <form @submit.prevent="submitExpense">
                            <div class="mb-3">
                                <label for="expenseCategory"
                                    class="block text-sm font-medium text-gray-700 mb-1">Expense Type</label>
                                <select id="expenseCategory" v-model="newExpense.category"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">Select Expense Type</option>
                                    <option v-for="category in expenseCategories" :value="category.value"
                                        :key="category.value">
                                        {{ category.label }}
                                    </option>
                                </select>
                            </div>

                            <div v-show="newExpense.category === 'Other'" class="mb-3">
                                <label for="otherExpenseCategory"
                                    class="block text-sm font-medium text-gray-700 mb-1">Specify
                                    Expense</label>
                                <input type="text" id="otherExpenseCategory" v-model="newExpense.otherCategory"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :required="newExpense.category === 'Other'" />
                            </div>

                            <div v-show="!isContribution" class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Is Recurrent?</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center text-sm">
                                        <input type="radio" id="expenseRecurringYes" name="is_recurring_expense"
                                            :value="true" v-model="newExpense.is_recurring" />
                                        <span class="ml-1">Yes</span>
                                    </label>
                                    <label class="flex items-center text-sm">
                                        <input type="radio" id="expenseRecurringNo" name="is_recurring_expense"
                                            :value="false" v-model="newExpense.is_recurring" />
                                        <span class="ml-1">No</span>
                                    </label>
                                </div>
                            </div>

                            <div v-show="isContribution" class="mb-4">
                                <label for="contributionType"
                                    class="block text-sm font-medium text-gray-700 mb-1">Specify
                                    the contribution</label>
                                <select v-if="newExpense.category === 'debt'" id="contributionDebt"
                                    v-model="newExpense.debtId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">Select Contribution</option>
                                    <option v-for="debt in props.data.debts" :key="debt.id" :value="debt.id">
                                        {{ debt.name }}
                                    </option>
                                </select>

                                <select v-if="newExpense.category === 'goal'" id="contributionGoal"
                                    v-model="newExpense.goalId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">Select Contribution</option>
                                    <option v-for="goal in props.data.goals" :key="goal.id" :value="goal.id">
                                        {{ goal.name }}
                                    </option>
                                </select>

                                <select v-if="newExpense.category === 'investment'" id="contributionInvestment"
                                    v-model="newExpense.investmentId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">Select Contribution</option>
                                    <option v-for="investment in props.data.investments" :key="investment.id"
                                        :value="investment.id">
                                        {{ investment.details_of_investment }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="expenseAmount" class="block text-sm font-medium text-gray-700 mb-1">Amount
                                    (KES)</label>
                                <input type="number" id="expenseAmount" v-model="newExpense.amount"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    min="0" step="0.01" required />
                            </div>

                            <div class="mb-3">
                                <label for="expenseDescription"
                                    class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="expenseDescription" v-model="newExpense.description" rows="2"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required></textarea>
                            </div>

                            <div v-show="!isContribution" class="mb-4">
                                <label for="expenseDate"
                                    class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" id="expenseDate" v-model="newExpense.expense_date"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="showExpenseModal = false"
                                    class="px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    {{ newExpense.processing ? 'Saving...' : 'Save Expense' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- EDIT TRANSACTION MODAL -->
                <template v-if="showEditModal">
                    <div class="fixed mr-16 sm:mr-0 inset-0 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full max-w-xs sm:max-w-sm md:max-w-md">
                            <h3 class="text-lg font-bold mb-4">Edit Transaction</h3>
                            <form @submit.prevent="saveEdit">
                                <div class="mb-4">
                                    <label for="transactionCategoryEdit"
                                        class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select v-show="selectedTransaction.type === 'expense'"
                                        v-model="selectedTransaction.category"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                        <!-- Keep the current selected category as an option -->
                                        <option :value="selectedTransaction.category">{{ selectedTransaction.category }}
                                        </option>
                                        <option v-for="category in expenseCategories" :key="category.value"
                                            :value="category.label">
                                            {{ category.label }}
                                        </option>
                                    </select>

                                    <select v-show="selectedTransaction.type === 'income'"
                                        v-model="selectedTransaction.category"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                        <!-- Keep the current selected category as an option -->
                                        <option :value="selectedTransaction.category">{{ selectedTransaction.category }}
                                        </option>
                                        <option v-for="category in incomeCategories" :key="category.value"
                                            :value="category.label">
                                            {{ category.label }}
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Is Recurrent?</label>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center text-sm">
                                            <input type="radio" id="editRecurringYes" name="edit_is_recurring"
                                                :value="true" v-model="selectedTransaction.is_recurring" />
                                            <span class="ml-1">Yes</span>
                                        </label>
                                        <label class="flex items-center text-sm">
                                            <input type="radio" id="editRecurringNo" name="edit_is_recurring"
                                                :value="false" v-model="selectedTransaction.is_recurring" />
                                            <span class="ml-1">No</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- <div v-show="selectedTransaction.is_recurring === 'yes'" class="mb-3">
                                    <label for="editRecurrencePattern"
                                        class="block text-sm font-medium text-gray-700 mb-1">Recurrence
                                        Pattern</label>
                                    <select type="text" id="editRecurrencePattern" v-model="selectedTransaction.recurrence_pattern"
                                        class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        :required="selectedTransaction.is_recurring === 'yes'">
                                        <option value="daily">Daily</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="quaterly">Quaterly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div> -->

                                <div class="mb-4">
                                    <label for="editAmount" class="block text-sm font-medium text-gray-700 mb-1">Amount
                                        (KES)</label>
                                    <input type="number" id="editAmount" name="amount"
                                        v-model="selectedTransaction.amount"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        min="0" step="0.01" required />
                                </div>

                                <div class="mb-4">
                                    <label for="editDescription"
                                        class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <input type="text" id="editDescription" name="description"
                                        v-model="selectedTransaction.description"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        required />
                                </div>

                                <div class="mb-4">
                                    <label for="editDate"
                                        class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                    <input type="date" id="editDate" name="transaction_date"
                                        v-model="selectedTransaction.transaction_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        required />
                                </div>

                                <div class="flex justify-end space-x-2">
                                    <button type="submit"
                                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">{{
                                        transactionForm.processing ? 'Saving...' : 'Submit' }}</button>
                                    <!-- CHANGED: transactionForm.processing -->
                                    <button type="button" @click="showEditModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2
                                                rounded">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>


                <!-- DELETE TRANSACTION MODAL -->
                <template v-if="showDeleteModal">
                    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full max-w-xs sm:max-w-sm md:max-w-md">
                            <h3 class="text-lg font-bold mb-4">Confirm Delete</h3>
                            <p class="mb-4">Are you sure you want to delete this transaction?</p>
                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="confirmDelete"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">{{
                                    confirmDelete.processing ? 'Deleting...' : 'Delete' }}</button>
                                <button @click="showDeleteModal = false"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded">Cancel</button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- BUDGET MODAL -->
                <div v-if="showBudgetModal" class="fixed inset-0 overflow-y-auto z-10 flex items-center justify-center">
                    <div class="fixed inset-0 bg-black bg-opacity-50" @click="showBudgetModal = false"></div>
                    <div
                        class="relative bg-purple-900 text-white rounded-lg max-w-md w-full mx-4 p-4 shadow-xl border-2 border-yellow-500">
                        <h3 class="text-base font-semibold text-yellow-500 mb-3">Monthly Budget Overview</h3>

                        <!-- Categorized Expenses -->
                        <div class="space-y-2">
                            <div v-for="(amount, category) in categorizedExpenses" :key="category"
                                class="bg-purple-900 border border-purple-700 rounded-lg p-2 hover:bg-purple-800 transition-colors">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-xs font-medium text-white">{{ category }}</h4>
                                    <span class="text-xs font-semibold text-yellow-500">
                                        KES {{ amount.toLocaleString() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-900 border border-yellow-500 rounded-lg p-2 mt-2">
                            <div class="flex justify-between items-center">
                                <h4 class="text-sm font-medium text-white">TOTAL: </h4>
                                <span class="text-sm font-semibold text-yellow-500">
                                    KES {{ monthlyExpenses.toLocaleString() }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-3 flex justify-end space-x-2">
                            <!-- <button @click="downloadBudget"
                                class="px-3 py-1 border border-white rounded-md shadow-sm text-xs font-medium text-purple-900 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-900">
                                Download
                            </button> -->
                            <button @click="showBudgetModal = false"
                                class="px-3 py-1 border border-yellow-500 rounded-md shadow-sm text-xs font-medium text-purple-900 bg-yellow-500 hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-900">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>