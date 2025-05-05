<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import AdminTable from '@/Components/AdminTable.vue';
import Input from '@/Components/Shared/Input.vue';
import Button from '@/Components/Shared/Button.vue';
import Select from '@/Components/Shared/Select.vue';
import FileInput from '@/Components/Shared/FileInput.vue';
import Editor from '@/Components/Shared/Editor.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';

const { openAlert, clearAlert, alertState } = useAlert()

const emit = defineEmits(['edit', 'delete'])

const isEditing = ref(false)
const editingBlogId = ref(null)

const editForm = useForm({
    blog_title: '',
    blog_image: '',
    blog_tag: '',
    content: ''
})

function handleEdit(blog) {
    isEditing.value = true
    editingBlogId.value = blog.id
    editForm.blog_title = blog.blog_title
    editForm.blog_tag = blog.blog_tag
    editForm.content = blog.blog_message
    editForm.blog_image = null
}

function updateBlog() {
    editForm.post(
        route('blogs.update', editingBlogId.value),   // PUT/PATCH route
        {
            onSuccess: () => {
                // refresh the local table data without a hard reload
                const idx = blogData.value.findIndex(e => e.id === editingBlogId.value)
                if (idx > -1) blogData.value[idx] = { ...editForm }  // optimistic UI
                isEditing.value = false
                editForm.reset()
                openAlert('success', 'Blog updated successfully!', 5000)
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

function handleDelete(blog) {
    if (!confirm(`Delete "${blog.blog_title}"? This can't be undone.`)) return

    useForm({}).delete(route('blogs.destroy', blog.id), {
        onSuccess: () => {
            blogsData.value = blogsData.value.filter(e => e.id !== blog.id)
            openAlert('success', 'Blog deleted.', 4000)
        },
        onError: () => openAlert('danger', 'Could not delete the blog.', 4000)
    })
}

import { ref } from 'vue';

const blogData = ref([])

const tableHeaders = ref([
    { key: 'blog_title', label: 'Name' },
    { key: 'blog_image', label: 'Image' },
    { key: 'blog_message', label: 'Content' }
])

const categories = [
    "Personal Finance & Budgeting",
    "Investing & Wealth Management",
    "Business & Entrepreneurship",
    "Tax & Compliance",
    "Financial Technology & Trends"
];


const props = defineProps({
    blogs: Array
})

const form = useForm({
    blog_title: '',
    blog_image: '',
    blog_tag: '',
    content: ''
})

const handleFile = (file) => {
    form.blog_image = file;
};

blogData.value = props.blogs

const handleSubmit = () => {
    form.post(route('blogs.store'), {
        onSuccess: () => {
            form.reset()
            openAlert('success', "Blog added succesfully!", 5000)
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
                <h1 class="text-2xl font-bold text-purple-900 mb-8">Blogs Management</h1>
                <div v-if="!isEditing" class="mb-8">
                    <h1 class="text-xl font-bold text-purple-900">Add New Blog</h1>
                    <form @submit.prevent="handleSubmit">
                        <div>
                            <Input v-model="form.blog_title" label="Title" placeholder="Enter blog title" id="title" />
                            <Select v-model="form.blog_tag" id="category" :options="categories"
                                select_title="Select category" label="Category" />
                            <FileInput label="Blog Image" v-model="form.blog_image" accept="image/*"
                                @file-selected="handleFile" />
                            <Editor label="Content" v-model="form.content" />
                            <Button type="submit">{{ form.processing ? 'Saving...' : 'Submit' }}</Button>
                        </div>
                    </form>
                </div>

                <div v-else class="mb-8">
                    <h1 class="text-xl font-bold text-purple-900">Edit Blog</h1>
                    <form @submit.prevent="updateBlog">
                        <div>
                            <Input v-model="editForm.blog_title" label="Title" placeholder="Enter blog title"
                                id="title" />
                            <Select v-model="editForm.blog_tag" id="category" :options="categories"
                                select_title="Select category" label="Category" />
                            <FileInput label="Blog Image" v-model="editForm.blog_image" accept="image/*"
                                @file-selected="file => editForm.blog_image = file" />
                            <Editor label="Content" v-model="editForm.content" />
                            <div class="flex gap-4">
                                <Button type="submit">{{ editForm.processing ? 'Updating...' : 'Save' }}</Button>
                                <Button type="button" @click="cancelEdit">Cancel</Button>
                            </div>
                        </div>
                    </form>
                </div>

                <div>
                    <h1 class="text-xl font-bold text-purple-900">All Blogs</h1>
                    <AdminTable :data="blogData" :headers="tableHeaders" :editable="true" @edit="handleEdit"
                        @delete="handleDelete" />
                </div>
            </AdminSidebar>
        </div>
    </AuthenticatedLayout>
</template>
