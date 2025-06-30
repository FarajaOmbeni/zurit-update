<template>
    <!-- Book Details Modal -->
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg relative">
            <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">
                ✕
            </button>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <img :src="book.image" :alt="book.title" class="w-[15rem] h-[22rem] rounded-md">
                </div>
                <div class="flex flex-col justify-between">
                    <div>
                        <h1 class="text-xl font-bold">{{ book.title }}</h1>
                        <p class="text-md mb-2">{{ book.description }}</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <p class="text-lg font-semibold text-yellow-600">Price: KES {{ book.price }}</p>
                        <button @click="openPaymentModal"
                            class="mt-4 bg-purple-600 text-white px-4 py-2 rounded-md text-sm hover:bg-purple-700 transition">
                            Purchase
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div v-if="showPaymentModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <!-- Alert ABOVE modal box -->
        <div class="absolute top-4 w-full flex justify-center z-[100]">
            <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />
        </div>

        <!-- Modal content box -->
        <div class="bg-white p-6 rounded-lg shadow-lg w-[28rem] relative z-[60]">
            <button @click="closePaymentModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">
                ✕
            </button>
            <h2 class="text-lg font-bold text-purple-900 mb-4">Enter Details</h2>

            <!-- Display form validation errors -->
            <div v-if="hasFormErrors" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                <h4 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h4>
                <ul class="text-sm text-red-600 space-y-1">
                    <li v-for="(errors, field) in form.errors" :key="field">
                        <strong>{{ formatFieldName(field) }}:</strong> {{ Array.isArray(errors) ? errors.join(', ') :
                            errors }}
                    </li>
                </ul>
            </div>

            <!-- Display general errors -->
            <div v-if="form.errors.general" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                <p class="text-sm text-red-600">{{ form.errors.general }}</p>
            </div>

            <form @submit.prevent="submitForm">
                <Input label="Name" placeholder="Enter your name" v-model="form.name" :error="form.errors.name"
                    required />
                <Input label="Email" type="email" placeholder="Enter your email" v-model="form.email"
                    :error="form.errors.email" required />
                <Input label="Confirm Email" type="email" placeholder="Confirm your email" v-model="form.confirm_email"
                    :error="form.errors.confirm_email" required />
                <Input label="Address" placeholder="Enter your address" v-model="form.address"
                    :error="form.errors.address" />
                <Input label="Phone number" placeholder="0712345678 (Safaricom number for payment)" v-model="form.phone"
                    :error="form.errors.phone" pattern="^(\+254|254|0)[17]\d{8}$"
                    title="Please enter a valid Kenyan phone number (e.g., 0712345678)" required />

                <!-- Processing state message -->
                <div v-if="form.processing" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                    <div class="flex items-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                        <p class="text-sm font-medium text-blue-800">
                            Processing payment... An M-Pesa STK Push has been sent to {{ form.phone }}.
                        </p>
                    </div>
                    <p class="text-xs text-blue-600 mt-1">Please check your phone and enter your M-Pesa PIN to complete
                        the transaction.</p>
                </div>

                <!-- Success state -->
                <div v-if="paymentSuccess" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
                    <p class="text-sm font-medium text-green-800">Payment initiated successfully!</p>
                    <p class="text-xs text-green-600 mt-1">You will receive confirmation via email and SMS.</p>
                </div>

                <Button type="submit" :processing="form.processing" :disabled="form.processing || paymentSuccess"
                    class="w-full">
                    <span v-if="form.processing">Processing Payment...</span>
                    <span v-else-if="paymentSuccess">Payment Initiated</span>
                    <span v-else>Submit Payment (KES {{ book.price }})</span>
                </Button>

                <!-- Additional help text -->
                <div class="mt-3 text-xs text-gray-500">
                    <p>• Make sure your phone has sufficient M-Pesa balance</p>
                    <p>• You will receive an M-Pesa prompt on your phone</p>
                    <p>• Enter your M-Pesa PIN to complete payment</p>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import Button from './Button.vue';
import Input from './Input.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';

const { openAlert, clearAlert, alertState } = useAlert()

const showPaymentModal = ref(false);
const paymentSuccess = ref(false);

const props = defineProps({
    show: Boolean,
    book: Object,
    user: {
        type: Object,
        default: null
    },
});

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    confirm_email: props.user?.email ?? '',
    price: '',
    phone: '',
    title: '',
    address: '',
});

// Computed properties for better error handling
const hasFormErrors = computed(() => {
    return Object.keys(form.errors).length > 0 && !form.errors.general;
});

// Helper functions
const formatFieldName = (fieldName) => {
    return fieldName.split('_').map(word =>
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
};

function openPaymentModal() {
    form.title = props.book.title;
    form.price = props.book.price;
    paymentSuccess.value = false;
    form.clearErrors();
    showPaymentModal.value = true;
}

function closePaymentModal() {
    showPaymentModal.value = false;
    paymentSuccess.value = false;
    form.clearErrors();
    form.reset();
}

function submitForm() {
    form.post(route('buy.book'), {
        preserveScroll: true, // inertia nicety
        onSuccess() { // we are redirected, nothing else to do here }
}})
}


// Watch for flash messages from the server
const { props: pageProps } = usePage();
watch(() => pageProps.flash, (flash) => {
    if (flash?.success?.message) {
        openAlert('success', flash.success.message, flash.success.duration || 5000);
    }
    if (flash?.error?.message) {
        openAlert('danger', flash.error.message, flash.error.duration || 8000);
    }
}, { deep: true, immediate: true });
</script>