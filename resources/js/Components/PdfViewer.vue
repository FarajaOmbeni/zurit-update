<template>
  <iframe 
    :src="route('course-materials.show', { material: materialId })"
    class="pdf-viewer"
    @load="disableInteractions"
  />
</template>

<script>
export default {
  props: ['materialId'],
  methods: {
    disableInteractions() {
      try {
        const iframe = this.$el;
        iframe.contentDocument.addEventListener('contextmenu', e => e.preventDefault());
        iframe.contentDocument.addEventListener('keydown', e => {
          if (e.ctrlKey && (e.key === 's' || e.key === 'p')) e.preventDefault();
        });
      } catch (e) {
        console.log('Security restrictions in place');
      }
    }
  }
}
</script>

<style>
.pdf-viewer {
  width: 100%;
  height: 80vh;
  border: none;
}
</style>