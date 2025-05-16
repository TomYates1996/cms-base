<template>
    <div class="widget-header">
        <h3 class="widget-subtitle">{{ subtitle }}</h3>
        <h1 class="widget-title">{{ title }}</h1>
        <p class="widget-description">{{ description }}</p>
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        subtitle: String,
        description: String,
    },

}
</script>

<style>
    .widget-header {
        padding: 20px 20px;
        max-width: var(--width-max);
        margin: 0px auto;
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