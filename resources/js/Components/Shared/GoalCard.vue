<template>
    <Alert v-if="alertState" :type="alertState.type" :message="alertState.message" :duration="alertState.duration"
        :auto-close="alertState.autoClose" @close="clearAlert" />
    <div class="bg-white shadow rounded-lg p-4 border-2 border-purple-500">
        <div class="flex justify-between items-center border-b pb-2 mb-2">
            <h3 class="text-lg font-semibold text-purple-700">{{ goal.name }}</h3>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-1 text-xs font-bold uppercase rounded-full" :class="{
                    'bg-yellow-400 text-black': goal.status === 'in_progress',
                    'bg-purple-400 text-white': goal.status === 'achieved',
                    'bg-gray-400 text-black': goal.status === 'abandoned'
                }">
                    {{ goal.status }}
                </span>
                <button @click="showEditModal = true"
                    class="p-1 text-purple-600 hover:text-purple-800 transition-colors"
                    :class="{ 'hidden': goal.status === 'achieved' }">
                    <PencilIcon class=" h-4 w-4" />
                </button>
                <button @click="showDeleteModal = true" 
                    class="p-1 text-red-600 hover:text-red-800 transition-colors"
                    :class="{ 'hidden': goal.status === 'achieved' }">
                    <TrashIcon class="h-4 w-4" />
                </button>
            </div>
        </div>
        <div class="mb-2">
            <p class="text-gray-600">{{ goal.description }}</p>
            <p class="mt-1"><strong>Goal:</strong> {{ formatCurrency(goal.target_amount) }}</p>
            <p class="mt-1"><strong>Saved:</strong> {{ formatCurrency(goal.current_amount) }}</p>
            <p class="mt-1"><strong>Remaining:</strong> {{ formatCurrency(goal.target_amount - goal.current_amount) }}
            </p>
            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                <div class="h-2.5 rounded-full bg-yellow-400" :style="{ width: progressPercentage + '%' }"></div>
            </div>
        </div>
        <div class="text-right text-sm text-gray-500">
            Target Date: {{ formatDate(goal.target_date) }}
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click="closeModal">
            <div class="bg-white rounded-lg p-5 w-full max-w-sm mx-4" @click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-purple-700">Edit Goal</h3>
                    <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-700">
                        <XIcon class="h-5 w-5" />
                    </button>
                </div>

                <form @submit.prevent="submitEdit">
                    <div class="space-y-3">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" id="name" v-model="editGoal.name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm" />
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" v-model="editGoal.description" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"></textarea>
                        </div>

                        <div>
                            <label for="target_amount" class="block text-sm font-medium text-gray-700">Target
                                Amount</label>
                            <input type="number" id="target_amount" v-model="editGoal.target_amount"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm" />
                        </div>

                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" id="start_date" v-model="editGoal.start_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm" />
                        </div>

                        <div>
                            <label for="target_date" class="block text-sm font-medium text-gray-700">Target Date</label>
                            <input type="date" id="target_date" v-model="editGoal.target_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm" />
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end space-x-3">
                        <button type="button" @click="showEditModal = false"
                            class="px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" :disabled="editGoal.processing"
                            class="px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
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

// Compute the progress percentage based on current_amount vs. target_amount
const progressPercentage = computed(() => {
    if (props.goal.target_amount === 0) return 0;
    return Math.min(100, (props.goal.current_amount / props.goal.target_amount) * 100);
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
    target_date: ''
});

// Populate form when modal is opened
const populateForm = () => {
    editGoal.name = props.goal.name;
    editGoal.description = props.goal.description;
    editGoal.target_amount = props.goal.target_amount;
    editGoal.start_date = props.goal.start_date || '';
    editGoal.target_date = props.goal.target_date;
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
    editGoal.put(route('goal.update', props.goal.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            openAlert('success', 'Goal Updated Succesfully.')
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

// Delete modal state
const showDeleteModal = ref(false);

// Close delete modal when clicking outside
const closeDeleteModal = () => {
    showDeleteModal.value = false;
};

const isDeleting = ref(false)

// Confirm delete action
const confirmDelete = () => {
    isDeleting.value = true
    axios.post(route('goal.destroy', props.goal.id))
        .then(() => {
            showDeleteModal.value = false;
            openAlert('success', 'Goal deleted successfully.');
            emit('delete', props.goal.id);
        })
        .catch((error) => {
            openAlert('danger', 'Failed to delete goal. Please try again.', 5000);
            console.error('Error deleting goal:', error);
        });
};
</script>