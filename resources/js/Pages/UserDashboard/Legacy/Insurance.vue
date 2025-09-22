<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue'
import LegacyProgress from '@/Components/Legacy/LegacyProgress.vue'
import Input from '@/Components/Shared/Input.vue'
import Select from '@/Components/Shared/Select.vue'
import Alert from '@/Components/Shared/Alert.vue'
import { useAlert } from '@/Components/Composables/useAlert'
import { ref } from 'vue'
import { PlusIcon, PencilIcon, TrashIcon, BanknotesIcon, CalendarIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    // e.g., [{ id, type, provider_name, policy_number, coverage_amount, premium_amount, renewal_date, notes }]
    insurances: { type: Array, default: () => [] }
})

const { openAlert, clearAlert, alertState } = useAlert()
const showForm = ref(false)
const showDeleteModal = ref(false)
const editingPolicy = ref(null)
const policyToDelete = ref(null)

const types = [
    { value: 'insurance', label: 'Insurance' },
    { value: 'pension', label: 'Pension' },
]

const form = useForm({
    type: 'insurance',
    provider_name: '',
    policy_number: '',
    coverage_amount: '',
    premium_amount: '',
    renewal_date: '',
    notes: '',
})

function formatCurrency(value) {
    if (value == null || value === '') return '—'
    return new Intl.NumberFormat('en-KE', {
        style: 'currency', currency: 'KES',
        minimumFractionDigits: 0, maximumFractionDigits: 0
    }).format(Number(value))
}

function formatDate(date) {
    if (!date) return 'Not specified'
    if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
        const [y, m, d] = date.split('-').map(Number)
        return new Date(y, m - 1, d).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
    }
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
}

function formatDateForInput(date) {
    if (!date) return ''
    if (/^\d{4}-\d{2}-\d{2}$/.test(date)) return date
    const d = new Date(date)
    const y = d.getFullYear()
    const m = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')
    return `${y}-${m}-${day}`
}

// Add / Edit
function startAdd() {
    editingPolicy.value = null
    form.reset()
    form.type = 'insurance'
    showForm.value = true
}

function startEdit(policy) {
    editingPolicy.value = policy
    form.type = policy.type || 'insurance'
    form.provider_name = policy.provider_name || ''
    form.policy_number = policy.policy_number || ''
    form.coverage_amount = policy.coverage_amount ?? ''
    form.premium_amount = policy.premium_amount ?? ''
    form.renewal_date = policy.renewal_date ? formatDateForInput(policy.renewal_date) : ''
    form.notes = policy.notes || ''
    showForm.value = true
}

function cancelEdit() {
    editingPolicy.value = null
    form.reset()
    showForm.value = false
}

// Submit (mirrors Assets’ submitForm)
function submitForm() {
    if (editingPolicy.value) {
        form.put(route('legacy.insurance.update', editingPolicy.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset()
                showForm.value = false
                editingPolicy.value = null
                openAlert('success', 'Insurance updated successfully!', 5000)
            },
            onError: (errors) => {
                const msg = Object.values(errors || {}).flat().join(' ')
                openAlert('danger', msg || 'Could not update insurance.', 10000)
            }
        })
    } else {
        form.post(route('legacy.insurance.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset()
                showForm.value = false
                openAlert('success', 'Insurance added successfully!', 5000)
            },
            onError: (errors) => {
                const msg = Object.values(errors || {}).flat().join(' ')
                openAlert('danger', msg || 'Could not add insurance.', 10000)
            }
        })
    }
}

// Delete (mirrors Assets’ modal flow)
function confirmDelete(policy) {
    policyToDelete.value = policy
    showDeleteModal.value = true
}

function deletePolicy() {
    if (!policyToDelete.value) return
    form.delete(route('legacy.insurance.destroy', policyToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false
            policyToDelete.value = null
            openAlert('success', 'Insurance deleted successfully!', 5000)
        },
        onError: (errors) => {
            const msg = Object.values(errors || {}).flat().join(' ')
            openAlert('danger', msg || 'Could not delete insurance.', 10000)
        }
    })
}

function cancelDelete() {
    showDeleteModal.value = false
    policyToDelete.value = null
}
</script>

