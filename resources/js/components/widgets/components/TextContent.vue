<template>
    <div class="text-section">
        <h3 v-if="slide.link" class="slide-title">
            <a :href="slide.link">{{ slide.title }}</a>
        </h3>
        <h3 v-if="!slide.link" class="slide-title">
            {{ slide.title }}
        </h3>
        <p class="slide-desc">{{ slide.description }}</p>
    </div>
</template>

<script>
export default {
    props: {
        slide: Object,
    },
}
</script>

<style>
    .text-section {
        padding: 10px 0px;
        display: flex;
        gap: 10px;
        flex-direction: column;
        align-content: flex-start;
        justify-content: flex-start;
        .slide-title {
            font: var(--slide-title-default);
        }
        .slide-desc {
            font: var(--slide-desc-default);
        }
    }
</style>