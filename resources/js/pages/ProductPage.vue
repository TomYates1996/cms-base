<template>
    <p>{{ data }}</p>
    <p>{{ data.title }}</p>
    <p>{{ data.description }}</p>
    <!-- <img v-for="(image, index) in data.media_gallery" :key="index" :src="'/' + image" alt=""> -->
     <MediaGallery :images="data.media_gallery"/>
</template>

<script>
import MediaGallery from '@/components/widgets/productPages/MediaGallery.vue';

export default {
    props: {
        data: Object,
    },
    components: {
        MediaGallery,
    },
}
</script>

<style>

</style>