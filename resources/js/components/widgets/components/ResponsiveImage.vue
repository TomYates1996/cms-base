<template>
    <picture class="responsive-image">
      <source
        v-for="(source, index) in sources"
        :key="index"
        :media="source.media"
        :srcset="source.src"
      />
      <img
        :src="fallbackSrc"
        :alt="slide.image.image_alt"
        class="w-full h-auto object-cover"
      />
    </picture>
  </template>
  
  <script>
  export default {
    props: {
      slide: Object,
      aspectRatios: Array,
    },
    data() {
      return {
        sources: [],
        fallbackSrc: '',
      };
    },
    created() {
      const urlPattern = /^\/?(slides\/)([^\/]+\.(jpg|jpeg|png|webp|gif))$/i;
      const match = this.slide.image.image_path.match(urlPattern);
  
      if (!match) {
        console.error('Invalid image URL format:', this.slide.image.image_path);
        return;
      }
  
      const folder = match[1];
      const fullFilename = match[2];
  
      const sortedRatios = [...this.aspectRatios].sort((a, b) => a.at - b.at);
  
      this.sources = sortedRatios.map(ratio => ({
        media: `(max-width: ${ratio.at}px)`,
        src: `/resize/${folder}${fullFilename}?w=${ratio.width}&h=${ratio.height}`,
      }));
  
      const fallback = sortedRatios[sortedRatios.length - 1];
      this.fallbackSrc = `/resize/${folder}${fullFilename}?w=${fallback.width}&h=${fallback.height}`;
    },
    };
</script>
  
<style>
</style>