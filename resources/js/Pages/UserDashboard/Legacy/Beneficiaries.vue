<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import Input from '@/Components/Shared/Input.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import { PlusIcon, TrashIcon, UserGroupIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    beneficiaries: {
        type: Array,
        default: () => []
    },
    assets: {
        type: Array,
        default: () => []
    }
});

const { openAlert, clearAlert, alertState } = useAlert();
const showBeneficiaryForm = ref(false);

// Initialize beneficiaries and allocations
const beneficiaries = ref([...props.beneficiaries]);
const allocations = ref(
    props.assets.map(asset => ({
        asset_id: asset.id,
        beneficiary_allocations: asset.beneficiary_allocations?.map(allocation => ({
            beneficiary_id: allocation.beneficiary_id,
            percentage: allocation.percentage,
            conditions: allocation.conditions || '',
            contingent_of: allocation.contingent_of || null
        })) || []
    }))
);

// Form for saving everything
const form = useForm({
    beneficiaries: [],
    allocations: []
});

// Form for saving individual beneficiary
const beneficiaryForm = useForm({
    full_name: '',
    national_id: '',
    relationship: '',
    is_minor: false,
    contact: ''
});

// Form for saving individual asset allocation
const allocationForm = useForm({
    asset_id: '',
    beneficiary_allocations: []
});

// Edit allocation modal state
const editModalOpen = ref(false);
const editingAsset = ref(null);
const editingAssetIndex = ref(null);
const modalAllocations = ref([]);

// Delete confirmation modal state
const deleteModalOpen = ref(false);
const deletingAllocation = ref(null);
const deletingAssetIndex = ref(null);
const deletingAllocationIndex = ref(null);

// Form for editing allocations in modal
const editAllocationForm = useForm({
    asset_id: '',
    beneficiary_allocations: []
});

// Add new beneficiary (toggle form)
function addBeneficiary() {
    showBeneficiaryForm.value = !showBeneficiaryForm.value;
}

// Submit beneficiary form
function submitBeneficiaryForm() {
    beneficiaryForm.post(route('legacy.beneficiaries.store'), {
        onSuccess: () => {
            openAlert('success', 'Beneficiary added successfully!', 5000);
            beneficiaryForm.reset();
            showBeneficiaryForm.value = false;
            // Refresh the page to get the updated beneficiary with ID
            window.location.reload();
        },
        onError: (errors) => {
            const errorMessage = Object.values(errors || {}).flat().join(' ') || 'Failed to add beneficiary';
            openAlert('danger', errorMessage, 5000);
        }
    });
}

// Remove beneficiary
function removeBeneficiary(index) {
    const beneficiary = beneficiaries.value[index];

    // Check if this beneficiary has any allocations
    const hasAllocations = allocations.value.some(assetAllocation =>
        assetAllocation.beneficiary_allocations.some(allocation =>
            allocation.beneficiary_id === beneficiary.id ||
            allocation.beneficiary_id === beneficiary.temp_id
        )
    );

    if (hasAllocations) {
        if (!confirm('This beneficiary has asset allocations. Removing them will clear all their allocations. Continue?')) {
            return;
        }

        // Remove all allocations for this beneficiary
        allocations.value.forEach(assetAllocation => {
            assetAllocation.beneficiary_allocations = assetAllocation.beneficiary_allocations.filter(allocation =>
                allocation.beneficiary_id !== beneficiary.id &&
                allocation.beneficiary_id !== beneficiary.temp_id
            );
        });
    }

    beneficiaries.value.splice(index, 1);
}

// Add allocation for an asset
function addAllocation(assetIndex) {
    if (beneficiaries.value.length === 0) {
        openAlert('warning', 'Please add at least one beneficiary first.', 5000);
        return;
    }

    allocations.value[assetIndex].beneficiary_allocations.push({
        beneficiary_id: '',
        percentage: 0,
        conditions: '',
        contingent_of: null
    });
}

// Open delete confirmation modal
function openDeleteConfirmation(assetIndex, allocationIndex) {
    const asset = props.assets[assetIndex];
    const allocation = allocations.value[assetIndex].beneficiary_allocations[allocationIndex];

    deletingAllocation.value = allocation;
    deletingAssetIndex.value = assetIndex;
    deletingAllocationIndex.value = allocationIndex;
    deleteModalOpen.value = true;
}

