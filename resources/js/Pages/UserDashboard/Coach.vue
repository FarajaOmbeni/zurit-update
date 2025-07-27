<template>
    <Sidebar title="Coach Dashboard">
        <div class="space-y-6">
            <!-- Current Coach Section -->
            <div v-if="coach" class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-purple-800 mb-4">Your Assigned Coach</h2>

                <div class="flex items-start space-x-6">
                    <!-- Coach Photo -->
                    <div class="flex-shrink-0">
                        <div v-if="coach.photo" class="w-24 h-24 rounded-full overflow-hidden">
                            <img :src="coach.photo" :alt="coach.name" class="w-full h-full object-cover">
                        </div>
                        <div v-else class="w-24 h-24 rounded-full bg-purple-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Coach Information -->
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ coach.name }}</h3>
                        <div class="space-y-2 text-gray-600">
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                {{ coach.email }}
                            </p>
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                                {{ coach.phone }}
                            </p>
                        </div>

                        <!-- Coach Bio -->
                        <div v-if="coach.bio" class="mt-4">
                            <h4 class="font-semibold text-gray-900 mb-2">About Your Coach</h4>
                            <p class="text-gray-600 leading-relaxed">{{ coach.bio }}</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex space-x-3">
                            <button
                                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Contact Coach
                            </button>
                            <button @click="removeCoach"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Remove Coach
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Coach Assigned Section -->
            <div v-else class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">No Coach Assigned</h2>
                    <p class="text-gray-600 mb-6">You don't have a coach assigned yet. Get personalized guidance by
                        connecting with one of our expert coaches.</p>

                    <button @click="showCoachSelection = true"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors">
                        Find a Coach
                    </button>
                </div>
            </div>

            <!-- Coach Selection Modal -->
            <div v-if="showCoachSelection"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-900">Select Your Coach</h3>
                        <button @click="showCoachSelection = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div v-for="availableCoach in availableCoaches" :key="availableCoach.id"
                            class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer"
                            @click="selectCoach(availableCoach)">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div v-if="availableCoach.photo" class="w-16 h-16 rounded-full overflow-hidden">
                                        <img :src="availableCoach.photo" :alt="availableCoach.name"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div v-else
                                        class="w-16 h-16 rounded-full bg-purple-200 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ availableCoach.name }}</h4>
                                    <p class="text-sm text-gray-600">{{ availableCoach.email }}</p>
                                    <p v-if="availableCoach.bio" class="text-sm text-gray-500 mt-2">{{
                                        availableCoach.bio }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Sidebar>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';

const props = defineProps({
    user: Object,
    coach: Object,
    availableCoaches: Array,
});

const showCoachSelection = ref(false);

const selectCoach = (coach) => {
    const form = useForm({
        coach_id: coach.id
    });

    form.post(route('coach.assign'), {
        onSuccess: () => {
            showCoachSelection.value = false;
        }
    });
};

const removeCoach = () => {
    const form = useForm({});

    form.delete(route('coach.remove'), {
        onSuccess: () => {
            // Coach will be removed from the page
        }
    });
};
</script>
