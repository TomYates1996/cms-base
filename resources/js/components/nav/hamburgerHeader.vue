<template>
  <nav class="nav" role="navigation">
    <a class="logo" :href="link" v-if="logo">
      <img :src="'/' + logo.image_path" :alt="logo.image_alt">
    </a>
    <a v-for="page in pages" :key="page.id" :href="page.slug" class="page-link">{{ page.title }}</a>
  </nav>
</template>

<script>
export default {
    props: {
        pages: Array,
        link: String,
        logo: Object,
    },
}
</script>

<style scoped>
  .logo img {
    max-width: 200px;
  }
</style>