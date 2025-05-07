<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import { ref, computed, onMounted } from 'vue';
import BudgetBarChart from '@/Components/Shared/BudgetBarChart.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import { formatDate } from '@/Components/Composables/useDateFormat';
import { expenseCategories } from '@/Components/Variables/expenseCategories';
import { incomeCategories } from '@/Components/Variables/incomeCategories';

//ALERT USAGE LOGIC, FROM COMPOSABLE
const { alertState, openAlert, clearAlert } = useAlert();

//GETTING PROPS FROM CONTROLLER LOGIC
const props = defineProps({
    data: Object,
    today: String,
});


//EDIT AND DELETE MODAL LOGIC
const showEditModal = ref(false);
const showDeleteModal = ref(false);

// Store the transaction selected for editing or deleting
const selectedTransaction = ref(null);
const incomes = ref(null);
const expenses = ref(null);

const currentMontlyIncomes = ref(props.data.incomes || []);
const currentMontlyExpenses = ref(props.data.expenses || []);

//GETTING THE TOP INCOME AND EXPENSES
const topIncomes = computed(() => {
    const TOP_N = 3;
    // 1. Get all income transactions for the current month
    const monthlyIncomes = currentMontlyIncomes.value;
    
    // 2. Group incomes by category and sum their amounts
    const groupedIncomes = monthlyIncomes.reduce((acc, income) => {
        const category = income.category;
        if (!acc[category]) {
            acc[category] = { totalAmount: 0, incomes: [] };
        }
        acc[category].totalAmount += parseFloat(income.amount);
        acc[category].incomes.push(income);
        return acc;
    }, {});

    // 3. Convert grouped incomes to an array, sort, and slice
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
    // 1. Get all non-recurrent expense transactions for the current month
    const monthlyExpenses = currentMontlyExpenses.value;
    
    // 2. Group expenses by category
    const groupedExpenses = monthlyExpenses.reduce((acc, expense) => {
        const category = expense.category;
        if (!acc[category]) {
            acc[category] = [];
        }
        acc[category].push(expense);
        return acc;
    }, {});

    // 3. Sort, slice, and format
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

            // Initialize the category if it doesn't exist
            if (!acc[category]) {
                acc[category] = 0;
            }

            // Add the expense amount to the corresponding category
            acc[category] += parseFloat(expense.amount);

            return acc;
        }, {});
});

// Methods to open modals
const openEditModal = (transaction) => {
    // Create a deep copy of the transaction
    selectedTransaction.value = {
        ...transaction,
        // Ensure the date is in the correct format for date input (YYYY-MM-DD)
        transaction_date: transaction.transaction_date
            ? new Date(transaction.transaction_date).toISOString().split('T')[0]
            : '',
    };
    incomes.value = props.data.incomeCategories;
    expenses.value = props.data.expenseCategories;
    showEditModal.value = true;
};

const openDeleteModal = (transaction) => {
    selectedTransaction.value = transaction;
    showDeleteModal.value = true;
};

const updateTransaction = useForm({
    category: '',
    description: '',
    amount: '',
    transaction_date: '',
    is_recurring: false,
    // recurrence_pattern: ''
})

// Methods to handle the modal actions
const saveEdit = () => {
    const transaction = selectedTransaction.value

    updateTransaction.category = transaction.category
    updateTransaction.description = transaction.description
    updateTransaction.amount = transaction.amount
    updateTransaction.is_recurring = transaction.is_recurring
    // updateTransaction.recurrence_pattern = transaction.recurrence_pattern
    updateTransaction.transaction_date = transaction.transaction_date

    const routeName = transaction.type == 'income' ? 'income.edit' : 'expense.edit'

    updateTransaction.put(route(routeName, transaction.id), {
        onSuccess: () => {
            showEditModal.value = false
            openAlert('success', 'Transaction Updated Succesfully', 5000)
        },
        onError: (errors) => {
            openAlert('danger', errors, 5000)
            console.error('Error editing transaction:', errors)
        }
    })
};

const confirmDelete = () => {
    const transaction = selectedTransaction.value

    const routeName = transaction.type == 'income' ? 'income.destroy' : 'expense.destroy'

    updateTransaction.delete(route(routeName, transaction.id), {
        onSuccess: () => {
            openAlert('warning', 'Transaction Deleted Succesfully', 5000)
            showDeleteModal.value = false
        },
        onError: (errors) => {
            openAlert('danger', 'Error Deleting Transaction', 5000)
        }
    })
    showDeleteModal.value = false;
};

