<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
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

// Add new beneficiary
function addBeneficiary() {
    beneficiaries.value.push({
        temp_id: `temp_${Date.now()}`,
        full_name: '',
        national_id: '',
        relationship: '',
        is_minor: false,
        contact: ''
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

// Remove allocation
function removeAllocation(assetIndex, allocationIndex) {
    allocations.value[assetIndex].beneficiary_allocations.splice(allocationIndex, 1);
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
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');
            openAlert('danger', errorMessages, 10000);
        }
    });
}

// Continue to next step
function continueToFiduciaries() {
    if (!isFormValid.value) {
        openAlert('warning', 'Please complete and save beneficiary allocations before proceeding.', 5000);
        return;
    }

    window.location.href = route('legacy.fiduciaries');
}

// Initialize with at least one beneficiary if none exist
if (beneficiaries.value.length === 0) {
    addBeneficiary();
}
</script>

<template>

    <Head title="Beneficiaries - Legacy & Estate Planning" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <DashboardBackButton />

                <div class="max-w-6xl mx-auto p-6">
                    <Alert v-if="alertState && alertState.show" :type="alertState.type" :message="alertState.message"
                        @close="clearAlert" />

                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            Step 2: Beneficiaries & Allocations
                        </h1>
                        <p class="text-gray-600">
                            Add your beneficiaries and specify how your assets will be distributed.
                        </p>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="mb-8">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <div
                                    class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                    ✓</div>
                                <span class="text-green-600 font-medium">Assets</span>
                            </div>
                            <div class="w-12 h-px bg-green-600"></div>
                            <div class="flex items-center space-x-2">
                                <div
                                    class="bg-purple-500 w-8 h-8 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                    2</div>
                                <span class="text-purple-600 font-medium">Beneficiaries</span>
                            </div>
                            <div class="w-12 h-px bg-gray-300"></div>
                            <div class="flex items-center space-x-2">
                                <div
                                    class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm">
                                    3</div>
                                <span class="text-gray-500">Fiduciaries</span>
                            </div>
                            <div class="w-12 h-px bg-gray-300"></div>
                            <div class="flex items-center space-x-2">
                                <div
                                    class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm">
                                    4</div>
                                <span class="text-gray-500">Insurance</span>
                            </div>
                            <div class="w-12 h-px bg-gray-300"></div>
                            <div class="flex items-center space-x-2">
                                <div
                                    class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm">
                                    5</div>
                                <span class="text-gray-500">Review</span>
                            </div>
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

                    <!-- Beneficiaries Section -->
                    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">Beneficiaries</h2>
                            <button @click="addBeneficiary"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                <PlusIcon class="w-5 h-5 mr-2" />
                                Add Beneficiary
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div v-for="(beneficiary, index) in beneficiaries"
                                :key="beneficiary.id || beneficiary.temp_id" class="border rounded-lg p-4 bg-gray-50">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        Beneficiary {{ index + 1 }}
                                    </h3>
                                    <button v-if="beneficiaries.length > 1" @click="removeBeneficiary(index)"
                                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
                                </div>

                                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <Input v-model="beneficiary.full_name" label="Full Name *" placeholder="John Doe"
                                        required />

                                    <Input v-model="beneficiary.national_id" label="National ID (Optional)"
                                        placeholder="12345678" />

                                    <Input v-model="beneficiary.relationship" label="Relationship (Optional)"
                                        placeholder="Son, Daughter, Spouse, etc." />

                                    <Input v-model="beneficiary.contact" label="Contact (Optional)"
                                        placeholder="Phone or email" />

                                    <div class="flex items-center space-x-3 pt-6">
                                        <input v-model="beneficiary.is_minor" type="checkbox"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                                        <label class="text-sm text-gray-700">Is Minor</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asset Allocations Section -->
                    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Asset Allocations</h2>

                        <div class="space-y-8">
                            <div v-for="(asset, assetIndex) in assets" :key="asset.id" class="border rounded-lg p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ asset.name }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            Value: {{ new Intl.NumberFormat('en-KE', {
                                                style: 'currency', currency:
                                                    'KES'
                                            }).format(asset.value) }}
                                        </p>
                                    </div>

                                    <div class="text-right">
                                        <div class="text-sm text-gray-500 mb-1">Total Allocated</div>
                                        <div :class="[
                                            'text-lg font-semibold',
                                            isAssetAllocationValid(assetIndex) ? 'text-green-600' : 'text-red-600'
                                        ]">
                                            {{ getTotalPercentage(assetIndex).toFixed(1) }}%
                                        </div>
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
                    <div v-if="assets.length === 0" class="text-center py-12">
                        <UserGroupIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No assets to allocate</h3>
                        <p class="text-gray-600 mb-6">
                            Please go back to the assets step and add some assets first.
                        </p>
                        <button @click="window.location.href = route('legacy.assets')"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            Go to Assets
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between pt-8 border-t">
                        <button @click="window.location.href = route('legacy.assets')"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            ← Back to Assets
                        </button>

                        <div class="space-x-4">
                            <button @click="submitForm" :disabled="form.processing || !isFormValid"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                {{ form.processing ? 'Saving...' : 'Save Allocations' }}
                            </button>

                            <button @click="continueToFiduciaries" :disabled="!isFormValid"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                Continue to Fiduciaries →
                            </button>
                        </div>
                    </div>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
