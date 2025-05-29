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
import Alert from '@/Components/Shared/Alert.vue';

//ALERT USAGE LOGIC, FROM COMPOSABLE
const { alertState, openAlert, clearAlert } = useAlert();

const emit = defineEmits(['edit', 'delete'])

const isEditing = ref(false)
const editingTestimonialId = ref(null)

const editForm = useForm({
    name: '',
    image: '',
    content: '',
})

function handleEdit(testimonial) {
    isEditing.value = true
    editingTestimonialId.value = testimonial.id
    editForm.name = testimonial.name
    editForm.content = testimonial.content
    editForm.image = null
}

function updateTestimonial() {
    editForm.post(
        route('testimonials.update', editingTestimonialId.value),   // PUT/PATCH route
        {
            onSuccess: () => {
                // refresh the local table data without a hard reload
                const idx = testimonials.value.findIndex(e => e.id === editingTestimonialId.value)
                if (idx > -1) testimonials.value[idx] = { ...editForm }  // optimistic UI
                isEditing.value = false
                editForm.reset()
                openAlert('success', 'Testimonial updated successfully!', 5000)
            },
            onError: (errors) => {
                openAlert('danger', Object.values(errors).flat().join(' '), 5000)
            }
        }
    )
}

function cancelEdit() {
    isEditing.value = false
    editForm.reset()
}

function handleDelete(testimonial) {
    if (!confirm(`Delete "${testimonial.name}'s" testimony? This can't be undone.`)) return

    useForm({}).delete(route('testimonials.destroy', testimonial.id), {
        onSuccess: () => {
            testimonials.value = testimonials.value.filter(e => e.id !== testimonial.id)
            openAlert('success', 'testimonial deleted.', 4000)
        },
        onError: () => openAlert('danger', 'Could not delete the testimonial.', 4000)
    })
}


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
                <div v-if="!isEditing" class="mb-12 px-4">
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

                <div v-else class="mb-12 px-4">
                    <h1 class="text-2xl font-bold text-purple-900">Edit Testimonials</h1>
                    <form @submit.prevent="updateTestimonial">
                        <div>
                            <Input v-model="editForm.name" label="Name" placeholder="Enter the testifier name"
                                id="name" />
                            <FileInput label="Testifier Image" v-model="editForm.image" accept="image/*"
                                @file-selected="file => editForm.image = file" />
                            <Editor label="Testimonial" v-model="editForm.content" />
                            <div class="flex gap-4">
                                <Button type="submit">{{ editForm.processing ? 'Saving...' : 'Save' }}</Button>
                                <Button type="button" @click="cancelEdit">Cancel</Button>
                            </div>
                        </div>
                    </form>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-purple-900 pl-4 mb-4">All Testimonials</h1>
                    <AdminTable :data="testimonials" :headers="tableHeaders" :editable="true" @edit="handleEdit"
                        @delete="handleDelete" />
                </div>
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
