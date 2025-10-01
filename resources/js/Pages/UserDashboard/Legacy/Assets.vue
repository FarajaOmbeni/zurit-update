<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import LegacyProgress from '@/Components/Legacy/LegacyProgress.vue';
import Input from '@/Components/Shared/Input.vue';
import Select from '@/Components/Shared/Select.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import { PlusIcon, BuildingOfficeIcon, BanknotesIcon, CalendarIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { ref, computed } from 'vue';

const props = defineProps({
    assets: {
        type: Array,
        default: () => []
    },
    investments: {
        type: Array,
        default: () => []
    },
    beneficiaries: {
        type: Array,
        default: () => []
    }
});

const { openAlert, clearAlert, alertState } = useAlert();
const showForm = ref(false);
const showDeleteModal = ref(false);
const editingAsset = ref(null);
const assetToDelete = ref(null);

// Allocation modal state (per-asset)
const showAllocationModal = ref(false);
const allocationLoading = ref(false);
const selectedAsset = ref(null);
const allocationEntries = ref([]); // [{ beneficiary_id, percentage }]
const beneficiaryOptions = computed(() => props.beneficiaries.map(b => ({ value: b.id, label: b.full_name })));
const allocationTotal = computed(() => allocationEntries.value.reduce((sum, e) => sum + (parseFloat(e.percentage) || 0), 0));

const form = useForm({
    name: '',
    type: '',
    description: '',
    value: '',
    acquisition_date: '',
});

const assetTypes = [
    { value: 'real_estate', label: 'Real Estate' },
    { value: 'vehicle', label: 'Vehicle' },
    { value: 'bank_account', label: 'Bank Account' },
    { value: 'investment', label: 'Investment' },
    { value: 'business', label: 'Business' },
    { value: 'jewelry', label: 'Jewelry' },
    { value: 'artwork', label: 'Artwork' },
    { value: 'electronics', label: 'Electronics' },
    { value: 'furniture', label: 'Furniture' },
    { value: 'other', label: 'Other' }
];

const investmentTypes = [
    { value: 'stocks', label: 'Stocks' },
    { value: 'bonds', label: 'Bonds' },
    { value: 'mmf', label: 'Money Market Fund' },
    { value: 'bills', label: 'Treasury Bills' },
    { value: 'real_estate', label: 'Real Estate Investment' },
    { value: 'mutual_funds', label: 'Mutual Funds' },
    { value: 'etf', label: 'Exchange Traded Fund' },
    { value: 'retirement', label: 'Retirement Fund' },
    { value: 'crypto', label: 'Cryptocurrency' },
    { value: 'other', label: 'Other' }
];

function submitForm() {
    if (editingAsset.value) {
        form.put(route('legacy.assets.update', editingAsset.value.id), {
            onSuccess: () => {
                form.reset();
                showForm.value = false;
                editingAsset.value = null;
                openAlert('success', 'Asset updated successfully!', 5000);
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors)
                    .flat()
                    .join(' ');
                openAlert('danger', errorMessages, 10000);
            }
        });
    } else {
        form.post(route('legacy.assets.store'), {
            onSuccess: () => {
                form.reset();
                showForm.value = false;
                openAlert('success', 'Asset added successfully!', 5000);
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors)
                    .flat()
                    .join(' ');
                openAlert('danger', errorMessages, 10000);
            }
        });
    }
}

function formatCurrency(value) {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
        minimumFractionDigits: 0
    }).format(value);
}

