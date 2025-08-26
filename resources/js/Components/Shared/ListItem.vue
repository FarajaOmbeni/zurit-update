<template>
    <div class="py-1">
        <span class="text-gold-300">{{ name }}: <span class="text-white">{{ details }}</span></span>
    </div>
</template>

<script setup>
defineProps({
    name: String,
    details: String
})
</script>