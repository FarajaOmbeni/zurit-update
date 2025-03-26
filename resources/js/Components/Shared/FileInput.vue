<template>
  <div class="flex flex-col mb-2">
    <label class="mb-1 font-medium text-purple-900">{{ label }}</label>

    <div
      class="border-2 border-dashed border-purple-900 rounded-lg p-4 text-center cursor-pointer hover:opacity-80"
      @click="triggerFileInput"
    >
      <p v-if="!selectedFileName" class="text-gray-500">Click to upload a file</p>
      <p v-else class="text-yellow-400 font-medium">{{ selectedFileName }}</p>
      <input
        type="file"
        :accept="accept"
        class="hidden"
        ref="fileInput"
        @change="handleFileChange"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';

const props = defineProps({
  label: { type: String, default: 'Upload File' },
  accept: { type: String, default: '' }, // E.g., "image/*" or "application/pdf"
});

const emit = defineEmits(['file-selected']);

const fileInput = ref(null);
const selectedFileName = ref('');
const previewUrl = ref(null);

const triggerFileInput = () => {
  fileInput.value.click();
};

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    selectedFileName.value = file.name;

    // Emit the file to the parent
    emit('file-selected', file);

    // Preview only if it's an image
    if (file.type.startsWith('image/')) {
      previewUrl.value = URL.createObjectURL(file);
    } else {
      previewUrl.value = null;
    }
  }
};
</script>
