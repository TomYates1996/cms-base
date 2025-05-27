<template>
    <div class="related-events-tab tab">
        <h2 class="tab-heading">Upcoming Events</h2>
        <ul class="related-events-list">
            <li v-for="event in events" :key="event.id">
                <div class="image-section">
                    <ResponsiveImage :slide="formatImageToFit(event)" :aspectRatios="aspectRatios"/>
                </div>
                <div class="text-section">
                    <h3 v-if="event.slug" class="slide-title">
                        <a :href="'/event/' + event.slug">{{ event.title }}</a>
                    </h3>
                    <p class="slide-desc">{{ event.short_description }}</p>
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
        events: Array,
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
        formatImageToFit(event) {
            const slide = {
                link : '/event/' + event.slug,
                image: {
                    image_path: '/' + event.thumbnail_image,
                    image_alt: event.title,
                },
                startDate: event.start_datetime,
                endDate: event.end_datetime,
            };
            return slide;
        },
    },
}
</script>

<style>
.related-events-list {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
</style>