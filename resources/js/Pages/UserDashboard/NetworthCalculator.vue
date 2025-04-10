<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import NetWorthChart from '@/Components/Shared/NetworthChart.vue';
import AssetsTable from '@/Components/Shared/AssetsTable.vue';
import LiabilitiesTable from '@/Components/Shared/LiabilitiesTable.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import Alert from '@/Components/Shared/Alert.vue';


const { alertState, openAlert, clearAlert } = useAlert();

const props = defineProps({
    assets: Array,
    liabilities: Array,
});

console.log(typeof(props.liabilities[0].amount))

// Modal control
const assetModalOpen = ref(false);
const liabilityModalOpen = ref(false);

// Form data
const newAsset = useForm({
    name: '',
    type: '',
    description: '',
    value: '',
    acquisition_date: '',
});

const newLiability = useForm({
    name: '',
    category: '',
    description: '',
    amount: '',
    due_date: '',
});

// Close modal when clicking outside
const closeAssetModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        assetModalOpen.value = false;
    }
};

const closeLiabilityModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        liabilityModalOpen.value = false;
    }
};

// Close modal functions
const closeAssetModal = () => {
    assetModalOpen.value = false;
};

const closeLiabilityModal = () => {
    liabilityModalOpen.value = false;
};

// Form submission
const submitAsset = () => {
    newAsset.post(route('asset.store'), {
        onSuccess: () => {
            newAsset.reset();
            closeAssetModal();
            openAlert('success', 'Asset added successfully', 5000);
        },
        onError: () => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages, 5000);
        }
    })
};

const submitLiability = () => {
    newLiability.post(route('liability.store'), {
        onSuccess: () => {
            newLiability.reset();
            closeLiabilityModal();
            openAlert('success', 'Liability added successfully', 5000);
        },
        onError: () => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages, 5000);
        }
    })
};
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                    :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />
                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 mb-6">
                    <button @click="assetModalOpen = true"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Asset
                    </button>
                    <button @click="liabilityModalOpen = true"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Liability
                    </button>
                </div>

                <div>
                    <NetWorthChart :assets="assets" :liabilities="liabilities" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-12">
                    <div>
                        <AssetsTable :assets="assets" />
                    </div>
                    <div>
                        <LiabilitiesTable :liabilities="liabilities" />
                    </div>
                </div>
            </Sidebar>
        </div>

        <!-- Add Asset Modal -->
        <div v-if="assetModalOpen" @click="closeAssetModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <!-- Modal content -->
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-green-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Add New Asset</h3>
                    <button @click="closeAssetModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitAsset" class="p-3">
                    <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                        <!-- Name Field -->
                        <div class="col-span-1">
                            <label for="asset-name" class="block text-gray-700 text-xs font-medium mb-1">Name</label>
                            <input type="text" id="asset-name" v-model="newAsset.name"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500"
                                required />
                        </div>

                        <!-- Type Field -->
                        <div class="col-span-1">
                            <label for="asset-type" class="block text-gray-700 text-xs font-medium mb-1">Type</label>
                            <select id="asset-type" v-model="newAsset.type"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500"
                                required>
                                <option value="">Select Type</option>
                                <option value="cash">Cash</option>
                                <option value="investment">Investment</option>
                                <option value="real_estate">Real Estate</option>
                                <option value="vehicle">Vehicle</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Value Field -->
                        <div class="col-span-1">
                            <label for="asset-value" class="block text-gray-700 text-xs font-medium mb-1">Value</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="asset-value" v-model="newAsset.value"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500"
                                    step="0.01" min="0" required />
                            </div>
                        </div>

                        <!-- Acquisition Date Field -->
                        <div class="col-span-1">
                            <label for="asset-date" class="block text-gray-700 text-xs font-medium mb-1">Acquisition
                                Date</label>
                            <input type="date" id="asset-date" v-model="newAsset.acquisition_date"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500" />
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="mt-2">
                        <label for="asset-description"
                            class="block text-gray-700 text-xs font-medium mb-1">Description</label>
                        <textarea id="asset-description" v-model="newAsset.description"
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500"
                            rows="2"></textarea>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex justify-end space-x-2 mt-3">
                        <button type="button" @click="closeAssetModal"
                            class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-green-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-2 py-1 text-xs bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-green-500"
                            :disabled="newAsset.processing">
                            {{ newAsset.processing ? 'Saving...' : 'Add Asset' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Liability Modal -->
        <div v-if="liabilityModalOpen" @click="closeLiabilityModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <!-- Modal content -->
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-red-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Add New Liability</h3>
                    <button @click="closeLiabilityModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitLiability" class="p-3">
                    <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                        <!-- Name Field -->
                        <div class="col-span-1">
                            <label for="liability-name"
                                class="block text-gray-700 text-xs font-medium mb-1">Name</label>
                            <input type="text" id="liability-name" v-model="newLiability.name"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500"
                                required />
                        </div>

                        <!-- Category Field -->
                        <div class="col-span-1">
                            <label for="liability-category"
                                class="block text-gray-700 text-xs font-medium mb-1">Category</label>
                            <select id="liability-category" v-model="newLiability.category"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500"
                                required>
                                <option value="">Select Category</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="loan">Loan</option>
                                <option value="mortgage">Mortgage</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Amount Field -->
                        <div class="col-span-1">
                            <label for="liability-amount"
                                class="block text-gray-700 text-xs font-medium mb-1">Amount</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="liability-amount" v-model="newLiability.amount"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500"
                                    step="0.01" min="0" required />
                            </div>
                        </div>

                        <!-- Due Date Field -->
                        <div class="col-span-1">
                            <label for="liability-due-date" class="block text-gray-700 text-xs font-medium mb-1">Due
                                Date</label>
                            <input type="date" id="liability-due-date" v-model="newLiability.due_date"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500" />
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="mt-2">
                        <label for="liability-description"
                            class="block text-gray-700 text-xs font-medium mb-1">Description</label>
                        <textarea id="liability-description" v-model="newLiability.description"
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500"
                            rows="2"></textarea>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex justify-end space-x-2 mt-3">
                        <button type="button" @click="closeLiabilityModal"
                            class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-red-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-2 py-1 text-xs bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-1 focus:ring-red-500"
                            :disabled="newLiability.processing">
                            {{ newLiability.processing ? 'Saving...' : 'Add Liability' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>