<template>

    <Head title="Insurance - Legacy & Estate Planning" />
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
                                Step 4: Insurance & Pensions
                            </h1>
                            <p class="text-gray-600">
                                Add your insurance or pension policies so they’re considered in your plan.
                            </p>
                        </div>

                        <div class="text-right">
                            <div class="text-sm text-gray-500 mb-1">Total Policies</div>
                            <div class="text-2xl font-bold"
                                :class="props.insurances.length ? 'text-purple-600' : 'text-gray-400'">
                                {{ props.insurances.length }}
                            </div>
                        </div>
                    </div>

                    <LegacyProgress :current="4" />

                    <!-- Add Button -->
                    <div class="mb-6">
                        <button @click="showForm ? (editingPolicy ? cancelEdit() : showForm = false) : startAdd()"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            <PlusIcon class="w-5 h-5 mr-2" />
                            {{ showForm ? 'Cancel' : 'Add Policy' }}
                        </button>
                    </div>

                    <!-- Add/Edit Form -->
                    <div v-if="showForm" class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ editingPolicy ? 'Edit Policy' : 'Add New Policy' }}
                        </h3>

                        <form @submit.prevent="submitForm" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <Select v-model="form.type" label="Type" :options="types" :error="form.errors?.type"
                                    required />
                                <Input v-model="form.provider_name" label="Provider"
                                    placeholder="e.g., Jubilee Insurance" :error="form.errors?.provider_name"
                                    required />
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <Input v-model="form.policy_number" label="Policy Number" placeholder="e.g., ABC-12345"
                                    :error="form.errors?.policy_number" />
                                <Input v-model="form.renewal_date" type="date" label="Renewal Date (optional)"
                                    :error="form.errors?.renewal_date" />
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <Input v-model="form.coverage_amount" type="number" step="0.01" min="0"
                                    label="Coverage Amount (KES)" placeholder="0.00"
                                    :error="form.errors?.coverage_amount" />
                                <Input v-model="form.premium_amount" type="number" step="0.01" min="0"
                                    label="Premium (KES)" placeholder="0.00" :error="form.errors?.premium_amount" />
                            </div>

                            <Input v-model="form.notes" label="Notes (optional)" placeholder="Any extra details"
                                :error="form.errors?.notes" />

                            <div class="flex space-x-4">
                                <button type="submit" :disabled="form.processing"
                                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                                    {{ form.processing ? (editingPolicy ? 'Updating...' : 'Adding...') : (editingPolicy
                                        ? 'Update Policy' : 'Add Policy') }}
                                </button>
                                <button type="button" @click="editingPolicy ? cancelEdit() : (showForm = false)"
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Policies List -->
                    <div v-if="props.insurances.length" class="space-y-4 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900">Your Policies</h3>

                        <div class="grid gap-4">
                            <div v-for="p in props.insurances" :key="p.id"
                                class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900">
                                            {{ p.provider_name }} <span class="text-sm text-gray-500">({{ p.type
                                                }})</span>
                                        </h4>

                                        <div class="text-sm text-gray-600 mt-1 space-y-1">
                                            <div v-if="p.policy_number"><span class="font-medium">Policy #:</span> {{
                                                p.policy_number }}</div>
                                            <div class="flex items-center space-x-4">
                                                <div class="flex items-center space-x-1" v-if="p.coverage_amount">
                                                    <BanknotesIcon class="w-4 h-4" />
                                                    <span>Coverage: {{ formatCurrency(p.coverage_amount) }}</span>
                                                </div>
                                                <div class="flex items-center space-x-1" v-if="p.premium_amount">
                                                    <BanknotesIcon class="w-4 h-4" />
                                                    <span>Premium: {{ formatCurrency(p.premium_amount) }}</span>
                                                </div>
                                                <div class="flex items-center space-x-1" v-if="p.renewal_date">
                                                    <CalendarIcon class="w-4 h-4" />
                                                    <span>Renews: {{ formatDate(p.renewal_date) }}</span>
                                                </div>
                                            </div>
                                            <div v-if="p.notes"><span class="font-medium">Notes:</span> {{ p.notes }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2 ml-4">
                                        <button @click="startEdit(p)"
                                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                            title="Edit">
                                            <PencilIcon class="w-5 h-5" />
                                        </button>
                                        <button @click="confirmDelete(p)"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                            title="Delete">
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-else class="text-center py-12">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No policies added yet</h3>
                        <p class="text-gray-600 mb-6">Add your first insurance or pension policy.</p>
                        <button @click="startAdd"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            <PlusIcon class="w-5 h-5 mr-2" />
                            Add Your First Policy
                        </button>
                    </div>

                    <!-- Wizard footer -->
                    <div class="flex items-center justify-between pt-8 border-t">
                        <Link :href="route('legacy.fiduciaries')"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                        ← Back to Fiduciaries
                        </Link>
                        <Link :href="route('legacy.review')"
                            class="bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
                        Continue to Review →
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
                            <h3 class="text-lg font-semibold text-gray-900">Delete Policy</h3>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-gray-600">
                            Are you sure you want to delete <strong>{{ policyToDelete?.provider_name }}</strong>?
                            This action cannot be undone.
                        </p>
                    </div>

                    <div class="flex space-x-4">
                        <button @click="deletePolicy" :disabled="form.processing"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                            {{ form.processing ? 'Deleting...' : 'Delete' }}
                        </button>
                        <button @click="cancelDelete"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
