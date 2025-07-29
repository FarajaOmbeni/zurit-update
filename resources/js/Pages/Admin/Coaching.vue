<template>
    <AdminSidebar title="Coaching Management">

        <Head title="Coaching Management" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Coaches</h2>
                <Link :href="route('coaching.create')"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Add Coach</span>
                </Link>
            </div>

            <!-- Coaches Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="coach in coaches" :key="coach.id"
                    class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <!-- Coach Photo -->
                    <div class="flex items-center space-x-4 mb-4">
                        <div
                            class="w-16 h-16 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                            <img v-if="coach.photo" :src="coach.photo" :alt="coach.name"
                                class="w-full h-full object-cover" />
                            <svg v-else class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ coach.name }}</h3>
                            <p class="text-sm text-gray-600">{{ coach.email }}</p>
                        </div>
                    </div>

                    <!-- Coach Info -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ coach.phone }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ coach.users_count }} clients</span>
                        </div>
                    </div>

                    <!-- Bio -->
                    <div v-if="coach.bio" class="mb-4">
                        <p class="text-sm text-gray-600 line-clamp-3">{{ coach.bio }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <Link :href="route('coaching.show', coach.id)"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm text-center">
                        View
                        </Link>
                        <Link :href="route('coaching.edit', coach.id)"
                            class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded text-sm text-center">
                        Edit
                        </Link>
                        <button @click="deleteCoach(coach.id)"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm"
                            :disabled="coach.users_count > 0"
                            :class="{ 'opacity-50 cursor-not-allowed': coach.users_count > 0 }">
                            Delete
                        </button>
                    </div>

                    <!-- Warning for coaches with clients -->
                    <div v-if="coach.users_count > 0" class="mt-2">
                        <p class="text-xs text-red-600">
                            Cannot delete: {{ coach.users_count }} client(s) assigned
                        </p>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="coaches.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No coaches</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new coach.</p>
                <div class="mt-6">
                    <Link :href="route('coaching.create')"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Coach
                    </Link>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            title="Delete Coach"
            message="Are you sure you want to delete this coach? This action cannot be undone."
            type="danger"
            confirm-text="Delete Coach"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />

        <!-- Alert -->
        <Alert 
            v-if="alertState" 
            :type="alertState.type" 
            :message="alertState.message"
            :duration="alertState.duration" 
            :auto-close="alertState.autoClose" 
            @close="clearAlert" 
        />
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
    coaches: Array,
});

const { alertState, openAlert, clearAlert } = useAlert();
const showDeleteModal = ref(false);
const coachToDelete = ref(null);

const deleteCoach = (coachId) => {
    coachToDelete.value = coachId;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (coachToDelete.value) {
        router.delete(route('coaching.destroy', coachToDelete.value), {
            onSuccess: () => {
                openAlert('success', 'Coach deleted successfully!', 5000);
            },
            onError: (errors) => {
                openAlert('danger', 'Error deleting coach. Please try again.', 5000);
            }
        });
    }
    showDeleteModal.value = false;
    coachToDelete.value = null;
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    coachToDelete.value = null;
};
</script>