// Close delete confirmation modal
function closeDeleteConfirmation() {
    deleteModalOpen.value = false;
    deletingAllocation.value = null;
    deletingAssetIndex.value = null;
    deletingAllocationIndex.value = null;
}

// Confirm and delete allocation
function confirmDeleteAllocation() {
    if (deletingAssetIndex.value !== null && deletingAllocationIndex.value !== null) {
        const allocation = deletingAllocation.value;

        // If this allocation has an ID, it's saved in the database - delete it
        if (allocation.id) {
            // Make API call to delete from database
            fetch(route('legacy.asset-allocation.delete', allocation.id), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Remove from local state
                        allocations.value[deletingAssetIndex.value].beneficiary_allocations.splice(deletingAllocationIndex.value, 1);
                        openAlert('success', data.message, 3000);
                    } else {
                        openAlert('danger', 'Failed to delete allocation. Please try again.', 5000);
                    }
                })
                .catch(error => {
                    console.error('Error deleting allocation:', error);
                    openAlert('danger', 'Failed to delete allocation. Please try again.', 5000);
                })
                .finally(() => {
                    closeDeleteConfirmation();
                });
        } else {
            // This is a local-only allocation, just remove from state
            allocations.value[deletingAssetIndex.value].beneficiary_allocations.splice(deletingAllocationIndex.value, 1);
            openAlert('success', 'Allocation removed successfully!', 3000);
            closeDeleteConfirmation();
        }
    }
}

// Remove allocation (legacy function - now calls confirmation)
function removeAllocation(assetIndex, allocationIndex) {
    openDeleteConfirmation(assetIndex, allocationIndex);
}

// Calculate total percentage for an asset
function getTotalPercentage(assetIndex) {
    return allocations.value[assetIndex].beneficiary_allocations.reduce(
        (total, allocation) => total + parseFloat(allocation.percentage || 0),
        0
    );
}

// Check if asset allocation is valid (totals 100%)
function isAssetAllocationValid(assetIndex) {
    const total = getTotalPercentage(assetIndex);
    return Math.abs(total - 100) < 0.01;
}

// Check if asset allocation is saved (has existing allocations in database)
function isAssetAllocationSaved(assetIndex) {
    const asset = props.assets[assetIndex];
    return asset.beneficiary_allocations && asset.beneficiary_allocations.length > 0;
}

// Get validation errors
const validationErrors = computed(() => {
    const errors = [];

    // Check beneficiaries
    beneficiaries.value.forEach((beneficiary, index) => {
        if (!beneficiary.full_name) {
            errors.push(`Beneficiary ${index + 1}: Full name is required`);
        }
    });

    // Check allocations
    allocations.value.forEach((assetAllocation, assetIndex) => {
        const asset = props.assets[assetIndex];
        const total = getTotalPercentage(assetIndex);

        if (assetAllocation.beneficiary_allocations.length > 0) {
            if (Math.abs(total - 100) > 0.01) {
                errors.push(`${asset.name}: Allocations must total 100% (currently ${total.toFixed(1)}%)`);
            }

            assetAllocation.beneficiary_allocations.forEach((allocation, allocIndex) => {
                if (!allocation.beneficiary_id) {
                    errors.push(`${asset.name} - Allocation ${allocIndex + 1}: Please select a beneficiary`);
                }
                if (!allocation.percentage || allocation.percentage <= 0) {
                    errors.push(`${asset.name} - Allocation ${allocIndex + 1}: Percentage must be greater than 0`);
                }
            });
        }
    });

    return errors;
});

// Check if form is valid
const isFormValid = computed(() => {
    return beneficiaries.value.length > 0 &&
        beneficiaries.value.every(b => b.full_name) &&
        validationErrors.value.length === 0;
});

// Check if step is completed (all assets allocated and valid)
const isStepCompleted = computed(() => {
    if (beneficiaries.value.length === 0) return false;
    if (props.assets.length === 0) return false;

    return allocations.value.every((assetAllocation, index) => {
        const total = getTotalPercentage(index);
        return assetAllocation.beneficiary_allocations.length > 0 &&
            Math.abs(total - 100) < 0.01;
    });
});

// Get beneficiary options for select
function getBeneficiaryOptions() {
    return beneficiaries.value
        .filter(b => b.full_name)
        .map(b => ({
            value: b.id || b.temp_id,
            label: b.full_name
        }));
}

