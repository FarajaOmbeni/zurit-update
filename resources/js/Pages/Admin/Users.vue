<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import AdminTable from '@/Components/AdminTable.vue';
import { ref, defineProps } from 'vue';

const props = defineProps({
    users: Array,
})

// Sample data received from the controller
const usersData = ref([]);

usersData.value = props.users

const tableHeaders = ref([
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'phone_number', label: 'Phone Number' }
]);
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <AdminSidebar>
                <h1 class="text-2xl font-bold text-purple-900 mb-4">User Management</h1>
                <AdminTable :data="usersData" :headers="tableHeaders" :editable="false"/>
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
