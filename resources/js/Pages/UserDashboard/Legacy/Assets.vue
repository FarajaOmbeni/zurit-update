<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import Input from '@/Components/Shared/Input.vue';
import Select from '@/Components/Shared/Select.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import { PlusIcon, BuildingOfficeIcon, BanknotesIcon, CalendarIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const props = defineProps({
    assets: {
        type: Array,
        default: () => []
    }
});

const { openAlert, clearAlert, alertState } = useAlert();
const showForm = ref(false);
const showDeleteModal = ref(false);
const editingAsset = ref(null);
const assetToDelete = ref(null);

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

function getAssetTypeLabel(type) {
    const assetType = assetTypes.find(t => t.value === type);
    return assetType ? assetType.label : type;
}

function getTotalValue() {
    return props.assets.reduce((total, asset) => total + parseFloat(asset.value || 0), 0);
}

function continueToBeneficiaries() {
    if (props.assets.length === 0) {
        openAlert('warning', 'Please add at least one asset before proceeding to beneficiaries.', 5000);
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
    form.acquisition_date = asset.acquisition_date || '';
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
                            <div class="text-sm text-gray-500 mb-1">Total Asset Value</div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ formatCurrency(getTotalValue()) }}
                            </div>
                        </div>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="mb-8">
                        <div class="flex items-center space-x-4">
                            <!-- Step 1: Assets (Current) -->
                            <Link :href="route('legacy.assets')" class="flex items-center space-x-2 cursor-pointer">
                            <div
                                class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                1</div>
                            <span class="text-purple-600 font-medium">Assets</span>
                            </Link>
                            <div class="w-12 h-px bg-gray-300"></div>

                            <!-- Step 2: Beneficiaries -->
                            <Link :href="route('legacy.beneficiaries')"
                                class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm hover:bg-purple-400 hover:text-white transition-colors">
                                2</div>
                            <span class="text-gray-500 hover:text-purple-600 transition-colors">Beneficiaries</span>
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

                    <!-- Empty State -->
                    <div v-else class="text-center py-12">
                        <BuildingOfficeIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No assets added yet</h3>
                        <p class="text-gray-600 mb-6">
                            Start by adding your first asset to begin your estate plan.
                        </p>
                        <button @click="editingAsset = null; showForm = true"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            <PlusIcon class="w-5 h-5 mr-2" />
                            Add Your First Asset
                        </button>
                    </div>

                    <!-- Continue Button -->
                    <div v-if="assets.length > 0" class="flex justify-end pt-8 border-t">
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
</template>
