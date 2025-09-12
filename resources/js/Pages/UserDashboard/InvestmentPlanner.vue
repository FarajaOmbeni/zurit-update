<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import InvestmentsTable from '@/Components/Shared/InvestmentsTable.vue';
import InvestmentChart from '@/Components/Shared/InvestmentChart.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import Alert from '@/Components/Shared/Alert.vue';
import { moneyMarketFunds, bonds, treasuryBills, reits, shares, realEstate, insurances } from '@/Components/Variables/investmentTypes';
import RealEstateTable from '@/Components/Shared/RealEstateTable.vue';
import StocksTable from '@/Components/Shared/StocksTable.vue';
import InsurancesTable from '@/Components/Shared/InsurancesTable.vue';

const { alertState, openAlert, clearAlert } = useAlert();

const FIXED_INCOME_TYPES = ['mmf', 'bills', 'bonds', 'other'];
const REAL_ESTATE_TYPES = ['residential', 'commercial', 'land'];
const STOCKS_TYPES = ['NSE', 'reits'];
const POLICY_TYPES = [
    'britam_holdings_plc',
    'jubilee_insurance',
    'cic_insurance_group',
    'apa_insurance',
    'old_mutual_kenya',
    'sanlam_kenya',
    'icea_lion_group',
    'pioneer_assurance',
    'liberty_life_assurance_kenya',
    'first_assurance_kenya'
]

const props = defineProps({
    investments: Array
});

const fixedIncomes = computed(() => {
    // Ensure props.investments is an array before filtering
    if (!props.investments) {
        return [];
    }
    return props.investments.filter(investment =>
        FIXED_INCOME_TYPES.includes(investment.type)
    );
});

const realEstateInvestments = computed(() => {
    if (!props.investments) {
        return [];
    }
    return props.investments.filter(investment =>
        REAL_ESTATE_TYPES.includes(investment.type)
    );
});

const stockInvestments = computed(() => {
    if (!props.investments) {
        return [];
    }
    return props.investments.filter(investment =>
        STOCKS_TYPES.includes(investment.type) || STOCKS_TYPES.includes(investment.details_of_investment)
    );
});

const insurancePolicies = computed(() => {
    if (!props.investments) {
        return [];
    }
    return props.investments.filter(investment =>
        POLICY_TYPES.includes(investment.type) || POLICY_TYPES.includes(investment.details_of_investment)
    );
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
    newInvestment.reset();
};

const closeModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeModal();
    }
};

// Modal state for choosing an investment
const isRealEstateModalOpen = ref(false);
const openRealEstateModal = () => {
    isRealEstateModalOpen.value = true;
};
const closeRealEstateModal = () => {
    isRealEstateModalOpen.value = false;
    newInvestment.reset();
};

const closeRealEstateModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeRealEstateModal();
    }
};

// Modal state for adding real estate investment
const isChoiceModalOpen = ref(false);
const openChoiceModal = () => {
    isChoiceModalOpen.value = true;
};
const closeChoiceModal = () => {
    isChoiceModalOpen.value = false;
};

const closeChoiceModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeChoiceModal();
    }
};

// Modal state for adding stocks investment
const isStockModalOpen = ref(false);
const openStockModal = () => {
    isStockModalOpen.value = true;
};
const closeStockModal = () => {
    isStockModalOpen.value = false;
    newInvestment.reset();

};

const closeStockModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeStockModal();
    }
};

// Modal state for adding insurances policies
const isInsuranceModalOpen = ref(false);
const openInsuranceModal = () => {
    isInsuranceModalOpen.value = true;
};
const closeInsuranceModal = () => {
    isInsuranceModalOpen.value = false;
    newInvestment.reset();

};

const closeInsuranceModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeInsuranceModal();
    }
};

// Modal state for adding reits investment
function onSelect() {
    if (newInvestment.type === 'reits') openReitsModal();
}

const isReitsModalOpen = ref(false);
const openReitsModal = () => {
    isReitsModalOpen.value = true;
};
const closeReitsModal = () => {
    isReitsModalOpen.value = false;
    newInvestment.reset();
};

const closeReitsModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeReitsModal();
    }
};

// Modal state for editing investment
const isEditModalOpen = ref(false);
const editingInvestment = ref(null);

const closeEditModal = () => {
    isEditModalOpen.value = false;
    editingInvestment.value = null;
};

const closeEditModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeEditModal();
    }
};

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
    type: 'select',
    details_of_investment: '',
    description: '',
    initial_amount: '',
    start_date: '',
    target_date: '',
    current_amount: '',
    expected_return_rate: '',
    frequency_of_return: '',
    commitment: false,
    committed_amount: '',
    duration_months: '',
    duration_years: '',
    insurance: 'insurance',
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
/* 1️⃣  auto-fill price when user picks a ticker */
watch(
    () => newInvestment.type,
    (ticker) => {
        const s = shares.find(x => x.ticker === ticker);
        newInvestment.expected_return_rate = s ? s.price : '';
        /* reset totals so math stays consistent */
        newInvestment.initial_amount = '';
        newInvestment.current_amount = '';
    }
);

// === REAL ESTATE EDIT LOGIC ===

const isEditRealEstateModalOpen = ref(false);
const editingRealEstate = ref(null); // Holds the original investment data for editing

// Form for editing a real estate investment
const editRealEstateForm = useForm({
    id: '',
    type: '',
    details_of_investment: '',
    initial_amount: '',
    current_amount: '', // Represents rental income here
    start_date: '',
    description: '',
});

const openEditRealEstateModal = (investment) => {
    editingRealEstate.value = { ...investment }; // Create a copy
    isEditRealEstateModalOpen.value = true;
};

const closeEditRealEstateModal = () => {
    isEditRealEstateModalOpen.value = false;
    editingRealEstate.value = null;
    editRealEstateForm.reset();
};

const closeEditRealEstateModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeEditRealEstateModal();
    }
};


// Watch for changes in the investment to be edited and populate the form
watch(editingRealEstate, (investment) => {
    if (investment) {
        editRealEstateForm.id = investment.id;
        editRealEstateForm.type = investment.type;
        editRealEstateForm.details_of_investment = investment.details_of_investment;
        editRealEstateForm.initial_amount = investment.initial_amount;
        editRealEstateForm.current_amount = investment.current_amount;
        editRealEstateForm.start_date = new Date(investment.start_date).toISOString().split('T')[0] || '';
        editRealEstateForm.description = investment.description || '';
    }
});

// Submit the edited real estate form
const submitEditRealEstateForm = () => {
    editRealEstateForm.put(route('invest.update', editRealEstateForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditRealEstateModal();
            // You might want to refresh your investments list or update the specific item
            openAlert('success', 'Real estate investment updated successfully!', 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages || 'An error occurred.', 5000);
        }
    });
};

// === STOCK/REIT EDIT LOGIC ===

const isEditStockModalOpen = ref(false);
const editingStock = ref(null);

// A single form for both stocks and reits
const editStockForm = useForm({
    id: '',
    type: '', // Holds stock Ticker
    details_of_investment: '', // Holds REIT Name or Stock Exchange
    initial_amount: '', // Number of shares
    current_amount: '', // Total Price
    expected_return_rate: '', // Price per share
    start_date: '',
    description: '',
});

// COMPUTED PROPERTY to display the correct name in the read-only field.
const shareDisplayName = computed(() => {
    if (!editStockForm.type) {
        return '';
    }
    // For REITs, the name is in 'details_of_investment'
    if (editStockForm.type === 'reits') {
        return editStockForm.details_of_investment;
    } else {
        return editStockForm.type;
    }
});


// You need a way to distinguish between a stock and a reit in your investment object.
const openEditStockModal = (investment) => {
    editingStock.value = { ...investment }; // Create a copy
    isEditStockModalOpen.value = true;
};

const closeEditStockModal = () => {
    isEditStockModalOpen.value = false;
    editingStock.value = null;
    editStockForm.reset();
};

const closeEditStockModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeEditStockModal();
    }
};

// Watch for changes and populate the form
watch(editingStock, (investment) => {
    if (investment) {
        editStockForm.id = investment.id;
        editStockForm.type = investment.type;
        editStockForm.details_of_investment = investment.details_of_investment;
        editStockForm.initial_amount = investment.initial_amount;
        editStockForm.current_amount = investment.current_amount;
        editStockForm.expected_return_rate = investment.expected_return_rate;
        editStockForm.start_date = new Date(investment.start_date).toISOString().split('T')[0] || '';
        editStockForm.description = investment.description || '';
    }
});


// Submit the edited stock/reit form
const submitEditStockForm = () => {
    editStockForm.put(route('invest.update', editStockForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditStockModal();
            openAlert('success', 'Share investment updated successfully!', 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages || 'An error occurred.', 5000);
        }
    });
};

// INSURANCE POLICIES
const selectedProviderPolicies = computed(() => {
    const provider = insurances.find(i => i.value === newInvestment.type);
    return provider ? provider.policies : [];
});

/* 1 ▸ state flags */
const isEditInsuranceModalOpen = ref(false);
const editingInsurance = ref(null);  // holds the item currently being edited

