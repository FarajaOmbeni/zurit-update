<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" @click="close"></div>
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Purchase</h3>
      <p class="text-sm text-gray-600 mb-4">You are about to buy:</p>
      <ul class="text-sm text-gray-800 mb-4">
        <li><span class="font-medium">Course:</span> {{ courseTitle }}</li>
        <li><span class="font-medium">Price:</span> KES {{ (price || 0).toLocaleString() }}</li>
      </ul>
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">M-Pesa Phone Number</label>
        <input
          :value="phone"
          @input="onInput($event.target.value)"
          type="tel"
          inputmode="tel"
          placeholder="07XXXXXXXX"
          class="w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
        />
        <p class="text-xs text-gray-500 mt-1">Edit if you want to use a different number.</p>
        <p v-if="error" class="text-sm text-red-600 mt-1">{{ error }}</p>
      </div>
      <div class="flex justify-end gap-2">
        <button @click="close" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700">Cancel</button>
        <button @click="confirm" :disabled="isSubmitting" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">Confirm</button>
      </div>
    </div>
  </div>
  </template>

<script setup>
const emit = defineEmits(['update:show', 'update:phone', 'confirm']);

const props = defineProps({
  show: { type: Boolean, default: false },
  courseTitle: { type: String, default: '' },
  price: { type: Number, default: 0 },
  phone: { type: String, default: '' },
  error: { type: String, default: '' },
  isSubmitting: { type: Boolean, default: false },
});

function close() {
  emit('update:show', false);
}

function onInput(val) {
  emit('update:phone', val);
}

function confirm() {
  emit('confirm');
}
</script>

