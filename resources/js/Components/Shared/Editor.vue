<template>
    <div>
        <p class="block mb-1 font-medium text-purple-900">
            {{ label }}
        </p>
        <ckeditor v-if="editor" v-model="internalValue" :editor="editor" :config="config" />
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Ckeditor, useCKEditorCloud } from '@ckeditor/ckeditor5-vue';

// Define the props and emit
const props = defineProps({
    label: String,
    modelValue: {
        type: String,
        default: ''
    }
});
const emit = defineEmits(['update:modelValue']);

// Create an internal ref that starts with the prop value
const internalValue = ref(props.modelValue);

// Watch for changes and emit updates so the parent stays in sync
watch(internalValue, (newVal) => {
    emit('update:modelValue', newVal);
});

// If the prop changes from the parent, update the internal value
watch(
    () => props.modelValue,
    (newVal) => {
        internalValue.value = newVal;
    }
);

const cloud = useCKEditorCloud({
    version: '44.3.0',
    premium: true
});

const editor = computed(() => {
    if (!cloud.data.value) {
        return null;
    }
    return cloud.data.value.CKEditor.ClassicEditor;
});

const licenseKey = import.meta.env.VITE_CK_EDITOR_LICENSE_KEY;
console.log("License: ", licenseKey)

const config = computed(() => {
    if (!cloud.data.value) {
        return null;
    }

    const {
        Essentials,
        Paragraph,
        Heading,
        Bold,
        Italic,
        Link,
        List,
        Alignment,
        // Image,
        // ImageToolbar,
        // ImageCaption,
        // ImageStyle,
        // ImageResize,
        BlockQuote
    } = cloud.data.value.CKEditor;

    // const { FormatPainter } = cloud.data.value.CKEditorPremiumFeatures;

    return {
        licenseKey,
        plugins: [
            Essentials,
            Paragraph,
            Heading,
            Bold,
            Italic,
            Link,
            List,
            Alignment,
            // Image,
            // ImageToolbar,
            // ImageCaption,
            // ImageStyle,
            // ImageResize,
            BlockQuote,
            // FormatPainter
        ],
        toolbar: [
            'heading',
            '|',
            'bold',
            'italic',
            'link',
            'bulletedList',
            'numberedList',
            'alignment',
            '|',
            'insertImage',
            'blockQuote',
            '|',
            'undo',
            'redo',
            '|',
            // 'formatPainter'
        ],
        // image: {
        //     toolbar: [
        //         'imageStyle:inline',
        //         'imageStyle:block',
        //         'imageStyle:side',
        //         '|',
        //         'imageTextAlternative'
        //     ]
        // },
        alignment: {
            options: ['left', 'center', 'right', 'justify']
        }
    };
});
</script>