//CHECK IF THERE IS DATA
const hasData = computed(() => {
    return props.data && (
        (props.data.incomes && props.data.incomes.length) ||
        (props.data.expenses && props.data.expenses.length) ||
        (props.data.goals && props.data.goals.length) ||
        (props.data.debts && props.data.debts.length) ||
        (props.data.investments && props.data.investments.length)
    );
});


// ADD INCOME AND EXPENSE LOGIC
// Modal states
const showIncomeModal = ref(false);
const showExpenseModal = ref(false);
const showContributeModal = ref(false);
const showBudgetModal = ref(false);

const selectedDebtDescription = computed(() => {
    if (newContribution.category === 'debt' && newContribution.debtId) {
        const selectedDebt = props.data.debts.find(debt => debt.id == newContribution.debtId);
        return selectedDebt ? selectedDebt.description : '';
    }
    return '';
});

const selectedGoalDescription = computed(() => {
    if (newContribution.category === 'goal' && newContribution.goalId) {
        const selectedGoal = props.data.goals.find(goal => goal.id == newContribution.goalId);
        return selectedGoal ? selectedGoal.description : '';
    }
    return '';
});


const newContribution = useForm({
    amount: '',
})

const submitContribution = () => {
    if (newContribution.category == 'debt') {
        newContribution.put(route('debt.contribute', newContribution.debtId), {
            onSuccess: () => {
                showContributeModal.value = false;
                showExpenseModal.value = false;
                newContribution.reset();
                openAlert('success', 'Contribution Made Succesfully.', 5000)
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors)
                    .flat()
                    .join(' ');

                openAlert('danger', errorMessages, 5000);
            }
        });
    } else if (newContribution.category == 'goal') {
        newContribution.put(route('goal.contribute', newContribution.goalId), {
            onSuccess: () => {
                showContributeModal.value = false;
                showExpenseModal.value = false;
                newContribution.reset();
                openAlert('success', 'Contribution Made Succesfully.', 5000)
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors)
                    .flat()
                    .join(' ');

                openAlert('danger', errorMessages, 5000);
            }
        });
    } else {
        newContribution.put(route('invest.contribute', newContribution.investmentId), {
            onSuccess: () => {
                showContributeModal.value = false;
                showExpenseModal.value = false;
                newContribution.reset();
                openAlert('success', 'Contribution Made Succesfully.', 5000)
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors)
                    .flat()
                    .join(' ');

                openAlert('danger', errorMessages, 5000);
            }
        });
    }
}


// Form data
const newIncome = useForm({
    category: '',
    otherCategory: '',
    amount: '',
    description: '',
    income_date: '',
    is_recurring: true,
    // recurrence_pattern: ''
});

const newExpense = useForm({
    category: '',
    otherCategory: '',
    amount: '',
    description: '',
    expense_date: '',
    is_recurring: true,
    // recurrence_pattern: ''
});

// Form submission handlers
const submitIncome = () => {
    newIncome.post(route('income.store'), {
        onSuccess: () => {
            showIncomeModal.value = false;
            calculateMonthlySummary();
            newIncome.reset();
            openAlert('success', 'Income added successfully', 5000)
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');

            openAlert('danger', errorMessages, 5000);
        }
    });
};

const submitExpense = () => {
    newExpense.post(route('expense.store'), {
        onSuccess: () => {
            showExpenseModal.value = false;
            calculateMonthlySummary();
            newExpense.reset();
            openAlert('success', 'Expense added successfully', 5000)
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');

            openAlert('danger', errorMessages, 5000);
        }
    });
};


// State
const currentMonth = ref(null);
const availableMonths = ref([]);
const monthlyIncome = ref(0);
const monthlyExpenses = ref(0);
const monthlyBalance = ref(0);

// Computed
const filteredTransactions = computed(() => {
    if (!currentMonth.value) return props.data.transactions;

    return props.data.transactions.filter(transaction => {
        const transactionDate = new Date(transaction.transaction_date);
        const monthYear = formatMonthYear(transactionDate);
        return monthYear === currentMonth.value;
    });
});

