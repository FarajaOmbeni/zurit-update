<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import AdminSidebar from '@/Components/AdminSidebar.vue';
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <AdminSidebar>
                <div class="">
                    Users
                </div>
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
