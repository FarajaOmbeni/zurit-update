<template>
    <AdminSidebar title="Coach Details">

        <Head title="Coach Details" />

        <div class="space-y-6">
            <!-- Back Button -->
            <div class="mb-6">
                <Link :href="route('coaching.index')"
                    class="text-purple-600 hover:text-purple-700 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Back to Coaches</span>
                </Link>
            </div>

            <!-- Coach Profile -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-start space-x-6">
                    <!-- Coach Photo -->
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                        <img v-if="coach.photo" :src="`/storage/coaches/${coach.photo}`" :alt="coach.name"
                            class="w-full h-full object-cover" />
                        <svg v-else class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>

                    <!-- Coach Info -->
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">{{ coach.name }}</h2>
                                <p class="text-lg text-gray-600">{{ coach.email }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <Link :href="route('coaching.edit', coach.id)"
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg">
                                Edit Coach
                                </Link>
                            </div>
                        </div>

                        <div class="mt-4 space-y-2">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="text-gray-600">{{ coach.phone }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="text-gray-600">{{ coach.users.length }} clients</span>
                            </div>
                        </div>

                        <!-- Bio -->
                        <div v-if="coach.bio" class="mt-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Bio</h3>
                            <p class="text-gray-600">{{ coach.bio }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clients Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Clients</h3>
                    <span class="text-sm text-gray-500">{{ coach.users.length }} total clients</span>
                </div>

                <!-- Assign Client Section -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-3">Assign New Client</h4>
                    <div class="flex space-x-3">
                        <input v-model="searchQuery" type="text" placeholder="Search users by name or email..."
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                            @input="searchUsers" />
                        <button @click="searchUsers"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                            Search
                        </button>
                    </div>

                    <!-- Search Results -->
                    <div v-if="searchResults.length > 0" class="mt-4 space-y-2">
                        <h5 class="font-medium text-gray-700">Search Results:</h5>
                        <div v-for="user in searchResults" :key="user.id"
                            class="flex justify-between items-center p-3 bg-white rounded border">
                            <div>
                                <p class="font-medium text-gray-900">{{ user.name }}</p>
                                <p class="text-sm text-gray-600">{{ user.email }}</p>
                                <p v-if="user.coach" class="text-xs text-orange-600">
                                    Currently assigned to: {{ user.coach.name }}
                                </p>
                            </div>
                            <button @click="assignClient(user.id)"
                                class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700"
                                :disabled="user.coach_id === coach.id"
                                :class="{ 'opacity-50 cursor-not-allowed': user.coach_id === coach.id }">
                                {{ user.coach_id === coach.id ? 'Already Assigned' : 'Assign' }}
                            </button>
                        </div>
                    </div>

                    <!-- No Results -->
                    <div v-else-if="searchQuery && !searching" class="mt-4 text-center py-4">
                        <p class="text-gray-500">No users found matching your search.</p>
                    </div>
                </div>

                <!-- Clients List -->
                <div v-if="coach.users.length > 0" class="space-y-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Current Clients:</h4>
                    <div v-for="client in coach.users" :key="client.id"
                        class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ client.name }}</h4>
                                <p class="text-sm text-gray-600">{{ client.email }}</p>
                                <div class="mt-2 flex space-x-4 text-sm text-gray-500">
                                    <span>{{ client.goals_count }} goals</span>
                                    <span>{{ client.investments_count }} investments</span>
                                </div>
                            </div>
                            <button @click="removeClient(client.id)" class="text-red-600 hover:text-red-700 text-sm">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No clients assigned</h3>
                    <p class="mt-1 text-sm text-gray-500">Use the search above to assign clients to this coach.</p>
                </div>
            </div>
        </div>

        <!-- Confirmation Modals -->
        <ConfirmationModal :show="showAssignModal" title="Assign Client"
            message="Are you sure you want to assign this client to the coach?" type="info" confirm-text="Assign Client"
            @confirm="confirmAssign" @cancel="cancelAssign" />

        <ConfirmationModal :show="showRemoveModal" title="Remove Client"
            message="Are you sure you want to remove this client from the coach?" type="warning"
            confirm-text="Remove Client" @confirm="confirmRemove" @cancel="cancelRemove" />

        <!-- Alert -->
        <Alert v-if="alertState" :type="alertState.type" :message="alertState.message" :duration="alertState.duration"
            :auto-close="alertState.autoClose" @close="clearAlert" />
    </AdminSidebar>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import Alert from '@/Components/Shared/Alert.vue';
import ConfirmationModal from '@/Components/Shared/ConfirmationModal.vue';
import { useAlert } from '@/Components/Composables/useAlert';

const props = defineProps({
    coach: Object,
});

const { alertState, openAlert, clearAlert } = useAlert();
const searchQuery = ref('');
const searchResults = ref([]);
const searching = ref(false);
const showAssignModal = ref(false);
const showRemoveModal = ref(false);
const userToAssign = ref(null);
const clientToRemove = ref(null);

const searchUsers = async () => {
    if (!searchQuery.value.trim()) {
        searchResults.value = [];
        return;
    }

    searching.value = true;
    try {
        const response = await fetch(`/admin/coaching/search-users?query=${encodeURIComponent(searchQuery.value)}`);
        const data = await response.json();
        searchResults.value = data.users || [];
    } catch (error) {
        console.error('Error searching users:', error);
        searchResults.value = [];
    } finally {
        searching.value = false;
    }
};

const assignClient = (userId) => {
    userToAssign.value = userId;
    showAssignModal.value = true;
};

const confirmAssign = () => {
    if (userToAssign.value) {
        router.post(route('coaching.assign-user', props.coach.id), {
            user_id: userToAssign.value
        }, {
            onSuccess: () => {
                openAlert('success', 'Client assigned to coach successfully!', 5000);
                searchResults.value = [];
                searchQuery.value = '';
            },
            onError: (errors) => {
                openAlert('danger', 'Error assigning client. Please try again.', 5000);
            }
        });
    }
    showAssignModal.value = false;
    userToAssign.value = null;
};

const cancelAssign = () => {
    showAssignModal.value = false;
    userToAssign.value = null;
};

const removeClient = (clientId) => {
    clientToRemove.value = clientId;
    showRemoveModal.value = true;
};

const confirmRemove = () => {
    if (clientToRemove.value) {
        router.post(route('coaching.remove-user', props.coach.id), {
            user_id: clientToRemove.value
        }, {
            onSuccess: () => {
                openAlert('success', 'Client removed from coach successfully!', 5000);
            },
            onError: (errors) => {
                openAlert('danger', 'Error removing client. Please try again.', 5000);
            }
        });
    }
    showRemoveModal.value = false;
    clientToRemove.value = null;
};

const cancelRemove = () => {
    showRemoveModal.value = false;
    clientToRemove.value = null;
};
</script>