<template>
    <div class="cards-2-across">
        <div class="widget-header">
            <h1>{{ widget.title }}</h1>
        </div>
        <div class="content">
            <div class="item" v-for="(slide, index) in widget.slides" :key="index">
                <div class="image-section">
                    <img :src="'/' + slide.image.image_path" :alt="slide.image.image_alt">
                </div>
                <div class="text-section">
                    <h3 v-if="slide.link" class="slide-title"><a :href="slide.link">{{ slide.title }}</a></h3>
                    <h3 v-if="!slide.link" class="slide-title">{{ slide.title }}</h3>
                    <p class="desc">{{ slide.description }}</p>
                </div>
            </div>
        </div>
    </div>
  
</template>

<script>
export default {
    props: {
        widget: Object,
    },
    created() {
        console.log(this.widget);
        
    }
}
</script>

<style scoped>
.cards-2-across {
    margin: 0px auto;
    max-width: var(--width-base);
    padding: 20px;
    .content {
        gap: 10px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        .image-section {
            display: flex;
            img {
                aspect-ratio: 1/1;
                object-fit: cover;
            }
        }
    }
}
</style>