// Get beneficiary name by ID
function getBeneficiaryName(beneficiaryId) {
    const beneficiary = beneficiaries.value.find(b =>
        (b.id && b.id === beneficiaryId) || (b.temp_id && b.temp_id === beneficiaryId)
    );
    return beneficiary ? beneficiary.full_name : 'Unknown';
}


// Open edit allocation modal
function openEditAllocationModal(assetIndex) {
    const asset = props.assets[assetIndex];

    if (asset.beneficiary_allocations && asset.beneficiary_allocations.length > 0) {
        editingAsset.value = asset;
        editingAssetIndex.value = assetIndex;

        // Load existing allocations into modal
        modalAllocations.value = asset.beneficiary_allocations.map(allocation => ({
            beneficiary_id: allocation.beneficiary_id,
            percentage: allocation.percentage,
            conditions: allocation.conditions || '',
            contingent_of: allocation.contingent_of || null
        }));

        editModalOpen.value = true;
    } else {
        openAlert('warning', 'No existing allocations found for this asset.', 3000);
    }
}

// Close edit allocation modal
function closeEditAllocationModal() {
    editModalOpen.value = false;
    editingAsset.value = null;
    editingAssetIndex.value = null;
    modalAllocations.value = [];
    editAllocationForm.reset();
}

// Add allocation in modal
function addModalAllocation() {
    modalAllocations.value.push({
        beneficiary_id: '',
        percentage: 0,
        conditions: '',
        contingent_of: null
    });
}

// Remove allocation in modal
function removeModalAllocation(index) {
    modalAllocations.value.splice(index, 1);
}

// Calculate total percentage in modal
function getModalTotalPercentage() {
    return modalAllocations.value.reduce(
        (total, allocation) => total + parseFloat(allocation.percentage || 0),
        0
    );
}

// Check if modal allocation is valid
function isModalAllocationValid() {
    const total = getModalTotalPercentage();
    return Math.abs(total - 100) < 0.01;
}

// Save edited allocations from modal
function saveEditedAllocations() {
    if (modalAllocations.value.length === 0) {
        openAlert('warning', 'Please add at least one allocation.', 5000);
        return;
    }

    // Validate that all allocations have beneficiaries selected
    const hasInvalidAllocations = modalAllocations.value.some(allocation =>
        !allocation.beneficiary_id || allocation.percentage <= 0
    );

    if (hasInvalidAllocations) {
        openAlert('danger', 'Please ensure all allocations have a beneficiary selected and percentage greater than 0.', 5000);
        return;
    }

    // Validate total percentage
    const totalPercentage = getModalTotalPercentage();
    if (Math.abs(totalPercentage - 100) > 0.01) {
        openAlert('danger', `Allocations must total 100%. Current total: ${totalPercentage.toFixed(1)}%`, 5000);
        return;
    }

    // Prepare the allocation data
    editAllocationForm.asset_id = editingAsset.value.id;
    editAllocationForm.beneficiary_allocations = modalAllocations.value.map(allocation => ({
        beneficiary_id: allocation.beneficiary_id,
        percentage: allocation.percentage,
        conditions: allocation.conditions || null,
        contingent_of: allocation.contingent_of || null
    }));

    editAllocationForm.post(route('legacy.asset-allocation.store'), {
        onSuccess: () => {
            openAlert('success', `Allocations for "${editingAsset.value.name}" updated successfully!`, 5000);
            closeEditAllocationModal();
            // Refresh the page to get the updated allocations
            window.location.reload();
        },
        onError: (errors) => {
            const errorMessage = Object.values(errors || {}).flat().join(' ') || 'Failed to update allocation';
            openAlert('danger', errorMessage, 5000);
        }
    });
}

