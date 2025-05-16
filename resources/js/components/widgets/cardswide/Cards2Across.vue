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
            // Add image aspect ratios 
            aspectRatios: [
                { width: 640, height: 250, at: 640 },
                { width: 466, height: 290, at: 1024 },
                { width: 800, height: 498, at: 1440 },
            ],
        }
    },
}
</script>