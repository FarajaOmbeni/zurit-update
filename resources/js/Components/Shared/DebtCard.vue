<template>
    <Alert v-if="alertState" :type="alertState.type" :message="alertState.message" :duration="alertState.duration"
        :auto-close="alertState.autoClose" @close="clearAlert" />
    <div class="bg-white shadow rounded-lg p-4 border-2 border-purple-500" :class="{
        'border-green-500': debtData.status === 'paid_off',
        'text-green-500': debtData.status === 'paid_off'
    }">
        <div class="flex justify-between items-center border-b pb-2 mb-2">
            <h3 class="text-lg font-semibold text-purple-700" :class="{
                'text-green-500': debtData.status === 'paid_off'
            }">{{ debtData.name }}</h3>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-1 text-xs font-bold uppercase rounded-full" :class="{
                    'bg-yellow-400 text-black': debtData.status === 'active',
                    'bg-green-500 text-gray-100': debtData.status === 'paid_off'
                }">
                    {{ debtData.status }}
                </span>
                <button @click="showEditModal = true"
                    class="p-1 text-purple-600 hover:text-purple-800 transition-colors"
                    :class="{ 'hidden': debtData.status === 'paid_off' }">
                    <PencilIcon class=" h-4 w-4" />
                </button>
                <button @click="showDeleteModal = true" class="p-1 text-red-600 hover:text-red-800 transition-colors"
                    :class="{ 'hidden': debtData.status === 'paid_off' }">
                    <TrashIcon class=" h-4 w-4" />
                </button>
            </div>
        </div>
        <div class="mb-2">
            <p class="text-gray-600" :class="{
                'text-green-500': debtData.status === 'paid_off'
            }">{{ debtData.description }}</p>
            <p class="mt-1"><strong>Due:</strong> {{ formatCurrency(debtData.initial_amount) }}
            </p>
            <p class="mt-1"><strong>Paid:</strong> {{ formatCurrency(debtData.current_amount) }}
            </p>
            <p v-show="debtData.status === 'in_progress'" class="mt-1"><strong>Balance:</strong> {{
                formatCurrency(debtData.initial_amount -
                debtData.current_amount) }}
            </p>
            <div class="w-full bg-gray-300 rounded-full h-2.5 mt-2">
                <div :class="debtData.status === 'active' ? 'bg-yellow-400' : 'bg-green-500'"
                    class="h-2.5 rounded-full" :style="{ width: progressPercentage }"></div>
            </div>
        </div>
        <div class="text-right text-red-500 font-bold text-sm" :class="{ 'hidden': debtData.status === 'paid_off' }">
            Due: {{ formatDate(debtData.due_date) }}
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click="closeModal">
            <div class="bg-white rounded-lg p-3 w-full max-w-sm mx-4" @click.stop>
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-base font-medium text-purple-700">Edit Debt</h3>
                    <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-700">
                        <XIcon class="h-4 w-4" />
                    </button>
                </div>

                <form @submit.prevent="submitEdit">
                    <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                        <!-- Name Field -->
                        <div class="col-span-1">
                            <label for="name" class="block text-xs font-medium text-gray-700">Name</label>
                            <input type="text" id="name" v-model="editDebt.name"
                                class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-1.5 px-2" />
                        </div>

                        <!-- Type Dropdown -->
                        <div class="col-span-1">
                            <label for="type" class="block text-xs font-medium text-gray-700">Type</label>
                            <select id="type" v-model="editDebt.type"
                                class="mt-0.5 w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                                <option value="" disabled>Select type</option>
                                <option v-for="type in debtTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Initial Amount Field -->
                        <div class="col-span-1">
                            <label for="initial_amount" class="block text-xs font-medium text-gray-700">Initial
                                Amount</label>
                            <input type="number" id="initial_amount" v-model="editDebt.initial_amount"
                                class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-1.5 px-2" />
                        </div>

                        <!-- Interest Rate Field -->
                        <div class="col-span-1">
                            <label for="interest_rate" class="block text-xs font-medium text-gray-700">Interest Rate
                                (%)</label>
                            <input type="number" id="interest_rate" v-model="editDebt.interest_rate" step="0.01"
                                class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-1.5 px-2" />
                        </div>

                        <!-- Start Date Field -->
                        <div class="col-span-1">
                            <label for="start_date" class="block text-xs font-medium text-gray-700">Start Date</label>
                            <input type="date" id="start_date" v-model="editDebt.start_date"
                                class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-1.5 px-2" />
                        </div>

                        <!-- Due Date Field -->
                        <div class="col-span-1">
                            <label for="due_date" class="block text-xs font-medium text-gray-700">Due Date</label>
                            <input type="date" id="due_date" v-model="editDebt.due_date"
                                class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-1.5 px-2" />
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="mt-2">
                        <label for="description" class="block text-xs font-medium text-gray-700">Description</label>
                        <textarea id="description" v-model="editDebt.description" rows="2"
                            class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-1.5 px-2"></textarea>
                    </div>

                    <div class="mt-3 flex justify-end space-x-2">
                        <button type="button" @click="showEditModal = false"
                            class="px-2 py-1 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" :disabled="editDebt.processing"
                            class="px-2 py-1 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            {{ editDebt.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click="closeDeleteModal">
            <div class="bg-white rounded-lg p-5 w-full max-w-sm mx-4" @click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-red-600">Delete Goal</h3>
                    <button @click="showDeleteModal = false" class="text-gray-500 hover:text-gray-700">
                        <XIcon class="h-5 w-5" />
                    </button>
                </div>

                <p class="text-gray-700 mb-4">Are you sure you want to delete this goal? This action cannot be undone.
                </p>

                <div class="flex justify-end space-x-3">
                    <button @click="showDeleteModal = false"
                        class="px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <form @submit.prevent="confirmDelete">
                        <button
                            class="px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            {{ isDeleting ? 'Deleting...' : 'Delete' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { formatDate } from '@/Components/Composables/useDateFormat';
