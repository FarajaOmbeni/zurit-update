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
            <button @click="showPaymentModal = false" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">
                ✕
            </button>
            <h2 class="text-lg font-bold text-purple-900 mb-4">Enter Details</h2>
            <form @submit.prevent="submitForm">
                <Input label="Name" placeholder="Enter your name" v-model="form.name" />
                <Input label="Email" placeholder="Enter your email" v-model="form.email" />
                <Input label="Confirm Email" placeholder="Confirm your email" v-model="form.confirm_email" />
                <Input label="Address" placeholder="Enter your address" v-model="form.address" />
                <Input label="Phone number" placeholder="e.g., 0712345678 or +254712345678" v-model="form.phone"
                    :error="form.errors.phone" />
                <p class="text-xs text-gray-500 -mt-2">Enter a valid Kenyan phone number (e.g., 0712345678 or
                    0112345678)</p>

                <p v-show="form.processing" class="font-bold text-sm text-green-500">We are sending you an MPESA STK
                    Push to
                    {{ form.phone }}. Input your pin to continue</p>

                <Button type="submit" :processing="form.processing">
                    {{ form.processing ? 'Loading...' : 'Submit' }}
                </Button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Button from './Button.vue';
import Input from './Input.vue';
import { useForm } from '@inertiajs/vue3';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';

const { openAlert, clearAlert, alertState } = useAlert()

const showPaymentModal = ref(false);

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

function openPaymentModal() {
    form.title = props.book.title;
    form.price = props.book.price;
    showPaymentModal.value = true;
}

function submitForm() {
    form.post(route('buy.book'), {
        onSuccess: () => {
            form.reset();
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');
            openAlert('danger', errorMessages, 10000);
        }
    });
}
</script>