<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, defineProps, ref } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import DebtCard from '@/Components/Shared/DebtCard.vue';
import DebtsTable from '@/Components/Shared/DebtsTable.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import { debtTypes } from '@/Components/Variables/debtTypes';

const { alertState, openAlert, clearAlert } = useAlert();

const props = defineProps({
    debts: Array,
    newDebt: Object,
});

// console.log(props.newDebt)

const debts = ref([...props.debts]);
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
    duration_months: '',
    duration_years: '',
    is_recurring: true, // Default to true for existing behavior
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
    newDebt.reset();
};

const submitForm = () => {
    // Using Inertia's post method with preserveState and preserveScroll options
    newDebt.post(route('debt.store'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            newDebt.reset();
            closeModal();
            openAlert('success', 'Debt added successfully', 5000);
            window.location.reload();
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

    <Head title="Debt Manager" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <DashboardBackButton />
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

                    <!-- Legend -->
                    <div class="mb-6 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Payment Types</h3>
                        <div class="flex items-center space-x-6 text-xs text-gray-600">
                            <div class="flex items-center space-x-2">
                                <svg class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Automatic monthly payments</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Manual payments</span>
                            </div>
                        </div>
                    </div>

                    <!-- Active Debts Section -->
                    <section class="mb-8">
                        <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                            :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />
                        <h2 class="text-xl font-semibold text-purple-700 mb-4">Active Debts</h2>
                        <div v-if="activeDebts.length" class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            <DebtCard v-for="debt in activeDebts" :key="debt.id || debt.debt_id" :debt="debt"
                                @delete="removeDebtFromList" />
                        </div>
                        <div v-else class="text-gray-600">
                            No active debts found.
                        </div>
                    </section>

                    <!-- Paid Off Debts Section -->
                    <section v-show="paidOffDebts.length > 0">
                        <h2 class="text-xl font-bold text-green-700 mb-4">Paid Off Debts</h2>
                        <div v-if="paidOffDebts.length" class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            <DebtCard v-for="debt in paidOffDebts" :key="debt.id || debt.debt_id" :debt="debt" />
                        </div>
                        <div v-else class="text-gray-600">
                            You have no paid off debts.
                        </div>
                    </section>
                </div>

                <div v-show="debts.length > 0" class="mt-4">
                    <h3 class="text-lg font-bold mb-4">Debt Full Details</h3>
                    <!-- Debt Table -->
                    <DebtsTable :debts="debts" />
                </div>
            </Sidebar>
        </div>

        <!-- Add Debt Modal -->
        <div v-if="isModalOpen" @click="closeModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-purple-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Add New Debt</h3>
                    <button @click="closeModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-3">
                    <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                        <!-- Name Field -->
                        <div class="col-span-1">
                            <label for="name" class="block text-gray-700 text-xs font-medium mb-1">Name</label>
                            <input type="text" id="name" v-model="newDebt.name"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                        </div>

                        <!-- Type Dropdown -->
                        <div class="col-span-1">
                            <label for="type" class="block text-gray-700 text-xs font-medium mb-1">Type</label>
                            <select id="type" v-model="newDebt.type"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                                <option value="" disabled>Select type</option>
                                <option v-for="type in debtTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Loan Amount Field -->
                        <div class="col-span-1">
                            <label for="initial_amount" class="block text-gray-700 text-xs font-medium mb-1">Loan
                                Amount</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="initial_amount" v-model="newDebt.initial_amount"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                    step="0.01" min="0" required>
                            </div>
                        </div>

                        <!-- Interest Rate Field -->
                        <div class="col-span-1">
                            <label for="interest_rate" class="block text-gray-700 text-xs font-medium mb-1">Interest
                                Rate</label>
                            <div class="relative">
                                <input type="number" id="interest_rate" v-model="newDebt.interest_rate"
                                    class="w-full pr-6 pl-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                    step="0.01" min="0" required>
                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Start Date Field -->
                        <div class="col-span-1">
                            <label for="start_date" class="block text-gray-700 text-xs font-medium mb-1">Start
                                Date</label>
                            <input type="date" id="start_date" v-model="newDebt.start_date"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                        </div>

                        <!-- Duration Field -->
                        <div class="col-span-1">
                            <label class="block text-gray-700 text-xs font-medium mb-1">Duration</label>
                            <div class="flex gap-2">
                                <!-- Years -->
                                <div class="flex-1">
                                    <input type="number" min="0" v-model="newDebt.duration_years" placeholder="Years"
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                        required>
                                </div>

                                <!-- Months -->
                                <div class="flex-1">
                                    <input type="number" min="0" max="11" v-model="newDebt.duration_months"
                                        placeholder="Months"
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                        required>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Description Field -->
                    <div class="mt-2">
                        <label for="description" class="block text-gray-700 text-xs font-medium mb-1">
                            Description
                        </label>
                        <textarea id="description" v-model="newDebt.description"
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                            rows="2"></textarea>
                    </div>

                    <!-- Recurring Payment Option -->
                    <div class="mt-3">
                        <label class="flex items-center">
                            <input type="checkbox" v-model="newDebt.is_recurring"
                                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <span class="ml-2 text-xs text-gray-700">
                                Set up automatic monthly payments
                            </span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ newDebt.is_recurring ? 'Payments will be automatically created monthly' : 'No automatic payments will be created - you will add payments manually when needed' }}
                        </p>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex justify-end space-x-2 mt-3">
                        <button type="button" @click="closeModal"
                            class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-2 py-1 text-xs bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500"
                            :disabled="newDebt.processing">
                            {{ newDebt.processing ? 'Saving...' : 'Add Debt' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>