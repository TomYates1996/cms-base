<template>
        <div class="big-image">
        <div class="item" v-if="widget.slides.length > 0">
            <div class="image-section">
            <ResponsiveImage :slide="widget.slides[0]" :aspectRatios="mosaicRatios" :imageHovers="imageHovers"/>
            </div>
            <TextContent :slide="widget.slides[0]"/>
        </div>
        </div>

        <div class="small-images">
        <div v-for="(slide, index) in widget.slides.slice(1)" :key="index" class="item">
            <div class="image-section">
            <ResponsiveImage :slide="slide" :aspectRatios="aspectRatios" :imageHovers="imageHovers"/>
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
        widget: Object,
        aspectRatios: Array,
        mosaicRatios: Array,
        imageHovers: Boolean,
    },

}
</script>

<style scoped>

</style>