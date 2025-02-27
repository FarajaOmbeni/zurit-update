<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import NetWorthChart from '@/Components/Shared/NetWorthChart.vue';
import AssetsTable from '@/Components/Shared/AssetsTable.vue';
import LiabilitiesTable from '@/Components/Shared/LiabilitiesTable.vue';

const props = defineProps({
    assets: Array,
    liabilities: Array,
})

</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div>
                    <NetWorthChart :assets="assets" :liabilities="liabilities" />
                </div>
                <div class="grid grd-cols-1 md:grid-cols-2 gap-4 mt-12">
                    <div>
                        <AssetsTable :assets="assets" />
                    </div>
                    <div>
                        <LiabilitiesTable :liabilities="liabilities" />
                    </div>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
