<template>
    <WidgetStructure :widget="widget" :aspectRatios="aspectRatios" :mosaicRatios="mosaicRatios" :imageHovers="imageHovers"/>
</template>

<script>
import WidgetStructure from '../components/WidgetStructure.vue';


export default {
    components: {
        WidgetStructure,
    },
    props: {
        widget: Object,
    },
    data() {
        return {
            // Add the image ratios here 
            // Smaller images
            aspectRatios: [
                { width: 153, height: 175, at: 640 },
                { width: 244, height: 211, at: 1024 },
                { width: 300, height: 400, at: 1440 },
            ],
            // Feature image ratio
            mosaicRatios: [
                { width: 640, height: 300, at: 640 },
                { width: 983, height: 442, at: 1024 },
                { width: 1800, height: 1200, at: 1440 },
            ],
            // If images will have the standard hover effect
            imageHovers: true,
        }
    },
}
</script>