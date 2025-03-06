<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import { ref, computed } from 'vue';
import BudgetBarChart from '@/Components/Shared/BudgetBarChart.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import { formatDate } from '@/Components/Composables/useDateFormat';
import { expenseCategories } from '@/Components/Variables/expenseCategories';
import { incomeCategories } from '@/Components/Variables/incomeCategories';

//ALERT USAGE LOGIC, FROM COMPOSABLE
const { alertState, openAlert, clearAlert } = useAlert();

//EDIT AND DELETE MODAL LOGIC
const showEditModal = ref(false);
const showDeleteModal = ref(false);

// Store the transaction selected for editing or deleting
const selectedTransaction = ref(null);
const incomes = ref(null);
const expenses = ref(null);

// Methods to open modals
const openEditModal = (transaction) => {
    selectedTransaction.value = { ...transaction };
    incomes.value = props.data.incomeCategories 
    expenses.value = props.data.expenseCategories 
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
})

// Methods to handle the modal actions
const saveEdit = () => {
    const transaction = selectedTransaction.value

    updateTransaction.category = transaction.category
    updateTransaction.description = transaction.description
    updateTransaction.amount = transaction.amount
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
            openAlert('success', 'Error Deleting Transaction', 5000)
        }
    })
    showDeleteModal.value = false;
};


//GETTING PROPS FROM CONTROLLER LOGIC
const props = defineProps({
    data: Object,
    currentMonth: String,
});
console.log("DATA", props.data)

//GETTING THE TOP INCOME AND EXPENSES
const TOP_N = 3;

const topIncomes = computed(() => {
    if (!props.data.incomes) return [];
    return [...props.data.incomes]
        .sort((a, b) => b.amount - a.amount)
        .slice(0, TOP_N)
        .map(income => ({
            amount: Math.round(income.amount),
            label: income.category,
            currency: income.currency || 'KES'
        }));
});

const topExpenses = computed(() => {
    if (!props.data.expenses) return [];
    return [...props.data.expenses]
        .sort((a, b) => b.amount - a.amount)
        .slice(0, TOP_N)
        .map(expense => ({
            amount: Math.round(expense.amount),
            label: expense.category,
            currency: expense.currency || 'KES'
        }));
});


//LOGIC TO GET TOTALS, ALSO SEE BAR GRAPH COMPONENT
// Compute total income by summing up all income amounts
const totalIncome = computed(() => {
    return props.data.incomes?.reduce((acc, income) => acc + parseFloat(income.amount), 0) || 0;
});
// Compute total expenses by summing up all expense amounts
const totalExpenses = computed(() => {
    return props.data.expenses?.reduce((acc, expense) => acc + parseFloat(expense.amount), 0) || 0;
});


const balance = computed(() =>
    totalIncome.value - totalExpenses.value
);

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

const selectedDebtDescription = computed(() => {
    if (newContribution.category === 'debt' && newContribution.debtId) {
        const selectedDebt = props.data.debts.find(debt => debt.id == newContribution.debtId);
        return selectedDebt ? selectedDebt.description : '';
    }
    return '';
});


const newContribution = useForm({
    amount: '',
})

