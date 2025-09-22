<template>
    <div class="flex flex-col mb-4">
        <label class="mb-2 font-medium text-purple-900">{{ label }}</label>

        <div v-if="!uploadedVideo" class="space-y-4">
            <!-- Upload Area -->
            <div
                class="border-2 border-dashed border-purple-900 rounded-lg p-6 text-center cursor-pointer hover:opacity-80 transition-opacity"
                @click="triggerFileInput"
                @dragover.prevent
                @drop.prevent="handleDrop"
            >
                <div v-if="!selectedFile">
                    <div class="mx-auto mb-2 w-12 h-12 text-purple-900">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                            />
                        </svg>
                    </div>
                    <p class="text-gray-600">
                        Click to upload a video or drag and drop
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        MP4, WebM, OGG, AVI, MOV (max 200MB)
                    </p>
                    <p class="text-xs text-amber-600 mt-1">
                        ⚠️ Large files may take time to upload. If upload fails,
                        try reducing file size.
                    </p>
                </div>

                <div v-else class="text-purple-600">
                    <p class="font-medium">{{ selectedFile.name }}</p>
                    <p class="text-sm">
                        {{ formatFileSize(selectedFile.size) }}
                    </p>
                    <p class="text-sm text-green-600 mt-2">
                        ✓ Video file selected
                    </p>
                </div>
            </div>

            <input
                type="file"
                ref="fileInput"
                accept="video/*,.mp4,.webm,.ogg,.avi,.mov"
                class="hidden"
                @change="handleFileSelect"
            />
        </div>

        <!-- Selected Video Preview -->
        <div v-else class="space-y-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="text-green-600 mr-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M2 6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                />
                                <path
                                    stroke="#fff"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 12l2 2 4-4"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-green-800">
                                Video file selected!
                            </p>
                            <p class="text-sm text-green-600">
                                {{ uploadedVideo.name }}
                            </p>
                        </div>
                    </div>
                    <button
                        @click="removeVideo"
                        class="text-red-600 hover:text-red-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from "vue";

const props = defineProps({
    label: { type: String, default: "Upload Video" },
    modelValue: { type: File, default: null },
});

const emit = defineEmits(["update:modelValue", "video-selected"]);

const fileInput = ref(null);
const selectedFile = ref(null);
const uploadedVideo = ref(null);

// Watch for modelValue changes (when parent component sets a file)
watch(
    () => props.modelValue,
    (newValue) => {
        if (newValue) {
            selectedFile.value = newValue;
            uploadedVideo.value = {
                name: newValue.name,
                size: newValue.size,
            };
        } else {
            selectedFile.value = null;
            uploadedVideo.value = null;
        }
    },
    { immediate: true },
);

const triggerFileInput = () => {
    fileInput.value.click();
};

const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file && isValidVideoFile(file)) {
        selectedFile.value = file;
        uploadedVideo.value = {
            name: file.name,
            size: file.size,
        };

        // Emit the file to parent component
        emit("update:modelValue", file);
        emit("video-selected", file);
    }
};

const handleDrop = (event) => {
    const file = event.dataTransfer.files[0];
    if (file && isValidVideoFile(file)) {
        selectedFile.value = file;
        uploadedVideo.value = {
            name: file.name,
            size: file.size,
        };

        // Emit the file to parent component
        emit("update:modelValue", file);
        emit("video-selected", file);
    }
};

const isValidVideoFile = (file) => {
    const validTypes = [
        "video/mp4",
        "video/webm",
        "video/ogg",
        "video/avi",
        "video/quicktime",
    ];
    const maxSize = 200 * 1024 * 1024; // 200MB

    if (!validTypes.includes(file.type)) {
        alert("Please select a valid video file (MP4, WebM, OGG, AVI, or MOV)");
        return false;
    }

    if (file.size > maxSize) {
        const fileSizeMB = Math.round(file.size / (1024 * 1024));
        alert(
            `File size is ${fileSizeMB}MB, which exceeds the 200MB limit. Please compress your video or choose a smaller file.`,
        );
        return false;
    }

    // Warn about large files that might hit server limits
    if (file.size > 40 * 1024 * 1024) {
        // 40MB
        const fileSizeMB = Math.round(file.size / (1024 * 1024));
        if (
            !confirm(
                `This file is ${fileSizeMB}MB. Large files may fail to upload due to server limits. Continue anyway?`,
            )
        ) {
            return false;
        }
    }

    return true;
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

const removeVideo = () => {
    uploadedVideo.value = null;
    selectedFile.value = null;
    emit("update:modelValue", null);

    // Reset file input
    if (fileInput.value) {
        fileInput.value.value = "";
    }
};
</script>

<style scoped>
/* No custom styles needed */
</style>