import { useForm } from '@inertiajs/vue3';
import { PencilIcon, XIcon, TrashIcon } from 'lucide-vue-next';
import { debtTypes } from '../Variables/debtTypes';
import { useAlert } from '@/Components/Composables/useAlert';
import Alert from '@/Components/Shared/Alert.vue';

const { alertState, openAlert, clearAlert } = useAlert();

const props = defineProps({
    debt: Object,
});

const emit = defineEmits(['update', 'delete']);

// Create a reactive copy of the debt prop to allow local updates
const debtData = ref({ ...props.debt });

// Update local data when prop changes
watch(() => props.debt, (newDebt) => {
    debtData.value = { ...newDebt };
}, { deep: true });

// Compute the repayment progress percentage based on initial and current amounts
const progressPercentage = computed(() => {
    if (debtData.value.current_amount === debtData.value.initial_amount) return 100;
    return Math.min(
        100,
        ((debtData.value.current_amount * 100) / debtData.value.initial_amount)
    );
});

// Simple currency formatter
const formatCurrency = (value) => {
    return 'KES ' + Math.round(value).toLocaleString();
};

// Edit modal state
const showEditModal = ref(false);

// Edit form
const editDebt = useForm({
    name: '',
    type: '',
    description: '',
    initial_amount: '',
    interest_rate: '',
    start_date: '',
    due_date: ''
});

// Populate form when modal is opened
const populateForm = () => {
    editDebt.name = debtData.value.name;
    editDebt.type = debtData.value.type || '';
    editDebt.description = debtData.value.description;
    editDebt.initial_amount = debtData.value.initial_amount;
    editDebt.interest_rate = debtData.value.interest_rate || '';
    editDebt.start_date = debtData.value.start_date || '';
    editDebt.due_date = debtData.value.due_date;
};

// Close modal when clicking outside
const closeModal = () => {
    showEditModal.value = false;
};

// Watch for modal open to populate form
watch(showEditModal, (newValue) => {
    if (newValue) {
        populateForm();
    }
});

// Submit edit form
const submitEdit = () => {
    editDebt.put(route('debt.update', debtData.value.id), {
        preserveScroll: true,
        onSuccess: (response) => {
            // Update the local debt data with response data
            if (response?.props?.debt) {
                debtData.value = response.props.debt;
            } else if (response?.props?.flash?.data) {
                // Alternative data location, depending on your Inertia setup
                debtData.value = { ...debtData.value, ...response.props.flash.data };
            } else {
                // Fallback to updating with form data
                Object.assign(debtData.value, editDebt);
            }

            showEditModal.value = false;
            openAlert('success', 'Debt Updated Successfully', 5000);
            emit('update');
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');
            openAlert('danger', errorMessages, 5000);
        }
    });
};

// Delete modal state
const showDeleteModal = ref(false);

// Close delete modal when clicking outside
const closeDeleteModal = () => {
    showDeleteModal.value = false;
};

const isDeleting = ref(false);

// Confirm delete action
const confirmDelete = () => {
    isDeleting.value = true;
    axios.post(route('debt.destroy', debtData.value.id))
        .then(() => {
            showDeleteModal.value = false;
            openAlert('success', 'Debt deleted successfully.');
            emit('delete', debtData.value.id);
        })
        .catch((error) => {
            openAlert('danger', 'Failed to delete debt. Please try again.', 5000);
            console.error('Error deleting debt:', error);
        })
        .finally(() => {
            isDeleting.value = false;
        });
};
</script>