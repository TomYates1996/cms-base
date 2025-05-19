<template>
    <div class="widget-header">
        <div class="header-text">
            <h3 class="widget-subtitle">{{ widget.subtitle }}</h3>
            <h1 class="widget-title">{{ widget.title }}</h1>
            <p class="widget-description">{{ widget.description }}</p>
        </div>
        <ViewAllButton v-if="widget.link" :widget="widget"/>
    </div>
</template>

<script>
import ViewAllButton from './ViewAllButton.vue';

export default {
    components: {
        ViewAllButton,
    },
    props: {
        widget: Object,
    },

}
</script>

<style>
    .widget-header {
        padding: 20px 20px;
        max-width: var(--width-max);
        margin: 0px auto;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        .widget-title {
            font: var(--widget-heading);
            color: var(--black);
        }
        .widget-subtitle {
            font: var(--widget-subheading);
            color: var(--primary-colour);
        }
        .widget-description {
            font: var(--body-font);
            color: var(--black);
        }
    }
</style>