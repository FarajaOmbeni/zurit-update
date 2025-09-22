<template>
    <AdminSidebar title="Add Sub-Course">
        <div class="max-w-3xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">
                        Add Sub-Course
                    </h2>

                    <!-- Global Error Display -->
                    <div
                        v-if="form.errors.error"
                        class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md"
                    >
                        <div class="flex items-center">
                            <div class="text-red-600 mr-2">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div>
                                <p class="text-red-800 font-medium">
                                    Upload Error
                                </p>
                                <p class="text-red-700 text-sm mt-1">
                                    {{ form.errors.error }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit">
                        <!-- Parent Course Selection -->
                        <div class="mb-6">
                            <label
                                for="parent_id"
                                class="block text-sm font-medium text-gray-700"
                                >Select Main Course *</label
                            >
                            <select
                                v-model="form.parent_id"
                                id="parent_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                required
                            >
                                <option value="">Choose a main course</option>
                                <option
                                    v-for="course in mainCourses"
                                    :key="course.id"
                                    :value="course.id"
                                >
                                    {{ course.title }}
                                </option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                Select the main course to which you want to add
                                this sub-course.
                            </p>
                            <p
                                v-if="form.errors.parent_id"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.parent_id }}
                            </p>
                        </div>

                        <div class="mb-6">
                            <label
                                for="title"
                                class="block text-sm font-medium text-gray-700"
                                >Sub-Course Title</label
                            >
                            <input
                                v-model="form.title"
                                type="text"
                                id="title"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                required
                            />
                            <p
                                v-if="form.errors.title"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <div class="mb-6">
                            <label
                                for="description"
                                class="block text-sm font-medium text-gray-700"
                                >Description</label
                            >
                            <textarea
                                v-model="form.description"
                                id="description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                required
                            ></textarea>
                            <p
                                v-if="form.errors.description"
                                class="mt-2 text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Study Materials Section -->
                        <div class="mb-6">
                            <h3 class="text-md font-medium text-gray-700 mb-4">
                                Study Materials
                            </h3>

                            <div
                                v-for="(material, index) in form.materials"
                                :key="index"
                                class="material-item mb-6 p-4 border rounded-lg"
                            >
                                <div class="mb-4">
                                    <label
                                        :for="`material-title-${index}`"
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Material Title</label
                                    >
                                    <input
                                        v-model="material.title"
                                        type="text"
                                        :id="`material-title-${index}`"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                        required
                                    />
                                </div>

                                <!-- PDF Upload Section -->
                                <div class="mb-4">
                                    <label
                                        :for="`material-file-${index}`"
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >PDF Document (Optional)</label
                                    >
                                    <input
                                        type="file"
                                        @change="
                                            handleFileChange($event, index)
                                        "
                                        :id="`material-file-${index}`"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100"
                                        accept=".pdf"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">
                                        Maximum file size: 10MB. Only PDF files
                                        are accepted.
                                    </p>
                                    <p
                                        v-if="material.file"
                                        class="mt-1 text-sm text-green-600"
                                    >
                                        ✓ PDF uploaded: {{ material.file.name }}
                                    </p>
                                </div>

                                <!-- Video Upload Section -->
                                <div class="mb-4">
                                    <VideoUploader
                                        :label="`Video for ${material.title || 'Material ' + (index + 1)} (Optional)`"
                                        v-model="material.video_file"
                                        @video-selected="
                                            handleVideoSelect($event, index)
                                        "
                                    />
                                </div>

                                <!-- Upload Status -->
                                <div
                                    v-if="
                                        !material.file && !material.video_file
                                    "
                                    class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-md"
                                >
                                    <p class="text-amber-800 text-sm">
                                        ⚠️ Please upload at least one file (PDF
                                        or Video) for this material.
                                    </p>
                                </div>
                                <button
                                    v-if="form.materials.length > 1"
                                    type="button"
                                    @click="removeMaterial(index)"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                >
                                    Remove Material
                                </button>
                            </div>

                            <button
                                type="button"
                                @click="addMaterial"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                            >
                                <svg
                                    class="-ml-0.5 mr-1.5 h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                    />
                                </svg>
                                Add Another Material
                            </button>
                        </div>

                        <div
                            class="flex items-center justify-end pt-6 border-t border-gray-200"
                        >
                            <button
                                type="button"
                                @click="
                                    $inertia.visit(route('admin.courses.index'))
                                "
                                class="mr-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">
                                    <svg
                                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        ></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        ></path>
                                    </svg>
                                    Saving...
                                </span>
                                <span v-else>Add Sub-Course</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminSidebar>
</template>

<script setup>
import AdminSidebar from "@/Components/AdminSidebar.vue";
import VideoUploader from "@/Components/VideoUploader.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    mainCourses: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    title: "",
    description: "",
    parent_id: "",
    materials: [
        {
            title: "",
            file: null,
            video_file: null,
        },
    ],
});

const addMaterial = () => {
    form.materials.push({
        title: "",
        file: null,
        video_file: null,
    });
};

const removeMaterial = (index) => {
    form.materials.splice(index, 1);
};

const handleFileChange = (event, index) => {
    const file = event.target.files[0];
    if (file && file.type === "application/pdf") {
        form.materials[index].file = file;
    } else {
        alert("Please upload a valid PDF file.");
        event.target.value = ""; // Reset the file input
    }
};

const handleVideoSelect = (file, index) => {
    form.materials[index].video_file = file;
};

const submit = () => {
    const formData = new FormData();
    formData.append("title", form.title);
    formData.append("description", form.description);
    formData.append("parent_id", form.parent_id);

    // Append each material
    form.materials.forEach((material, index) => {
        formData.append(`materials[${index}][title]`, material.title);
        if (material.file) {
            formData.append(`materials[${index}][file]`, material.file);
        }
    });

    form.post(route("admin.courses.store"), {
        data: formData,
        forceFormData: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<style scoped>
.material-item {
    background-color: #f9fafb;
    border-color: #e5e7eb;
}

.material-item:last-of-type {
    margin-bottom: 0;
}
</style>
