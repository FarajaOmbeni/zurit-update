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
                </div>

                <!-- Subscription Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-10">
                    <div class="bg-white rounded-lg shadow p-5">
                        <h2 class="text-lg font-semibold mb-4">Subscription Overview</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-500">Total Subscriptions</div>
                                <div class="text-2xl font-bold">{{ props.totalSubscriptions?.toLocaleString?.() ?? props.totalSubscriptions }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Active Subscriptions</div>
                                <div class="text-2xl font-bold">{{ props.activeSubscriptions?.toLocaleString?.() ?? props.activeSubscriptions }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Monthly Revenue (KES)</div>
                                <div class="text-2xl font-bold">KES {{ (props.monthlyRevenue ?? 0).toLocaleString() }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Yearly Revenue (KES)</div>
                                <div class="text-2xl font-bold">KES {{ (props.yearlyRevenue ?? 0).toLocaleString() }}</div>
                            </div>
                            <div class="col-span-2">
                                <div class="text-sm text-gray-500">MRR Equivalent (KES)</div>
                                <div class="text-2xl font-bold">KES {{ (props.mrrEquivalent ?? 0).toLocaleString() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-5">
                        <h2 class="text-lg font-semibold mb-4">Quick Chart</h2>
                        <div class="h-64">
                            <BarChart :chartData="chartData" :chartOptions="chartOptions" />
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
