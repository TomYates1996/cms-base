<template>
    <button class="view-all-btn">
        <a :href="widget.link">{{ widget.link_text }}</a>
    </button>
</template>

<script>
export default {
    props: {
        widget: Object,
    },
}
</script>

<style>

</style>