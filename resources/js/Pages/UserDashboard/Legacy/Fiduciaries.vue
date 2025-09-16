<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import LegacyProgress from '@/Components/Legacy/LegacyProgress.vue';
import Input from '@/Components/Shared/Input.vue';
import Select from '@/Components/Shared/Select.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import { ShieldCheckIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const props = defineProps({
    fiduciaries: {
        type: Object,
        default: () => null
    }
});

const { openAlert, clearAlert, alertState } = useAlert();

// Institution options for dropdown
const institutionOptions = [
    { value: 'law_firm', label: 'Law Firm' },
    { value: 'bank_trust', label: 'Bank Trust Department' },
    { value: 'trust_company', label: 'Professional Trust Company' },
    { value: 'attorney', label: 'Attorney' },
    { value: 'accountant', label: 'Accountant' },
    { value: 'religious_org', label: 'Religious Organization' },
    { value: 'family_company', label: 'Family Company/Entity' },
    { value: 'other', label: 'Other Institution' },
];

// Local editable arrays
const executors = ref(Array.isArray(props.fiduciaries?.executors) ? JSON.parse(JSON.stringify(props.fiduciaries.executors)) : []);
const trustees = ref(Array.isArray(props.fiduciaries?.trustees) ? JSON.parse(JSON.stringify(props.fiduciaries.trustees)) : []);
const guardians = ref(Array.isArray(props.fiduciaries?.guardians) ? JSON.parse(JSON.stringify(props.fiduciaries.guardians)) : []);

// Row helpers
function newRow() {
    return {
        institution_type: '',
        institution_name: '',
        contact_name: '',
        email: '',
        phone: ''
    };
}

function addExecutor() { executors.value.push(newRow()); }
function addTrustee() { trustees.value.push(newRow()); }
function addGuardian() { guardians.value.push(newRow()); }

function removeRow(list, index) { list.splice(index, 1); }

// Save
const form = useForm({
    executors: executors.value,
    trustees: trustees.value,
    guardians: guardians.value,
});

function syncForm() {
    form.executors = executors.value;
    form.trustees = trustees.value;
    form.guardians = guardians.value;
}

function saveFiduciaries() {
    syncForm();
    form.post(route('legacy.fiduciaries.save'), {
        preserveScroll: true,
        onSuccess: () => {
            openAlert('success', 'Fiduciaries saved successfully!', 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages || 'Failed to save fiduciaries.', 10000);
        }
    });
}
</script>

<template>

    <Head title="Fiduciaries - Legacy & Estate Planning" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <DashboardBackButton />

                <div class="max-w-6xl mx-auto p-6">
                    <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                        :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />

                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Step 3: Fiduciaries</h1>
                        <p class="text-gray-600">Appoint executors, trustees, and guardians. Use institutions for
                            professional management.</p>
                    </div>

                    <LegacyProgress :current="3" />

                    <!-- Executors -->
                    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Executors</h3>
                            <button @click="addExecutor" type="button"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-3 rounded-lg flex items-center">
                                <PlusIcon class="w-5 h-5 mr-2" /> Add Executor
                            </button>
                        </div>

                        <div v-if="executors.length === 0" class="text-sm text-gray-500">No executors added yet.</div>
                        <div class="space-y-4">
                            <div v-for="(row, idx) in executors" :key="idx" class="border rounded-lg p-4">
                                <div class="grid md:grid-cols-3 gap-4">
                                    <Select v-model="row.institution_type" label="Institution Type"
                                        :options="institutionOptions" :error="''" />
                                    <Input v-model="row.institution_name" label="Institution Name"
                                        placeholder="e.g., Acme Trust Co." />
                                    <Input v-model="row.contact_name" label="Contact Person (Optional)"
                                        placeholder="Full name" />
                                </div>
                                <div class="grid md:grid-cols-3 gap-4 mt-3">
                                    <Input v-model="row.email" type="email" label="Email (Optional)"
                                        placeholder="email@example.com" />
                                    <Input v-model="row.phone" label="Phone (Optional)" placeholder="07xx xxx xxx" />
                                    <div class="flex items-end justify-end">
                                        <button @click="removeRow(executors.value, idx)" type="button"
                                            class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg">
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trustees -->
                    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Trustees</h3>
                            <button @click="addTrustee" type="button"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-3 rounded-lg flex items-center">
                                <PlusIcon class="w-5 h-5 mr-2" /> Add Trustee
                            </button>
                        </div>

                        <div v-if="trustees.length === 0" class="text-sm text-gray-500">No trustees added yet.</div>
                        <div class="space-y-4">
                            <div v-for="(row, idx) in trustees" :key="idx" class="border rounded-lg p-4">
                                <div class="grid md:grid-cols-3 gap-4">
                                    <Select v-model="row.institution_type" label="Institution Type"
                                        :options="institutionOptions" :error="''" />
                                    <Input v-model="row.institution_name" label="Institution Name"
                                        placeholder="e.g., Acme Trust Co." />
                                    <Input v-model="row.contact_name" label="Contact Person (Optional)"
                                        placeholder="Full name" />
                                </div>
                                <div class="grid md:grid-cols-3 gap-4 mt-3">
                                    <Input v-model="row.email" type="email" label="Email (Optional)"
                                        placeholder="email@example.com" />
                                    <Input v-model="row.phone" label="Phone (Optional)" placeholder="07xx xxx xxx" />
                                    <div class="flex items-end justify-end">
                                        <button @click="removeRow(trustees.value, idx)" type="button"
                                            class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg">
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Guardians -->
                    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Guardians</h3>
                            <button @click="addGuardian" type="button"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-3 rounded-lg flex items-center">
                                <PlusIcon class="w-5 h-5 mr-2" /> Add Guardian
                            </button>
                        </div>

                        <div v-if="guardians.length === 0" class="text-sm text-gray-500">No guardians added yet.</div>
                        <div class="space-y-4">
                            <div v-for="(row, idx) in guardians" :key="idx" class="border rounded-lg p-4">
                                <div class="grid md:grid-cols-3 gap-4">
                                    <Select v-model="row.institution_type" label="Institution Type"
                                        :options="institutionOptions" :error="''" />
                                    <Input v-model="row.institution_name" label="Institution Name"
                                        placeholder="e.g., Acme Trust Co." />
                                    <Input v-model="row.contact_name" label="Contact Person (Optional)"
                                        placeholder="Full name" />
                                </div>
                                <div class="grid md:grid-cols-3 gap-4 mt-3">
                                    <Input v-model="row.email" type="email" label="Email (Optional)"
                                        placeholder="email@example.com" />
                                    <Input v-model="row.phone" label="Phone (Optional)" placeholder="07xx xxx xxx" />
                                    <div class="flex items-end justify-end">
                                        <button @click="removeRow(guardians.value, idx)" type="button"
                                            class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg">
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6 border-t">
                        <div class="text-sm text-gray-500 flex items-center space-x-2">
                            <ShieldCheckIcon class="w-5 h-5 text-gray-400" />
                            <span>Review and save your fiduciaries.</span>
                        </div>
                        <div class="space-x-3">
                            <button @click="saveFiduciaries" :disabled="form.processing"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg">
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </div>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
