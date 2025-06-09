<template>
    <Alert v-if="alertState" :type="alertState.type" :message="alertState.message" :duration="alertState.duration"
        :auto-close="alertState.autoClose" @close="clearAlert" />
    <div class="bg-white shadow rounded-lg p-4 border-2 border-purple-500">
        <div class="flex justify-between items-center border-b pb-2 mb-2">
            <h3 class="text-lg font-semibold text-purple-700">{{ localGoal.name }}</h3>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-1 text-xs font-bold uppercase rounded-full" :class="{
                    'bg-yellow-400 text-black': localGoal.status === 'in_progress',
                    'bg-green-500 text-white': localGoal.status === 'achieved',
                    'bg-gray-400 text-black': localGoal.status === 'abandoned'
                }">
                    {{ localGoal.status }}
                </span>
                <button @click="showEditModal = true"
                    class="p-1 text-purple-600 hover:text-purple-800 transition-colors"
                    :class="{ 'hidden': localGoal.status === 'achieved' }">
                    <PencilIcon class=" h-4 w-4" />
                </button>
                <button @click="showDeleteModal = true" class="p-1 text-red-600 hover:text-red-800 transition-colors"
                    :class="{ 'hidden': localGoal.status === 'achieved' }">
                    <TrashIcon class="h-4 w-4" />
                </button>
            </div>
        </div>
        <div class="mb-2" :class="localGoal.status === 'in_progress' ? 'text-gray-600' : 'text-green-500'">
            <p>{{ localGoal.description
                }}</p>
            <p class="mt-1"><strong>Goal:</strong> {{ formatCurrency(localGoal.target_amount) }}</p>
            <p class="mt-1"><strong>Saved:</strong> {{ formatCurrency(localGoal.current_amount) }}</p>
            <p v-show="localGoal.status === 'in_progress'" class="mt-1"><strong>Remaining:</strong> {{
                formatCurrency(localGoal.target_amount -
                localGoal.current_amount) }}
            </p>
            <p v-show="localGoal.status === 'in_progress'" class="mt-1"><strong>Minimum Contribution:</strong> {{
                formatCurrency(minimumContribution) }}
            </p>
            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                <div :class="localGoal.status === 'in_progress' ? 'bg-yellow-400' : 'bg-green-500'"
                    class="h-2.5 rounded-full" :style="{ width: progressPercentage + '%' }"></div>
            </div>
        </div>
        <div v-show="localGoal.status === 'in_progress'" class="text-right text-sm text-gray-500">
            Target Date: {{ formatDate(localGoal.target_date) }}
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click="closeModal">
            <div class="bg-white rounded-lg p-3 w-full max-w-sm mx-4" @click.stop>
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-base font-medium text-purple-700">Edit Goal</h3>
                    <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-700">
                        <XIcon class="h-4 w-4" />
                    </button>
                </div>

                <form @submit.prevent="submitEdit">
                    <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                        <!-- Name Field -->
                        <div class="col-span-2">
                            <label for="name" class="block text-xs font-medium text-gray-700">Name</label>
                            <input type="text" id="name" v-model="editGoal.name"
                                class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-1.5 px-2" />
                        </div>

                        <!-- Target Amount Field -->
                        <div class="col-span-2">
                            <label for="target_amount" class="block text-xs font-medium text-gray-700">Target
                                Amount</label>
                            <input type="number" id="target_amount" v-model="editGoal.target_amount"
                                class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-1.5 px-2" />
                        </div>

                        <!-- Start Date Field -->
                        <div class="col-span-1">
                            <label for="start_date" class="block text-xs font-medium text-gray-700">Start Date</label>
                            <input type="date" id="start_date" v-model="editGoal.start_date"
                                class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-1.5 px-2" />
                        </div>

                        <!-- Target Date Field -->
                        <div class="col-span-1">
                            <label for="target_date" class="block text-xs font-medium text-gray-700">Due Date</label>
                            <input type="date" id="target_date" v-model="editGoal.target_date"
                                class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-1.5 px-2" />
                        </div>

                        <!-- Description Field -->
                        <div class="col-span-2">
                            <label for="description" class="block text-xs font-medium text-gray-700">Description</label>
                            <textarea id="description" v-model="editGoal.description" rows="2"
                                class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-1.5 px-2"></textarea>
                        </div>
                    </div>

                    <div class="mt-3 flex justify-end space-x-2">
                        <button type="button" @click="showEditModal = false"
                            class="px-2 py-1 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" :disabled="editGoal.processing"
                            class="px-2 py-1 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            {{ editGoal.processing ? 'Saving...' : 'Save Changes' }}
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
import { useAlert } from '@/Components/Composables/useAlert';
import Alert from '@/Components/Shared/Alert.vue';
import axios from 'axios';

