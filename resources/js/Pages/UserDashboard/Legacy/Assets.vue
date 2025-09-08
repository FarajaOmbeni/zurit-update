<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import Input from '@/Components/Shared/Input.vue';
import Select from '@/Components/Shared/Select.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import { PlusIcon, BuildingOfficeIcon, BanknotesIcon, CalendarIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const props = defineProps({
    assets: {
        type: Array,
        default: () => []
    }
});

const { openAlert, clearAlert, alertState } = useAlert();
const showForm = ref(false);

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

function navigateToStep(step) {
    const routes = {
        'assets': route('legacy.assets'),
        'beneficiaries': route('legacy.beneficiaries'),
        'fiduciaries': route('legacy.fiduciaries'),
        'insurance': route('legacy.insurance'),
        'review': route('legacy.review')
    };

    if (routes[step]) {
        window.location.href = routes[step];
    }
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
                </div>

                <!-- Progress Indicator -->
                <div class="mb-8">
                    <div class="flex items-center space-x-4">
                        <!-- Step 1: Assets (Current) -->
                        <div class="flex items-center space-x-2 cursor-pointer" @click="navigateToStep('assets')">
                            <div
                                class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                1</div>
                            <span class="text-purple-600 font-medium">Assets</span>
                        </div>
                        <div class="w-12 h-px bg-gray-300"></div>

                        <!-- Step 2: Beneficiaries -->
                        <div class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity"
                            @click="navigateToStep('beneficiaries')">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm hover:bg-purple-400 hover:text-white transition-colors">
                                2</div>
                            <span class="text-gray-500 hover:text-purple-600 transition-colors">Beneficiaries</span>
                        </div>
                        <div class="w-12 h-px bg-gray-300"></div>

                        <!-- Step 3: Fiduciaries -->
                        <div class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity"
                            @click="navigateToStep('fiduciaries')">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm hover:bg-purple-400 hover:text-white transition-colors">
                                3</div>
                            <span class="text-gray-500 hover:text-purple-600 transition-colors">Fiduciaries</span>
                        </div>
                        <div class="w-12 h-px bg-gray-300"></div>

                        <!-- Step 4: Insurance -->
                        <div class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity"
                            @click="navigateToStep('insurance')">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm hover:bg-purple-400 hover:text-white transition-colors">
                                4</div>
                            <span class="text-gray-500 hover:text-purple-600 transition-colors">Insurance</span>
                        </div>
                        <div class="w-12 h-px bg-gray-300"></div>

                        <!-- Step 5: Review -->
                        <div class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity"
                            @click="navigateToStep('review')">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm hover:bg-purple-400 hover:text-white transition-colors">
                                5</div>
                            <span class="text-gray-500 hover:text-purple-600 transition-colors">Review</span>
                        </div>
                    </div>
                </div>

                <!-- Add Asset Button -->
                <div class="mb-6">
                    <button @click="showForm = !showForm"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                        <PlusIcon class="w-5 h-5 mr-2" />
                        {{ showForm ? 'Cancel' : 'Add Asset' }}
                    </button>
                </div>

                <!-- Add Asset Form -->
                <div v-if="showForm" class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Asset</h3>

                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <Input v-model="form.name" label="Asset Name" placeholder="e.g., Family Home, Toyota Camry"
                                :error="form.errors.name" required />

                            <Select v-model="form.type" label="Asset Type" :options="assetTypes"
                                :error="form.errors.type" required />
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <Input v-model="form.value" type="number" step="0.01" min="0" label="Estimated Value (KES)"
                                placeholder="0.00" :error="form.errors.value" required />

                            <Input v-model="form.acquisition_date" type="date" label="Acquisition Date (Optional)"
                                :error="form.errors.acquisition_date" />
                        </div>

                        <Input v-model="form.description" label="Description (Optional)"
                            placeholder="Additional details about the asset" :error="form.errors.description" />

                        <div class="flex space-x-4">
                            <button type="submit" :disabled="form.processing"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                {{ form.processing ? 'Adding...' : 'Add Asset' }}
                            </button>

                            <button type="button" @click="showForm = false"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
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
                    <button @click="showForm = true"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                        <PlusIcon class="w-5 h-5 mr-2" />
                        Add Your First Asset
                    </button>
                </div>

                <!-- Continue Button -->
                <div v-if="assets.length > 0" class="flex justify-end pt-8 border-t">
                    <button @click="continueToBeneficiaries"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                        Continue to Beneficiaries →
                    </button>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
