<template>
<div class="py-4">
<button
:type="type"
@click="onClick"
class="inline-flex items-center px-4 py-2 bg-purple-900 text-white font-semibold 
        rounded hover:bg-purple-800 focus:outline-none focus:ring-2 
        focus:ring-yellow-400 disabled:opacity-50 disabled:cursor-not-allowed"
:disabled="processing"
>
<!-- Use a slot so you can pass any content inside the button -->
<slot />
</button>
</div>
</template>

<script setup>
const props = defineProps({
  type: {
    type: String,
    default: 'button'
  }
});

const emits = defineEmits(['click']);

function onClick(event) {
  emits('click', event);
}
</script>

<style scoped>
/* Optional additional scoped CSS */
</style>
