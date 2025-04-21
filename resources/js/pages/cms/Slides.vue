<template>
            <div class="page-wrap">
                <div class="page-left">
                    <ul class="slide-list" v-if="!showModal.edit && !showModal.new">
                        <li class="slide-item" v-for="slide in slides" :key="slide.id">
                            <div class="details">
                                <p class="slide-title">{{ slide.title }}</p>
                                <p class="slide-desc">{{ slide.description }}</p>
                                <p class="slide-link">{{ slide.link }}</p>
                                <img v-if="slide.image_id" :src="slide.image_path" :alt="slide.image_alt">
                            </div>
                            <div class="tools">
                                <button class="edit-slide">
                                    <font-awesome-icon @click="editSlide(slide)" :icon="['fas', 'pen-to-square']" />
                                </button>
                                <button v-if="$page.props.auth.user" @click="deleteSlide(slide)" class="option">
                                    <font-awesome-icon :icon="['fas', 'trash-can']" />
                                </button>
                            </div>
                        </li>
                    </ul>
                        <EditSlide v-if="showModal.edit" :slide="currentSlide" :images="images" @cancelEdit="cancelEdit()"/>
                        <Newslide v-if="showModal.new" :images="images" @refreshImages="getImages" @cancelNew="newSlide()"/>
                </div>
                <div class="page-right">
                    <button class="btn-default new-slide-toggle" @click="newSlide()" aria-expanded="showNewSlide.toString()" aria-controls="new-slide-form" aria-label="Create a new slide">
                        {{  showModal.new ? 'Cancel' : 'New Slide' }}
                    </button>
                </div>
            </div>

</template>
  
<script>
import NewImage from '@/components/cms/slides/images/NewImage.vue';
import Newslide from '@/components/cms/slides/NewSlide.vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';
import OptionsBar from '@/components/cms/structure/OptionsBar.vue';
import CMSLayout from '@/layouts/CMSLayout.vue';
import EditSlide from '@/components/cms/slides/EditSlide.vue';

  
export default {
    layout: CMSLayout,
    components: {
        Newslide,
        NewImage,
        Link,
        OptionsBar,
        EditSlide,
    },
    data() {
        return {
            slides: [],
            images: [],
            showModal: {
                new: false,
                edit: false,
            },
            currentSlide: {},
        }
    },
    created() {
        this.getImages();
    },
    methods: {
        editSlide(slide) {
            this.currentSlide = slide;
            this.showModal.edit = true;
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
        },
        deleteSlide(slide) {
            axios.delete(`/cms/slides/delete/${slide.id}`)
            .then(response => {
                this.$inertia.visit('/cms/slides', { method: 'get' });
            })
            .catch(error => {
                console.log('error');
            })
        },
        newSlide() {
            this.showModal.new = !this.showModal.new;
            this.showModal.edit = false;
        },
        cancelEdit() {
            this.showModal.edit = false;
        },
    }
}
</script>
  
<style scoped>


.page-wrap {
    display: flex;
    gap: 20px;
    .page-left {
        width: 80%;
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
                    width: 50px;
                    height: 50px;
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
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
}

.page {
    display: flex;
    width: 100%;
    .left-section {
        width: 25%;
    }
    .right-section {
        width: 75%;
    }
}
</style>