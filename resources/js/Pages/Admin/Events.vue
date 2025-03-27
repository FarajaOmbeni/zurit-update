<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import AdminTable from '@/Components/AdminTable.vue';
import Input from '@/Components/Shared/Input.vue';
import Button from '@/Components/Shared/Button.vue';
import Select from '@/Components/Shared/Select.vue';
import FileInput from '@/Components/Shared/FileInput.vue';
import { useAlert } from '@/Components/Composables/useAlert';
import Alert from '@/Components/Shared/Alert.vue';
import { ref } from 'vue';

const { openAlert, clearAlert, alertState } = useAlert()

const props = defineProps({
    events: Array
})

const eventsData = ref([])
eventsData.value = props.events

const form = useForm({
    name: '',
    date: '',
    image: '',
    price: '',
    registration_link: ''
})

const tableHeaders = ref([
    { key: 'name', label: 'Name' },
    { key: 'date', label: 'Date' },
    { key: 'price', label: 'Price' },
    { key: 'registration_link', label: 'Registration Link' },
])

const handleFile = (file) => {
    form.image = file;
};

const handleSubmit = () => {
    form.post(route('events.store'), {
        onSuccess: () => {
            form.reset()
            openAlert('success', "Event added succesfully!", 5000)
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
                <h1 class="text-2xl font-bold text-purple-900 mb-8">Events Management</h1>
                <div class="mb-8">
                    <h1 class="text-xl font-bold text-purple-900">Add New Event</h1>
                    <form @submit.prevent="handleSubmit">
                        <div>
                            <Input v-model="form.name" label="Event Name" placeholder="Enter the name of the event"
                                id="name" />
                            <Input type="date" v-model="form.date" label="Date" id="date" />
                            <Select v-model="form.price" select_title="Free or Paid?" label="Free or Paid?"
                                :options="['Free', 'Paid']" />
                            <FileInput label="Event Image" v-model="form.image" accept="image/*"
                                @file-selected="handleFile" />
                            <Input v-model="form.registration_link" label="Registration Link" id="link" />
                            <Button type="submit">{{ form.processing ? 'Saving...' : 'Submit' }}</Button>
                        </div>
                    </form>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-purple-900 mb-4">All Events</h1>
                    <AdminTable :data="eventsData" :headers="tableHeaders" :editable="true" />
                </div>
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
