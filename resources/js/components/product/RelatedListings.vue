<template>
    <div class="related-listings-tab tab">
        <h2 class="tab-heading">Related Listings</h2>
        <ul class="related-listings-list">
            <li v-for="listing in listings" :key="listing.id">
                <div class="image-section">
                    <ResponsiveImage :slide="formatImageToFit(listing)" :aspectRatios="aspectRatios"/>
                </div>
                <div class="text-section">
                    <h3 v-if="listing.slug" class="slide-title">
                        <a :href="'/listing/' + listing.slug">{{ listing.title }}</a>
                    </h3>
                    <p class="slide-desc">{{ listing.short_description }}</p>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
import ResponsiveImage from '@/components/widgets/components/ResponsiveImage.vue';

export default {
    components: {
        ResponsiveImage,
    },
    props: {
        listings: Array,
    },
    data() {
        return {
            aspectRatios: [
                { width: 600, height: 320, at: 640 },
                { width: 300, height: 180, at: 1024 },
                { width: 500, height: 310, at: 1440 },
            ],
        }
    },
    methods: {
        formatImageToFit(listing) {
            const slide = {
                link : '/listing/' + listing.slug,
                image: {
                    image_path: '/' + listing.thumbnail_image,
                    image_alt: listing.title,
                },
            };
            return slide;
        },
    },
}
</script>

<style>
.related-listings-list {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
</style>