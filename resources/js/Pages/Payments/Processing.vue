<script setup>
import { Head } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    payment: {
        type: Object,
        required: true
    },
    phone: {
        type: String,
        required: true
    },
    bookTitle: {
        type: String,
        default: null
    },
    type: {
        type: String,
        required: true // 'book' or 'zuriscore'
    }
});

const paymentStatus = ref(props.payment.status);
const paymentReason = ref(props.payment.reason);
const isLoading = ref(true);
const statusMessage = ref('');
const statusType = ref('info'); // info, success, danger, warning
let pollingInterval = null;

const checkPaymentStatus = async () => {
    try {
        const url = props.type === 'book'
            ? `/book/status/${props.payment.id}`
            : `/user/zuriscore/status/${props.payment.id}`;

        const response = await axios.get(url);
        paymentStatus.value = response.data.status;
        paymentReason.value = response.data.reason;

        updateStatusMessage();

        // Stop polling if payment is complete (success or failed)
        if (paymentStatus.value === 'succeeded' || paymentStatus.value === 'failed') {
            clearInterval(pollingInterval);
            isLoading.value = false;
        }
    } catch (error) {
        console.error('Error checking payment status:', error);
        statusMessage.value = 'Error checking payment status. Please refresh the page.';
        statusType.value = 'danger';
        isLoading.value = false;
    }
};

const updateStatusMessage = () => {
    switch (paymentStatus.value) {
        case 'pending':
            statusMessage.value = `STK push sent to ${props.phone}. Please check your phone and enter your M-Pesa PIN to complete the payment.`;
            statusType.value = 'info';
            break;
        case 'succeeded':
            if (props.type === 'book') {
                statusMessage.value = `Payment successful! Your book "${props.bookTitle}" order is confirmed. Check your email and spam folder if necessary. \nWe will contact you shortly for delivery.`;
            } else {
                statusMessage.value = 'Payment successful! Your ZuriScore report is being generated and will be sent to your email shortly.';
            }
            statusType.value = 'success';
            break;
        case 'failed':
            statusMessage.value = getFailureMessage(paymentReason.value);
            statusType.value = 'danger';
            break;
        default:
            statusMessage.value = 'Processing payment...';
            statusType.value = 'info';
    }
};

const getFailureMessage = (reason) => {
    const lowerReason = reason?.toLowerCase() || '';

    if (lowerReason.includes('cancel') || lowerReason.includes('cancelled')) {
        return 'Payment was cancelled. You can try again by going back to the form.';
    } else if (lowerReason.includes('pin') || lowerReason.includes('wrong')) {
        return 'Payment failed due to incorrect PIN. Please try again.';
    } else if (lowerReason.includes('insufficient')) {
        return 'Payment failed due to insufficient funds. Please ensure you have enough balance and try again.';
    } else if (lowerReason.includes('timeout')) {
        return 'Payment timed out. Please try again after 2 minutes.';
    } else {
        return `Payment failed: ${reason || 'Unknown error'}. Please try again.`;
    }
};

const goBack = () => {
    if (props.type === 'book') {
        window.location.href = '/books';
    } else {
        window.location.href = '/user/zuriscore';
    }
};

onMounted(() => {
    updateStatusMessage();

    // Start polling every 3 seconds if payment is still pending
    if (paymentStatus.value === 'pending') {
        pollingInterval = setInterval(checkPaymentStatus, 3000);
    } else {
        isLoading.value = false;
    }
});

onUnmounted(() => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
});
</script>

<template>

    <Head title="Payment Processing" />
    <Navbar />
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 mt-20">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h1 class="text-2xl font-semibold text-gray-900 mb-6 text-center">
                    {{ type === 'book' ? 'Book Purchase' : 'ZuriScore Payment' }} Processing
                </h1>

                <!-- Payment Status Card -->
                <div class="border border-gray-200 rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-center mb-4">
                        <!-- Loading spinner -->
                        <div v-if="isLoading" class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500">
                        </div>

                        <!-- Success icon -->
                        <div v-else-if="paymentStatus === 'succeeded'"
                            class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>

                        <!-- Error icon -->
                        <div v-else-if="paymentStatus === 'failed'"
                            class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Status Message -->
                    <div :class="{
                        'text-blue-700 bg-blue-50 border-blue-200': statusType === 'info',
                        'text-green-700 bg-green-50 border-green-200': statusType === 'success',
                        'text-red-700 bg-red-50 border-red-200': statusType === 'danger',
                        'text-yellow-700 bg-yellow-50 border-yellow-200': statusType === 'warning'
                    }" class="p-4 rounded-lg border text-center">
                        <p class="font-medium">{{ statusMessage }}</p>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Payment Details</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phone Number:</span>
                            <span class="font-medium">{{ phone }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Amount:</span>
                            <span class="font-medium">KES {{ payment.amount }}</span>
                        </div>
                        <div v-if="bookTitle" class="flex justify-between">
                            <span class="text-gray-600">Book:</span>
                            <span class="font-medium">{{ bookTitle }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium capitalize" :class="{
                                'text-blue-600': paymentStatus === 'pending',
                                'text-green-600': paymentStatus === 'succeeded',
                                'text-red-600': paymentStatus === 'failed'
                            }">{{ paymentStatus }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center space-x-4">
                    <button @click="goBack" v-if="paymentStatus === 'failed'"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Try Again
                    </button>

                    <button @click="goBack" v-if="paymentStatus === 'succeeded'"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                        {{ type === 'book' ? 'Order Another Book' : 'Generate Another Report' }}
                    </button>

                    <button @click="goBack"
                        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        {{ type === 'book' ? 'Back to Books' : 'Back to Form' }}
                    </button>
                </div>

                <!-- Auto-refresh notice -->
                <div v-if="isLoading" class="mt-6 text-center text-sm text-gray-500">
                    <p>This page will automatically update when payment is processed.</p>
                </div>
            </div>
        </div>
    </div>
    <Footer />
</template>
