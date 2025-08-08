<template>
    <Sidebar title="Coach Dashboard">

        <Head title="Clients" />
        <div class="space-y-6">
            <!-- Coach Profile Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
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
                        <h2 class="text-2xl font-bold text-purple-800 mb-2">{{ coach.name }}</h2>
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
                            <h4 class="font-semibold text-gray-900 mb-2">About Me</h4>
                            <p class="text-gray-600 leading-relaxed">{{ coach.bio }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clients Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-purple-800">My Clients</h3>
                    <div class="text-sm text-gray-600">
                        {{ clients.length }} client{{ clients.length !== 1 ? 's' : '' }}
                    </div>
                </div>

                <!-- Clients List -->
                <div v-if="clients.length > 0" class="space-y-4">
                    <div v-for="client in clients" :key="client.id"
                        class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Client Avatar -->
                                <div class="w-12 h-12 rounded-full bg-purple-200 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                </div>

                                <!-- Client Info -->
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ client.name }}</h4>
                                    <p class="text-sm text-gray-600">{{ client.email }}</p>
                                    <p class="text-xs text-gray-500">Joined {{ formatDate(client.created_at) }}</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <button @click="viewClientProfile(client)"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                    View Profile
                                </button>
                                <button @click="scheduleMeeting(client)"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                    Schedule a meeting
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Clients Message -->
                <div v-else class="text-center py-8">
                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Clients Yet</h3>
                    <p class="text-gray-600 mb-4">You don't have any assigned clients yet. Clients will appear here once
                        they select you as their coach.</p>
                    <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                        View Available Clients
                    </button>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Clients</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ clients.length }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Meeting Modal -->
        <transition name="fade">
            <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Schedule Meeting with {{ selectedClient.name }}
                    </h3>
                    <form @submit.prevent="submitMeeting" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Client Email</label>
                            <input type="email" v-model="form.email" disabled
                                class="w-full border rounded-lg p-2 bg-gray-100 text-gray-600" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" v-model="form.date" required
                                class="w-full border rounded-lg p-2 focus:ring-purple-500 focus:border-purple-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                            <input type="time" v-model="form.time" required
                                class="w-full border rounded-lg p-2 focus:ring-purple-500 focus:border-purple-500" />
                        </div>
                        <div class="flex justify-end space-x-2 pt-4">
                            <button type="button" @click="closeModal"
                                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
    </Sidebar>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    coach: Object,
    clients: Array,
});

const totalGoals = computed(() => {
    return props.clients.reduce((total, client) => total + (client.goals_count || 0), 0);
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

// Navigation & modal state
const viewClientProfile = (client) => {
    window.location.href = `/coach/client/${client.id}`;
};

const isModalOpen = ref(false);
const selectedClient = ref({});
const form = reactive({
    email: '',
    date: '',
    time: ''
});

const scheduleMeeting = (client) => {
    selectedClient.value = client;
    form.email = client.email;
    form.date = '';
    form.time = '';
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const submitMeeting = () => {
  router.post('/coach/meetings', {
      client_id: selectedClient.value.id,
      date: form.date,
      time: form.time,
  }, {
      onSuccess: () => {
          closeModal();
          toast.success('Meeting created and invite sent!');
      },
      onError: () => toast.error('Could not create meeting'),
  });
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
