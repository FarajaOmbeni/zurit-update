<template>
    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
        <div class="flex-shrink-0 mr-4">
            <div :class="[
                'w-4 h-4 rounded-full',
                statusColor
            ]"></div>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-medium text-gray-900">{{ title }}</h4>
            <p class="text-lg font-semibold text-gray-900 mt-1">{{ value }}</p>
            <p v-if="change !== undefined" :class="[
                'text-xs mt-1',
                change >= 0 ? 'text-green-600' : 'text-red-600'
            ]">
                {{ change >= 0 ? '+' : '' }}{{ change.toFixed(1) }}% change
            </p>
        </div>
        <div class="flex-shrink-0">
            <div :class="[
                'w-8 h-8 rounded-full flex items-center justify-center',
                statusBgColor
            ]">
                <svg v-if="status === 'green'" class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg v-else-if="status === 'amber'" class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <svg v-else class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    status: {
        type: String,
        required: true,
        validator: (value) => ['green', 'amber', 'red'].includes(value),
    },
    value: {
        type: [String, Number],
        required: true,
    },
    change: {
        type: Number,
        default: undefined,
    },
});

const statusColor = computed(() => {
    switch (props.status) {
        case 'green':
            return 'bg-green-500';
        case 'amber':
            return 'bg-yellow-500';
        case 'red':
            return 'bg-red-500';
        default:
            return 'bg-gray-400';
    }
});

const statusBgColor = computed(() => {
    switch (props.status) {
        case 'green':
            return 'bg-green-100';
        case 'amber':
            return 'bg-yellow-100';
        case 'red':
            return 'bg-red-100';
        default:
            return 'bg-gray-100';
    }
});
</script> 