<template>
    <div class="mb-20">
        <p class="text-center text-4xl md:text-5xl font-extrabold text-purple-700 mb-10">Upcoming Events</p>
        <div class="text-center">
            <a class="bg-purple-500 p-2 text-xl font-bold text-white rounded hover:opacity-90 transiton" href="/calendar" target="_blank">View Calendar</a>
        </div>
        <div class="flex flex-wrap justify-center gap-8 mt-12">
            <div v-for="(event, index) in events" :key="index"
                class="max-w-sm bg-purple-700 rounded-2xl overflow-hidden shadow-lg text-white">
                <img class="w-full h-48 object-cover" :src="`storage/events/${event.image}`" alt="Event Image" />
                <div class="p-6">
                    <h2 class="text-2xl font-bold mb-2">
                        {{ event.title }}
                    </h2>
                    <p class="text-sm font-bold text-center mb-4 text-yellow-500">
                        Date: {{ formatDate(event.date) }}
                    </p>
                    <div class="flex gap-3 items-center justify-between">
                        <div class="text-lg font-semibold">
                            {{ event.price }}
                        </div>
                        <a :href="event.registration_link" target="_blank"
                            class="bg-yellow-500 text-purple-700 font-bold py-2 px-4 rounded hover:bg-yellow-400 cursor-pointer">
                            Register
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { formatDate } from './Composables/useDateFormat';

defineProps({
    events: {
        type: Array,
        required: true
    }
})
</script>