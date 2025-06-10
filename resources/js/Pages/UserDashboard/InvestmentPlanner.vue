<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import InvestmentsTable from '@/Components/Shared/InvestmentsTable.vue';
import InvestmentChart from '@/Components/Shared/InvestmentChart.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import Alert from '@/Components/Shared/Alert.vue';
import { moneyMarketFunds, bonds, treasuryBills } from '@/Components/Variables/investmentTypes';

const { alertState, openAlert, clearAlert } = useAlert();

const props = defineProps({
    investments: Array
});

// Create a reactive copy of the investments from props
const investments = ref([...props.investments]);

// Modal state for adding new investment
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

// Modal state for editing investment
const isEditModalOpen = ref(false);
const editingInvestment = ref(null);

const openEditModal = (investment) => {
    // Create a copy of the investment to avoid directly mutating the original
    editingInvestment.value = {
        id: investment.id,
        type: investment.type,
        details_of_investment: investment.details_of_investment,
        description: investment.description || '',
        initial_amount: investment.initial_amount,
        current_amount: investment.current_amount,
        start_date: investment.start_date,
        target_date: investment.target_date,
        expected_return_rate: investment.expected_return_rate,
        frequency_of_return: investment.frequency_of_return || 'yearly',
        status: investment.status
    };
    isEditModalOpen.value = true;
};

const closeEditModal = () => {
    isEditModalOpen.value = false;
    editingInvestment.value = null;
};

const closeEditModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeEditModal();
    }
};

// Form for editing investment
const editInvestmentForm = useForm({
    id: '',
    type: '',
    details_of_investment: '',
    description: '',
    initial_amount: '',
    current_amount: '',
    start_date: '',
    target_date: '',
    expected_return_rate: '',
    frequency_of_return: '',
    status: 'active'
});

// Watch for changes in editingInvestment and update the form
watch(editingInvestment, (investment) => {
    if (investment) {
        editInvestmentForm.id = investment.id;
        editInvestmentForm.type = investment.type;
        editInvestmentForm.details_of_investment = investment.details_of_investment;
        editInvestmentForm.description = investment.description;
        editInvestmentForm.initial_amount = investment.initial_amount;
        editInvestmentForm.current_amount = investment.current_amount;
        editInvestmentForm.start_date = new Date(investment.start_date).toISOString().split('T')[0] || '';
        editInvestmentForm.target_date = new Date(investment.target_date).toISOString().split('T')[0] || '';
        editInvestmentForm.expected_return_rate = investment.expected_return_rate;
        editInvestmentForm.frequency_of_return = investment.frequency_of_return;
        editInvestmentForm.status = investment.status;
    }
});

// Form data for creating a new investment
const newInvestment = useForm({
    type: '',
    details_of_investment: '',
    description: '',
    initial_amount: '',
    start_date: '',
    target_date: '',
    expected_return_rate: '',
    frequency_of_return: '',
    commitment: false,
    committed_amount: '',
    duration_months: '',
    duration_years: '',
});

watch(() => newInvestment.type, () => {
    newInvestment.expected_return_rate = null;
    newInvestment.description = '';
    newInvestment.details_of_investment = '';
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
            const description = `Issue Number: ${selectedBill.issue_number} Auction Date: ${selectedBill.auction_date}Value Dated: ${selectedBill.value_dated}`;
            newInvestment.expected_return_rate = selectedBill.return;
            newInvestment.description = description
        }
    }
});

// Apply the same watchers for editInvestmentForm
watch(() => editInvestmentForm.details_of_investment, (newVal) => {
    if (editInvestmentForm.type === 'mmf') {
        const selectedFund = moneyMarketFunds.find(fund => fund.label === newVal);
        if (selectedFund) {
            editInvestmentForm.expected_return_rate = selectedFund.return;
        }
    }
    if (editInvestmentForm.type === 'bonds') {
        const selectedBond = bonds.find(bond => bond.label === newVal);
        if (selectedBond) {
            editInvestmentForm.expected_return_rate = selectedBond.return;
        }
    }
    if (editInvestmentForm.type === 'bills') {
        const selectedBill = treasuryBills.find(bill => bill.label === newVal);
        if (selectedBill) {
            editInvestmentForm.expected_return_rate = selectedBill.return;
        }
    }
});