/* 2 ▸ inertia-form model
   – include EVERY field your API expects; you can omit optional ones
*/
const editInsuranceForm = useForm({
    id: '',
    type: '',
    details_of_investment: '',
    frequency_of_return: '',     // monthly / yearly  ❬added❭
    initial_amount: '',
    current_amount: '',          // maturity value
    target_date: '',          // term length      ❬added❭
    expected_return_rate: '',    // %                ❬added❭
    start_date: '',
    description: '',
    insurance: 'insurance',      // backend flag     ❬added❭
});

/* 3 ▸ open / close helpers */
function openEditInsuranceModal(investment) {
    editingInsurance.value = { ...investment };
    isEditInsuranceModalOpen.value = true;
}

function closeEditInsuranceModal() {
    isEditInsuranceModalOpen.value = false;
    editingInsurance.value = null;
    editInsuranceForm.reset();
}

/* close when user clicks outside */
function closeEditInsuranceModalOnOutsideClick(event) {
    if (event.target.classList.contains('modal-overlay')) {
        closeEditInsuranceModal();
    }
}

/* 4 ▸ sync selected investment → form  */
watch(
    editingInsurance,
    (inv) => {
        if (!inv) return;

        /** If your API sometimes sends null, guard with fallback '' */
        editInsuranceForm.id = inv.id ?? '';
        editInsuranceForm.type = inv.type ?? '';
        editInsuranceForm.details_of_investment = inv.details_of_investment ?? '';
        editInsuranceForm.frequency_of_return = inv.frequency_of_return ?? '';
        editInsuranceForm.initial_amount = inv.initial_amount ?? '';
        editInsuranceForm.current_amount = inv.current_amount ?? '';
        editInsuranceForm.target_date = inv.target_date
            ? new Date(inv.target_date).toISOString().split('T')[0]
            : '';
        editInsuranceForm.description = inv.description ?? '';
        editInsuranceForm.expected_return_rate = inv.expected_return_rate ?? '';
        editInsuranceForm.start_date =
            inv.start_date
                ? new Date(inv.start_date).toISOString().split('T')[0]
                : '';
        editInsuranceForm.description = inv.description ?? '';
    },
    { immediate: true }
);

/* 5 ▸ submit handler */
function submitEditInsuranceForm() {
    editInsuranceForm.put(route('invest.update', editInsuranceForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditInsuranceModal();
            // optionally refresh the list here (Inertia’s partial reload, etc.)
            openAlert('success', 'Insurance policy updated successfully!', 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages || 'An error occurred.', 5000);
        },
    });
}
const selectedProviderEditPolicies = computed(() => {
    const provider = insurances.find(i => i.value === editInsuranceForm.type);
    return provider ? provider.policies : [];

});


</script>