// Save individual asset allocation
function saveAssetAllocation(assetIndex) {
    const asset = props.assets[assetIndex];
    const assetAllocation = allocations.value[assetIndex];

    // Validate that we have allocations
    if (assetAllocation.beneficiary_allocations.length === 0) {
        openAlert('warning', 'Please add at least one allocation for this asset.', 5000);
        return;
    }

    // Validate that all allocations have beneficiaries selected
    const hasInvalidAllocations = assetAllocation.beneficiary_allocations.some(allocation =>
        !allocation.beneficiary_id || allocation.percentage <= 0
    );

    if (hasInvalidAllocations) {
        openAlert('danger', 'Please ensure all allocations have a beneficiary selected and percentage greater than 0.', 5000);
        return;
    }

    // Validate total percentage
    const totalPercentage = getTotalPercentage(assetIndex);
    if (Math.abs(totalPercentage - 100) > 0.01) {
        openAlert('danger', `Allocations must total 100%. Current total: ${totalPercentage.toFixed(1)}%`, 5000);
        return;
    }

    // Prepare the allocation data
    allocationForm.asset_id = asset.id;
    allocationForm.beneficiary_allocations = assetAllocation.beneficiary_allocations.map(allocation => ({
        beneficiary_id: allocation.beneficiary_id,
        percentage: allocation.percentage,
        conditions: allocation.conditions || null,
        contingent_of: allocation.contingent_of || null
    }));

    allocationForm.post(route('legacy.asset-allocation.store'), {
        onSuccess: () => {
            const isEdit = isAssetAllocationSaved(assetIndex);
            const message = isEdit
                ? `Allocations for "${asset.name}" updated successfully!`
                : `Allocations for "${asset.name}" saved successfully!`;
            openAlert('success', message, 5000);
            // Refresh the page to get the updated allocations
            window.location.reload();
        },
        onError: (errors) => {
            const errorMessage = Object.values(errors || {}).flat().join(' ') || 'Failed to save allocation';
            openAlert('danger', errorMessage, 5000);
        }
    });
}

// Submit form
function submitForm() {
    if (!isFormValid.value) {
        openAlert('danger', 'Please fix the validation errors before saving.', 8000);
        return;
    }

    // Prepare data
    form.beneficiaries = beneficiaries.value.map(b => ({
        ...b,
        temp_id: b.temp_id || b.id
    }));

    form.allocations = allocations.value.map(assetAllocation => ({
        ...assetAllocation,
        beneficiary_allocations: assetAllocation.beneficiary_allocations.map(allocation => ({
            ...allocation,
            beneficiary_temp_id: allocation.beneficiary_id
        }))
    }));

    form.post(route('legacy.allocations.save'), {
        onSuccess: () => {
            openAlert('success', 'Beneficiaries and allocations saved successfully!', 5000);
        },
        onError: (errors) => {
            const errorMessage = Object.values(errors || {}).flat().join(' ') || 'Failed to save beneficiaries and allocations';
            openAlert('danger', errorMessage, 5000);
        }
    });
}

// Continue to next step
function continueToFiduciaries() {
    if (beneficiaries.value.length === 0) {
        openAlert('warning', 'Please add at least one beneficiary before proceeding to fiduciaries.', 5000);
        return;
    }

    // Check if all assets have valid allocations
    const hasUnallocatedAssets = allocations.value.some((assetAllocation, index) => {
        const asset = props.assets[index];
        return assetAllocation.beneficiary_allocations.length === 0;
    });

    if (hasUnallocatedAssets) {
        openAlert('warning', 'Please allocate all assets to beneficiaries before proceeding.', 5000);
        return;
    }

    // Check if all allocations are valid (total 100%)
    const hasInvalidAllocations = allocations.value.some((assetAllocation, index) => {
        const total = getTotalPercentage(index);
        return Math.abs(total - 100) > 0.01;
    });

    if (hasInvalidAllocations) {
        openAlert('warning', 'Please ensure all asset allocations total 100% before proceeding.', 5000);
        return;
    }

    window.location.href = route('legacy.fiduciaries');
}


</script>

