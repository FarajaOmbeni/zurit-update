<!-- TrainingTab.vue -->
<template>
    <div class="w-full flex justify-center">
        <button class="px-6 w-36 h-14 rounded py-2 text-sm font-bold transition-colors duration-200" :class="[
        isActive
            ? 'bg-amber-400 text-gray-900'
            : 'bg-gray-900 text-white hover:bg-gray-800'
    ]" @click="$emit('click')">
            {{ label }}
        </button>
    </div>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        required: true
    },
    isActive: {
        type: Boolean,
        default: false
    }
})

defineEmits(['click'])
</script>