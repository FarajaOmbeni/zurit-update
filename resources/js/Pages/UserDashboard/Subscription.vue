<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { useAlert } from '@/Components/Composables/useAlert';
import Alert from '@/Components/Shared/Alert.vue';
import { computed } from 'vue';

const { alertState, openAlert, clearAlert } = useAlert();

const page = usePage();
const user = page.props.auth.user || {};
const subscription = page.props.auth.subscription || {};

const form = useForm({
    package: 'monthly'
})

// Package pricing
const packages = {
    monthly: { price: 500, duration: '1 month', label: 'Monthly Subscription' },
    yearly: { price: 4500, duration: '12 months', label: 'Yearly Subscription', savings: 'Save KES 1,500!' }
}

// Computed properties
const isSubscribed = computed(() => subscription.is_active)
const subscriptionExpiry = computed(() => {
    if (!subscription.expires_at) return null
    return new Date(subscription.expires_at).toLocaleDateString()
})

function handleSubmit() {
    form.post(route('subscribe'), {
        onSuccess: () => {
            // Inertia::location() will automatically redirect to Pesapal payment page
            openAlert('success', 'Redirecting to secure payment page...', 3000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(' ');
            openAlert('danger', errorMessages, 5000);
        }
    });
}

function cancelSubscription() {
    if (confirm('Are you sure you want to cancel your subscription? You will still have access until your current period expires.')) {
        useForm().post(route('subscription.cancel'), {
            onSuccess: () => {
                openAlert('success', 'Subscription cancelled successfully', 5000);
                window.location.reload();
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors).flat().join(' ');
                openAlert('danger', errorMessages, 5000);
            }
        });
    }
}

function reactivateSubscription() {
    useForm().post(route('subscription.reactivate'), {
        onSuccess: () => {
            openAlert('success', 'Subscription reactivated successfully', 5000);
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
                <!-- Already Subscribed State -->
                <div v-if="isSubscribed"
                    class="w-full max-w-lg bg-gradient-to-br from-green-800 via-green-700 to-green-600 text-white rounded-2xl shadow-2xl p-8 md:p-10 text-center">
                    <div class="mb-6">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-extrabold leading-tight mb-2">
                            🎉 You're All Set!
                        </h1>
                        <p class="text-lg text-white/90">
                            Welcome to the <span class="text-yellow-300 font-semibold">Prosperity Tools</span>
                        </p>
                    </div>

                    <!-- Subscription Details -->
                    <div class="bg-white/10 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold mb-3">Your Subscription</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Plan:</span>
                                <span class="font-medium capitalize">{{ subscription.package || 'Active' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Status:</span>
                                <span class="font-medium">{{ subscription.status === 'active' ? 'Active' :
                                    subscription.status }}</span>
                            </div>
                            <div v-if="subscriptionExpiry" class="flex justify-between">
                                <span>Expires:</span>
                                <span class="font-medium">{{ subscriptionExpiry }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <a :href="route('budget.index')"
                            class="block w-full py-3 px-6 bg-white text-green-800 font-semibold rounded-full shadow hover:bg-gray-100 transition">
                            🚀 Go to Dashboard
                        </a>

                        <button v-if="subscription.status === 'active'" @click="cancelSubscription"
                            class="w-full py-2 px-6 border border-white/50 text-white font-medium rounded-full hover:bg-white/10 transition">
                            Cancel Subscription
                        </button>

                        <button v-if="subscription.status === 'cancelled'" @click="reactivateSubscription"
                            class="w-full py-3 px-6 bg-yellow-500 text-green-800 font-semibold rounded-full shadow hover:bg-yellow-400 transition">
                            Reactivate Subscription
                        </button>
                    </div>
                </div>

                <!-- Subscription Purchase Form -->
                <div v-else
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
                        <li class="flex items-start gap-2"><span class="text-green-300">✔</span> Net worth tracking</li>
                        <li class="flex items-start gap-2"><span class="text-green-300">✔</span> Financial calculators
                        </li>
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

                    <!-- Subscribe Button -->
                    <button @click="handleSubmit" :disabled="form.processing"
                        :class="[form.processing ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-white text-purple-800 hover:bg-purple-50', 'inline-block font-semibold px-6 py-3 rounded-full shadow active:scale-95 transition w-full']">
                        {{ form.processing ? 'Processing...' : `🎉 Pay KES ${packages[form.package].price} - Start
                        Subscription` }}
                    </button>

                    <!-- Confirmation message -->
                    <p class="p-2 text-green-300 mt-4" :class="form.processing ? 'block' : 'hidden'">
                        Redirecting to secure payment...
                    </p>

                    <!-- Security notice -->
                    <div class="mt-4 flex items-center justify-center gap-2 text-sm text-white/80">
                        <span>🔒</span>
                        <span>Secure payment powered by Pesapal</span>
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