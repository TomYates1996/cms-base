<template>
    <ul class="slide-list">
        <li class="slide-item" v-for="slide in slides" :key="slide.id">
            <p class="slide-title">{{ slide.title }}</p>
            <p class="slide-desc">{{ slide.description }}</p>
            <p class="slide-link">{{ slide.link }}</p>
            <img :src="slide.image" :alt="slide.image_alt">
        </li>
    </ul>
    <Newslide/>
</template>
  
<script>
import Newslide from '@/components/cms/slides/NewSlide.vue';
import axios from 'axios';
  
export default {
    components: {
        Newslide
    },
    data() {
        return {
            slides: []
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