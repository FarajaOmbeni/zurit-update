<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import AdminTable from '@/Components/AdminTable.vue';
import { ref } from 'vue';

const props = defineProps({
    events: Array
})

const eventsData = ref([])
eventsData.value = props.events

const tableHeaders = ref([
    { key: 'name', label: 'Name' },
    { key: 'date', label: 'Date' },
    { key: 'price', label: 'Price' },
    { key: 'registration_link', label: 'Registration Link' },
])

</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <AdminSidebar>
                <h1 class="text-2xl font-bold text-purple-900 mb-4">Add Events to Homepage</h1>
                <AdminTable 
                    :data="eventsData"
                    :headers="tableHeaders"
                    :editable="true"
                />
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
