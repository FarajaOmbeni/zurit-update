<script setup>
import { computed, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Sidebar from '@/Components/Sidebar.vue';
import GoalCard from '@/Components/Shared/GoalCard.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import Alert from '@/Components/Shared/Alert.vue';

const { alertState, openAlert, clearAlert } = useAlert();

const props = defineProps({
    goals: Array,
});

// Create a local reactive copy of the goals
const goals = ref([...props.goals]);

// Remove a goal from the list when deleted
const removeGoalFromList = (goalId) => {
    goals.value = goals.value.filter(goal => goal.id !== goalId);
};

// Modal state
const isModalOpen = ref(false);
const openModal = () => {
    isModalOpen.value = true;
};
const closeModal = () => {
    isModalOpen.value = false;
    resetForm();
};

const closeModalOnOutsideClick = (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeModal();
    }
};

// Form data for creating a new goal
const newGoal = useForm({
    name: '',
    description: '',
    target_amount: '',
    start_date: '',
    duration_months: '',
    duration_years: '',
    commitment: false
});

// Reset form fields using the built-in reset() method
const resetForm = () => {
    newGoal.reset();
};

const submitForm = () => {
    newGoal.post(route('goal.store'), {
        onSuccess: (response) => {
            newGoal.reset();
            closeModal();
            openAlert('success', 'Goal added successfully', 5000);
            window.location.reload();
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages, 5000);
        }
    });
};

// Computed filters for goals
const activeGoals = computed(() => goals.value.filter(goal => goal.status !== 'achieved'));
const completedGoals = computed(() => goals.value.filter(goal => goal.status === 'achieved'));
</script>


<template>

    <Head title="Goal Setting" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div class="container mx-auto p-4">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-purple-700">Your Goals</h1>
                        <button @click="openModal"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Add Goal
                        </button>
                    </div>

                    <div class="container mx-auto p-4">
                        <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                            :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />
                        <!-- Active Goals Section -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-purple-700 mb-4">Active Goals</h2>
                            <div v-if="activeGoals.length" class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                                <GoalCard v-for="goal in activeGoals" :key="goal.id || goal.goal_id" :goal="goal"
                                    @update="goals.value = [...props.goals]" @delete="removeGoalFromList" />
                            </div>
                            <div v-else class="text-gray-600">
                                No active goals found.
                            </div>
                        </section>

                        <!-- Completed Goals Section -->
                        <section v-show="completedGoals.length > 0">
                            <h2 class="text-xl font-semibold text-purple-700 mb-4">Completed Goals</h2>
                            <div v-if="completedGoals.length"
                                class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                                <GoalCard v-for="goal in completedGoals" :key="goal.id || goal.goal_id" :goal="goal" />
                            </div>
                            <div v-else class="text-gray-600">
                                No completed goals found.
                            </div>
                        </section>

                    </div>
                </div>
            </Sidebar>
        </div>

        <!-- Add Goal Modal -->
        <div v-if="isModalOpen" @click="closeModalOnOutsideClick"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-purple-600 text-white px-3 py-2 flex justify-between items-center">
                    <h3 class="text-base font-medium">Add New Goal</h3>
                    <button @click="closeModal" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-3">
                    <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                        <!-- Name Field -->
                        <div class="col-span-2">
                            <label for="name" class="block text-gray-700 text-xs font-medium mb-1">Name</label>
                            <input type="text" id="name" v-model="newGoal.name"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                        </div>

                        <!-- Target Amount Field -->
                        <div class="col-span-2">
                            <label for="target_amount" class="block text-gray-700 text-xs font-medium mb-1">Target
                                Amount</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xs">KES</span>
                                </div>
                                <input type="number" id="target_amount" v-model="newGoal.target_amount"
                                    class="w-full pl-8 pr-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                    step="0.01" min="0" required>
                            </div>
                        </div>

                        <!-- Start Date Field -->
                        <div class="col-span-1">
                            <label for="start_date" class="block text-gray-700 text-xs font-medium mb-1">Start
                                Date</label>
                            <input type="date" id="start_date" v-model="newGoal.start_date"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                required>
                        </div>

                        <!-- Duration Field -->
                        <div class="col-span-1">
                            <label class="block text-gray-700 text-xs font-medium mb-1">Duration</label>
                            <div class="flex gap-2">
                                <!-- Years -->
                                <div class="flex-1">
                                    <input type="number" min="0" v-model="newGoal.duration_years" placeholder="Years"
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                        required>
                                </div>

                                <!-- Months -->
                                <div class="flex-1">
                                    <input type="number" min="0" max="11" v-model="newGoal.duration_months"
                                        placeholder="Months"
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="col-span-2">
                            <label for="description"
                                class="block text-gray-700 text-xs font-medium mb-1">Description</label>
                            <textarea id="description" v-model="newGoal.description"
                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
                                rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Repayment Commitment Radio Buttons -->
                    <div class="flex items-center gap-4 mt-2">
                        <span class="text-xs text-gray-700 font-bold">Commit to monthly repayment?</span>
                        <label class="flex items-center text-xs gap-1">
                            <input type="radio" v-model="newGoal.commitment" :value="true" class="text-purple-500" />
                            Yes
                        </label>
                        <label class="flex items-center text-xs gap-1">
                            <input type="radio" v-model="newGoal.commitment" :value="false" class="text-purple-500" />
                            No
                        </label>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex justify-end space-x-2 mt-3">
                        <button type="button" @click="closeModal"
                            class="px-2 py-1 text-xs border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-2 py-1 text-xs bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-purple-500">
                            {{ newGoal.processing ? 'Saving...' : 'Add Goal' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>