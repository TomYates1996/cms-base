<template>
        <div class="big-image">
        <div class="item" v-if="slides.length > 0">
            <div class="image-section">
            <ResponsiveImage :slide="slides[0]"/>
            </div>
            <TextContent :slide="slides[0]"/>
        </div>
        </div>

        <div class="small-images">
        <div v-for="(slide, index) in slides.slice(1)" :key="index" class="item">
            <div class="image-section">
            <ResponsiveImage :slide="slide"/>
            </div>
            <TextContent :slide="slide"/>
        </div>
        </div>
</template>

<script>
import ResponsiveImage from '../components/ResponsiveImage.vue';
import TextContent from '../components/TextContent.vue';

export default {
    components: {
        TextContent,
        ResponsiveImage,
    },
    props: {
        slides: Array,
    },

}
</script>

<style scoped>
    .image-section {
        display: flex;
        img {
            aspect-ratio: 1/1;
            object-fit: cover;
        }
    }
        .big-image {
        grid-column: 1 / 2; /* First image takes the full space on the left */
        }

        .small-images {
        grid-column: 2 / 3; /* Small images are on the right */
        display: flex;
        flex-direction: column;
        gap: 16px;
        }

        .item {
        margin-bottom: 16px;
        }
</style>