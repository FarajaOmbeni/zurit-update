<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import InvestmentsTable from '@/Components/Shared/InvestmentsTable.vue';
import InvestmentChart from '@/Components/Shared/InvestmentChart.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import Alert from '@/Components/Shared/Alert.vue';
import { moneyMarketFunds, bonds, treasuryBills } from '@/Components/Variables/investmentTypes';

import { watch } from 'vue';

const { alertState, openAlert, clearAlert } = useAlert();

const props = defineProps({
    investments: Array
});

// Create a reactive copy of the investments from props
const investments = ref([...props.investments]);

// Modal state
const isModalOpen = ref(false);
const openModal = () => {
    isModalOpen.value = true;
};
const closeModal = () => {
    isModalOpen.value = false;
    resetForm();
};

const closeModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeModal();
    }
};

// Form data for creating a new investment
const newInvestment = useForm({
    type: '',
    details_of_investment: '',
    description: '',
    initial_amount: '',
    start_date: '',
    target_date: '',
    expected_return_rate: '',
    frequency_of_return: ''
});

watch(() => newInvestment.details_of_investment, (newVal) => {
    if (newInvestment.type === 'mmf') {
        const selectedFund = moneyMarketFunds.find(fund => fund.label === newVal);
        if (selectedFund) {
            newInvestment.expected_return_rate = selectedFund.return;
        }
    }
    if (newInvestment.type === 'bonds') {
        const selectedBond = bonds.find(bond => bond.label === newVal);
        if (selectedBond) {
            newInvestment.expected_return_rate = selectedBond.return;
        }
    }
    if (newInvestment.type === 'bills') {
        const selectedBill = treasuryBills.find(bill => bill.label === newVal);
        if (selectedBill) {
            newInvestment.expected_return_rate = selectedBill.return;
        }
    }
});


// Reset form fields using the built-in reset() method
const resetForm = () => {
    newInvestment.reset();
};

const submitForm = () => {
    newInvestment.post(route('invest.store'), {
        onSuccess: (response) => {
            // If the server returns the new investment as part of the response
            if (response.props && response.props.investment) {
                investments.value.push(response.props.investment);
            } else {
                // Fallback: create a temporary investment object using the form data
                const tempInvestment = {
                    id: Date.now(), // temporary unique id
                    type: newInvestment.type,
                    details_of_investment: newInvestment.details_of_investment,
                    description: newInvestment.description,
                    initial_amount: newInvestment.initial_amount,
                    start_date: newInvestment.start_date,
                    target_date: newInvestment.target_date,
                    expected_return_rate: newInvestment.expected_return_rate,
                    frequency_of_return: newInvestment.frequency_of_return,
                    status: 'active' // or set appropriate default status
                };
                investments.value.push(tempInvestment);
            }

            newInvestment.reset();
            closeModal();
            openAlert('success', 'Investment added successfully', 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
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
                <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                    :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />
                <div class="flex justify-between items-center mb-6">
                    <h3 class="mb-6 text-2xl font-bold">Investments Tracker</h3>
                    <button @click="openModal"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Add Investment
                    </button>
                </div>
                <div class="mb-12">
                    <!-- Investment Chart -->
                    <InvestmentChart :investments="investments" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-purple-700">Your Investments</h1>
                    <InvestmentsTable :investments="investments" />
                </div>
            </Sidebar>
        </div>

        <!-- Add Investment Modal -->
        <div v-if="isModalOpen" @click="closeModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-purple-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Add New Investment</h3>
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
                        <!-- Type Field -->
                        <div class="col-span-1">
                            <label for="type" class="block text-gray-700 text-xs font-medium mb-1">Type</label>
                            <select type="text" id="type" v-model="newInvestment.type"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                                <option value="mmf">Money Market Fund</option>
                                <option value="bonds">Bonds</option>
                                <option value="bills">Treasury Bills</option>
                            </select>
                        </div>

                        <!-- Details of Investment Field -->
                        <div class="col-span-1">
                            <label for="details_of_investment" class="block text-gray-700 text-xs font-medium mb-1">
                                Details
                            </label>
                            <select type="text" id="details_of_investment" v-model="newInvestment.details_of_investment"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                                <option v-show="newInvestment.type === 'mmf'" v-for="mmf in moneyMarketFunds"
                                    :key="mmf.value" :value="mmf.label">
                                    {{ mmf.label }}
                                </option>
                                <option v-show="newInvestment.type === 'bonds'" v-for="bond in bonds" :key="bond.value"
                                    :value="bond.label">
                                    {{ bond.label }}
                                </option>
                                <option v-show="newInvestment.type === 'bills'" v-for="bill in treasuryBills"
                                    :key="bill.value" :value="bill.label">
                                    {{ bill.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Expected Return Field -->
                        <div class="col-span-1">
                            <label for="expected_return_rate"
                                class="block text-gray-700 text-xs font-medium mb-1">Return Rate</label>
                            <div class="relative">
                                <input type="text" id="expected_return_rate"
                                    v-model="newInvestment.expected_return_rate"
                                    class="w-full pr-6 px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500" />
                                <span
                                    class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-500 text-xs">%</span>
                            </div>
                        </div>

                        <!-- Frequency of Return -->
                        <div v-if="newInvestment.type !== 'bills'" class="col-span-1">
                            <label for="frequency_of_return" class="block text-gray-700 text-xs font-medium mb-1">Return
                                Frequency</label>
                            <select type="text" id="frequency_of_return" v-model="newInvestment.frequency_of_return"
                                class="w-full pr-6 px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500">
                                <option value="monthly">Monthly</option>
                                <option value selected="yearly">Yearly</option>
                            </select>
                        </div>

                        <!-- Target Amount Field -->
                        <div :class="newInvestment.type == 'bills'? 'col-span-1':'col-span-2'">
                            <label for="initial_amount" class="block text-gray-700 text-xs font-medium mb-1">Initial
                                Investment</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="target_amount" v-model="newInvestment.initial_amount"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                    step="0.01" min="0" required />
                            </div>
                        </div>

                        <!-- Start Date Field -->
                        <div v-if="newInvestment.type !== 'bills'" class="col-span-1">
                            <label for="start_date" class="block text-gray-700 text-xs font-medium mb-1">Start
                                Date</label>
                            <input type="date" id="start_date" v-model="newInvestment.start_date"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required />
                        </div>

                        <!-- Target Date Field -->
                        <div v-if="newInvestment.type !== 'bills'" class="col-span-1">
                            <label for="target_date" class="block text-gray-700 text-xs font-medium mb-1">Target
                                Date</label>
                            <input type="date" id="target_date" v-model="newInvestment.target_date"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required />
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="mt-2">
                        <label for="description"
                            class="block text-gray-700 text-xs font-medium mb-1">Description</label>
                        <textarea id="description" v-model="newInvestment.description"
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                            rows="2"></textarea>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex justify-end space-x-2 mt-3">
                        <button type="button" @click="closeModal"
                            class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-2 py-1 text-xs bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            {{ newInvestment.processing ? 'Saving...' : 'Add Investment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
