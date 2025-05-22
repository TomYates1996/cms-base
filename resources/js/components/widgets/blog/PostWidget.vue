<template>
    <div v-if="!isAllowed">
        <div class="bg-red-100 text-red-800 p-4 rounded-lg">
            This widget can only be used at <strong>/blog/post</strong>
        </div>
    </div>

    <div v-else class="blog-post-temp">
        Blog post will go here.
    </div>
</template>

<script>
export default {
  props: {
    widget: Object,
  },
  computed: {
    isAllowed() {
      return this.$page.url === '/cms/pages/edit-content/blog/post';
    },
  },
  created() {
    console.log(this.$page.url);
    
  },
};
</script>

<style scoped>
    .blog-post-temp {
        max-width: var(--width-max);
        margin: 10px auto;
        padding: 20px;
        border: 2px solid var(--black);
    }
</style>