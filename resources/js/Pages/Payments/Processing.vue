<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Echo from 'laravel-echo'

const props = defineProps({ paymentId: Number, amount: Number, title: String })

const error = ref(null)

function goto(route) { router.visit(route) }

onMounted(() => {
    // 1️⃣  Prefer real-time broadcast first
    const channel = Echo.private(`payments.${props.paymentId}`)
    channel.listen('MpesaPaymentSucceeded', e => {
        goto(route('payments.success', { payment: props.paymentId }))
    }).listen('MpesaPaymentFailed', e => {
        goto(route('payments.failed', { payment: props.paymentId }))
    })

    // 2️⃣  Fallback polling every 5 s (covers page reloads / Echo blocked)
    const poll = setInterval(async () => {
        const { data } = await axios.get(route('payments.status', props.paymentId))
        if (data.status === 'success') goto(route('payments.success', { payment: props.paymentId }))
        if (data.status === 'failed') goto(route('payments.failed', { payment: props.paymentId }))
    }, 5000)

    // clear interval when component unmounts
    onUnmounted(() => clearInterval(poll))
})
</script>

<template>
    <div class="h-screen flex flex-col items-center justify-center">
        <svg class="animate-spin h-10 w-10 text-purple-600" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>
        <p class="mt-4 text-sm text-gray-600">Processing KES {{ amount }} payment for "{{ title }}". <br>
            Approve the STK Push on your phone…</p>

        <p v-if="error" class="mt-4 text-red-600 text-sm">{{ error }}</p>
    </div>
</template>
