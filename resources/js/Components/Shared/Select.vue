<template>
    <div class="py-4">
        <label v-if="label" :for="id" class="block mb-1 font-medium text-purple-900">
            {{ label }}
        </label>

        <select :id="id" v-model="internalValue" :type="type" :placeholder="placeholder"
            class="w-full px-3 py-2 border-1 border-purple-900 focus:outline-none focus:ring-1 rounded">
            <option value="" class="hidden">{{ select_title }}</option>
            <option v-for="option in options" :value="option.value">{{ option.label }}</option>
        </select>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    placeholder: {
        type: String,
        default: ''
    },
    id: {
        type: String,
        default: ''
    },
    options: {
        type: Array,
        default: () => []
    },
    select_title: {
        type: String,
        default: ''
    }
});

const emits = defineEmits(['update:modelValue']);

// Use a computed property to create a two-way binding
const internalValue = computed({
    get: () => props.modelValue,
    set: (value) => emits('update:modelValue', value)
});
</script>
