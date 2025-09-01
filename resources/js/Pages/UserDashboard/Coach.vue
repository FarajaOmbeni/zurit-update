<template>
    <Sidebar title="Coach Dashboard">
        <Head title="Coach"/>
        <div class="space-y-6">
            <!-- Current Coach Section -->
            <div v-if="coach" class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-purple-800 mb-4">Your Assigned Coach</h2>

                <div class="flex items-start space-x-6">
                    <!-- Coach Photo -->
                    <div class="flex-shrink-0">
                        <div v-if="coach.photo" class="w-24 h-24 rounded-full overflow-hidden">
                            <img :src="`/storage/coaches/${coach.photo}`" :alt="coach.name" class="w-full h-full object-cover">
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
                            <!-- <button @click="removeCoach"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Remove Coach
                            </button> -->
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

                    <button @click="showContactInfo = true"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors">
                        Find a Coach
                    </button>
                </div>
            </div>

            <!-- Contact Information Modal -->
            <div v-if="showContactInfo"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-900">Contact Zurit Consulting</h3>
                        <button @click="showContactInfo = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="text-center mb-6">
                            <div
                                class="w-16 h-16 rounded-full bg-purple-100 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-gray-600">To be assigned a coach, please contact our team directly:</p>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <a href="mailto:jmugonyi@zurticonsulting.com"
                                        class="text-purple-600 hover:text-purple-700 font-medium">
                                        jmugonyi@zurticonsulting.com
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Phone</p>
                                    <a href="tel:+254759092412"
                                        class="text-purple-600 hover:text-purple-700 font-medium">
                                        +254 759 092 412
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Location</p>
                                    <p class="text-gray-900">Zuider Complex, Mbagathi Hospital Road, Off Mbagathi Way
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600 text-center">
                                Our team will review your request and assign the most suitable coach based on your needs
                                and goals.
                            </p>
                        </div>
                    <div class="flex justify-center mt-8">
                        <button
                            @click="sendCoachRequest"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded shadow transition duration-200"
                        >
                            Send Request for a Coach
                        </button>
                    </div>
                    <div v-if="requestSent" class="mt-4 text-green-600 text-center font-medium">
                        Your request has been sent! Our admin will contact you soon.
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </Sidebar>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';

const props = defineProps({
    user: Object,
    coach: Object,
    availableCoaches: Array,
});
console.log(props.coach);

const showContactInfo = ref(false);

// const removeCoach = () => {
//     const form = useForm({});

//     form.delete(route('coach.remove'), {
//         onSuccess: () => {
//             // Coach will be removed from the page
//         }
//     });
// };
const requestSent = ref(false);

const sendCoachRequest = () => {
    const form = useForm({});
    form.post(route('coach.request'), {
        onSuccess: () => {
            requestSent.value = true;
        },
        onError: () => {
            // Optionally handle error, e.g. show a message
            requestSent.value = false;
        }
    });
};

</script>
