<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>

    <Head title="Profile" />
    <Sidebar>
        <AuthenticatedLayout>
            <div class="bg-white p-6 rounded-lg">
                <div class="max-w-7xl space-y-6">
                    <div class="p-4 shadow bg-gray-100 sm:rounded-lg sm:p-8">
                        <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status"
                            class="max-w-xl" />
                    </div>

                    <div class="bg-gray-100 p-4 shadow sm:rounded-lg sm:p-8">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>

                    <div class="bg-gray-100 p-4 shadow sm:rounded-lg sm:p-8">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </Sidebar>
</template>