const { alertState, openAlert, clearAlert } = useAlert();

const props = defineProps({
    goal: Object,
});

const emit = defineEmits(['update', 'delete']);

// Create a local copy of the goal to modify
const localGoal = ref({ ...props.goal });

// Watch for external goal changes
watch(() => props.goal, (newGoal) => {
    localGoal.value = { ...newGoal };
}, { deep: true });

// Compute the progress percentage based on current_amount vs. target_amount
const progressPercentage = computed(() => {
    if (localGoal.value.target_amount === 0) return 0;
    return Math.min(100, (localGoal.value.current_amount / localGoal.value.target_amount) * 100);
});

// Simple currency formatter
const formatCurrency = (value) => {
    return 'KES ' + Math.round(value).toLocaleString();
};

// Edit modal state
const showEditModal = ref(false);

// Edit form
const editGoal = useForm({
    name: '',
    description: '',
    target_amount: '',
    start_date: '',
    duration_months: '',
    duration_years: '',
    target_date: '',
    commitment: false
});

// Populate form when modal is opened
const populateForm = () => {
    editGoal.name = localGoal.value.name;
    editGoal.description = localGoal.value.description;
    editGoal.target_amount = localGoal.value.target_amount;
    editGoal.start_date = new Date(localGoal.value.start_date).toISOString().split('T')[0] || '';
    editGoal.target_date = new Date(localGoal.value.target_date).toISOString().split('T')[0] || '';
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
    editGoal.put(route('goal.update', localGoal.value.id), {
        preserveScroll: true,
        onSuccess: (response) => {
            // Update local goal with form data to show changes immediately
            if (response?.props?.goal) {
                // If server returns the updated goal, use that
                localGoal.value = response.props.goal;
            } else {
                // Otherwise, update with form data
                localGoal.value = {
                    ...localGoal.value,
                    name: editGoal.name,
                    description: editGoal.description,
                    target_amount: editGoal.target_amount,
                    start_date: editGoal.start_date,
                    duration_months: editGoal.duration_months,
                    duration_years: editGoal.duration_years
                };
            }

            showEditModal.value = false;
            openAlert('success', 'Goal Updated Successfully.');

            // Still emit update event for parent components that might need to know
            emit('update', localGoal.value);
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
    axios.post(route('goal.destroy', localGoal.value.id))
        .then(() => {
            showDeleteModal.value = false;
            openAlert('success', 'Goal deleted successfully.');
            emit('delete', localGoal.value.id);
        })
        .catch((error) => {
            isDeleting.value = false;
            openAlert('danger', 'Failed to delete goal. Please try again.', 5000);
            console.error('Error deleting goal:', error);
        });
};

const minimumContribution = computed(() => {
    const start = new Date(localGoal.value.start_date);
    const target = new Date(localGoal.value.target_date);

    const years = target.getFullYear() - start.getFullYear();
    const months = target.getMonth() - start.getMonth();
    const totalMonths = (years * 12) + months;

    if (totalMonths <= 0) return 0;

    return localGoal.value.target_amount / totalMonths;
});

</script>