<template>

    <Head title="Investment Planner" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <DashboardBackButton />
                <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                    :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />
                <div class="flex justify-between items-center mb-6">
                    <h3 class="mb-6 text-2xl font-bold">Investments Tracker</h3>
                    <button @click="openChoiceModal"
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
                    <h1 class="text-2xl font-bold text-purple-700">Fixed Income Investments</h1>
                    <InvestmentsTable :investments="fixedIncomes" @edit-investment="openEditModal"
                        @delete-investment="openDeleteModal" />
                </div>
                <div v-show="realEstateInvestments.length > 0">
                    <h1 class="text-2xl font-bold text-purple-700">Real Estate Investments</h1>
                    <RealEstateTable :investments="realEstateInvestments" @edit-investment="openEditRealEstateModal"
                        @delete-investment="openDeleteModal" />
                </div>

                <div v-show="stockInvestments.length > 0">
                    <h1 class="text-2xl font-bold text-purple-700">Stock and Reits Investments</h1>
                    <StocksTable :investments="stockInvestments" @edit-investment="openEditStockModal"
                        @delete-investment="openDeleteModal" />
                </div>

                <div v-show="insurancePolicies.length > 0">
                    <h1 class="text-2xl font-bold text-purple-700">Insurance Policies</h1>
                    <InsurancesTable :investments="insurancePolicies" @edit-investment="openEditInsuranceModal"
                        @delete-investment="openDeleteModal" />
                </div>
            </Sidebar>
        </div>

        <!-- Investment Selection modal -->
        <div v-if="isChoiceModalOpen" @click="closeChoiceModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">

            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">

                <div class="bg-purple-500 text-white px-4 py-3 flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Select Investment Type</h3>
                    <button @click="closeChoiceModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="p-5">
                    <div class="flex flex-col space-y-3">
                        <button @click="openModal"
                            class="w-full px-4 py-3 text-left bg-purple-500 hover:bg-opacity-90 text-white font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Add Fixed Income Investment
                        </button>
                        <button @click="openRealEstateModal"
                            class="w-full px-4 py-3 text-left bg-purple-500 hover:bg-opacity-90 text-white font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Add Real Estate Investment
                        </button>
                        <button @click="openStockModal"
                            class="w-full px-4 py-3 text-left bg-purple-500 hover:bg-opacity-90 text-white font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Add Stocks Investment
                        </button>
                        <button @click="openInsuranceModal"
                            class="w-full px-4 py-3 text-left bg-purple-500 hover:bg-opacity-90 text-white font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Add Insurance Policy
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Add Real Estate -->
        <div v-if="isRealEstateModalOpen" @click="closeRealEstateModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-purple-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Add Real Estate Investment</h3>
                    <button @click="closeRealEstateModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-4">
                    <div class="grid grid-cols-2 gap-x-3 gap-y-3">
                        <div class="col-span-1">
                            <label for="property_type" class="block text-gray-700 text-xs font-medium mb-1">Property
                                Type</label>
                            <select id="property_type" v-model="newInvestment.type" @change="onSelect"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                                <option value="select" hidden>Select Type</option>
                                <option v-for="estate in realEstate" :key="estate.label" :value="estate.value">{{
                                    estate.label }}</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label for="location" class="block text-gray-700 text-xs font-medium mb-1">Location</label>
                            <input type="text" id="location" v-model="newInvestment.details_of_investment"
                                placeholder="e.g., Kilimani, Nairobi"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required />
                        </div>

                        <div class="col-span-1">
                            <label for="purchase_price" class="block text-gray-700 text-xs font-medium mb-1">Purchase
                                Price</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="purchase_price" v-model="newInvestment.initial_amount"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                    min="0" required />
                            </div>
                        </div>

                        <div class="col-span-1">
                            <label for="purchase_date" class="block text-gray-700 text-xs font-medium mb-1">Purchase
                                Date</label>
                            <input type="date" id="purchase_date" v-model="newInvestment.start_date"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required />
                        </div>

                        <div class="col-span-2">
                            <label for="current_amount" class="block text-gray-700 text-xs font-medium mb-1">Rental
                                Income
                                (p.m.)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="current_amount" v-model="newInvestment.current_amount"
                                    class="w-full pl-10 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="real_estate_notes"
                            class="block text-gray-700 text-xs font-medium mb-1">Description</label>
                        <textarea id="real_estate_notes" v-model="newInvestment.description"
                            placeholder="e.g., qwetu homes, kejani homes, 3 bedroom appartment,..."
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                            rows="2"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="closeRealEstateModal"
                            class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-2 py-1 text-xs bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            {{ newInvestment.processing ? 'Saving...' : 'Add Property' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Reits Investment -->
        <div v-if="isReitsModalOpen" @click="closeReitsModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-purple-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Add Reits Investment</h3>
                    <button @click="closeReitsModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-4">
                    <div class="grid grid-cols-2 gap-x-3 gap-y-3">
                        <div class="col-span-1">
                            <label for="stock_ticker" class="block text-gray-700 text-xs font-medium mb-1">Name</label>
                            <select id="stock_ticker" v-model="newInvestment.details_of_investment" @change="
                                newInvestment.expected_return_rate = reits.find(r => r.name === newInvestment.details_of_investment).price;
                            newInvestment.current_amount = newInvestment.initial_amount * newInvestment.expected_return_rate;
                            " class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                                <option disabled value="">-- Select --</option>
                                <option v-for="reit in reits" :key="reit.name" :value="reit.name">
                                    {{ reit.name }}
                                </option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label for="number_of_shares" class="block text-gray-700 text-xs font-medium mb-1">Number of
                                Shares</label>
                            <input type="number" id="number_of_shares" v-model.number="newInvestment.initial_amount"
                                @input="newInvestment.current_amount = newInvestment.initial_amount * newInvestment.expected_return_rate"
                                step="any" min="0"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required />
                        </div>

                        <div class="col-span-1">
                            <label for="purchase_price_per_share"
                                class="block text-gray-700 text-xs font-medium mb-1">Share Price</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="purchase_price_per_share"
                                    v-model.number="newInvestment.expected_return_rate"
                                    @input="newInvestment.current_amount = newInvestment.initial_amount * newInvestment.expected_return_rate"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500 bg-gray-100 cursor-not-allowed"
                                    step="0.01" min="0" required readonly />
                            </div>
                        </div>

                        <div class="col-span-1">
                            <label for="total_price" class="block text-gray-700 text-xs font-medium mb-1">Total
                                Price</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="total_price" v-model.number="newInvestment.current_amount"
                                    @input="newInvestment.initial_amount = newInvestment.current_amount / newInvestment.expected_return_rate"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                    step="0.01" min="0" required />
                            </div>
                        </div>

                        <div class="col-span-2">
                            <label for="stock_purchase_date"
                                class="block text-gray-700 text-xs font-medium mb-1">Purchase
                                Date</label>
                            <input type="date" id="stock_purchase_date" v-model="newInvestment.start_date"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="stock_notes"
                            class="block text-gray-700 text-xs font-medium mb-1">Description</label>
                        <textarea id="stock_notes" v-model="newInvestment.description"
                            placeholder="e.g., Reason for buying, target price"
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                            rows="2"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="closeReitsModal"
                            class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-2 py-1 text-xs bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            {{ newInvestment.processing ? 'Saving...' : 'Add Reit' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Stocks Investment -->
        <div v-if="isStockModalOpen" @click="closeStockModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-purple-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Add Stock Investment</h3>
                    <button @click="closeStockModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-4">
                    <!-- STOCKS modal grid (only the inputs changed) -->
                    <div class="grid grid-cols-2 gap-x-3 gap-y-3">
                        <!-- 1. Ticker -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Stock Ticker</label>
                            <select v-model="newInvestment.type"
                                class="w-full px-2 py-1.5 text-xs text-gray-900 border rounded-md focus:ring-1 focus:ring-purple-500"
                                required>
                                <option disabled value="">Select Ticker</option>
                                <option v-for="s in shares" :key="s.ticker" :value="s.ticker">
                                    {{ s.ticker }}
                                </option>
                            </select>

                        </div>

                        <!-- 2. Exchange -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Exchange</label>
                            <select v-model="newInvestment.details_of_investment"
                                class="w-full px-2 py-1.5 text-xs border rounded-md focus:ring-1 focus:ring-purple-500"
                                required>
                                <option disabled value="">Select Exchange</option>
                                <option selected value="NSE">NSE (Nairobi)</option>
                            </select>
                        </div>

                        <!-- 3. Shares -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Number of Shares</label>
                            <input type="number" step="any" min="0" v-model.number="newInvestment.initial_amount"
                                @input="
                                    newInvestment.current_amount =
                                    newInvestment.initial_amount * newInvestment.expected_return_rate
                                    "
                                class="w-full px-2 py-1.5 text-xs border rounded-md focus:ring-1 focus:ring-purple-500"
                                required />
                        </div>

                        <!-- 4. Price / share -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Price / Share</label>
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-2 text-xs text-gray-500">KES</span>
                                <input type="number" step="0.01" min="0"
                                    v-model.number="newInvestment.expected_return_rate"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border rounded-md focus:ring-1 focus:ring-purple-500 bg-gray-100 cursor-not-allowed"
                                    required readonly />
                            </div>
                        </div>

                        <!-- 5. Total -->
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Total Price</label>
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-2 text-xs text-gray-500">KES</span>
                                <input type="number" step="0.01" min="0" v-model.number="newInvestment.current_amount"
                                    @input="
                                        newInvestment.initial_amount =
                                        newInvestment.expected_return_rate
                                            ? newInvestment.current_amount / newInvestment.expected_return_rate
                                            : 0
                                        "
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border rounded-md focus:ring-1 focus:ring-purple-500"
                                    required />
                            </div>
                        </div>

                        <!-- 6. Purchase date -->
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Purchase Date</label>
                            <input type="date" v-model="newInvestment.start_date"
                                class="w-full px-2 py-1.5 text-xs border rounded-md focus:ring-1 focus:ring-purple-500"
                                required />
                        </div>
                    </div>


                    <div class="mt-3">
                        <label for="stock_notes"
                            class="block text-gray-700 text-xs font-medium mb-1">Description</label>
                        <textarea id="stock_notes" v-model="newInvestment.description"
                            placeholder="e.g., Reason for buying, target price"
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                            rows="2"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="closeStockModal"
                            class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-2 py-1 text-xs bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            {{ newInvestment.processing ? 'Saving...' : 'Add Stock' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Insurance Policy -->
        <div v-if="isInsuranceModalOpen" @click="closeInsuranceModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-purple-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Add Insurance Policy</h3>
                    <button @click="closeInsuranceModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-4">
                    <div class="grid grid-cols-2 gap-x-3 gap-y-3">

                        <!-- Provider dropdown -->
                        <div class="col-span-2">
                            <label for="provider" class="block text-gray-700 text-xs font-medium mb-1">
                                Provider
                            </label>
                            <select id="provider" v-model="newInvestment.type" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md
               focus:outline-none focus:ring-1 focus:ring-purple-500" required>
                                <option value="select" hidden>Select Provider</option>
                                <option v-for="ins in insurances" :key="ins.value" :value="ins.value">
                                    {{ ins.name }}
                                </option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Policy Type – shown only when a provider is chosen -->
                        <div class="col-span-2" v-if="selectedProviderPolicies.length">
                            <label for="policy_type" class="block text-gray-700 text-xs font-medium mb-1">
                                Policy Type
                            </label>
                            <select id="policy_type" v-model="newInvestment.details_of_investment" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md
               focus:outline-none focus:ring-1 focus:ring-purple-500" required>
                                <option value="" hidden>Select Policy</option>
                                <option v-for="p in selectedProviderPolicies" :key="p" :value="p">
                                    {{ p }}
                                </option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Premium Frequency -->
                        <div class="col-span-1">
                            <label for="premium_frequency" class="block text-gray-700 text-xs font-medium mb-1">
                                Premium Frequency
                            </label>
                            <select id="premium_frequency" v-model="newInvestment.frequency_of_return" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md
               focus:outline-none focus:ring-1 focus:ring-purple-500" required>
                                <option value="" hidden>Select</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>

                        <!-- Premium Amount -->
                        <div class="col-span-1">
                            <label for="premium_amount" class="block text-gray-700 text-xs font-medium mb-1">
                                Premium Amount
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input id="premium_amount" v-model.number="newInvestment.initial_amount" type="number"
                                    min="0" class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md
                 focus:outline-none focus:ring-1 focus:ring-purple-500" required />
                            </div>
                        </div>

                        <!-- Term Length -->
                        <div class="col-span-1">
                            <label for="term_years" class="block text-gray-700 text-xs font-medium mb-1">
                                Term Length (Years)
                            </label>
                            <select id="term_years" v-model="newInvestment.duration_years" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md
               focus:outline-none focus:ring-1 focus:ring-purple-500" required>
                                <option value="" disabled selected>Select Years</option>
                                <option v-for="year in 31" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>

                        <input type="hidden" value="insurance" v-model="newInvestment.insurance">

                        <!-- Expected Returns -->
                        <div class="col-span-1">
                            <label for="expected_returns" class="block text-gray-700 text-xs font-medium mb-1">
                                Expected Returns (%)
                            </label>
                            <div class="relative">
                                <input id="expected_returns" v-model.number="newInvestment.expected_return_rate"
                                    type="number" min="0" step="0.01" class="w-full pr-6 pl-2 py-1.5 text-xs border border-gray-300 rounded-md
                 focus:outline-none focus:ring-1 focus:ring-purple-500" required />
                                <span
                                    class="absolute inset-y-0 right-2 flex items-center pointer-events-none text-gray-500 text-xs">
                                    %
                                </span>
                            </div>
                        </div>

                        <!-- Policy Start Date (optional) -->
                        <div class="col-span-2">
                            <label for="policy_start" class="block text-gray-700 text-xs font-medium mb-1">
                                Policy Start Date
                            </label>
                            <input id="policy_start" v-model="newInvestment.start_date" type="date" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md
               focus:outline-none focus:ring-1 focus:ring-purple-500" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-3">
                        <label for="policy_notes" class="block text-gray-700 text-xs font-medium mb-1">
                            Description / Notes
                        </label>
                        <textarea id="policy_notes" v-model="newInvestment.description"
                            placeholder="e.g., unit-linked plan, riders, exclusions…" rows="2" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md
             focus:outline-none focus:ring-1 focus:ring-purple-500"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="closeInsuranceModal" class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700
             hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-2 py-1 text-xs bg-purple-600 text-white rounded-md
             hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            {{ newInvestment.processing ? 'Saving…' : 'Add Policy' }}
                        </button>
                    </div>
                </form>
            </div>
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
                                <option value="select" hidden>Select Type</option>
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
                                    <select v-model="newInvestment.duration_years"
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500">
                                        <option value="" disabled selected>Years</option>
                                        <option v-for="year in 31" :key="year - 1" :value="year - 1">{{ year - 1 }}</option>
                                    </select>
                                </div>

                                <div class="flex-1">
                                    <select v-model="newInvestment.duration_months"
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500">
                                        <option value="" disabled selected>Months</option>
                                        <option v-for="month in 12" :key="month - 1" :value="month - 1">{{ month - 1 }}
                                        </option>
                                    </select>
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
                    <div v-show="newInvestment.type === 'mmf'" class="flex items-center gap-4 mt-2">
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
                    </div>

                    <!-- Target Amount Field -->
                    <div v-show="newInvestment.commitment == true" class="mt-4">
                        <label for="initial_amount" class="block text-gray-700 text-xs font-medium mb-1">How much do you
                            commit?</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-xs">KES</span>
                            </div>
                            <input type="number" id="target_amount" v-model="newInvestment.committed_amount"
                                class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                step="0.01" min="0" />
                        </div>
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

        <!-- Edit Real Estate -->
        <div v-if="isEditRealEstateModalOpen" @click="closeEditRealEstateModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-purple-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Edit Real Estate Investment</h3>
                    <button @click="closeEditRealEstateModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitEditRealEstateForm" class="p-4">
                    <div class="grid grid-cols-2 gap-x-3 gap-y-3">
                        <div class="col-span-1">
                            <label for="edit_property_type"
                                class="block text-gray-700 text-xs font-medium mb-1">Property Type</label>
                            <select id="edit_property_type" v-model="editRealEstateForm.type"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                                <option value="select" hidden>Select Type</option>
                                <option v-for="estate in realEstate" :key="estate.label" :value="estate.value">{{
                                    estate.label }}</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label for="edit_location"
                                class="block text-gray-700 text-xs font-medium mb-1">Location</label>
                            <input type="text" id="edit_location" v-model="editRealEstateForm.details_of_investment"
                                placeholder="e.g., Kilimani, Nairobi"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required />
                        </div>

                        <div class="col-span-1">
                            <label for="edit_purchase_price"
                                class="block text-gray-700 text-xs font-medium mb-1">Purchase Price</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="edit_purchase_price"
                                    v-model="editRealEstateForm.initial_amount"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                    min="0" step="0.01" required />
                            </div>
                        </div>

                        <div class="col-span-1">
                            <label for="edit_purchase_date"
                                class="block text-gray-700 text-xs font-medium mb-1">Purchase Date</label>
                            <input type="date" id="edit_purchase_date" v-model="editRealEstateForm.start_date"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required />
                        </div>

                        <div class="col-span-2">
                            <label for="edit_rental_income" class="block text-gray-700 text-xs font-medium mb-1">Rental
                                Income (p.m.)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="edit_rental_income" v-model="editRealEstateForm.current_amount"
                                    class="w-full pl-10 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                    min="0" step="0.01" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="edit_real_estate_notes"
                            class="block text-gray-700 text-xs font-medium mb-1">Description</label>
                        <textarea id="edit_real_estate_notes" v-model="editRealEstateForm.description"
                            placeholder="e.g., 3-bedroom apartment, 1/4 acre plot"
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                            rows="2"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="closeEditRealEstateModal"
                            class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-2 py-1 text-xs bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            {{ editRealEstateForm.processing ? 'Updating...' : 'Update Property' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Reits and Stocks -->
        <div v-if="isEditStockModalOpen" @click="closeEditStockModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-purple-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Edit Share Investment</h3>
                    <button @click="closeEditStockModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitEditStockForm" class="p-4">
                    <div class="grid grid-cols-2 gap-x-3 gap-y-3">
                        <div class="col-span-1">
                            <label class="block text-gray-700 text-xs font-medium mb-1">Name/Ticker</label>
                            <input type="text" :value="shareDisplayName"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed"
                                readonly />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-gray-700 text-xs font-medium mb-1">Number of Shares</label>
                            <input type="number" v-model.number="editStockForm.initial_amount"
                                @input="editStockForm.current_amount = editStockForm.initial_amount * editStockForm.expected_return_rate"
                                class="w-full px-2 py-1.5 text-xs border rounded-md" step="any" min="0" required />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-gray-700 text-xs font-medium mb-1">Price / Share</label>
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-2 text-xs text-gray-500">KES</span>
                                <input type="number" v-model.number="editStockForm.expected_return_rate"
                                    @input="editStockForm.current_amount = editStockForm.initial_amount * editStockForm.expected_return_rate"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border rounded-md bg-gray-100 cursor-not-allowed"
                                    step="0.01" min="0" required readonly />
                            </div>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-gray-700 text-xs font-medium mb-1">Total Price</label>
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-2 text-xs text-gray-500">KES</span>
                                <input type="number" v-model.number="editStockForm.current_amount"
                                    @input="editStockForm.initial_amount = editStockForm.expected_return_rate ? editStockForm.current_amount / editStockForm.expected_return_rate : 0"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border rounded-md" step="0.01" min="0"
                                    required />
                            </div>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-gray-700 text-xs font-medium mb-1">Purchase Date</label>
                            <input type="date" v-model="editStockForm.start_date"
                                class="w-full px-2 py-1.5 text-xs border rounded-md" required />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="block text-gray-700 text-xs font-medium mb-1">Description</label>
                        <textarea v-model="editStockForm.description"
                            placeholder="e.g., Reason for buying, target price"
                            class="w-full px-2 py-1.5 text-xs border rounded-md" rows="2"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="closeEditStockModal"
                            class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-2 py-1 text-xs bg-purple-600 text-white rounded-md hover:bg-purple-700">
                            {{ editStockForm.processing ? 'Updating...' : 'Update Investment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Insurance Policy -->
        <div v-if="isEditInsuranceModalOpen" @click="closeEditInsuranceModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <!-- Header -->
                <div class="bg-purple-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Edit Insurance Policy</h3>
                    <button @click="closeEditInsuranceModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- FORM -->
                <form @submit.prevent="submitEditInsuranceForm" class="p-4">
                    <div class="grid grid-cols-2 gap-x-3 gap-y-3">
                        <!-- Provider -->
                        <div class="col-span-2">
                            <label for="edit_provider" class="block text-gray-700 text-xs font-medium mb-1">
                                Provider
                            </label>
                            <select id="edit_provider" v-model="editInsuranceForm.type" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md
                   focus:outline-none focus:ring-1 focus:ring-purple-500" required>
                                <option value="" hidden>Select Provider</option>
                                <option v-for="ins in insurances" :key="ins.value" :value="ins.value">
                                    {{ ins.name }}
                                </option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Policy Type -->
                        <div class="col-span-2">
                            <label for="edit_policy_type" class="block text-gray-700 text-xs font-medium mb-1">
                                Policy Type
                            </label>
                            <select id="edit_policy_type" v-model="editInsuranceForm.details_of_investment" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md
                   focus:outline-none focus:ring-1 focus:ring-purple-500" required>
                                <option value="" hidden>Select Policy</option>
                                <option v-for="p in selectedProviderEditPolicies" :key="p" :value="p">
                                    {{ p }}
                                </option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Premium Frequency -->
                        <div class="col-span-1">
                            <label for="edit_premium_freq" class="block text-gray-700 text-xs font-medium mb-1">
                                Premium Frequency
                            </label>
                            <select id="edit_premium_freq" v-model="editInsuranceForm.frequency_of_return" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md
                   focus:outline-none focus:ring-1 focus:ring-purple-500" required>
                                <option value="" hidden>Select</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>

                        <!-- Premium Amount -->
                        <div class="col-span-1">
                            <label for="edit_premium_amount" class="block text-gray-700 text-xs font-medium mb-1">
                                Premium Amount
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input id="edit_premium_amount" v-model.number="editInsuranceForm.initial_amount"
                                    type="number" min="0" class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md
                     focus:outline-none focus:ring-1 focus:ring-purple-500" required />
                            </div>
                        </div>

                        <!-- maturity Date -->
                        <div class="col-span-1">
                            <label for="edit_term_years" class="block text-gray-700 text-xs font-medium mb-1">
                                Maturity Date
                            </label>
                            <input id="edit_term_years" v-model.number="editInsuranceForm.target_date" type="date"
                                min="1" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md
                   focus:outline-none focus:ring-1 focus:ring-purple-500" required />
                        </div>

                        <!-- Expected Returns -->
                        <div class="col-span-1">
                            <label for="edit_expected_returns" class="block text-gray-700 text-xs font-medium mb-1">
                                Expected Returns (%)
                            </label>
                            <div class="relative">
                                <input id="edit_expected_returns"
                                    v-model.number="editInsuranceForm.expected_return_rate" type="number" min="0"
                                    step="0.01" class="w-full pr-6 pl-2 py-1.5 text-xs border border-gray-300 rounded-md
                     focus:outline-none focus:ring-1 focus:ring-purple-500" required />
                                <span
                                    class="absolute inset-y-0 right-2 flex items-center pointer-events-none text-gray-500 text-xs">
                                    %
                                </span>
                            </div>
                        </div>

                        <!-- Maturity Value -->
                        <div class="col-span-2">
                            <label for="edit_maturity_value" class="block text-gray-700 text-xs font-medium mb-1">
                                Maturity Value
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input id="edit_maturity_value" v-model.number="editInsuranceForm.current_amount"
                                    type="number" min="0" class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md
                     focus:outline-none focus:ring-1 focus:ring-purple-500" required />
                            </div>
                        </div>

                        <!-- Policy Start Date -->
                        <div class="col-span-2">
                            <label for="edit_policy_start" class="block text-gray-700 text-xs font-medium mb-1">
                                Policy Start Date
                            </label>
                            <input id="edit_policy_start" v-model="editInsuranceForm.start_date" type="date" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md
                   focus:outline-none focus:ring-1 focus:ring-purple-500" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-3">
                        <label for="edit_policy_notes" class="block text-gray-700 text-xs font-medium mb-1">
                            Description / Notes
                        </label>
                        <textarea id="edit_policy_notes" v-model="editInsuranceForm.description"
                            placeholder="e.g., unit-linked plan, riders, exclusions…" rows="2" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md
                 focus:outline-none focus:ring-1 focus:ring-purple-500"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="closeEditInsuranceModal" class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700
                 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-2 py-1 text-xs bg-purple-600 text-white rounded-md
                 hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            {{ editInsuranceForm.processing ? 'Updating…' : 'Update Policy' }}
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