<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import { ref, computed } from 'vue';
import BudgetBarChart from '@/Components/Shared/BudgetBarChart.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';

//ALERT USAGE LOGIC, FROM COMPOSABLE
const { alertState, openAlert, clearAlert } = useAlert();

//EDIT AND DELETE MODAL LOGIC
const showEditModal = ref(false);
const showDeleteModal = ref(false);

// Store the transaction selected for editing or deleting
const selectedTransaction = ref(null);

// Methods to open modals
const openEditModal = (transaction) => {
    // Create a shallow copy if you plan on editing the values
    selectedTransaction.value = { ...transaction };
    showEditModal.value = true;
};

const openDeleteModal = (transaction) => {
    selectedTransaction.value = transaction;
    showDeleteModal.value = true;
};

const form = useForm({
    category: '',
    description: '',
    amount: ''
})

// Methods to handle the modal actions
const saveEdit = () => {
    const transaction = selectedTransaction.value

    form.category = transaction.category
    form.description = transaction.description
    form.amount = transaction.amount

    const routeName = transaction.type == 'income' ? 'income.edit' : 'expense.edit'

    form.put(route(routeName, transaction.id), {
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

    form.delete(route(routeName, transaction.id), {
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
    incomeData: {
        type: Array,
    },
    expenseData: {
        type: Array,
    },
    recentTransactions: {
        type: Array,
    },
    currentMonthString: String,
    alert: Object,
});
console.log(props.recentTransactions)

//GETTING THE TOP INCOME AND EXPENSES
function getTopNItems(data, n) {
    return data
        .slice() // Create a shallow copy to avoid mutating the original array
        .sort((a, b) => b.amount - a.amount) // Sort in descending order
        .slice(0, n); // Get the first N items
}

// Get the top 2 income and expense items
const topIncomeData = getTopNItems(props.incomeData, 3);
const topExpenseData = getTopNItems(props.expenseData, 3);


//LOGIC TO MAKE THE BAR GRAPHS, ALSO SEE BAR GRAPH COMPONENT
const totalIncome = computed(() =>
    props.incomeData.reduce((sum, item) => sum + item.amount, 0)
);

const totalExpenses = computed(() =>
    props.expenseData.reduce((sum, item) => sum + item.amount, 0)
);

const balance = computed(() =>
    totalIncome.value - totalExpenses.value
);

// Check if there is no data
const noData = computed(() => {
    return props.incomeData.length === 0 &&
        props.expenseData.length === 0 &&
        props.recentTransactions.length === 0;
});


// ADD INCOME AND EXPENSE LOGIC
// Modal states
const showIncomeModal = ref(false);
const showExpenseModal = ref(false);

// Form data
const newIncome = useForm({
    type: '',
    amount: ''
});

const newExpense = useForm({
    type: '',
    amount: '',
    description: ''
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
                        <h1 class="text-2xl font-semibold text-gray-900"> {{ currentMonthString }}'s Budget</h1>
                        <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                            :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />

                        <div v-if="!noData" class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
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

                        <div v-if="noData" class="mt-6 text-center">
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

                        <div v-if="!noData" class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                            <BudgetBarChart title="Top Income Sources" :items="topIncomeData" type="income"
                                class="bg-white shadow rounded-lg" />

                            <BudgetBarChart title="Top Expenses" :items="topExpenseData" type="expense"
                                class="bg-white shadow rounded-lg" />
                        </div>

                        <div v-if="!noData" class="mt-8">
                            <h2 class="text-lg font-medium text-gray-900">All Transactions</h2>
                            <div class="mt-4 bg-white shadow rounded-lg">
                                <ul class="divide-y divide-gray-200">
                                    <li v-for="(transaction, index) in recentTransactions" :key="index"
                                        class="px-4 py-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ transaction.description || transaction.category }}
                                                </p>
                                                <p class="text-sm text-gray-500">{{ transaction.date }}</p>
                                            </div>
                                            <div
                                                class="flex flex-col  gap-2 md:flex-row md:gap-0 text-sm items-center space-x-2">
                                                <div :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'"
                                                    class="font-medium">
                                                    {{ transaction.type === 'income' ? '+' : '-' }} KES {{
                                                    transaction.amount }}
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
                            </div>
                        </div>

                    </div>
                </div>

                <div v-if="showIncomeModal" class="fixed inset-0 overflow-y-auto z-10 flex items-center justify-center">
                    <div class="fixed inset-0 bg-black bg-opacity-50" @click="showIncomeModal = false"></div>
                    <div class="relative bg-white rounded-lg max-w-md w-full mx-4 p-6 shadow-xl">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Add Income</h3>
                        <form @submit.prevent="submitIncome">
                            <div class="mb-4">
                                <label for="incomeType" class="block text-sm font-medium text-gray-700 mb-1">Income
                                    Type</label>
                                <select id="incomeType" v-model="newIncome.type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">Select Income Type</option>
                                    <option value="Salary">Salary</option>
                                    <option value="Freelance">Freelance</option>
                                    <option value="Business">Business</option>
                                    <option value="Bonuses">Bonuses</option>
                                    <option value="Investment">Investment</option>
                                    <option value="Rental Income">Rental Income</option>
                                    <option value="Pension Income">Pension Income</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="incomeAmount" class="block text-sm font-medium text-gray-700 mb-1">Amount
                                    (KES)</label>
                                <input type="number" id="incomeAmount" v-model="newIncome.amount"
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
                                    Save Income
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div v-if="showExpenseModal"
                    class="fixed mr-16 sm:mr-0 inset-0 overflow-y-auto z-10 flex items-center justify-center">
                    <div class="fixed inset-0 bg-black bg-opacity-50" @click="showExpenseModal = false"></div>
                    <div class="relative bg-white rounded-lg max-w-md w-full mx-4 p-6 shadow-xl">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Add Expense</h3>
                        <form @submit.prevent="submitExpense">
                            <div class="mb-4">
                                <label for="expenseType" class="block text-sm font-medium text-gray-700 mb-1">Expense
                                    Type</label>
                                <select id="expenseType" v-model="newExpense.type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">Select Expense Type</option>
                                    <option value="Rent">Rent/Mortgage</option>
                                    <option value="Groceries">Groceries</option>
                                    <option value="Transportation">Transportation</option>
                                    <option value="Utilities">Utilities</option>
                                    <option value="Entertainment">Entertainment</option>
                                    <option value="Healthcare">Healthcare</option>
                                    <option value="Insurance">Insurance</option>
                                    <option value="Investment">Investment</option>
                                    <option value="Savings">Savings</option>
                                    <option value="Other">Other</option>
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

                            <div class="flex justify-end space-x-3">
                                <button type="button" @click="showExpenseModal = false"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Save Expense
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


                <template v-if="showEditModal">
                    <div class="fixed mr-16 sm:mr-0 inset-0 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full max-w-xs sm:max-w-sm md:max-w-md">
                            <h3 class="text-lg font-bold mb-4">Edit Transaction</h3>
                            <form @submit.prevent="updateTransaction">
                                <div class="mb-4">
                                    <label for="transactionType"
                                        class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select v-if="selectedTransaction.type=='expense'" id="transactionType"
                                        name="category" v-model="selectedTransaction.category"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                        <option value="">Select Expense Type</option>
                                        <option value="Rent">Rent/Mortgage</option>
                                        <option value="Groceries">Groceries</option>
                                        <option value="Transportation">Transportation</option>
                                        <option value="Utilities">Utilities</option>
                                        <option value="Entertainment">Entertainment</option>
                                        <option value="Healthcare">Healthcare</option>
                                        <option value="Insurance">Insurance</option>
                                        <option value="Investment">Investment</option>
                                        <option value="Savings">Savings</option>
                                        <option value="Other">Other</option>
                                    </select>

                                    <select v-else id="incomeType" v-model="selectedTransaction.category"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        name="category" required>
                                        <option value="">Select Income Type</option>
                                        <option value="Salary">Salary</option>
                                        <option value="Freelance">Freelance</option>
                                        <option value="Business">Business</option>
                                        <option value="Bonuses">Bonuses</option>
                                        <option value="Investment">Investment</option>
                                        <option value="Rental Income">Rental Income</option>
                                        <option value="Pension Income">Pension Income</option>
                                        <option value="Other">Other</option>
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

                                <div v-if="selectedTransaction.description" class="mb-4">
                                    <label for="transactionAmount"
                                        class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <input type="text" id="transactionAmount" name="description"
                                        v-model="selectedTransaction.description"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        required />
                                </div>

                                <div class="flex justify-end space-x-2">
                                    <button @click="saveEdit"
                                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Save</button>
                                    <button @click="showEditModal = false"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                <template v-if="showDeleteModal">
                    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full max-w-xs sm:max-w-sm md:max-w-md">
                            <h3 class="text-lg font-bold mb-4">Confirm Delete</h3>
                            <p class="mb-4">Are you sure you want to delete this transaction?</p>
                            <div class="flex justify-end space-x-2">
                                <button @click="confirmDelete"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Delete</button>
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