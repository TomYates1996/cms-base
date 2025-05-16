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
                { width: 640, height: 300, at: 640 },
                { width: 1024, height: 460, at: 1024 },
                { width: 2000, height: 800, at: 1440 },
            ],
        }
    },
}
</script>