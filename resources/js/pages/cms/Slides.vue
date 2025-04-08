<template>
    <ul class="slide-list">
        <li class="slide-item" v-for="slide in slides" :key="slide.id">
            <p class="slide-title">{{ slide.title }}</p>
            <p class="slide-desc">{{ slide.description }}</p>
            <p class="slide-link">{{ slide.link }}</p>
            <img :src="slide.image" :alt="slide.image_alt">
        </li>
    </ul>
    <Newslide :images="images"/>
    <NewImage/>
</template>
  
<script>
import NewImage from '@/components/cms/slides/images/NewImage.vue';
import Newslide from '@/components/cms/slides/NewSlide.vue';
import axios from 'axios';
  
export default {
    components: {
        Newslide,
        NewImage,
    },
    data() {
        return {
            slides: [],
            images: [],
        }
    },
    mounted() {
        this.fetchSlides();
    },
    methods: {
          // Get the list of slides
        fetchSlides() {
            axios.get('/api/slides')
            .then((response) => {
                this.slides = response.data.slides; 
            })
            .catch((error) => {
                console.error('Error fetching slides:', error);
            });
            axios.get('/api/images/all')
            .then((response) => {
                this.images = response.data.images; 
            })
            .catch((error) => {
                console.error('Error fetching images:', error);
            });
        }
    }
}
</script>
  
<style scoped>
.slide-list {
    .slide-item {
        display: flex;
        justify-content: space-between;
    }
}
</style>