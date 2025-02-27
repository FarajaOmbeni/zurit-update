<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import InvestmentsTable from '@/Components/Shared/InvestmentsTable.vue';
import InvestmentChart from '@/Components/Shared/InvestmentChart.vue';

const props = defineProps({
    investments: Array
})
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <h3 class="mb-6 text-2xl font-bold">Investments Tracker</h3>
                <div class="mb-12">
                    <!-- Investment Chart -->
                    <InvestmentChart :investments="investments" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-purple-700">Your Investments</h1>
                    <InvestmentsTable :investments="investments" />
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
