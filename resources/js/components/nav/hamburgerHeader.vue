<template>
  <nav role="navigation">
    <a v-for="page in pages" :key="page.id" :href="page.slug" class="page-link">{{ page.title }}</a>
  </nav>
</template>

<script>
export default {
    props: {
        pages: Array,
    },
}
</script>

<style>

</style>