// Reset form fields using the built-in reset() method
const resetForm = () => {
    newInvestment.reset();
};

const submitForm = () => {
    if (newInvestment.type === 'bonds' && newInvestment.start_date && newInvestment.target_date) {
        const startDate = new Date(newInvestment.start_date);
        const targetDate = new Date(newInvestment.target_date);
        const oneYearLater = new Date(startDate);
        oneYearLater.setFullYear(oneYearLater.getFullYear() + 1);

        if (targetDate < oneYearLater) {
            openAlert('warning', 'The minimum duration is 1 year', 5000);
            return;
        }
    }
    
    newInvestment.post(route('invest.store'), {
        onSuccess: (response) => {
            // If the server returns the new investment as part of the response
            if (response.props && response.props.investment) {
                investments.value.push({
                    ...response.props.investment,
                    initial_amount: parseFloat(response.props.investment.initial_amount) || 0,
                    expected_return_rate: parseFloat(response.props.investment.expected_return_rate) || 0
                });
            } else {
                // Fallback: create a temporary investment object using the form data
                const tempInvestment = {
                    id: Date.now(), // temporary unique id
                    type: newInvestment.type,
                    details_of_investment: newInvestment.details_of_investment,
                    description: newInvestment.description,
                    initial_amount: parseFloat(newInvestment.initial_amount) || 0,
                    start_date: newInvestment.start_date,
                    target_date: newInvestment.target_date,
                    expected_return_rate: parseFloat(newInvestment.expected_return_rate) || 0,
                    frequency_of_return: newInvestment.frequency_of_return,
                    status: 'active', // or set appropriate default status
                    current_amount: parseFloat(newInvestment.initial_amount) || 0 // Initialize current_amount
                };
                investments.value.push(tempInvestment);
            }

            newInvestment.reset();
            window.location.reload();
            closeModal();
            openAlert('success', 'Investment added successfully', 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages, 5000);
        }
    });
};

// Submit edit form
const submitEditForm = () => {
    editInvestmentForm.put(route('invest.update', editInvestmentForm.id), {
        onSuccess: () => {
            // Update the investment in the local array
            const index = investments.value.findIndex(inv => inv.id === editInvestmentForm.id);
            if (index !== -1) {
                // Ensure numeric values are properly parsed
                investments.value[index] = {
                    ...editInvestmentForm,
                    initial_amount: parseFloat(editInvestmentForm.initial_amount) || 0,
                    current_amount: parseFloat(editInvestmentForm.current_amount) || 0,
                    expected_return_rate: parseFloat(editInvestmentForm.expected_return_rate) || 0
                };
            }

            closeEditModal();
            openAlert('success', 'Investment updated successfully', 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages, 5000);
        }
    });
};

// Add state for delete confirmation modal
const isDeleteModalOpen = ref(false);
const investmentToDelete = ref(null);

const openDeleteModal = (investment) => {
    investmentToDelete.value = investment;
    isDeleteModalOpen.value = true;
};

const closeDeleteModal = () => {
    isDeleteModalOpen.value = false;
    investmentToDelete.value = null;
};

const closeDeleteModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeDeleteModal();
    }
};

// Function to handle investment deletion
const confirmDelete = () => {
    if (!investmentToDelete.value) return;

    const id = investmentToDelete.value.id;

    // Call the API to delete the investment
    useForm().delete(route('invest.destroy', id), {
        onSuccess: () => {
            // Remove the investment from the local array
            investments.value = investments.value.filter(inv => inv.id !== id);

            closeDeleteModal();
            openAlert('success', 'Investment deleted successfully', 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages, 5000);
            closeDeleteModal();
        }
    });
};
</script>

