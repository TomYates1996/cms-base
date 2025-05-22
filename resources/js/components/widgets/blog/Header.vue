<template>
    <div v-html="widget.content" class="blog-header core-widget"></div>
</template>

<script>
export default {
    components: {
    },
    props: {
        widget: Object,
    },
    data() {
        return {
            // Add image aspect ratios 
            aspectRatios: [
                { width: 640, height: 250, at: 640 },
                { width: 466, height: 290, at: 1024 },
                { width: 800, height: 498, at: 1440 },
            ],
        }
    },
}
</script>

<style>
    .blog-header {
        width: 100%;
        padding: 20px 20px 0px;
        h1 {
            font: var(--blog-header-h1);
        }
        p {
            font: var(--blog-header-author);
        }
    }
</style>