// Lifecycle hooks
onMounted(() => {
    initializeMonths();
});

// Methods
function initializeMonths() {
    const months = {};

    // Extract all unique month-year combinations from transactions
    props.data.transactions.forEach(transaction => {
        const date = new Date(transaction.transaction_date);
        const monthYear = formatMonthYear(date);
        months[monthYear] = true;
    });

    // Convert to array and sort (most recent first)
    availableMonths.value = Object.keys(months).sort((a, b) => {
        const [monthA, yearA] = a.split(' ');
        const [monthB, yearB] = b.split(' ');
        return yearB - yearA || getMonthIndex(monthB) - getMonthIndex(monthA);
    });

    // Set default to most recent month
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
    return months.indexOf(monthName.split(' ')[0]);
}

function selectMonth(monthName) {
    currentMonth.value = monthName;
    calculateMonthlySummary();
}

function calculateMonthlySummary() {
    monthlyIncome.value = filteredTransactions.value
        .filter(t => t.type === 'income')
        .reduce((sum, t) => sum + parseFloat(t.amount), 0);

    monthlyExpenses.value = filteredTransactions.value
        .filter(t => t.type !== 'income')
        .reduce((sum, t) => sum + parseFloat(t.amount), 0);

    monthlyBalance.value = monthlyIncome.value - monthlyExpenses.value;
}
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div v-show="hasData" class="flex justify-between">
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900"> {{ today }}'s Budget</h1>
                            </div>
                            <div class="px-6">
                                <button @click="showBudgetModal = true"
                                    class="w-full py-2 px-4 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition duration-150">
                                    View Budget
                                </button>
                            </div>
                        </div>
                        <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                            :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />

                        <div v-if="hasData" class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
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
                                                <!-- <button @click="openEditModal(transaction)"
                                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                                                    Edit
                                                </button>
                                                <button @click="openDeleteModal(transaction)"
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
                                    :required="newIncome.value === 'other'" />
                            </div>

                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Is Recurrent?</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center text-sm">
                                        <input type="radio" id="yes" name="is_recurring" value="1"
                                            v-model="newIncome.is_recurring" checked/>
                                        <span class="ml-1">Yes</span>
                                    </label>
                                    <label class="flex items-center text-sm">
                                        <input type="radio" id="no" name="is_recurring" value="0"
                                            v-model="newIncome.is_recurring" />
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
                                <button type="submit"
                                    class="px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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
                                    <option v-for="category in expenseCategories" :value="category.label"
                                        :key="category.value">
                                        {{ category.label }}
                                    </option>
                                </select>
                            </div>

                            <div v-show="newExpense.category === 'Other'" class="mb-3">
                                <label for="incomeCategory" class="block text-sm font-medium text-gray-700 mb-1">Specify
                                    Expense</label>
                                <input type="text" id="incomeCategory" v-model="newExpense.otherCategory"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :required="newExpense.value === 'other'" />
                            </div>

                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Is Recurrent?</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center text-sm">
                                        <input type="radio" id="yes" name="is_recurring" value="1"
                                            v-model="newExpense.is_recurring" checked />
                                        <span class="ml-1">Yes</span>
                                    </label>
                                    <label class="flex items-center text-sm">
                                        <input type="radio" id="no" name="is_recurring" value="0"
                                            v-model="newExpense.is_recurring" />
                                        <span class="ml-1">No</span>
                                    </label>
                                </div>
                            </div>

                            <!-- <div v-show="newExpense.is_recurring === 'yes'" class="mb-3">
                                <label for="incomeCategory"
                                    class="block text-sm font-medium text-gray-700 mb-1">Recurrence
                                    Pattern</label>
                                <select type="text" id="incomeCategory" v-model="newExpense.recurrence_pattern"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :required="newExpense.is_recurring === 'yes'">
                                    <option value="daily">Daily</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quaterly">Quaterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div> -->

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

                            <div class="mb-4">
                                <label for="expenseDate"
                                    class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" id="expenseDate" v-model="newExpense.expense_date"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required />
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="showContributeModal = true"
                                    class="px-3 py-1.5 border border-yellow-300 rounded-md shadow-sm text-sm text-yellow-700 bg-white hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400">
                                    Contribute
                                </button>
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


                <!-- CONTRIBUTE MODAL  -->
                <div v-if="showContributeModal"
                    class="fixed mr-16 sm:mr-0 inset-0 overflow-y-auto z-10 flex items-center justify-center">
                    <div class="fixed inset-0 bg-black bg-opacity-50" @click="showContributeModal = false"></div>
                    <div class="relative bg-white rounded-lg max-w-md w-full mx-4 p-6 shadow-xl">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Contribute</h3>
                        <form @submit.prevent="submitContribution">
                            <div class="mb-4">
                                <label for="contributeType"
                                    class="block text-sm font-medium text-gray-700 mb-1">Contribute
                                    to</label>
                                <select id="contributeType"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    v-model="newContribution.category" required>
                                    <option value="">Select Contribution</option>
                                    <option value="goal">Goal</option>
                                    <option value="debt">Debt</option>
                                    <option value="investment">Investment</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="contributionType"
                                    class="block text-sm font-medium text-gray-700 mb-1">Specify
                                    the contribution</label>
                                <select v-if="newContribution.category === 'debt'" id="contributionType"
                                    v-model="newContribution.debtId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">Select Contribution</option>
                                    <option v-for="debt in data.debts" :key="debt.id" :value="debt.id">
                                        {{ debt.name }}
                                    </option>
                                </select>

                                <select v-if="newContribution.category === 'goal'" id="contributionType"
                                    v-model="newContribution.goalId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">Select Contribution</option>
                                    <option v-for="goal in data.goals" :key="goal.id" :value="goal.id">
                                        {{ goal.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="contribution_amount"
                                    class="block text-sm font-medium text-gray-700 mb-1">Amount
                                    (KES)</label>
                                <input type="number" id="contribution_amount" v-model="newContribution.amount"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    min="0" step="0.01" required />
                            </div>

                            <div class="mb-4">
                                <label for="expenseDescription"
                                    class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="expenseDescription" rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    readonly :value="selectedDebtDescription || selectedGoalDescription"></textarea>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" @click="showContributeModal = false"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    {{ newContribution.processing ? 'Contributing...' : 'Contribute' }}
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
                                    <label for="transactionType"
                                        class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select v-show="selectedTransaction.type === 'expense'"
                                        v-model="selectedTransaction.category"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        required>
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
                                            <input type="radio" id="yes" name="is_recurring" :value="true"
                                                v-model="selectedTransaction.is_recurring" 
                                                :checked="selectedTransaction.is_recurring == true"
                                                />
                                            <span class="ml-1">Yes</span>
                                        </label>
                                        <label class="flex items-center text-sm">
                                            <input type="radio" id="no" name="is_recurring" :value="false"
                                                v-model="selectedTransaction.is_recurring" 
                                                :checked="selectedTransaction.is_recurring == false"
                                                />
                                            <span class="ml-1">No</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- <div v-show="selectedTransaction.is_recurring === 'yes'" class="mb-3">
                                    <label for="incomeCategory"
                                        class="block text-sm font-medium text-gray-700 mb-1">Recurrence
                                        Pattern</label>
                                    <select type="text" id="incomeCategory" v-model="selectedTransaction.recurrence_pattern"
                                        class="w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        :required="selectedTransaction.recurrence_pattern === 'yes'">
                                        <option value="daily">Daily</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="quaterly">Quaterly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div> -->

                                <div class="mb-4">
                                    <label for="incomeAmount"
                                        class="block text-sm font-medium text-gray-700 mb-1">Amount
                                        (KES)</label>
                                    <input type="number" id="incomeAmount" name="amount"
                                        v-model="selectedTransaction.amount"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        min="0" step="0.01" required />
                                </div>

                                <div class="mb-4">
                                    <label for="transactionAmount"
                                        class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <input type="text" id="transactionAmount" name="description"
                                        v-model="selectedTransaction.description"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        required />
                                </div>

                                <div class="mb-4">
                                    <label for="transactionDate"
                                        class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                    <input type="date" id="transactionDate" name="transaction_date"
                                        v-model="selectedTransaction.transaction_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        required />
                                </div>

                                <div class="flex justify-end space-x-2">
                                    <button @click="saveEdit"
                                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">{{
                                            updateTransaction.processing ? 'Saving...' : 'Submit' }}</button>
                                    <button @click="showEditModal = false"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded">Cancel</button>
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
                                <button @click="confirmDelete"
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