<template>

    <Head title="Beneficiaries - Legacy & Estate Planning" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <DashboardBackButton />

                <div class="max-w-6xl mx-auto p-6">
                    <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                        :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />

                    <!-- Header -->
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                                Step 2: Beneficiaries & Allocations
                            </h1>
                            <p class="text-gray-600">
                                Add your beneficiaries and specify how your assets will be distributed.
                            </p>
                        </div>

                        <div v-if="isStepCompleted" class="text-right">
                            <div class="text-sm text-gray-500 mb-1">Step Status</div>
                            <div class="text-2xl font-bold text-green-600 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Complete
                            </div>
                        </div>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="mb-8">
                        <div class="flex items-center space-x-4">
                            <!-- Step 1: Assets (Completed) -->
                            <Link :href="route('legacy.assets')"
                                class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity">
                            <div
                                class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium hover:bg-green-700 transition-colors">
                                ✓</div>
                            <span
                                class="text-green-600 font-medium hover:text-green-700 transition-colors">Assets</span>
                            </Link>
                            <div class="w-12 h-px bg-green-600"></div>

                            <!-- Step 2: Beneficiaries (Current) -->
                            <Link :href="route('legacy.beneficiaries')"
                                class="flex items-center space-x-2 cursor-pointer">
                            <div :class="[
                                'w-8 h-8 text-white rounded-full flex items-center justify-center text-sm font-medium',
                                isStepCompleted ? 'bg-green-600' : 'bg-purple-500'
                            ]">
                                {{ isStepCompleted ? '✓' : '2' }}
                            </div>
                            <span :class="[
                                'font-medium',
                                isStepCompleted ? 'text-green-600' : 'text-purple-600'
                            ]">Beneficiaries</span>
                            </Link>
                            <div :class="[
                                'w-12 h-px',
                                isStepCompleted ? 'bg-green-600' : 'bg-gray-300'
                            ]"></div>

                            <!-- Step 3: Fiduciaries -->
                            <Link :href="route('legacy.fiduciaries')"
                                class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm hover:bg-purple-400 hover:text-white transition-colors">
                                3</div>
                            <span class="text-gray-500 hover:text-purple-600 transition-colors">Fiduciaries</span>
                            </Link>
                            <div class="w-12 h-px bg-gray-300"></div>

                            <!-- Step 4: Insurance -->
                            <Link :href="route('legacy.insurance')"
                                class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm hover:bg-purple-400 hover:text-white transition-colors">
                                4</div>
                            <span class="text-gray-500 hover:text-purple-600 transition-colors">Insurance</span>
                            </Link>
                            <div class="w-12 h-px bg-gray-300"></div>

                            <!-- Step 5: Review -->
                            <Link :href="route('legacy.review')"
                                class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm hover:bg-purple-400 hover:text-white transition-colors">
                                5</div>
                            <span class="text-gray-500 hover:text-purple-600 transition-colors">Review</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Validation Errors -->
                    <div v-if="validationErrors.length > 0" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <ExclamationTriangleIcon class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                            <div>
                                <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following issues:</h3>
                                <ul class="text-sm text-red-700 space-y-1">
                                    <li v-for="error in validationErrors" :key="error">• {{ error }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Add Beneficiary Button -->
                    <div class="mb-6">
                        <button @click="addBeneficiary"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            <PlusIcon class="w-5 h-5 mr-2" />
                            {{ showBeneficiaryForm ? 'Cancel' : 'Add Beneficiary' }}
                        </button>
                    </div>

                    <!-- Add Beneficiary Form -->
                    <div v-if="showBeneficiaryForm" class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Beneficiary</h3>

                        <form @submit.prevent="submitBeneficiaryForm" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <Input v-model="beneficiaryForm.full_name" label="Full Name" placeholder="John Doe"
                                    :error="beneficiaryForm.errors.full_name" required />

                                <Input v-model="beneficiaryForm.national_id" label="National ID (Optional)"
                                    placeholder="12345678" :error="beneficiaryForm.errors.national_id" />
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <Input v-model="beneficiaryForm.relationship" label="Relationship (Optional)"
                                    placeholder="Son, Daughter, Spouse, etc."
                                    :error="beneficiaryForm.errors.relationship" />

                                <Input v-model="beneficiaryForm.contact" label="Contact (Optional)"
                                    placeholder="Phone or email" :error="beneficiaryForm.errors.contact" />
                            </div>

                            <div class="flex items-center space-x-3">
                                <input v-model="beneficiaryForm.is_minor" type="checkbox"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                <label class="text-sm text-gray-700">Is Minor</label>
                            </div>

                            <div class="flex space-x-4">
                                <button type="submit" :disabled="beneficiaryForm.processing"
                                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                    {{ beneficiaryForm.processing ? 'Adding...' : 'Add Beneficiary' }}
                                </button>

                                <button type="button" @click="showBeneficiaryForm = false"
                                    class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Beneficiaries List -->
                    <div v-if="beneficiaries.length > 0" class="space-y-4 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900">Your Beneficiaries</h3>

                        <div class="grid gap-4">
                            <div v-for="(beneficiary, index) in beneficiaries"
                                :key="beneficiary.id || beneficiary.temp_id"
                                class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <UserGroupIcon class="w-6 h-6 text-green-600 flex-shrink-0" />
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">
                                                    {{ beneficiary.full_name }}
                                                </h4>
                                                <p class="text-sm text-gray-500">
                                                    {{ beneficiary.relationship || 'No relationship specified' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div v-if="beneficiary.contact" class="text-gray-600 mb-3">
                                            Contact: {{ beneficiary.contact }}
                                        </div>

                                        <div class="flex items-center space-x-6 text-sm text-gray-500">
                                            <div v-if="beneficiary.national_id" class="flex items-center space-x-1">
                                                <span class="font-medium">ID:</span>
                                                <span>{{ beneficiary.national_id }}</span>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <span class="font-medium">Status:</span>
                                                <span
                                                    :class="beneficiary.is_minor ? 'text-orange-600' : 'text-green-600'">
                                                    {{ beneficiary.is_minor ? 'Minor' : 'Adult' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex space-x-2">
                                        <button v-if="beneficiaries.length > 1" @click="removeBeneficiary(index)"
                                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                            <TrashIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State for Beneficiaries -->
                    <div v-else class="text-center py-12">
                        <UserGroupIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No beneficiaries added yet</h3>
                        <p class="text-gray-600 mb-6">
                            Start by adding your first beneficiary to begin your estate plan.
                        </p>
                        <button @click="showBeneficiaryForm = true"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            <PlusIcon class="w-5 h-5 mr-2" />
                            Add Your First Beneficiary
                        </button>
                    </div>

                    <!-- Asset Allocations Section -->
                    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">Asset Allocations</h2>
                            <div class="text-right">
                                <div class="text-sm text-gray-500 mb-1">Allocation Status</div>
                                <div class="text-lg font-semibold text-blue-600">
                                    {{allocations.filter(a => a.beneficiary_allocations.length > 0).length}} of {{
                                        props.assets.length }} Assets Allocated
                                </div>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <div v-for="(asset, assetIndex) in props.assets" :key="asset.id"
                                class="border rounded-lg p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ asset.name }}
                                            <span v-if="isAssetAllocationSaved(assetIndex)"
                                                class="text-sm text-green-600 font-normal">(Saved)</span>
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            Value: {{ new Intl.NumberFormat('en-KE', {
                                                style: 'currency', currency:
                                                    'KES'
                                            }).format(asset.value) }}
                                        </p>
                                    </div>

                                    <div class="flex items-center space-x-4">
                                        <div class="text-right">
                                            <div class="text-sm text-gray-500 mb-1">Total Allocated</div>
                                            <div :class="[
                                                'text-lg font-semibold',
                                                isAssetAllocationValid(assetIndex) ? 'text-green-600' : 'text-red-600'
                                            ]">
                                                {{ getTotalPercentage(assetIndex).toFixed(1) }}%
                                            </div>
                                        </div>

                                        <button v-if="!isAssetAllocationSaved(assetIndex)"
                                            @click="saveAssetAllocation(assetIndex)"
                                            :disabled="!isAssetAllocationValid(assetIndex) || allocationForm.processing"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                            {{ allocationForm.processing ? 'Saving...' : 'Save Allocation' }}
                                        </button>

                                        <button v-else @click="openEditAllocationModal(assetIndex)"
                                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                            Edit Allocation
                                        </button>
                                    </div>
                                </div>

                                <div class="space-y-4 mb-4">
                                    <div v-for="(allocation, allocIndex) in allocations[assetIndex].beneficiary_allocations"
                                        :key="allocIndex" class="flex items-end space-x-4 p-4 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Beneficiary
                                            </label>
                                            <select v-model="allocation.beneficiary_id"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                <option value="">Select beneficiary</option>
                                                <option v-for="option in getBeneficiaryOptions()" :key="option.value"
                                                    :value="option.value">
                                                    {{ option.label }}
                                                </option>
                                            </select>
                                        </div>

                                        <div class="w-24">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Percentage
                                            </label>
                                            <input v-model.number="allocation.percentage" type="number" step="0.1"
                                                min="0" max="100"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                        </div>

                                        <div class="flex-1">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Conditions (Optional)
                                            </label>
                                            <input v-model="allocation.conditions" type="text"
                                                placeholder="e.g., Upon reaching age 25"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                        </div>

                                        <button @click="removeAllocation(assetIndex, allocIndex)"
                                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                            <TrashIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>

                                <button @click="addAllocation(assetIndex)"
                                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                    <PlusIcon class="w-5 h-5 mr-2" />
                                    Add Allocation
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="props.assets.length === 0" class="text-center py-12">
                        <UserGroupIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No assets to allocate</h3>
                        <p class="text-gray-600 mb-6">
                            Please go back to the assets step and add some assets first.
                        </p>
                        <Link :href="route('legacy.assets')"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                        Go to Assets
                        </Link>
                    </div>

                    <!-- Continue Button -->
                    <div v-if="props.assets.length > 0 && beneficiaries.length > 0"
                        class="flex justify-end pt-8 border-t">
                        <button @click="continueToFiduciaries" :disabled="!isFormValid"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            Continue to Fiduciaries →
                        </button>
                    </div>
                </div>
            </Sidebar>
        </div>

        <!-- Edit Allocation Modal -->
        <div v-if="editModalOpen" @click="closeEditAllocationModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div @click.stop class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
                <div class="bg-purple-600 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-medium">Edit Asset Allocations</h3>
                    <button @click="closeEditAllocationModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <!-- Asset Details -->
                    <div v-if="editingAsset" class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ editingAsset.name }}</h4>
                        <p class="text-sm text-gray-600">
                            Value: {{ new Intl.NumberFormat('en-KE', {
                                style: 'currency', currency: 'KES'
                            }).format(editingAsset.value) }}
                        </p>
                    </div>

                    <!-- Total Percentage Display -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Total Allocated</span>
                            <span :class="[
                                'text-lg font-semibold',
                                isModalAllocationValid() ? 'text-green-600' : 'text-red-600'
                            ]">
                                {{ getModalTotalPercentage().toFixed(1) }}%
                            </span>
                        </div>
                        <div v-if="!isModalAllocationValid()" class="text-sm text-red-600 mt-1">
                            Allocations must total exactly 100%
                        </div>
                    </div>

                    <!-- Allocations List -->
                    <div class="space-y-4 mb-6">
                        <div v-for="(allocation, index) in modalAllocations" :key="index"
                            class="flex items-end space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Beneficiary
                                </label>
                                <select v-model="allocation.beneficiary_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">Select beneficiary</option>
                                    <option v-for="option in getBeneficiaryOptions()" :key="option.value"
                                        :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>

                            <div class="w-24">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Percentage
                                </label>
                                <input v-model.number="allocation.percentage" type="number" step="0.1" min="0" max="100"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                            </div>

                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Conditions (Optional)
                                </label>
                                <input v-model="allocation.conditions" type="text"
                                    placeholder="e.g., Upon reaching age 25"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                            </div>

                            <button @click="removeModalAllocation(index)"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded-lg flex items-center transition-colors duration-200">
                                <TrashIcon class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Add Allocation Button -->
                    <div class="mb-6">
                        <button @click="addModalAllocation"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            <PlusIcon class="w-5 h-5 mr-2" />
                            Add Allocation
                        </button>
                    </div>

                    <!-- Modal Buttons -->
                    <div class="flex justify-end space-x-4">
                        <button @click="closeEditAllocationModal"
                            class="px-4 py-2 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button @click="saveEditedAllocations"
                            :disabled="!isModalAllocationValid() || editAllocationForm.processing"
                            class="px-4 py-2 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ editAllocationForm.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="deleteModalOpen" @click="closeDeleteConfirmation"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div @click.stop class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
                <div class="bg-red-600 text-white px-6 py-4 flex justify-between items-center rounded-t-lg">
                    <h3 class="text-lg font-medium">Confirm Deletion</h3>
                    <button @click="closeDeleteConfirmation" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="flex items-start space-x-3 mb-4">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Are you sure?</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                You are about to delete the allocation for
                                <span class="font-medium">{{ getBeneficiaryName(deletingAllocation?.beneficiary_id)
                                }}</span>
                                from
                                <span class="font-medium">{{ props.assets[deletingAssetIndex]?.name }}</span>.
                                This action cannot be undone.
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button @click="closeDeleteConfirmation"
                            class="px-4 py-2 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-red-500">
                            Cancel
                        </button>
                        <button @click="confirmDeleteAllocation"
                            class="px-4 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-1 focus:ring-red-500">
                            Delete Allocation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
