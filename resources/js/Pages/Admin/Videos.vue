<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import Input from '@/Components/Shared/Input.vue';
import Button from '@/Components/Shared/Button.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';

const { openAlert, clearAlert, alertState } = useAlert()

const form = useForm({
    video_link: ''
})

const handleSubmit = () => {
    form.post(route('videos.store'), {
        onSuccess: () => {
            form.reset()
            openAlert('success', "Video added succesfully!", 5000)
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');

            openAlert('danger', errorMessages, 5000);
        }
    })
}

</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <AdminSidebar>
                <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                    :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />
                <h1 class="text-2xl font-bold text-purple-900 mb-4">Change Homepage Video</h1>
                <form @submit.prevent="handleSubmit">
                    <Input label="Video Link" placeholder="Enter Video Link" v-model="form.video_link" />
                    <Button type="submit">{{ form.isProcessing ? 'Saving...': 'Submit' }}</Button>
                </form>
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
