<!-- ContactFormModal.vue -->
<template>
    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="transform opacity-0"
        enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100"
        leave-to-class="transform opacity-0">
        <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>

            <!-- Modal -->
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-md rounded-xl bg-gray-900 shadow-lg p-6">
                    <!-- Close button -->
                    <button @click="$emit('close')" class="absolute right-4 top-4 text-gray-400 hover:text-gray-200">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Form Content -->
                    <div class="mt-4">
                        <h2 class="text-2xl font-bold text-amber-400 mb-6">
                            Enter your details and we will get back to you soon
                        </h2>

                        <form @submit.prevent="handleSubmit" class="space-y-4">
                            <div>
                                <label class="block text-white mb-2">
                                    Email<span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.email" type="email" required
                                    class="w-full p-2 rounded bg-white text-gray-900"
                                    :class="{ 'border-2 border-red-500': errors.email }" />
                                <span v-if="errors.email" class="text-red-500 text-sm">{{ errors.email }}</span>
                            </div>

                            <div>
                                <label class="block text-white mb-2">
                                    Phone Number<span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.phone" type="tel" required
                                    class="w-full p-2 rounded bg-white text-gray-900"
                                    :class="{ 'border-2 border-red-500': errors.phone }" />
                                <span v-if="errors.phone" class="text-red-500 text-sm">{{ errors.phone }}</span>
                            </div>

                            <p class="text-white text-sm">
                                All fields marked with <span class="text-red-500">*</span> are required
                            </p>

                            <button type="submit"
                                class="w-full bg-purple-600 text-white py-2 rounded font-medium hover:bg-purple-700 transition-colors duration-200">
                                Submit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, reactive } from 'vue'

const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true
    }
})

const emit = defineEmits(['close', 'submit'])

const form = reactive({
    email: '',
    phone: ''
})

const errors = reactive({
    email: '',
    phone: ''
})

const validateForm = () => {
    let isValid = true
    errors.email = ''
    errors.phone = ''

    // Email validation
    if (!form.email) {
        errors.email = 'Email is required'
        isValid = false
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Please enter a valid email'
        isValid = false
    }

    // Phone validation
    if (!form.phone) {
        errors.phone = 'Phone number is required'
        isValid = false
    } else if (!/^\d{10}$/.test(form.phone.replace(/\D/g, ''))) {
        errors.phone = 'Please enter a valid 10-digit phone number'
        isValid = false
    }

    return isValid
}

const handleSubmit = () => {
    if (validateForm()) {
        emit('submit', { ...form })
        form.email = ''
        form.phone = ''
    }
}
</script>