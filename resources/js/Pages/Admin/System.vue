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
})

console.log(props)
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
                <div class="mx-auto max-w-[90%] mt-14">
                    <BarChart :chartData="chartData" :chartOptions="chartOptions" />
                </div>
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
