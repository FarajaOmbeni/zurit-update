<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, defineProps, ref } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import DebtCard from '@/Components/Shared/DebtCard.vue';
import DebtsTable from '@/Components/Shared/DebtsTable.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import { debtTypes } from '@/Components/Variables/debtTypes';

const { alertState, openAlert, clearAlert } = useAlert();

const props = defineProps({
    debts: Array,
});

const debts = ref([...props.debts])
const activeDebts = computed(() => debts.value.filter(debt => debt.status === 'active'));
const paidOffDebts = computed(() => debts.value.filter(debt => debt.status === 'paid_off'));

const removeDebtFromList = (debtId) => {
    debts.value = debts.value.filter(debt => debt.id !== debtId);
};

// Modal state
const isModalOpen = ref(false);

// Form data
const newDebt = useForm({
    name: '',
    type: '',
    description: '',
    initial_amount: '',
    interest_rate: '',
    start_date: '',
    due_date: ''
});



// Open modal
const openModal = () => {
    isModalOpen.value = true;
};

// Close modal and reset form
const closeModal = () => {
    isModalOpen.value = false;
    resetForm();
};

// Reset form fields
const resetForm = () => {
    newDebt.value = {
        name: '',
        type: '',
        description: '',
        initial_amount: '',
        interest_rate: '',
        start_date: '',
        due_date: ''
    };
};

const submitForm = () => {
    newDebt.post(route('debt.store'), {
        onSuccess: () => {
            newDebt.reset();
            closeModal();
            openAlert('success', 'Debt added successfully', 5000)
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');

            openAlert('danger', errorMessages, 5000);
        }
    });
};

// Function to close modal when clicking outside
const closeModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeModal();
    }
};
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div class="container mx-auto p-4">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-purple-700">Your Debts</h1>
                        <button @click="openModal"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Add Debt
                        </button>
                    </div>

                    <!-- Active Debts Section -->
                    <section class="mb-8">
                        <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                            :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />
                        <h2 class="text-xl font-semibold text-purple-700 mb-4">Active Debts</h2>
                        <div v-if="activeDebts.length" class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            <DebtCard v-for="debt in activeDebts" :key="debt.debt_id" :debt="debt"
                            @delete="removeDebtFromList" />
                        </div>
                        <div v-else class="text-gray-600">
                            No active debts found.
                        </div>
                    </section>

                    <!-- Paid Off Debts Section -->
                    <section>
                        <h2 class="text-xl font-bold text-green-700 mb-4">Paid Off Debts</h2>
                        <div v-if="paidOffDebts.length" class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            <DebtCard v-for="debt in paidOffDebts" :key="debt.debt_id" :debt="debt" />
                        </div>
                        <div v-else class="text-gray-600">
                            You have no paid off debts.
                        </div>
                    </section>
                </div>

                <div class="mt-4">
                    <h3 class="text-lg font-bold mb-4">Debt Full Details</h3>
                    <!-- Debt Table -->
                    <DebtsTable :debts="debts" />
                </div>
            </Sidebar>
        </div>

        <!-- Add Debt Modal -->
        <div v-if="isModalOpen" @click="closeModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden">
                <div class="bg-purple-600 text-white px-4 py-3 flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Add New Debt</h3>
                    <button @click="closeModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-4">
                    <!-- Name Field -->
                    <div class="mb-3">
                        <label for="name" class="block text-gray-700 text-sm font-medium mb-1">Name</label>
                        <input type="text" id="name" v-model="newDebt.name"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                            required>
                    </div>

                    <!-- Type Dropdown -->
                    <div class="mb-3">
                        <label for="type" class="block text-gray-700 text-sm font-medium mb-1">Type</label>
                        <select id="type" v-model="newDebt.type"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                            required>
                            <option value="" disabled>Select a debt type</option>
                            <option v-for="type in debtTypes" :key="type.value" :value="type.value">
                                {{ type.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Description Field -->
                    <div class="mb-3">
                        <label for="description"
                            class="block text-gray-700 text-sm font-medium mb-1">Description</label>
                        <textarea id="description" v-model="newDebt.description"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                            rows="2"></textarea>
                    </div>

                    <!-- Loan Amount Field -->
                    <div class="mb-3">
                        <label for="initial_amount" class="block text-gray-700 text-sm font-medium mb-1">Loan
                            Amount</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">KES</span>
                            </div>
                            <input type="number" id="initial_amount" v-model="newDebt.initial_amount"
                                class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                step="0.01" min="0" required>
                        </div>
                    </div>

                    <!-- Interest Rate Field -->
                    <div class="mb-3">
                        <label for="interest_rate" class="block text-gray-700 text-sm font-medium mb-1">Interest
                            Rate</label>
                        <div class="relative">
                            <input type="number" id="interest_rate" v-model="newDebt.interest_rate"
                                class="w-full pr-7 pl-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                step="0.01" min="0" required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Initial Date Field -->
                    <div class="mb-3">
                        <label for="start_date" class="block text-gray-700 text-sm font-medium mb-1">Initial
                            Date</label>
                        <input type="date" id="start_date" v-model="newDebt.start_date"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                            required>
                    </div>

                    <!-- Due Date Field -->
                    <div class="mb-4">
                        <label for="due_date" class="block text-gray-700 text-sm font-medium mb-1">Due Date</label>
                        <input type="date" id="due_date" v-model="newDebt.due_date"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                            required>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="closeModal"
                            class="px-3 py-1.5 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-3 py-1.5 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            {{ newDebt.processing ? 'Saving...' : 'Add Debt' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
