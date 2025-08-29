<template>
    <div>
        <ul class="list-disc list-inside py-1">
            <li class="text-gold-300">{{ name }}: <span class="text-white">{{ details }}</span></li>
        </ul>
    </div>
</template>

<script setup>
defineProps({
    name: String,
    details: String
})
</script>