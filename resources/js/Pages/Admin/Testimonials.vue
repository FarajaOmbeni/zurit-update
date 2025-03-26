<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import Editor from '@/Components/Shared/Editor.vue';
import AdminTable from '@/Components/AdminTable.vue';
import { ref } from 'vue';
import Input from '@/Components/Shared/Input.vue';
import Button from '@/Components/Shared/Button.vue';
import FileInput from '@/Components/Shared/FileInput.vue';
import { useAlert } from '@/Components/Composables/useAlert';

//ALERT USAGE LOGIC, FROM COMPOSABLE
const { alertState, openAlert, clearAlert } = useAlert();

const testimonials = ref([])

const tableHeaders = ref([
    { key: 'name', label: 'Name' },
    { key: 'content', label: 'Content' },
])

const props = defineProps({
    testimonials: Array
})

testimonials.value = props.testimonials

const form = useForm({
    name: '',
    image: '',
    content: ''
})

const handleFile = (file) => {
    form.image = file;
};

const handleSubmit = () => {
    form.post(route('testimonials.store'), {
        onSuccess: () => {
            window.location.reload()
            openAlert('success', "Testimonial added succesfully!", 5000)
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
                <div class="mb-12 px-4">
                    <h1 class="text-2xl font-bold text-purple-900">Add Testimonials to Homepage</h1>
                    <form @submit.prevent="handleSubmit">
                        <div>
                            <Input v-model="form.name" label="Name" placeholder="Enter the testifier name" id="name" />
                            <FileInput label="Testifier Image" v-model="form.image" accept="image/*"
                                @file-selected="handleFile" />
                            <Editor label="Testimonial" v-model="form.content" />
                            <Button type="submit">{{ form.processing ? 'Saving...' : 'Submit' }}</Button>
                        </div>
                    </form>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-purple-900 pl-4 mb-4">All Testimonials</h1>
                    <AdminTable :data="testimonials" :headers="tableHeaders" :editable="true" />
                </div>
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
