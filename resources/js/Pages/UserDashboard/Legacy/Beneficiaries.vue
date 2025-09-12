<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import Input from '@/Components/Shared/Input.vue';
import Select from '@/Components/Shared/Select.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import { PlusIcon, PencilIcon, TrashIcon, UsersIcon } from '@heroicons/vue/24/outline';
import { ref, watch, computed } from 'vue';

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

// Beneficiary form state
const showForm = ref(false);
const editingBeneficiary = ref(null);
const showDeleteModal = ref(false);
const beneficiaryToDelete = ref(null);

const beneficiaryForm = useForm({
    full_name: '',
    national_id: '',
    relationship: '',
    is_minor: false,
    email: '',
    phone_number: '',
});

function resetBeneficiaryForm() {
    beneficiaryForm.reset();
    beneficiaryForm.clearErrors();
}

function submitBeneficiaryForm() {
    if (editingBeneficiary.value) {
        beneficiaryForm.put(route('legacy.beneficiaries.update', editingBeneficiary.value.id), {
            onSuccess: () => {
                resetBeneficiaryForm();
                showForm.value = false;
                editingBeneficiary.value = null;
                openAlert('success', 'Beneficiary updated successfully!', 5000);
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors).flat().join(' ');
                openAlert('danger', errorMessages, 10000);
            }
        });
    } else {
        beneficiaryForm.post(route('legacy.beneficiaries.store'), {
            onSuccess: () => {
                resetBeneficiaryForm();
                showForm.value = false;
                openAlert('success', 'Beneficiary added successfully!', 5000);
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors).flat().join(' ');
                openAlert('danger', errorMessages, 10000);
            }
        });
    }
}

function editBeneficiary(beneficiary) {
    editingBeneficiary.value = beneficiary;
    beneficiaryForm.full_name = beneficiary.full_name;
    beneficiaryForm.national_id = beneficiary.national_id || '';
    beneficiaryForm.relationship = beneficiary.relationship || '';
    beneficiaryForm.is_minor = !!beneficiary.is_minor;
    beneficiaryForm.email = beneficiary.email || '';
    beneficiaryForm.phone_number = beneficiary.phone_number || '';
    showForm.value = true;
}

function cancelEditBeneficiary() {
    editingBeneficiary.value = null;
    resetBeneficiaryForm();
    showForm.value = false;
}

function confirmDeleteBeneficiary(beneficiary) {
    beneficiaryToDelete.value = beneficiary;
    showDeleteModal.value = true;
}

function deleteBeneficiary() {
    if (!beneficiaryToDelete.value) return;
    beneficiaryForm.delete(route('legacy.beneficiaries.destroy', beneficiaryToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            beneficiaryToDelete.value = null;
            openAlert('success', 'Beneficiary deleted successfully!', 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages, 10000);
        }
    });
}

function cancelDeleteBeneficiary() {
    showDeleteModal.value = false;
    beneficiaryToDelete.value = null;
}

// Allocation modal state
const showAllocationModal = ref(false);
const allocationLoading = ref(false);
const selectedAssetId = ref('');
const selectedBeneficiary = ref(null);

// Allocation entries: [{ beneficiary_id, percentage, conditions?, contingent_of? }]
const allocationEntries = ref([]);

const assetOptions = computed(() => props.assets.map(a => ({ value: a.id, label: a.name })));
const beneficiaryOptions = computed(() => props.beneficiaries.map(b => ({ value: b.id, label: b.full_name })));

const allocationTotal = computed(() => allocationEntries.value.reduce((sum, e) => sum + (parseFloat(e.percentage) || 0), 0));

watch(selectedAssetId, async (newId) => {
    if (showAllocationModal.value && newId) {
        await loadAllocations(newId);
    }
});

function openAllocationModal(beneficiary) {
    selectedBeneficiary.value = beneficiary;
    // Default to first asset if not selected
    selectedAssetId.value = props.assets[0]?.id || '';
    allocationEntries.value = [];
    showAllocationModal.value = true;
    if (selectedAssetId.value) {
        // Best-effort async load; no need to await for immediate UI
        loadAllocations(selectedAssetId.value);
    }
}

async function loadAllocations(assetId) {
    allocationLoading.value = true;
    try {
        const url = route('legacy.asset-allocation.status') + `?asset_id=${assetId}`;
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Failed to load allocations');
        const data = await response.json();

        // Map existing allocations
        const existing = (data.allocations || []).map(a => ({
            beneficiary_id: a.beneficiary_id,
            percentage: a.percentage,
            conditions: a.conditions || '',
            contingent_of: a.contingent_of || null,
        }));

        // Ensure selected beneficiary is present
        const hasSelected = existing.some(e => e.beneficiary_id === selectedBeneficiary.value.id);
        if (!hasSelected) {
            existing.push({ beneficiary_id: selectedBeneficiary.value.id, percentage: 0, conditions: '', contingent_of: null });
        }
        allocationEntries.value = existing;
    } catch (e) {
        openAlert('danger', 'Could not load current allocations. Please try again.', 8000);
    } finally {
        allocationLoading.value = false;
    }
}

