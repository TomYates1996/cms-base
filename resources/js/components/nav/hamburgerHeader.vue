<template>
  <nav class="nav" role="navigation">
    <a class="logo" :href="'/' + link" v-if="logo">
      <img :src="'/' + logo.image_path" :alt="logo.image_alt">
    </a>
    <ul class="page-list">
      <li v-for="page in pages" :key="page.id">
        <a :href="page.slug">{{ page.title }}</a>
      </li>
    </ul>
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
    max-width: 160px;
  }
  .nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    .page-list {
      display: flex;
      gap: 10px;
      justify-content: center;
      align-items: center;
    }
  }
</style>