const submitContribution = () => {
    newContribution.put(route('debt.contribute', newContribution.debtId), {
        onSuccess: () => {
            showContributeModal.value = false;
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


// Form data
const newIncome = useForm({
    category: '',
    amount: '',
    description: '',
    income_date: '',
});

const newExpense = useForm({
    category: '',
    amount: '',
    description: '',
    expense_date: '',
});

// Form submission handlers
const submitIncome = () => {
    newIncome.post(route('income.store'), {
        onSuccess: () => {
            showIncomeModal.value = false;
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
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h1 class="text-2xl font-semibold text-gray-900"> {{ currentMonth }}'s Budget</h1>
                        <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                            :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />

                        <div v-if="hasData" class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Income</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-green-600">KES {{
                                        totalIncome.toLocaleString() }}</dd>
                                </div>
                            </div>

                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Expenses</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-red-500">KES {{
                                        totalExpenses.toLocaleString() }}</dd>
                                </div>
                            </div>

                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <dt class="text-sm font-medium text-gray-500 truncate">Balance</dt>
                                    <dd class="mt-1 text-3xl font-semibold"
                                        :class="balance >= 0 ? 'text-green-600' : 'text-red-600'">
                                        KES {{ balance.toLocaleString() }}
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

                        <div v-if="hasData" class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                            <BudgetBarChart title="Top Income Sources" :items="topIncomes" type="income"
                                class="bg-white shadow rounded-lg" />

                            <BudgetBarChart title="Top Expenses" :items="topExpenses" type="expense"
                                class="bg-white shadow rounded-lg" />
                        </div>

                        <div class="mt-8">
                            <h2 class="text-lg font-medium text-gray-900">All Transactions</h2>
                            <div class="mt-4 bg-white shadow rounded-lg">
                                <ul class="divide-y divide-gray-200">
                                    <li v-for="transaction in data.transactions" :key="transaction.id"
                                        class="px-4 py-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ transaction.description }}
                                                </p>
                                                <p class="text-sm text-gray-500">{{
                                                    formatDate(transaction.transaction_date) }}</p>
                                            </div>
                                            <div
                                                class="flex flex-col  gap-2 md:flex-row md:gap-0 text-sm items-center space-x-2">
                                                <div :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'"
                                                    class="font-medium">
                                                    {{ transaction.type === 'income' ? '+' : '-' }} KES {{
                                                    Math.round(transaction.amount).toLocaleString() }}
                                                </div>
                                                <!-- Edit Button -->
                                                <button @click="openEditModal(
                                                    transaction)"
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
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ADD INCOME MODAL -->
                <div v-if="showIncomeModal" class="fixed inset-0 overflow-y-auto z-10 flex items-center justify-center">
                    <div class="fixed inset-0 bg-black bg-opacity-50" @click="showIncomeModal = false"></div>
                    <div class="relative bg-white rounded-lg max-w-md w-full mx-4 p-6 shadow-xl">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Add Income</h3>
                        <form @submit.prevent="submitIncome">
                            <div class="mb-4">
                                <label for="incomeCategory" class="block text-sm font-medium text-gray-700 mb-1">Income
                                    Type</label>
                                <select id="incomeCategory" v-model="newIncome.category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">Select Income Category</option>
                                    <option v-for="category in incomeCategories" :key="category.value" :value="category.label" >{{ category.label }}</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="incomeDescription" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea type="text" id="incomeDescription" v-model="newIncome.description"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    min="0" step="0.01" required></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="incomeAmount" class="block text-sm font-medium text-gray-700 mb-1">Amount
                                    (KES)</label>
                                <input type="number" id="incomeAmount" v-model="newIncome.amount"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    min="0" step="0.01" required />
                            </div>

                            <div class="mb-4">
                                <label for="incomeDate" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" id="incomeDate" v-model="newIncome.income_date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    min="0" step="0.01" required />
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" @click="showIncomeModal = false"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    {{ newIncome.processing ? 'Saving...':'Save Income' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ADD EXPENSE MODAL -->
                <div v-if="showExpenseModal"
                    class="fixed mr-16 sm:mr-0 inset-0 overflow-y-auto z-10 flex items-center justify-center">
                    <div class="fixed inset-0 bg-black bg-opacity-50" @click="showExpenseModal = false"></div>
                    <div class="relative bg-white rounded-lg max-w-md w-full mx-4 p-6 shadow-xl">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Add Expense</h3>
                        <form @submit.prevent="submitExpense">
                            <div class="mb-4">
                                <label for="expenseCategory" class="block text-sm font-medium text-gray-700 mb-1">Expense
                                    Type</label>
                                <select id="expenseCategory" v-model="newExpense.category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">Select Expense Type</option>
                                    <option v-for="category in expenseCategories" :value="category.label" :key="category.value">{{ category.label }}</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="expenseAmount" class="block text-sm font-medium text-gray-700 mb-1">Amount
                                    (KES)</label>
                                <input type="number" id="expenseAmount" v-model="newExpense.amount"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    min="0" step="0.01" required />
                            </div>

                            <div class="mb-4">
                                <label for="expenseDescription"
                                    class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="expenseDescription" v-model="newExpense.description" rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="expenseDate"
                                    class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" id="expenseDate" v-model="newExpense.expense_date" rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required />
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" @click="showContributeModal = true"
                                    class="px-4 py-2 border border-yellow-300 rounded-md shadow-sm text-sm font-medium text-yellow-700 bg-white hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Contribute
                                </button>
                                <button type="button" @click="showExpenseModal = false"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    {{ newExpense.processing ? 'Saving...':'Save Expense' }}
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
                                    readonly :value="selectedDebtDescription"></textarea>
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
                                            :value="category">
                                            {{ category.label }}
                                        </option>
                                    </select>
                                </div>

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
                                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">{{ updateTransaction.processing ? 'Saving...' : 'Submit' }}</button>
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
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">{{ confirmDelete.processing ? 'Deleting...':'Delete' }}</button>
                                <button @click="showDeleteModal = false"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded">Cancel</button>
                            </div>
                        </div>
                    </div>
                </template>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>