function addAllocationRow() {
    allocationEntries.value.push({ beneficiary_id: null, percentage: 0, conditions: '', contingent_of: null });
}

function removeAllocationRow(index) {
    allocationEntries.value.splice(index, 1);
}

function validateAllocations() {
    // No duplicate beneficiaries
    const ids = allocationEntries.value.map(e => e.beneficiary_id).filter(Boolean);
    const unique = new Set(ids);
    if (ids.length !== unique.size) {
        openAlert('danger', 'Each beneficiary can only appear once per asset.', 8000);
        return false;
    }

    // Total cannot exceed 100
    if (allocationTotal.value > 100.01) {
        openAlert('danger', `Total allocation cannot exceed 100%. Current: ${allocationTotal.value}%`, 8000);
        return false;
    }

    // Must include selected beneficiary
    if (!allocationEntries.value.some(e => e.beneficiary_id === selectedBeneficiary.value.id)) {
        openAlert('danger', 'Selected beneficiary must be included in the allocations.', 8000);
        return false;
    }

    // Remove empty rows
    allocationEntries.value = allocationEntries.value.filter(e => e.beneficiary_id != null);
    return true;
}

function saveAllocations() {
    if (!selectedAssetId.value) {
        openAlert('danger', 'Please select an asset first.', 6000);
        return;
    }
    if (!validateAllocations()) return;

    const payload = {
        asset_id: selectedAssetId.value,
        beneficiary_allocations: allocationEntries.value.map(e => ({
            beneficiary_id: e.beneficiary_id,
            percentage: Number(e.percentage) || 0,
            conditions: e.conditions || null,
            contingent_of: e.contingent_of || null,
        }))
    };

    // Use a transient form instance to POST
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

function getTotalAssetsValue() {
    return props.assets.reduce((total, asset) => total + parseFloat(asset.value || 0), 0);
}

function formatCurrency(value) {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
        minimumFractionDigits: 0
    }).format(value || 0);
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
                                Step 2: Your Beneficiaries
                            </h1>
                            <p class="text-gray-600">
                                Manage your beneficiaries.
                            </p>
                        </div>

                        <div class="text-right">
                            <div class="text-sm text-gray-500 mb-1">Total Asset Value</div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ formatCurrency(getTotalAssetsValue()) }}
                            </div>
                        </div>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="mb-8">
                        <div class="flex items-center space-x-4">
                            <!-- Step 1: Assets -->
                            <Link :href="route('legacy.assets')"
                                class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm hover:bg-purple-400 hover:text-white transition-colors">
                                1</div>
                            <span class="text-gray-500 hover:text-purple-600 transition-colors">Assets</span>
                            </Link>
                            <div class="w-12 h-px bg-gray-300"></div>

                            <!-- Step 2: Beneficiaries (Current) -->
                            <Link :href="route('legacy.beneficiaries')"
                                class="flex items-center space-x-2 cursor-pointer">
                            <div
                                class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                2</div>
                            <span class="text-purple-600 font-medium">Beneficiaries</span>
                            </Link>
                            <div class="w-12 h-px bg-gray-300"></div>

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

                    <!-- Add Beneficiary Button -->
                    <div class="mb-6">
                        <button
                            @click="showForm ? (editingBeneficiary ? cancelEditBeneficiary() : showForm = false) : showForm = true"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            <PlusIcon class="w-5 h-5 mr-2" />
                            {{ showForm ? 'Cancel' : 'Add Beneficiary' }}
                        </button>
                    </div>

                    <!-- Add/Edit Beneficiary Form -->
                    <div v-if="showForm" class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ editingBeneficiary ? 'Edit Beneficiary' : 'Add New Beneficiary' }}
                        </h3>

                        <form @submit.prevent="submitBeneficiaryForm" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <Input v-model="beneficiaryForm.full_name" label="Full Name"
                                    placeholder="e.g., Jane Doe" :error="beneficiaryForm.errors.full_name" required />
                                <Input v-model="beneficiaryForm.national_id" label="National ID (Optional)"
                                    placeholder="ID Number" :error="beneficiaryForm.errors.national_id" />
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <Input v-model="beneficiaryForm.relationship" label="Relationship (Optional)"
                                    placeholder="e.g., Spouse, Child" :error="beneficiaryForm.errors.relationship" />
                                <Input v-model="beneficiaryForm.email" type="email" label="Email (Optional)"
                                    placeholder="example@email.com" :error="beneficiaryForm.errors.email" />
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <Input v-model="beneficiaryForm.phone_number" label="Phone (Optional)"
                                    placeholder="07xx xxx xxx" :error="beneficiaryForm.errors.phone_number" />
                            </div>

                            <div class="flex items-center space-x-2">
                                <input id="is_minor" type="checkbox" v-model="beneficiaryForm.is_minor"
                                    class="h-4 w-4 text-purple-600 border-gray-300 rounded" />
                                <label for="is_minor" class="text-gray-700">Is Minor</label>
                            </div>

                            <div class="flex space-x-4">
                                <button type="submit" :disabled="beneficiaryForm.processing"
                                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                    {{ beneficiaryForm.processing ? (editingBeneficiary ? 'Updating...' : 'Adding...') :
                                        (editingBeneficiary ? 'Update Beneficiary' : 'Add Beneficiary') }}
                                </button>

                                <button type="button"
                                    @click="editingBeneficiary ? cancelEditBeneficiary() : showForm = false"
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Beneficiaries List -->
                    <div v-if="beneficiaries.length > 0" class="space-y-4 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900">Your Beneficiaries</h3>

                        <div class="grid gap-4">
                            <div v-for="b in beneficiaries" :key="b.id"
                                class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <UsersIcon class="w-6 h-6 text-blue-600 flex-shrink-0" />
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">
                                                    {{ b.full_name }}
                                                </h4>
                                                <p class="text-sm text-gray-500">
                                                    {{ b.relationship || 'Beneficiary' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="text-gray-600 mb-3">
                                            <div v-if="b.national_id"><span class="text-gray-500">ID:</span> {{
                                                b.national_id }}</div>
                                            <div v-if="b.email"><span class="text-gray-500">Email:</span> {{
                                                b.email }}</div>
                                            <div v-if="b.phone_number"><span class="text-gray-500">Phone:</span> {{
                                                b.phone_number }}</div>
                                            <div><span class="text-gray-500">Minor:</span> {{ b.is_minor ? 'Yes' : 'No'
                                            }}</div>
                                        </div>

                                        <div class="text-sm text-gray-500">
                                            <span class="text-gray-600 font-medium">Allocations:</span>
                                            <span>
                                                {{(assets || []).filter(a => (a.beneficiary_allocations ||
                                                    a.beneficiaryAllocations || []).some(al => al.beneficiary_id === b.id ||
                                                        al.beneficiary?.id === b.id)).length}} asset(s)
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center space-x-2 ml-4">
                                        <button @click="editBeneficiary(b)"
                                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                            title="Edit Beneficiary">
                                            <PencilIcon class="w-5 h-5" />
                                        </button>
                                        <button @click="confirmDeleteBeneficiary(b)"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                            title="Delete Beneficiary">
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-12">
                        <UsersIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No beneficiaries added yet</h3>
                        <p class="text-gray-600 mb-6">
                            Add your first beneficiary to start assigning your assets.
                        </p>
                        <button @click="editingBeneficiary = null; showForm = true"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            <PlusIcon class="w-5 h-5 mr-2" />
                            Add Your First Beneficiary
                        </button>
                    </div>

                    <!-- Continue Button -->
                    <div v-if="beneficiaries.length > 0" class="flex justify-end pt-8 border-t">
                        <Link :href="route('legacy.fiduciaries')"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                        Continue to Fiduciaries →
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
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Delete Beneficiary
                            </h3>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-gray-600">
                            Are you sure you want to delete <strong>{{ beneficiaryToDelete?.full_name }}</strong>?
                            If they are allocated to any asset, you must remove their allocations first.
                        </p>
                    </div>

                    <div class="flex space-x-4">
                        <button @click="deleteBeneficiary" :disabled="beneficiaryForm.processing"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            {{ beneficiaryForm.processing ? 'Deleting...' : 'Delete' }}
                        </button>
                        <button @click="cancelDeleteBeneficiary"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Allocation Modal -->
        <div v-if="showAllocationModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
                <div class="p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Manage Allocations</h3>
                        <p class="text-gray-600 text-sm">Selected beneficiary: <strong>{{ selectedBeneficiary?.full_name
                        }}</strong></p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <Select v-model="selectedAssetId" label="Asset" :options="assetOptions" :error="''" required />
                        <div class="flex items-end">
                            <button @click="addAllocationRow" type="button"
                                class="bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold py-2 px-3 rounded-lg transition-colors duration-200">
                                + Add Beneficiary Allocation
                            </button>
                        </div>
                    </div>

                    <div v-if="allocationLoading" class="text-gray-600 text-sm mb-4">Loading current allocations...
                    </div>

                    <div class="space-y-3 max-h-80 overflow-y-auto mb-4">
                        <div v-for="(entry, idx) in allocationEntries" :key="idx"
                            class="grid md:grid-cols-6 gap-2 items-end">
                            <div class="md:col-span-4">
                                <Select v-model="entry.beneficiary_id" label="Beneficiary" :options="beneficiaryOptions"
                                    :error="''" />
                            </div>
                            <div class="md:col-span-2">
                                <Input v-model="entry.percentage" type="number" step="0.01" min="0" max="100"
                                    label="%" />
                            </div>
                            <div class="md:col-span-6 flex justify-end">
                                <button @click="removeAllocationRow(idx)" type="button"
                                    class="text-red-600 hover:text-red-700 text-sm">Remove</button>
                            </div>
                        </div>
                    </div>

                    <div class="text-sm text-gray-600 mb-4">
                        Total: <strong :class="allocationTotal > 100 ? 'text-red-600' : 'text-gray-900'">{{
                            allocationTotal }}%</strong>
                        <span class="ml-2">(You can save partial allocations. Total must not exceed 100%.)</span>
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
    </AuthenticatedLayout>
</template>