<template>

    <Head title="Investment Planner" />
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
                <div v-show="investments.length > 0">
                    <h1 class="text-2xl font-bold text-purple-700">Your Investments</h1>
                    <InvestmentsTable :investments="investments" @edit-investment="openEditModal"
                        @delete-investment="openDeleteModal" />
                </div>
            </Sidebar>
        </div>

        <!-- Add Investment Modal -->
        <div v-if="isModalOpen" @click="closeModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <!-- Modal content -->
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
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Details of Investment Field -->
                        <div class="col-span-1">
                            <label for="details_of_investment" class="block text-gray-700 text-xs font-medium mb-1">
                                Details
                            </label>
                            <select v-show="newInvestment.type !== 'other'" type="text" id="details_of_investment"
                                v-model="newInvestment.details_of_investment"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                :required="newInvestment.type !== 'other'">
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
                            <input placeholder="Investment Description" v-show="newInvestment.type === 'other'"
                                type="text" id="details_of_investment" v-model="newInvestment.details_of_investment"
                                class="w-full pr-6 px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                :required="newInvestment.type === 'other'" />
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
                        <div :class="newInvestment.type == 'bills' ? 'col-span-1' : 'col-span-2'">
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

                        <!-- Duration Field -->
                        <div v-show="newInvestment.type !== 'bills'" class="col-span-1">
                            <label class="block text-gray-700 text-xs font-medium mb-1">Duration</label>
                            <div class="flex gap-2">
                                <!-- Years -->
                                <div class="flex-1">
                                    <input type="number" min="0" v-model="newInvestment.duration_years" placeholder="Years"
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                        >
                                </div>

                                <!-- Months -->
                                <div class="flex-1">
                                    <input type="number" min="0" max="11" v-model="newInvestment.duration_months"
                                        placeholder="Months"
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                        >
                                </div>
                            </div>
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

                    <!-- Contribution Commitment Radio Buttons -->
                    <!-- <div v-show="newInvestment.type === 'mmf'" class="flex items-center gap-4 mt-2">
                        <span class="text-xs text-gray-700 font-bold">Commit to monthly contribution?</span>
                        <label class="flex items-center text-xs gap-1">
                            <input type="radio" v-model="newInvestment.commitment" :value="true"
                                class="text-purple-500" />
                            Yes
                        </label>
                        <label class="flex items-center text-xs gap-1">
                            <input type="radio" v-model="newInvestment.commitment" :value="false"
                                class="text-purple-500" />
                            No
                        </label>
                    </div> -->

                    <!-- Target Amount Field -->
                    <!-- <div v-show="newInvestment.commitment == true" class="mt-4">
                        <label for="initial_amount" class="block text-gray-700 text-xs font-medium mb-1">How much do you
                            commit?</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-xs">KES</span>
                            </div>
                            <input type="number" id="target_amount" v-model="newInvestment.committed_amount"
                                class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                step="0.01" min="0" required />
                        </div>
                    </div> -->

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

        <!-- Edit Investment Modal -->
        <div v-if="isEditModalOpen" @click="closeEditModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-purple-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Edit Investment</h3>
                    <button @click="closeEditModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitEditForm" class="p-3">
                    <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                        <!-- Type Field -->
                        <div class="col-span-1">
                            <label for="edit_type" class="block text-gray-700 text-xs font-medium mb-1">Type</label>
                            <select type="text" id="edit_type" v-model="editInvestmentForm.type"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                                <option value="mmf">Money Market Fund</option>
                                <option value="bonds">Bonds</option>
                                <option value="bills">Treasury Bills</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Details of Investment Field -->
                        <div class="col-span-1">
                            <label for="edit_details_of_investment"
                                class="block text-gray-700 text-xs font-medium mb-1">
                                Details
                            </label>
                            <select v-show="editInvestmentForm.type !== 'other'" id="edit_details_of_investment"
                                v-model="editInvestmentForm.details_of_investment"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                :required="editInvestmentForm.type !== 'other'">
                                <option v-show="editInvestmentForm.type === 'mmf'" v-for="mmf in moneyMarketFunds"
                                    :key="mmf.value" :value="mmf.label">
                                    {{ mmf.label }}
                                </option>
                                <option v-show="editInvestmentForm.type === 'bonds'" v-for="bond in bonds"
                                    :key="bond.value" :value="bond.label">
                                    {{ bond.label }}
                                </option>
                                <option v-show="editInvestmentForm.type === 'bills'" v-for="bill in treasuryBills"
                                    :key="bill.value" :value="bill.label">
                                    {{ bill.label }}
                                </option>
                            </select>
                            <input placeholder="Investment Description" v-show="editInvestmentForm.type === 'other'"
                                type="text" id="edit_details_of_investment"
                                v-model="editInvestmentForm.details_of_investment"
                                class="w-full pr-6 px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                :required="editInvestmentForm.type === 'other'" />
                        </div>

                        <!-- Expected Return Field -->
                        <div class="col-span-1">
                            <label for="edit_expected_return_rate"
                                class="block text-gray-700 text-xs font-medium mb-1">Return Rate</label>
                            <div class="relative">
                                <input type="text" id="edit_expected_return_rate"
                                    v-model="editInvestmentForm.expected_return_rate"
                                    class="w-full pr-6 px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500" />
                                <span
                                    class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-500 text-xs">%</span>
                            </div>
                        </div>

                        <!-- Frequency of Return -->
                        <div v-if="editInvestmentForm.type !== 'bills'" class="col-span-1">
                            <label for="edit_frequency_of_return"
                                class="block text-gray-700 text-xs font-medium mb-1">Return
                                Frequency</label>
                            <select type="text" id="edit_frequency_of_return"
                                v-model="editInvestmentForm.frequency_of_return"
                                class="w-full pr-6 px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500">
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>

                        <!-- Initial Amount Field -->
                        <div :class="editInvestmentForm.type == 'bills' ? 'col-span-1' : 'col-span-1'">
                            <label for="edit_initial_amount"
                                class="block text-gray-700 text-xs font-medium mb-1">Initial
                                Investment</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="edit_initial_amount"
                                    v-model="editInvestmentForm.initial_amount"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                    step="0.01" min="0" required />
                            </div>
                        </div>

                        <!-- Current Amount Field -->
                        <div class="col-span-1">
                            <label for="edit_current_amount"
                                class="block text-gray-700 text-xs font-medium mb-1">Current
                                Amount</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="edit_current_amount"
                                    v-model="editInvestmentForm.current_amount"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                    step="0.01" min="0" required />
                            </div>
                        </div>

                        <!-- Start Date Field -->
                        <div v-if="editInvestmentForm.type !== 'bills'" class="col-span-1">
                            <label for="edit_start_date" class="block text-gray-700 text-xs font-medium mb-1">Start
                                Date</label>
                            <input type="date" id="edit_start_date" v-model="editInvestmentForm.start_date"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required />
                        </div>

                        <!-- Target Date Field -->
                        <div v-if="editInvestmentForm.type !== 'bills'" class="col-span-1">
                            <label for="edit_target_date" class="block text-gray-700 text-xs font-medium mb-1">Target
                                Date</label>
                            <input type="date" id="edit_target_date" v-model="editInvestmentForm.target_date"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required />
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="mt-2">
                        <label for="edit_description"
                            class="block text-gray-700 text-xs font-medium mb-1">Description</label>
                        <textarea id="edit_description" v-model="editInvestmentForm.description"
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                            rows="2"></textarea>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex justify-end space-x-2 mt-3">
                        <button type="button" @click="closeEditModal"
                            class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-2 py-1 text-xs bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            {{ editInvestmentForm.processing ? 'Updating...' : 'Update Investment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Delete Confirmation Modal -->
    <div v-if="isDeleteModalOpen" @click="closeDeleteModalOnOutsideClick"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
            <div class="bg-red-600 text-white px-3 py-2 flex justify-between items-center">
                <h3 class="text-base font-medium">Confirm Deletion</h3>
                <button @click="closeDeleteModal" class="text-white hover:text-gray-200 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="p-4">
                <p class="text-gray-700 mb-4">
                    Are you sure you want to delete the investment
                    <span class="font-bold">{{ investmentToDelete?.details_of_investment }}</span>?
                    This action cannot be undone.
                </p>

                <div class="flex justify-end space-x-2">
                    <button @click="closeDeleteModal"
                        class="px-3 py-1.5 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-red-500">
                        Cancel
                    </button>
                    <button @click="confirmDelete"
                        class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-1 focus:ring-red-500">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>