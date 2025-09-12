<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    current: { type: Number, required: true }
});

const steps = [
    { num: 1, name: 'Assets', route: 'legacy.assets' },
    { num: 2, name: 'Beneficiaries', route: 'legacy.beneficiaries' },
    { num: 3, name: 'Fiduciaries', route: 'legacy.fiduciaries' },
    { num: 4, name: 'Insurance', route: 'legacy.insurance' },
    { num: 5, name: 'Review', route: 'legacy.review' },
];

function isCurrent(step) {
    return props.current === step.num;
}
</script>

<template>
    <div class="mb-8">
        <!-- Desktop / Tablet: horizontal with separators -->
        <div class="hidden md:flex items-center space-x-4">
            <template v-for="(step, idx) in steps" :key="step.num">
                <Link :href="route(step.route)" class="flex items-center space-x-2 cursor-pointer"
                    :class="!isCurrent(step) ? 'hover:opacity-75 transition-opacity' : ''">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                    :class="isCurrent(step) ? 'bg-purple-500 text-white' : 'bg-gray-300 text-gray-500 hover:bg-purple-400 hover:text-white transition-colors'">
                    {{ step.num }}
                </div>
                <span
                    :class="isCurrent(step) ? 'text-purple-600 font-medium' : 'text-gray-500 hover:text-purple-600 transition-colors'">
                    {{ step.name }}
                </span>
                </Link>
                <div v-if="idx < steps.length - 1" class="w-12 h-px bg-gray-300"></div>
            </template>
        </div>

        <!-- Mobile: grid rows -->
        <div class="grid grid-cols-2 gap-3 md:hidden">
            <Link v-for="step in steps" :key="step.num" :href="route(step.route)"
                class="flex items-center space-x-2 p-2 rounded-lg"
                :class="isCurrent(step) ? 'bg-purple-50' : 'bg-gray-50 hover:bg-gray-100 transition-colors'">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                :class="isCurrent(step) ? 'bg-purple-500 text-white' : 'bg-gray-300 text-gray-600'">
                {{ step.num }}
            </div>
            <span :class="isCurrent(step) ? 'text-purple-700 font-medium' : 'text-gray-700'">
                {{ step.name }}
            </span>
            </Link>
        </div>
    </div>

</template>
