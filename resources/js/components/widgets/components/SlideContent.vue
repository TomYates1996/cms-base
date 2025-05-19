<template>
    <div class="item" v-for="(slide, index) in widget.slides" :key="index">
        <div class="image-section">
            <ResponsiveImage :slide="slide" :aspectRatios="aspectRatios"/>
        </div>
        <TextContent :slide="slide" :widget="widget"/>
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
        widget: Object,
        aspectRatios: Array,
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