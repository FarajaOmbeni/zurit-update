<!-- TrainingModal.vue -->
<template>
    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="transform opacity-0"
        enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100"
        leave-to-class="transform opacity-0">
        <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>

            <!-- Modal -->
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-2xl rounded-xl bg-purple-800 text-white shadow-lg">
                    <!-- Close button -->
                    <button @click="$emit('close')" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Content -->
                    <div class="p-6">
                        <h2 class="text-3xl font-bold text-yellow-500 mb-4 uppercase">{{ training?.title }}</h2>
                        <hr>
                        <div class="space-y-4 mt-6 text-lg">
                            <p class="">{{ training?.description }}</p>

                            <!-- Additional training details -->
                            <div class="text-xl font-bold"> We impart our clients with practical knowledge on: </div>
                            <div v-if="training?.details" class="space-y-4">
                                <ul class="list-disc list-inside space-y-2">
                                    <li v-for="(detail, index) in training?.details" :key="index">
                                        {{ detail }}
                                    </li>
                                </ul>
                            </div>

                            <div v-if="training?.duration" class="flex text-lg items-center">
                                <span class="font-medium">Duration:</span>
                                <span class="ml-2">{{ training?.duration }}</span>
                            </div>

                            <div v-if="training?.price" class="flex font-bold items-center text-lg">
                                <span class="font-medium">Price:</span>
                                <span class="ml-2">Kshs {{ training?.price }}</span>
                            </div>
                            <div class="text-xl font-bold">Who should attend?</div>
                            <div v-if="training?.attend" class="flex items-center text-lg">
                                <span class="font-medium">{{ training?.attend }}</span>
                            </div>
                            <div v-if="training?.more_info" class="flex items-center text-lg">
                                <span class="font-medium">{{ training?.more_info }}</span>
                            </div>



                            <!-- Enroll button -->
                            <button @click="$emit('enroll', training?.id)"
                                class="w-full bg-amber-400 text-gray-900 py-3 rounded-lg font-medium hover:bg-amber-500 transition-colors duration-200">
                                Enroll Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
defineProps({
    isOpen: {
        type: Boolean,
        required: true
    },
    training: {
        type: Object,
        default: null
    }
})

defineEmits(['close', 'enroll'])
</script>