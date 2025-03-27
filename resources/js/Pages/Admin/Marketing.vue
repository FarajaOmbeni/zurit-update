<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import Editor from '@/Components/Shared/Editor.vue';
import Button from '@/Components/Shared/Button.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';

const { openAlert, clearAlert, alertState } = useAlert()

const props = defineProps({
    emails: Array
})

console.log(props.emails)

const form = useForm({
    content: '',
})

const handleSubmit = () => {
    form.post(route('marketing.send'), {
        onSuccess: () => {
            form.reset()
            openAlert('success', "Emails sent succesfully!", 5000)
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
                <h1 class="text-2xl font-bold text-purple-900 mb-4">Send Marketing Emails</h1>
                <form @submit.prevent="handleSubmit">
                    <Editor label="Write the Email" v-model="form.content" />
                    <Button type="submit">{{ form.processing ? 'Sending...' : 'Send' }}</Button>
                </form>
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
