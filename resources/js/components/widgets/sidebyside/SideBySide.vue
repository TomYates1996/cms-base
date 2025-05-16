<template>
    <WidgetStructure :widget="widget" :aspectRatios="aspectRatios"/>
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
            aspectRatios: [
                { width: 320, height: 220, at: 640 },
                { width: 960, height: 600, at: 1024 },
                { width: 640, height: 400, at: 1440 },
            ],
        }
    },
}
</script>