<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { useAlert } from '@/Components/Composables/useAlert';
import Alert from '@/Components/Shared/Alert.vue';

const { alertState, openAlert, clearAlert } = useAlert();


const page = usePage();
const user = page.props.auth.user || {};

const form = useForm({
    phone: ''
})

function handleSubmit() {
    form.post(route('subscribe'), {
        onSuccess: (response) => {
            form.reset();
            openAlert('success', 'Subscribed successfully', 5000);
            window.location.reload();
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages, 5000);
        }
    });
}
</script>

<template>
    <!-- Document head -->

    <Head title="Subscribe to Prosperity Tools" />

    <!-- Wrapper that only renders for logged‑in users -->
    <AuthenticatedLayout>
        <Sidebar>
            <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />
            <!-- Center viewport -->
            <div class="min-h-screen flex items-center justify-center px-4 py-10">
                <!-- Card: matches original purple Zurit styling -->
                <div
                    class="w-full max-w-lg bg-gradient-to-br from-purple-800 via-purple-700 to-purple-600 text-white rounded-2xl shadow-2xl p-8 md:p-10 text-center">
                    <!-- Title -->
                    <h1 class="text-4xl font-extrabold leading-tight mb-3">
                        Unlock <span class="text-yellow-300">Prosperity&nbsp;Tools</span>
                    </h1>
                    <!-- Intro -->
                    <p class="text-lg md:text-xl mb-6 text-white/90">
                        Hey {{ user.name || 'friend' }}! Ready to level-up your finances? Our Prosperity Toolkit gives
                        you budgeting, investment and debt dashboards—everything you need to grow with confidence.
                    </p>

                    <!-- Features -->
                    <ul class="space-y-2 text-left mb-8">
                        <li class="flex items-start gap-2"><span class="text-green-300">✔</span> Real-time budget
                            insights</li>
                        <li class="flex items-start gap-2"><span class="text-green-300">✔</span> Smart debt-payoff
                            planner</li>
                        <li class="flex items-start gap-2"><span class="text-green-300">✔</span> Guided investment
                            calculator</li>
                    </ul>

                    <form @submit.prevent="handleSubmit" class="space-y-4">
                        <input v-model="form.phone" type="text" inputmode="numeric" maxlength="12"
                            placeholder="Enter your Mpesa number for payment"
                            class="w-full px-4 py-3 rounded-lg border border-purple-300 bg-white text-purple-800 placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent shadow-sm disabled:opacity-50"
                            :disabled="form.processing" />

                        <button type="submit" :disabled="form.processing"
                            :class="[form.processing ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-white text-purple-800 hover:bg-purple-50', 'inline-block font-semibold px-6 py-3 rounded-full shadow active:scale-95 transition w-full']">
                            {{ form.processing ? 'Subscribing...' : '🎉 Start/Renew My Subscription' }}
                        </button>
                    </form>

                    <!-- Confirmation message -->
                    <p class="p-2 text-green-300 mt-4" :class="form.processing ? 'block' : 'hidden'">
                        We have sent you an MPesa STK push. Confirm to complete the payment.
                    </p>
                    <p class="mt-3 text-sm text-white/80">
                        Just KES 200 for 31 days of unlimited access.
                    </p>
                </div>
            </div>
        </Sidebar>
    </AuthenticatedLayout>
</template>
