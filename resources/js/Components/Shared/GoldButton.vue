<template>
    <Link
        :to="url"
        class="px-6 py-3 border border-gold-400 text-gold-300 font-semibold rounded-lg transition hover:bg-gold-400 hover:text-purple-900">
    {{ button_name }}
    </Link>
</template>
 
<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    button_name: String,
    url: String,
})
</script>