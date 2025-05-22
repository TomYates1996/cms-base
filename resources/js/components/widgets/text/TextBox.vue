<template>
    <div v-html="widget.content" class="prose text-box core-widget"></div>
</template>

<script>
export default {
    components: {
    },
    props: {
        widget: Object,
    },
    data() {
        return {
        }
    }   
}
</script>

<style>
    .text-box {
        width: 100%;
        padding: 0px 20px;
    }
</style>
