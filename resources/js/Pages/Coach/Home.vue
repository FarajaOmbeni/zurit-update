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
                            <!-- <div class="flex space-x-2">
                                <button @click="viewClientProfile(client)"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                    View Profile
                                </button>
                                <button @click="contactClient(client)"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                    Contact
                                </button>
                            </div> -->
                        </div>

                        <!-- Client Progress Summary -->
                        <!-- <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="text-center">
                                    <div class="font-semibold text-purple-600">{{ client.goals_count || 0 }}</div>
                                    <div class="text-gray-500">Goals</div>
                                </div>
                                <div class="text-center">
                                    <div class="font-semibold text-blue-600">{{ client.investments_count || 0 }}</div>
                                    <div class="text-gray-500">Investments</div>
                                </div>
                            </div>
                        </div> -->
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

                <!-- <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Active Goals</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ totalGoals }}</p>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </Sidebar>
</template>

<script setup>
import { ref, computed } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    coach: Object,
    clients: Array,
});
console.log(props.clients)

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

const viewClientProfile = (client) => {
    // Navigate to client profile page
    window.location.href = `/coach/client/${client.id}`;
};

const contactClient = (client) => {
    // Open contact modal or navigate to messaging
    console.log('Contact client:', client.name);
};
</script>