function formatDate(date) {
    if (!date) return 'Not specified';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatDateForInput(date) {
    if (!date) return '';
    // Convert date to YYYY-MM-DD format for HTML date input
    const dateObj = new Date(date);
    const year = dateObj.getFullYear();
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const day = String(dateObj.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function getAssetTypeLabel(type) {
    const assetType = assetTypes.find(t => t.value === type);
    return assetType ? assetType.label : type;
}

function getInvestmentTypeLabel(type) {
    const investmentType = investmentTypes.find(t => t.value === type);
    return investmentType ? investmentType.label : type;
}

function getTotalValue() {
    const assetsValue = props.assets.reduce((total, asset) => total + parseFloat(asset.value || 0), 0);
    const investmentsValue = props.investments.reduce((total, investment) => total + parseFloat(investment.value || 0), 0);
    return assetsValue + investmentsValue;
}

function getAssetAllocations(asset) {
    const rel = asset.beneficiary_allocations || asset.beneficiaryAllocations || [];
    return rel.map(a => ({
        id: a.id,
        beneficiary_id: a.beneficiary_id || a.beneficiary?.id,
        beneficiary_name: a.beneficiary?.full_name,
        percentage: a.percentage,
    })).filter(a => a.beneficiary_id);
}

function getAllocatedAssetsCount() {
    return props.assets.filter(a => getAssetAllocations(a).length > 0).length;
}

function getInvestmentAllocations(investment) {
    // Investments don't have beneficiary allocations yet, but we'll prepare for future implementation
    return [];
}

function getAllocatedInvestmentsCount() {
    return props.investments.filter(i => getInvestmentAllocations(i).length > 0).length;
}

function continueToBeneficiaries() {
    if (props.assets.length === 0 && props.investments.length === 0) {
        openAlert('warning', 'Please add at least one asset or investment before proceeding to beneficiaries.', 5000);
        return;
    }
    window.location.href = route('legacy.beneficiaries');
}

function editAsset(asset) {
    editingAsset.value = asset;
    form.name = asset.name;
    form.type = asset.type;
    form.description = asset.description || '';
    form.value = asset.value;
    form.acquisition_date = asset.acquisition_date ? formatDateForInput(asset.acquisition_date) : '';
    showForm.value = true;
}

function cancelEdit() {
    editingAsset.value = null;
    form.reset();
    showForm.value = false;
}

function confirmDelete(asset) {
    assetToDelete.value = asset;
    showDeleteModal.value = true;
}

function deleteAsset() {
    if (!assetToDelete.value) return;

    form.delete(route('legacy.assets.destroy', assetToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            assetToDelete.value = null;
            openAlert('success', 'Asset deleted successfully!', 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');
            openAlert('danger', errorMessages, 10000);
        }
    });
}

function cancelDelete() {
    showDeleteModal.value = false;
    assetToDelete.value = null;
}

async function openAllocationModal(asset) {
    selectedAsset.value = asset;
    allocationEntries.value = getAssetAllocations(asset).map(a => ({
        beneficiary_id: a.beneficiary_id,
        percentage: a.percentage,
    }));
    showAllocationModal.value = true;

    // Also try fetching latest from server
    allocationLoading.value = true;
    try {
        const url = route('legacy.asset-allocation.status') + `?asset_id=${asset.id}`;
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (response.ok) {
            const data = await response.json();
            allocationEntries.value = (data.allocations || []).map(a => ({
                beneficiary_id: a.beneficiary_id,
                percentage: a.percentage,
            }));
        }
    } catch (e) {
        // ignore; we already seeded from props
    } finally {
        allocationLoading.value = false;
    }
}

function addAllocationRow() {
    allocationEntries.value.push({ beneficiary_id: null, percentage: 0 });
}

function removeAllocationRow(index) {
    allocationEntries.value.splice(index, 1);
}

function validateAllocations() {
    const ids = allocationEntries.value.map(e => e.beneficiary_id).filter(Boolean);
    const unique = new Set(ids);
    if (ids.length !== unique.size) {
        openAlert('danger', 'Each beneficiary can only be allocated once per asset.', 8000);
        return false;
    }
    if (allocationTotal.value > 100.01) {
        openAlert('danger', `Total allocation cannot exceed 100%. Current: ${allocationTotal.value}%`, 8000);
        return false;
    }
    allocationEntries.value = allocationEntries.value.filter(e => e.beneficiary_id != null);
    return true;
}

function saveAllocations() {
    if (!selectedAsset.value) return;
    if (!validateAllocations()) return;

    const payload = {
        asset_id: selectedAsset.value.id,
        beneficiary_allocations: allocationEntries.value.map(e => ({
            beneficiary_id: e.beneficiary_id,
            percentage: Number(e.percentage) || 0,
        }))
    };

    const allocForm = useForm(payload);
    allocForm.post(route('legacy.asset-allocation.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showAllocationModal.value = false;
            openAlert('success', 'Allocations saved successfully!', 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages || 'Could not save allocations.', 10000);
        }
    });
}

</script>

<template>

    <Head title="Assets - Legacy & Estate Planning" />
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
                                Step 1: Your Assets
                            </h1>
                            <p class="text-gray-600">
                                Document all your assets that will be part of your estate plan.
                            </p>
                        </div>

                        <div class="text-right">
                            <div class="text-sm text-gray-500 mb-1">Total Value</div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ formatCurrency(getTotalValue()) }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ getAllocatedAssetsCount() }}/{{ assets.length }} assets allocated
                                <span v-if="investments.length > 0"> • {{ getAllocatedInvestmentsCount() }}/{{
                                    investments.length }} investments allocated</span>
                            </div>
                        </div>
                    </div>

                    <LegacyProgress :current="1" />

                    <!-- Add Asset Button -->
                    <div class="mb-6">
                        <button @click="showForm ? (editingAsset ? cancelEdit() : showForm = false) : showForm = true"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            <PlusIcon class="w-5 h-5 mr-2" />
                            {{ showForm ? 'Cancel' : 'Add Asset' }}
                        </button>
                    </div>

                    <!-- Add/Edit Asset Form -->
                    <div v-if="showForm" class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ editingAsset ? 'Edit Asset' : 'Add New Asset' }}
                        </h3>

                        <form @submit.prevent="submitForm" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <Input v-model="form.name" label="Asset Name"
                                    placeholder="e.g., Family Home, Toyota Camry" :error="form.errors.name" required />

                                <Select v-model="form.type" label="Asset Type" :options="assetTypes"
                                    :error="form.errors.type" required />
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <Input v-model="form.value" type="number" step="0.01" min="0"
                                    label="Estimated Value (KES)" placeholder="0.00" :error="form.errors.value"
                                    required />

                                <Input v-model="form.acquisition_date" type="date" label="Acquisition Date (Optional)"
                                    :error="form.errors.acquisition_date" />
                            </div>

                            <Input v-model="form.description" label="Description (Optional)"
                                placeholder="Additional details about the asset" :error="form.errors.description" />

                            <div class="flex space-x-4">
                                <button type="submit" :disabled="form.processing"
                                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                    {{ form.processing ? (editingAsset ? 'Updating...' : 'Adding...') : (editingAsset ?
                                        'Update Asset' : 'Add Asset') }}
                                </button>

                                <button type="button" @click="editingAsset ? cancelEdit() : showForm = false"
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Assets List -->
                    <div v-if="assets.length > 0" class="space-y-4 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900">Your Assets</h3>

                        <div class="grid gap-4">
                            <div v-for="asset in assets" :key="asset.id"
                                class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <BuildingOfficeIcon class="w-6 h-6 text-blue-600 flex-shrink-0" />
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">
                                                    {{ asset.name }}
                                                </h4>
                                                <p class="text-sm text-gray-500">
                                                    {{ getAssetTypeLabel(asset.type) }}
                                                </p>
                                            </div>
                                        </div>

                                        <div v-if="asset.description" class="text-gray-600 mb-3">
                                            {{ asset.description }}
                                        </div>

                                        <div class="flex items-center space-x-6 text-sm text-gray-500">
                                            <div class="flex items-center space-x-1">
                                                <BanknotesIcon class="w-4 h-4" />
                                                <span>{{ formatCurrency(asset.value) }}</span>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <CalendarIcon class="w-4 h-4" />
                                                <span>{{ formatDate(asset.acquisition_date) }}</span>
                                            </div>
                                        </div>
                                        <!-- Allocations list -->
                                        <div class="mt-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="text-sm text-gray-600">
                                                    <span class="font-medium">Allocations:</span>
                                                    <span>
                                                        {{ getAssetAllocations(asset).length > 0 ? '' : 'None yet' }}
                                                    </span>
                                                </div>
                                                <button @click="openAllocationModal(asset)" type="button"
                                                    class="text-purple-700 hover:text-purple-800 text-sm font-medium">
                                                    Manage Allocations
                                                </button>
                                            </div>
                                            <div v-if="getAssetAllocations(asset).length > 0" class="space-y-1">
                                                <div v-for="alloc in getAssetAllocations(asset)"
                                                    :key="alloc.beneficiary_id"
                                                    class="flex items-center justify-between text-sm">
                                                    <span class="text-gray-700">{{ alloc.beneficiary_name }}</span>
                                                    <span class="text-gray-900 font-medium">{{ alloc.percentage
                                                    }}%</span>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Total: {{getAssetAllocations(asset).reduce((s, a) => s +
                                                        (parseFloat(a.percentage) || 0), 0)}}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center space-x-2 ml-4">
                                        <button @click="editAsset(asset)"
                                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                            title="Edit Asset">
                                            <PencilIcon class="w-5 h-5" />
                                        </button>
                                        <button @click="confirmDelete(asset)"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                            title="Delete Asset">
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Investments List -->
                    <div v-if="investments.length > 0" class="space-y-4 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900">Your Investments</h3>

                        <div class="grid gap-4">
                            <div v-for="investment in investments" :key="investment.id"
                                class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <BanknotesIcon class="w-6 h-6 text-green-600 flex-shrink-0" />
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">
                                                    {{ investment.name }}
                                                </h4>
                                                <p class="text-sm text-gray-500">
                                                    {{ getInvestmentTypeLabel(investment.type) }}
                                                </p>
                                            </div>
                                        </div>

                                        <div v-if="investment.description" class="text-gray-600 mb-3">
                                            {{ investment.description }}
                                        </div>

                                        <div class="flex items-center space-x-6 text-sm text-gray-500">
                                            <div class="flex items-center space-x-1">
                                                <BanknotesIcon class="w-4 h-4" />
                                                <span>{{ formatCurrency(investment.value) }}</span>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <CalendarIcon class="w-4 h-4" />
                                                <span>{{ formatDate(investment.start_date) }}</span>
                                            </div>
                                            <div v-if="investment.expected_return_rate"
                                                class="flex items-center space-x-1">
                                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                                    {{ investment.expected_return_rate }}% expected return
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Investment Details -->
                                        <div class="mt-3 text-xs text-gray-500 space-y-1">
                                            <div v-if="investment.status">
                                                <span class="font-medium">Status:</span> {{ investment.status }}
                                            </div>
                                        </div>

                                        <!-- Allocations list (placeholder for future implementation) -->
                                        <div class="mt-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="text-sm text-gray-600">
                                                    <span class="font-medium">Allocations:</span>
                                                    <span>Not yet available for investments</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons (read-only for now) -->
                                    <div class="flex items-center space-x-2 ml-4">
                                        <button disabled
                                            class="p-2 text-gray-300 cursor-not-allowed rounded-lg transition-colors duration-200"
                                            title="Investment management not available in legacy planning">
                                            <PencilIcon class="w-5 h-5" />
                                        </button>
                                        <button disabled
                                            class="p-2 text-gray-300 cursor-not-allowed rounded-lg transition-colors duration-200"
                                            title="Investment management not available in legacy planning">
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="assets.length === 0 && investments.length === 0" class="text-center py-12">
                        <BuildingOfficeIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No assets or investments added yet</h3>
                        <p class="text-gray-600 mb-6">
                            Start by adding your first asset to begin your estate plan. Your existing investments will
                            be automatically included.
                        </p>
                        <button @click="editingAsset = null; showForm = true"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            <PlusIcon class="w-5 h-5 mr-2" />
                            Add Your First Asset
                        </button>
                    </div>

                    <!-- Continue Button -->
                    <div v-if="assets.length > 0 || investments.length > 0" class="flex justify-end pt-8 border-t">
                        <Link :href="route('legacy.beneficiaries')"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                        Continue to Beneficiaries →
                        </Link>
                    </div>
                </div>
            </Sidebar>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <TrashIcon class="w-6 h-6 text-red-600" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Delete Asset
                            </h3>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-gray-600">
                            Are you sure you want to delete <strong>{{ assetToDelete?.name }}</strong>?
                            This action cannot be undone.
                        </p>
                    </div>

                    <div class="flex space-x-4">
                        <button @click="deleteAsset" :disabled="form.processing"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            {{ form.processing ? 'Deleting...' : 'Delete' }}
                        </button>
                        <button @click="cancelDelete"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <!-- Allocation Modal -->
    <div v-if="showAllocationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-xl w-full mx-4">
            <div class="p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Manage Allocations</h3>
                    <p class="text-gray-600 text-sm">Asset: <strong>{{ selectedAsset?.name }}</strong></p>
                </div>

                <div class="flex items-center justify-between mb-3">
                    <div class="text-sm text-gray-600">
                        Total: <strong :class="allocationTotal > 100 ? 'text-red-600' : 'text-gray-900'">{{
                            allocationTotal }}%</strong>
                        <span class="ml-2">(Partial allocations allowed. Do not exceed 100%.)</span>
                    </div>
                    <button @click="addAllocationRow" type="button"
                        class="bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold py-2 px-3 rounded-lg transition-colors duration-200">
                        + Add Row
                    </button>
                </div>

                <div v-if="allocationLoading" class="text-gray-600 text-sm mb-4">Loading current allocations...</div>

                <div class="space-y-3 max-h-80 overflow-y-auto mb-4">
                    <div v-for="(entry, idx) in allocationEntries" :key="idx"
                        class="grid md:grid-cols-6 gap-2 items-end">
                        <div class="md:col-span-4">
                            <Select v-model="entry.beneficiary_id" label="Beneficiary" :options="beneficiaryOptions"
                                :error="''" />
                        </div>
                        <div class="md:col-span-2">
                            <Input v-model="entry.percentage" type="number" step="0.01" min="0" max="100" label="%" />
                        </div>
                        <div class="md:col-span-6 flex justify-end">
                            <button @click="removeAllocationRow(idx)" type="button"
                                class="text-red-600 hover:text-red-700 text-sm">Remove</button>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button @click="saveAllocations" :disabled="allocationLoading"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                        Save Allocations
                    </button>
                    <button @click="showAllocationModal = false"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Allocation Modal -->
</template>
