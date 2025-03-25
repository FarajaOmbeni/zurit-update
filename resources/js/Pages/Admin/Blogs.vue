<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import AdminTable from '@/Components/AdminTable.vue';
import { ref } from 'vue';

const blogData = ref([])

const tableHeaders = ref([
    { key: 'blog_title', label: 'Name' },
    { key: 'blog_image', label: 'Image' },
    { key: 'blog_message', label: 'Content' }
])

const props = defineProps({
    blogs: Array
})

blogData.value = props.blogs

</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <AdminSidebar>
                <h1 class="text-2xl font-bold text-purple-900 mb-4">Blogs Management</h1>
                <AdminTable 
                    :data="blogData" :headers="tableHeaders" :editable="true" 
                />
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
