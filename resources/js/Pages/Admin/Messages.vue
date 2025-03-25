<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import AdminTable from '@/Components/AdminTable.vue';
import { ref } from 'vue';

const props = defineProps({
    messages: Array,
})
// Sample data received from the controller
const userMessages = ref([]);

userMessages.value = props.messages

const tableHeaders = ref([
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'userMessage', label: 'Message' }
]);

</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <AdminSidebar>
                <h1 class="text-2xl font-bold text-purple-900 mb-4">User Messages</h1>
                <AdminTable :data="userMessages" :headers="tableHeaders" :editable="false" />
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
