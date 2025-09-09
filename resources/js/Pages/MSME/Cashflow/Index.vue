<template>
    <Head title="Cashflow Management" />
    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">Cashflow Management</h1>
                                <p class="text-gray-600 mt-2">Track your income and expenses with detailed analytics</p>
                            </div>
                            <div class="mt-4 md:mt-0 flex space-x-3">
                                <button @click="openAddModal"
                                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    Add Transaction
                                </button>
                                <Link :href="route('cashflow.analytics')"
                                      class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    View Analytics
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Income</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(summary.total_income) }}</p>
                                    <p :class="[
                                        'text-sm',
                                        summary.income_growth >= 0 ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        {{ summary.income_growth >= 0 ? '+' : '' }}{{ summary.income_growth?.toFixed(1) }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Expenses</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(summary.total_expenses) }}</p>
                                    <p :class="[
                                        'text-sm',
                                        summary.expense_growth <= 0 ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        {{ summary.expense_growth >= 0 ? '+' : '' }}{{ summary.expense_growth?.toFixed(1) }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div :class="[
                                    'w-8 h-8 rounded-full flex items-center justify-center mr-3',
                                    summary.net_cashflow >= 0 ? 'bg-green-100' : 'bg-red-100'
                                ]">
                                    <svg :class="[
                                        'w-4 h-4',
                                        summary.net_cashflow >= 0 ? 'text-green-600' : 'text-red-600'
                                    ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Net Cashflow</p>
                                    <p :class="[
                                        'text-2xl font-semibold',
                                        summary.net_cashflow >= 0 ? 'text-green-600' : 'text-red-600'
                                    ]">{{ formatCurrency(summary.net_cashflow) }}</p>
                                    <p :class="[
                                        'text-sm',
                                        summary.net_growth >= 0 ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        {{ summary.net_growth >= 0 ? '+' : '' }}{{ summary.net_growth?.toFixed(1) }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Transactions</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ summary.entries_count }}</p>
                                    <p class="text-sm text-gray-600">Avg: {{ formatCurrency(summary.average_transaction) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Filters</h3>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select v-model="filters.type" @change="applyFilters" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <option value="all">All</option>
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select v-model="filters.category" @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <option value="">All Categories</option>
                                    <optgroup v-if="filters.type === 'income' || filters.type === 'all'" label="Income">
                                        <option v-for="(label, value) in categories.income" :key="value" :value="value">{{ label }}</option>
                                    </optgroup>
                                    <optgroup v-if="filters.type === 'expense' || filters.type === 'all'" label="Expenses">
                                        <option v-for="(label, value) in categories.expense" :key="value" :value="value">{{ label }}</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                                <select v-model="filters.payment_method" @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <option value="">All Methods</option>
                                    <option v-for="(label, value) in paymentMethods" :key="value" :value="value">{{ label }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input v-model="filters.start_date" @change="applyFilters" type="date" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input v-model="filters.end_date" @change="applyFilters" type="date" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Transactions</h3>
                            <button class="text-blue-600 hover:text-blue-800 text-sm">
                                Export to Excel
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="entries.data.length === 0">
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                            </svg>
                                            <p>No transactions found</p>
                                            <p class="text-sm">Add your first transaction to get started</p>
                                        </td>
                                    </tr>
                                    <tr v-for="entry in entries.data" :key="entry.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(entry.entry_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                entry.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                            ]">
                                                {{ entry.type === 'income' ? 'Income' : 'Expense' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ entry.description || 'No description' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ getCategoryLabel(entry.category, entry.type) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ getPaymentMethodLabel(entry.payment_method) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <span :class="[
                                                entry.type === 'income' ? 'text-green-600' : 'text-red-600'
                                            ]">
                                                {{ entry.type === 'income' ? '+' : '-' }}{{ formatCurrency(entry.amount) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button @click="editEntry(entry)" class="text-blue-600 hover:text-blue-900">Edit</button>
                                                <button @click="deleteEntry(entry)" class="text-red-600 hover:text-red-900">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="entries.last_page > 1" class="mt-6">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing {{ entries.from }} to {{ entries.to }} of {{ entries.total }} results
                                </div>
                                <div class="flex space-x-1">
                                    <Link v-for="link in entries.links" :key="link.label" 
                                          :href="link.url" 
                                          v-html="link.label"
                                          :class="[
                                              'px-3 py-2 text-sm border rounded',
                                              link.active ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                          ]">
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Transaction Modal -->
        <TransactionModal 
            :show="showModal"
            :entry="selectedEntry"
            :categories="categories"
            :payment-methods="paymentMethods"
            @close="closeModal"
            @saved="onTransactionSaved"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TransactionModal from '@/Components/MSME/TransactionModal.vue';
import { useFormatCurrency } from '@/Components/Composables/useFormatCurrency';
import { formatDate } from '@/Components/Composables/useDateFormat';
import { ref, reactive } from 'vue';

const props = defineProps({
    entries: Object,
    summary: Object,
    categories: Object,
    paymentMethods: Object,
    filters: Object,
});

const { formatCurrency } = useFormatCurrency();

const showModal = ref(false);
const selectedEntry = ref(null);

const filters = reactive({
    type: props.filters.type || 'all',
    category: props.filters.category || '',
    payment_method: props.filters.payment_method || '',
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || '',
});

const openAddModal = () => {
    selectedEntry.value = null;
    showModal.value = true;
};

const editEntry = (entry) => {
    selectedEntry.value = entry;
    showModal.value = true;
};

const deleteEntry = (entry) => {
    if (confirm('Are you sure you want to delete this transaction?')) {
        router.delete(route('cashflow.destroy', entry.id));
    }
};

const closeModal = () => {
    showModal.value = false;
    selectedEntry.value = null;
};

const onTransactionSaved = () => {
    closeModal();
    router.reload();
};

const applyFilters = () => {
    router.get(route('cashflow.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    });
};

const getCategoryLabel = (category, type) => {
    const categoryOptions = props.categories[type] || {};
    return categoryOptions[category] || category;
};

const getPaymentMethodLabel = (method) => {
    return props.paymentMethods[method] || method;
};
</script> 