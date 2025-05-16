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
                { width: 640, height: 400, at: 640 },
                { width: 466, height: 300, at: 1024 },
                { width: 768, height: 400, at: 1440 },
            ],
        }
    },
}
</script>