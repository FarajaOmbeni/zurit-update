<template>
  <div class="py-4">
    <label v-if="label" :for="id" class="block mb-1 font-medium text-purple-900">
      {{ label }}
    </label>

    <input :id="id" v-model="internalValue" :type="type" :placeholder="placeholder" :class="[
      'w-full px-3 py-2 border-1 focus:outline-none focus:ring-1 rounded',
      error ? 'border-red-500 focus:ring-red-500' : 'border-purple-900 focus:ring-purple-500'
    ]" />

    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
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
  error: {
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
