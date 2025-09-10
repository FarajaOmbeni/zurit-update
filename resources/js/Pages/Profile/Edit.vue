<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import { ref, computed } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});


// Get page props
const page = usePage();
const user = computed(() => page.props.auth.user);
const subscription = computed(() => page.props.auth.subscription);

// Show subscription section only for authenticated users
const showSubscriptionSection = computed(() => !!user.value);

// Reactive state
const showCancelModal = ref(false);
const cancelling = ref(false);
const reactivating = ref(false);
const alertMessage = ref('');
const alertType = ref('success');

// Alert styling
const alertClass = computed(() => {
    return alertType.value === 'success'
        ? 'bg-green-50 border border-green-200'
        : 'bg-red-50 border border-red-200';
});

// Format date helper
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Show alert
const showAlert = (message, type = 'success') => {
    alertMessage.value = message;
    alertType.value = type;
    setTimeout(() => {
        clearAlert();
    }, 5000);
};

// Clear alert
const clearAlert = () => {
    alertMessage.value = '';
    alertType.value = 'success';
};

// Cancel subscription
const cancelSubscription = async () => {
    if (cancelling.value) return;

    cancelling.value = true;

    try {
        const form = useForm({});

        await form.post(route('subscription.cancel'), {
            onSuccess: () => {
                showCancelModal.value = false;
                showAlert('Subscription cancelled successfully. You will have access until your current period expires.', 'success');
                // Reload page to update subscription status
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            },
            onError: (errors) => {
                const errorMessage = Object.values(errors).flat().join(' ') || 'Failed to cancel subscription. Please try again.';
                showAlert(errorMessage, 'error');
            }
        });
    } catch (error) {
        showAlert('An error occurred while cancelling your subscription.', 'error');
    } finally {
        cancelling.value = false;
    }
};

// Reactivate subscription
const reactivateSubscription = async () => {
    if (reactivating.value) return;

    reactivating.value = true;

    try {
        const form = useForm({});

        await form.post(route('subscription.reactivate'), {
            onSuccess: () => {
                showAlert('Subscription reactivated successfully!', 'success');
                // Reload page to update subscription status
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            },
            onError: (errors) => {
                const errorMessage = Object.values(errors).flat().join(' ') || 'Failed to reactivate subscription. Please try again.';
                showAlert(errorMessage, 'error');
            }
        });
    } catch (error) {
        showAlert('An error occurred while reactivating your subscription.', 'error');
    } finally {
        reactivating.value = false;
    }
};
</script>

<template>

    <Head title="Profile" />
    <Sidebar>
        <AuthenticatedLayout>
            <div class="bg-white p-6 rounded-lg">
                <div class="max-w-7xl space-y-6">
                    <div class="p-4 shadow bg-gray-100 sm:rounded-lg sm:p-8">
                        <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status"
                            class="max-w-xl" />
                    </div>

                    <div class="bg-gray-100 p-4 shadow sm:rounded-lg sm:p-8">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>

                    <div class="bg-gray-100 p-4 shadow sm:rounded-lg sm:p-8">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>
            </div>

            <!-- Subscription Management Section -->
            <div v-if="showSubscriptionSection" class="bg-white rounded-lg shadow-lg p-6 mx-4 mb-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                    Subscription Management
                </h3>

                <!-- Active Subscription -->
                <div v-if="subscription && subscription.is_active"
                    class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-semibold text-green-800">Active Subscription</span>
                            </div>
                            <div class="mt-2 text-sm text-green-700">
                                <p><strong>Plan:</strong> {{ subscription.package ?
                                    subscription.package.charAt(0).toUpperCase() + subscription.package.slice(1) :
                                    'Active'
                                    }}</p>
                                <p v-if="subscription.expires_at"><strong>Expires:</strong> {{
                                    formatDate(subscription.expires_at) }}</p>
                                <p><strong>Status:</strong> {{ subscription.status?.charAt(0).toUpperCase() +
                                    subscription.status?.slice(1) || 'Active' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Actions -->
                <div class="space-y-3">
                    <!-- View Subscription Details -->
                    <a :href="route('subscription.plans')"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150 w-full justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        View Subscription Details
                    </a>

                    <!-- Cancel Subscription Button -->
                    <button v-if="subscription && subscription.is_active && subscription.status === 'active'"
                        @click="showCancelModal = true"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 w-full justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        Cancel Subscription
                    </button>

                    <!-- Reactivate Subscription Button -->
                    <button v-if="subscription && subscription.status === 'cancelled'" @click="reactivateSubscription"
                        :disabled="reactivating"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 w-full justify-center disabled:opacity-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        {{ reactivating ? 'Reactivating...' : 'Reactivate Subscription' }}
                    </button>

                    <!-- No Subscription -->
                    <div v-if="!subscription || !subscription.is_active" class="text-center">
                        <p class="text-gray-600 mb-3">No active subscription</p>
                        <a :href="route('subscription.plans')"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Subscribe Now
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cancel Subscription Modal -->
            <div v-if="showCancelModal"
                class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.863-.833-2.633 0L4.181 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Cancel Subscription</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500 mb-4">
                                Are you sure you want to cancel your subscription? You will still have access until your
                                current billing period expires.
                            </p>
                            <div v-if="subscription && subscription.expires_at"
                                class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-4">
                                <p class="text-sm text-yellow-800">
                                    <strong>Access until:</strong> {{ formatDate(subscription.expires_at) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-3 justify-center">
                            <button @click="showCancelModal = false"
                                class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                Keep Subscription
                            </button>
                            <button @click="cancelSubscription" :disabled="cancelling"
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50">
                                {{ cancelling ? 'Cancelling...' : 'Yes, Cancel' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <div v-if="alertMessage" :class="alertClass"
                class="fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 max-w-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg v-if="alertType === 'success'" class="h-5 w-5 text-green-400" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <svg v-else class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium"
                            :class="alertType === 'success' ? 'text-green-800' : 'text-red-800'">
                            {{ alertMessage }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button @click="clearAlert"
                                :class="alertType === 'success' ? 'text-green-500 hover:bg-green-100' : 'text-red-500 hover:bg-red-100'"
                                class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </Sidebar>
</template>
