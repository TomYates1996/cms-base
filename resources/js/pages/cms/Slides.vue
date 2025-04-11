<template>
    <div class="page-wrap">
        <div class="page-left">
            <ul class="slide-list">
                <li class="slide-item" v-for="slide in slides" :key="slide.id">
                    <div class="details">
                        <p class="slide-title">{{ slide.title }}</p>
                        <p class="slide-desc">{{ slide.description }}</p>
                        <p class="slide-link">{{ slide.link }}</p>
                        <img v-if="slide.image_id" :src="slide.image_path" :alt="slide.image_alt">
                    </div>
                    <button class="edit-slide">
                        <font-awesome-icon @click="editSlide(slide)" :icon="['fas', 'pen-to-square']" />
                    </button>
                </li>
            </ul>
        </div>
        <div class="page-right">
            <Newslide :images="images" @refreshImages="getImages"/>
            <NewImage @refreshImages="getImages"/>
        </div>
    </div>
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
    created() {
        this.getImages();
    },
    methods: {
        editSlide(slide) {
            this.$inertia.visit(`/cms/slides/edit/${slide.id}`, {
                method: 'get',
            });
        },
        getImages() {
            axios.get('/api/images/all')
            .then((response) => {
                this.images = response.data.images; 
                this.fetchSlides();   
            })
        },
        // Get the list of slides
        fetchSlides() {
            axios.get('/api/slides')
            .then((response) => {
                this.slides = response.data.slides; 
                this.slides.forEach(slide => {
                    if (slide.image_id) {
                        let image = this.images.find(image => image.id === slide.image_id)
                        slide.image_path = '/' + image.image_path;
                        slide.image_alt = image.image_alt;
                    }
                })
            })
            .catch((error) => {
                console.error('Error fetching slides:', error);
            });
        }
    }
}
</script>
  
<style scoped>


.page-wrap {
    display: flex;
    .page-left {
        width: 80%;
        padding: 20px;
        .slide-list {
            display: flex;
            flex-direction: column;
            .slide-item {
                display: flex;
                width: 100%;
                border-bottom: 1px solid var(--black);
                padding: 6px 0px;
                justify-content: flex-start;
                .details {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    align-items: center;
                    width: 100%;
                }
                img {
                    width: 80px;
                    height: 80px;
                    object-fit: cover;
                }
                .edit-slide {
                    margin-left: auto;
                }
            }
        }
    }
    .page-right {
        width: 20%;
        border-left: 2px solid var(--black);
        padding: 20px;
    }
}
</style>