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
                { width: 320, height: 320, at: 640 },
                { width: 480, height: 100, at: 1024 },
                { width: 768, height: 400, at: 1440 },
            ],
        }
    },
}
</script>