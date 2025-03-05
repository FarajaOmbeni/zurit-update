<template>
    <div class="bg-white shadow rounded-lg p-4 border-2 border-purple-500" :class="{
        'border-green-500': debt.status === 'paid_off',
        'text-green-500': debt.status === 'paid_off'
    }">
        <div class="flex justify-between items-center border-b pb-2 mb-2">
            <h3 class="text-lg font-semibold text-purple-700" :class="{
                'text-green-500': debt.status === 'paid_off'
            }">{{ debt.name }}</h3>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-1 text-xs font-bold uppercase rounded-full" :class="{
                    'bg-yellow-400 text-black': debt.status === 'active',
                    'bg-green-500 text-gray-100': debt.status === 'paid_off'
                }">
                    {{ debt.status }}
                </span>
                <button @click="showEditModal = true"
                    class="p-1 text-purple-600 hover:text-purple-800 transition-colors"
                    :class="{ 'text-green-600 hover:text-green-800': debt.status === 'paid_off' }">
                    <PencilIcon class="h-4 w-4" />
                </button>
            </div>
        </div>
        <div class="mb-2">
            <p class="text-gray-600" :class="{
                'text-green-500': debt.status === 'paid_off'
            }">{{ debt.description }}</p>
            <p class="mt-1"><strong>Due:</strong> {{ formatCurrency(debt.initial_amount) }}
            </p>
            <p class="mt-1"><strong>Paid:</strong> {{ formatCurrency(debt.current_amount) }}
            </p>
            <p class="mt-1"><strong>Balance:</strong> {{ formatCurrency(debt.initial_amount - debt.current_amount) }}
            </p>
            <div class="w-full bg-gray-300 rounded-full h-2.5 mt-2">
                <div class="h-2.5 rounded-full bg-green-400" :style="{ width: progressPercentage + '%' }"></div>
            </div>
        </div>
        <div class="text-right text-red-500 font-bold text-sm" :class="{ 'hidden': debt.status === 'paid_off' }">
            Due: {{ formatDate(debt.due_date) }}
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click="closeModal">
            <div class="bg-white rounded-lg p-5 w-full max-w-sm mx-4" @click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-purple-700">Edit Debt</h3>
                    <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-700">
                        <XIcon class="h-5 w-5" />
                    </button>
                </div>

                <form @submit.prevent="submitEdit">
                    <div class="space-y-3">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" id="name" v-model="editDebt.name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm" />
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select id="type" v-model="editDebt.type"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                                <option value="" disabled>Select a debt type</option>
                                <option v-for="type in debtTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" v-model="editDebt.description" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"></textarea>
                        </div>

                        <div>
                            <label for="initial_amount" class="block text-sm font-medium text-gray-700">Initial
                                Amount</label>
                            <input type="number" id="initial_amount" v-model="editDebt.initial_amount"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm" />
                        </div>

                        <div>
                            <label for="interest_rate" class="block text-sm font-medium text-gray-700">Interest Rate
                                (%)</label>
                            <input type="number" id="interest_rate" v-model="editDebt.interest_rate" step="0.01"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm" />
                        </div>

                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" id="start_date" v-model="editDebt.start_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm" />
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                            <input type="date" id="due_date" v-model="editDebt.due_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm" />
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end space-x-3">
                        <button type="button" @click="showEditModal = false"
                            class="px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" :disabled="editDebt.processing"
                            class="px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            {{ editDebt.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { formatDate } from '@/Components/Composables/useDateFormat';
import { useForm } from '@inertiajs/vue3';
import { PencilIcon, XIcon } from 'lucide-vue-next';
import { debtTypes } from '../Variables/debtTypes';
import { useAlert } from '../Composables/useAlert';

const { alertState, openAlert, clearAlert } = useAlert();


const props = defineProps({
    debt: Object,
});

const emit = defineEmits(['update']);

// Compute the repayment progress percentage based on initial and current amounts
const progressPercentage = computed(() => {
    if (props.debt.current_amount === props.debt.initial_amount) return 100;
    return Math.min(
        100,
        ((props.debt.current_amount * 100) / props.debt.initial_amount)
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
    editDebt.name = props.debt.name;
    editDebt.type = props.debt.type || '';
    editDebt.description = props.debt.description;
    editDebt.initial_amount = props.debt.initial_amount;
    editDebt.interest_rate = props.debt.interest_rate || '';
    editDebt.start_date = props.debt.start_date || '';
    editDebt.due_date = props.debt.due_date;
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
    editDebt.put(route('debt.update', props.debt.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            openAlert('success', 'Debt Updated Succesfully', 5000)
            emit('update');
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ')
            openAlert('danger', errorMessages, 5000)
        }
    });
};
</script>