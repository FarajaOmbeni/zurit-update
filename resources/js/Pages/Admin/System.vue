<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import AdminCard from '@/Components/AdminCard.vue';
import BarChart from '@/Components/Shared/BarChart.vue';
import { ref } from 'vue';

const props = defineProps({
    users: Number,
    blogs: Number,
    subscribed: Number,
    totalSubscriptions: Number,
    activeSubscriptions: Number,
    monthlyRevenue: Number,
    yearlyRevenue: Number,
    mrrEquivalent: Number,
    monthlySubscribers: Number,
    yearlySubscribers: Number,
    trialUsers: Number,
    // Mpesa Payment statistics
    totalPayments: Number,
    successfulPayments: Number,
    pendingPayments: Number,
    failedPayments: Number,
    bookRevenue: Number,
    zuriscoreRevenue: Number,
    subscriptionRevenue: Number,
    totalPaymentRevenue: Number,
    bookPaymentCount: Number,
    zuriscorePaymentCount: Number,
    subscriptionPaymentCount: Number,
})

const dataCount = [props.users, props.blogs, props.subscribed]

// Chart data
const chartData = ref({
    labels: ["Users", "Blogs", "Subscribed"],
    datasets: [
        {
            label: "Count",
            backgroundColor: "rgba(75, 192, 192, 0.2)",
            borderColor: "rgba(75, 192, 192, 1)",
            borderWidth: 1,
            data: dataCount
        }
    ]
});

// Chart options
const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false
});
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <AdminSidebar>
                <h1 class="text-2xl font-bold text-purple-900 mb-12">System Insights</h1>
                <div class="flex flex-wrap gap-4 justify-between">
                    <AdminCard title="Users" :number="props.users" />
                    <AdminCard title="Blogs" :number="props.blogs" />
                    <AdminCard title="Subscribed" :number="props.subscribed" />
                    <AdminCard title="Free Trial" :number="props.trialUsers" />
                </div>

                <!-- Package Breakdown -->
                <div class="mt-8">
                    <div class="bg-white rounded-lg shadow p-5">
                        <h2 class="text-lg font-semibold mb-4">Subscription Packages</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">Monthly Package</h3>
                                    <span class="text-sm text-gray-500">KES 500/month</span>
                                </div>
                                <div class="text-3xl font-bold text-blue-600 mb-2">{{ props.monthlySubscribers || 0 }}
                                </div>
                                <div class="text-sm text-gray-500">Active subscribers</div>
                                <div class="mt-2 text-sm text-gray-600">
                                    Revenue: KES {{ (props.monthlyRevenue ?? 0).toLocaleString() }}
                                </div>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">Yearly Package</h3>
                                    <span class="text-sm text-gray-500">KES 4,500/year</span>
                                </div>
                                <div class="text-3xl font-bold text-green-600 mb-2">{{ props.yearlySubscribers || 0 }}
                                </div>
                                <div class="text-sm text-gray-500">Active subscribers</div>
                                <div class="mt-2 text-sm text-gray-600">
                                    Revenue: KES {{ (props.yearlyRevenue ?? 0).toLocaleString() }}
                                </div>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">Free Trial</h3>
                                    <span class="text-sm text-gray-500">3 months free</span>
                                </div>
                                <div class="text-3xl font-bold text-orange-600 mb-2">{{ props.trialUsers || 0 }}
                                </div>
                                <div class="text-sm text-gray-500">Trial users</div>
                                <div class="mt-2 text-sm text-gray-600">
                                    No revenue generated
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mpesa Payments Overview -->
                <div class="mt-8">
                    <div class="bg-white rounded-lg shadow p-5">
                        <h2 class="text-lg font-semibold mb-4">Mpesa Payments Overview</h2>

                        <!-- Payment Status Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="text-sm text-gray-500">Total Payments</div>
                                <div class="text-2xl font-bold text-blue-600">{{ props.totalPayments || 0 }}</div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="text-sm text-gray-500">Successful</div>
                                <div class="text-2xl font-bold text-green-600">{{ props.successfulPayments || 0 }}</div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="text-sm text-gray-500">Pending</div>
                                <div class="text-2xl font-bold text-yellow-600">{{ props.pendingPayments || 0 }}</div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="text-sm text-gray-500">Failed</div>
                                <div class="text-2xl font-bold text-red-600">{{ props.failedPayments || 0 }}</div>
                            </div>
                        </div>

                        <!-- Payment Revenue by Purpose -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">Book Sales</h3>
                                    <span class="text-sm text-gray-500">{{ props.bookPaymentCount || 0 }}
                                        payments</span>
                                </div>
                                <div class="text-3xl font-bold text-purple-600 mb-2">KES {{ (props.bookRevenue ||
                                    0).toLocaleString() }}</div>
                                <div class="text-sm text-gray-500">Revenue from book purchases</div>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">ZuriScore Reports</h3>
                                    <span class="text-sm text-gray-500">{{ props.zuriscorePaymentCount || 0 }}
                                        payments</span>
                                </div>
                                <div class="text-3xl font-bold text-indigo-600 mb-2">KES {{ (props.zuriscoreRevenue ||
                                    0).toLocaleString() }}</div>
                                <div class="text-sm text-gray-500">Revenue from ZuriScore reports</div>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">Subscriptions</h3>
                                    <span class="text-sm text-gray-500">{{ props.subscriptionPaymentCount || 0 }}
                                        payments</span>
                                </div>
                                <div class="text-3xl font-bold text-green-600 mb-2">KES {{ (props.subscriptionRevenue ||
                                    0).toLocaleString() }}</div>
                                <div class="text-sm text-gray-500">Revenue from subscriptions</div>
                            </div>
                        </div>

                        <!-- Total Payment Revenue -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Total Payment Revenue</h3>
                                <div class="text-2xl font-bold text-gray-900">KES {{ (props.totalPaymentRevenue ||
                                    0).toLocaleString() }}</div>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Combined revenue from all Mpesa payments</p>
                        </div>
                    </div>
                </div>

                <div class="mx-auto max-w-[90%] mt-14">
                    <BarChart :chartData="chartData" :chartOptions="chartOptions" />
                </div>
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
