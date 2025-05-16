<template>
    <div class="item" v-for="(slide, index) in slides" :key="index">
        <div class="image-section">
            <ResponsiveImage :slide="slide"/>
        </div>
        <TextContent :slide="slide"/>
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
        }
    }
</style>