<template>
    <CoachSidebar title="My Meetings">
        <Head title="Meetings" />
        <div class="bg-white rounded-lg shadow-lg">
            <!-- Header -->
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Coaching Sessions</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Manage and view all your scheduled coaching sessions
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                            {{ upcomingMeetings.length }} Upcoming
                        </span>
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">
                            {{ completedMeetings.length }} Completed
                        </span>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="px-6 py-3 border-b border-gray-200">
                <div class="flex space-x-4">
                    <button @click="activeTab = 'all'" :class="[
                        'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                        activeTab === 'all'
                            ? 'bg-purple-100 text-purple-700'
                            : 'text-gray-500 hover:text-gray-700'
                    ]">
                        All Meetings ({{ meetings.length }})
                    </button>
                    <button @click="activeTab = 'upcoming'" :class="[
                        'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                        activeTab === 'upcoming'
                            ? 'bg-purple-100 text-purple-700'
                            : 'text-gray-500 hover:text-gray-700'
                    ]">
                        Upcoming ({{ upcomingMeetings.length }})
                    </button>
                    <button @click="activeTab = 'completed'" :class="[
                        'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                        activeTab === 'completed'
                            ? 'bg-purple-100 text-purple-700'
                            : 'text-gray-500 hover:text-gray-700'
                    ]">
                        Completed ({{ completedMeetings.length }})
                    </button>
                </div>
            </div>

            <!-- Meetings Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Client
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date & Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="meeting in filteredMeetings" :key="meeting.id" class="hover:bg-gray-50">
                            <!-- Client Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div
                                            class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-purple-800">
                                                {{ meeting.client_name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ meeting.client_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ meeting.client_email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Date & Time -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ formatDate(meeting.start_time) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ formatTime(meeting.start_time) }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[
                                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                    meeting.status === 'upcoming'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-gray-100 text-gray-800'
                                ]">
                                    {{ meeting.status === 'upcoming' ? 'Upcoming' : 'Completed' }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a v-if="meeting.status === 'upcoming'" :href="meeting.start_url" target="_blank"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Start Meeting
                                    </a>
                                    <button @click="copyToClipboard(meeting.start_url)"
                                        class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Copy Link
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty State -->
                <div v-if="filteredMeetings.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3a4 4 0 118 0v4m-4 12a2 2 0 100-4 2 2 0 000 4zm0 0v4m0-10V3" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">
                        {{ activeTab === 'all' ? 'No meetings' : `No ${activeTab} meetings` }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ activeTab === 'all' ? 'You haven\'t scheduled any coaching sessions yet.' : `No ${activeTab}
                        meetings to display.` }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Success Toast -->
        <div v-if="showToast" class="fixed bottom-4 right-4 z-50">
            <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
                Link copied to clipboard!
            </div>
        </div>
    </CoachSidebar>
</template>

<script setup>
import CoachSidebar from '@/Components/CoachSidebar.vue';
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    coach: Object,
    meetings: Array,
});

console.log(props.meetings);

const activeTab = ref('all');
const showToast = ref(false);

// Computed properties for filtering meetings
const upcomingMeetings = computed(() => {
    return props.meetings.filter(meeting => meeting.status === 'upcoming');
});

const completedMeetings = computed(() => {
    return props.meetings.filter(meeting => meeting.status === 'completed');
});

const filteredMeetings = computed(() => {
    switch (activeTab.value) {
        case 'upcoming':
            return upcomingMeetings.value;
        case 'completed':
            return completedMeetings.value;
        default:
            return props.meetings;
    }
});

// Utility functions
const formatDate = (dateTime) => {
    return new Date(dateTime).toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatTime = (dateTime) => {
    return new Date(dateTime).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        showToast.value = true;
        setTimeout(() => {
            showToast.value = false;
        }, 3000);
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
};
</script>
