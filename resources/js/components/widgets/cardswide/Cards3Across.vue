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
                { width: 640, height: 250, at: 640 },
                { width: 308, height: 191, at: 1024 },
                { width: 600, height: 373, at: 1440 },
            ],
        }
    },
}
</script>