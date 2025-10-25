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

                <!-- Total Users Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Total Users</h3>
                            <p class="text-3xl font-bold text-purple-600">{{ usersData.length }}</p>
                            <p class="text-sm text-gray-500">Registered users in the system</p>
                        </div>
                    </div>
                </div>

                <AdminTable :data="usersData" :headers="tableHeaders" :editable="false" />
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
