<template>
  <div>
    <!-- Label -->
    <p class="mb-1 font-medium text-purple-900">{{ label }}</p>
    <!-- Container for the Quill editor -->
    <div ref="editorContainer" class="quill-editor border border-gray-300"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: 'Editor'
  }
});
const emit = defineEmits(['update:modelValue']);

const editorContainer = ref(null);
let quillInstance = null;

const updateEditorHeight = () => {
  const qlEditor = editorContainer.value.querySelector('.ql-editor');
  if (qlEditor) {
    qlEditor.style.height = 'auto';
    qlEditor.style.height = `${qlEditor.scrollHeight}px`;
  }
};

onMounted(() => {
  // Initialize Quill on the container element with alignment controls
  quillInstance = new Quill(editorContainer.value, {
    theme: 'snow',
    modules: {
      toolbar: [
        [{ header: [1, 2, false] }],
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        // Alignment rich controls: displays left, center, right, and justify options
        [{ align: [] }],
        ['link', 'image'],
        ['clean']
      ]
    }
  });

  // Set initial content if provided
  if (props.modelValue) {
    quillInstance.clipboard.dangerouslyPasteHTML(props.modelValue);
  }
  
  // Adjust initial height of the editor
  updateEditorHeight();

  // Listen to text changes to update model and adjust editor height
  quillInstance.on('text-change', () => {
    const qlEditor = editorContainer.value.querySelector('.ql-editor');
    const htmlContent = qlEditor.innerHTML;
    emit('update:modelValue', htmlContent);
    updateEditorHeight();
  });
});

// Watch for external model changes (v-model)
watch(
  () => props.modelValue,
  (newValue) => {
    const qlEditor = editorContainer.value.querySelector('.ql-editor');
    if (quillInstance && newValue !== qlEditor.innerHTML) {
      quillInstance.clipboard.dangerouslyPasteHTML(newValue);
      updateEditorHeight();
    }
  }
);
</script>

<style scoped>
/* Make sure the editor starts as one line and auto-expands */
.quill-editor .ql-editor {
  padding: 0.5rem;
  overflow-y: hidden !important;
  min-height: 1.5em;
  height: auto !important;
}
</style>
