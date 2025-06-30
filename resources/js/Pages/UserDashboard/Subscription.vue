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
    card_number: '',
    expiry_month: '',
    expiry_year: '',
    cvv: '',
    cardholder_name: '',
    package: 'monthly'
})

// Package pricing
const packages = {
    monthly: { price: 500, duration: '1 month', label: 'Monthly Subscription' },
    yearly: { price: 4500, duration: '12 months', label: 'Yearly Subscription', savings: 'Save KES 1,500!' }
}

// Format card number with spaces
function formatCardNumber(value) {
    const v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    const matches = v.match(/\d{4,16}/g);
    const match = matches && matches[0] || '';
    const parts = [];
    for (let i = 0, len = match.length; i < len; i += 4) {
        parts.push(match.substring(i, i + 4));
    }
    if (parts.length) {
        return parts.join(' ');
    } else {
        return v;
    }
}

// Handle card number input
function handleCardNumberInput(event) {
    const formatted = formatCardNumber(event.target.value);
    form.card_number = formatted;
}

// Format expiry date
function handleExpiryInput(event) {
    let value = event.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    event.target.value = value;
}

function handleSubmit() {
    form.post(route('subscribe'), {
        onSuccess: (response) => {
            form.reset();
            openAlert('success', 'Payment processed successfully', 5000);
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

                    <!-- Package Selection -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold mb-4 text-white">Choose Your Plan</h3>
                        <div class="space-y-3">
                            <!-- Monthly Package -->
                            <label
                                class="relative flex items-center p-4 bg-white/10 rounded-lg cursor-pointer hover:bg-white/15 transition-colors border-2"
                                :class="form.package === 'monthly' ? 'border-yellow-300 bg-white/15' : 'border-transparent'">
                                <input type="radio" v-model="form.package" value="monthly" class="sr-only">
                                <div class="flex-1">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-semibold text-white">Monthly Subscription</div>
                                            <div class="text-white/80 text-sm">Full access for 1 month</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-yellow-300">KES 500</div>
                                            <div class="text-white/70 text-sm">per month</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4" v-if="form.package === 'monthly'">
                                    <div class="w-5 h-5 bg-yellow-300 rounded-full flex items-center justify-center">
                                        <div class="w-2 h-2 bg-purple-800 rounded-full"></div>
                                    </div>
                                </div>
                            </label>

                            <!-- Yearly Package -->
                            <label
                                class="relative flex items-center p-4 bg-white/10 rounded-lg cursor-pointer hover:bg-white/15 transition-colors border-2"
                                :class="form.package === 'yearly' ? 'border-yellow-300 bg-white/15' : 'border-transparent'">
                                <input type="radio" v-model="form.package" value="yearly" class="sr-only">
                                <div class="flex-1">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-semibold text-white">Yearly Subscription</div>
                                            <div class="text-white/80 text-sm">Full access for 12 months</div>
                                            <div class="text-green-300 text-sm font-medium">💰 Save KES 1,500!</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-yellow-300">KES 4,500</div>
                                            <div class="text-white/70 text-sm">KES 375/month</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4" v-if="form.package === 'yearly'">
                                    <div class="w-5 h-5 bg-yellow-300 rounded-full flex items-center justify-center">
                                        <div class="w-2 h-2 bg-purple-800 rounded-full"></div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <form @submit.prevent="handleSubmit" class="space-y-4">
                        <!-- Cardholder Name -->
                        <div>
                            <input v-model="form.cardholder_name" type="text" placeholder="Cardholder Name"
                                class="w-full px-4 py-3 rounded-lg border border-purple-300 bg-white text-purple-800 placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent shadow-sm disabled:opacity-50"
                                :disabled="form.processing" required />
                        </div>

                        <!-- Card Number -->
                        <div>
                            <input :value="form.card_number" @input="handleCardNumberInput" type="text"
                                placeholder="1234 5678 9012 3456" maxlength="19"
                                class="w-full px-4 py-3 rounded-lg border border-purple-300 bg-white text-purple-800 placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent shadow-sm disabled:opacity-50"
                                :disabled="form.processing" required />
                        </div>

                        <!-- Expiry and CVV -->
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <input v-model="form.expiry_month" type="text" placeholder="MM" maxlength="2"
                                    pattern="[0-9]*" inputmode="numeric"
                                    class="w-full px-4 py-3 rounded-lg border border-purple-300 bg-white text-purple-800 placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent shadow-sm disabled:opacity-50"
                                    :disabled="form.processing" required />
                            </div>
                            <div class="flex-1">
                                <input v-model="form.expiry_year" type="text" placeholder="YY" maxlength="2"
                                    pattern="[0-9]*" inputmode="numeric"
                                    class="w-full px-4 py-3 rounded-lg border border-purple-300 bg-white text-purple-800 placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent shadow-sm disabled:opacity-50"
                                    :disabled="form.processing" required />
                            </div>
                            <div class="flex-1">
                                <input v-model="form.cvv" type="text" placeholder="CVV" maxlength="4" pattern="[0-9]*"
                                    inputmode="numeric"
                                    class="w-full px-4 py-3 rounded-lg border border-purple-300 bg-white text-purple-800 placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:border-transparent shadow-sm disabled:opacity-50"
                                    :disabled="form.processing" required />
                            </div>
                        </div>

                        <button type="submit" :disabled="form.processing"
                            :class="[form.processing ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-white text-purple-800 hover:bg-purple-50', 'inline-block font-semibold px-6 py-3 rounded-full shadow active:scale-95 transition w-full']">
                            {{ form.processing ? 'Processing Payment...' : `🎉 Pay KES ${packages[form.package].price} -
                            Start Subscription` }}
                        </button>
                    </form>

                    <!-- Confirmation message -->
                    <p class="p-2 text-green-300 mt-4" :class="form.processing ? 'block' : 'hidden'">
                        Processing your payment securely...
                    </p>

                    <!-- Security notice -->
                    <div class="mt-4 flex items-center justify-center gap-2 text-sm text-white/80">
                        <span>🔒</span>
                        <span>Secure SSL encrypted payment</span>
                    </div>

                    <p class="mt-3 text-sm text-white/80">
                        {{ form.package === 'yearly' ? 'KES 4,500 for 12 months of unlimited access (Save KES 1,500!)' :
                        'KES 500 for 1 month of unlimited access' }}
                    </p>
                </div>
            </div>
        </Sidebar>
    </AuthenticatedLayout>
</template>