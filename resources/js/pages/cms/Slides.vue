<template>
            <div class="page-wrap">
                <div class="page-left">
                    <h1 class="crm-header">Slides</h1>
                    <table v-if="!showModal.edit && !showModal.new" class="page-list slide-list" role="table" aria-label="Slide List">
                        <thead>
                            <tr class="table-head">
                                <th scope="col">Actions</th>
                                <th scope="col">Image</th>
                                <th scope="col">Title</th>
                                <!-- <th scope="col">Description</th> -->
                                <th scope="col">Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="slide in slides" :key="slide.id" class="page-item">
                                <td class="options-section" aria-label="Slide actions">
                                    <button class="option" @click="editSlide(slide)" title="Edit slide" aria-label="Edit slide titled {{ slide.title }}">
                                        <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                                    </button>
                                    <button v-if="$page.props.auth.user" @click="deleteSlide(slide)" class="option" title="Delete slide" aria-label="Delete slide titled {{ slide.title }}">
                                        <font-awesome-icon :icon="['fas', 'trash-can']" />
                                    </button>
                                </td>
                                <td>
                                    <img v-if="slide.image_id" :src="slide.image_path" :alt="slide.image_alt || 'Slide image'" class="slide-item-img" />
                                </td>
                                <td>{{ slide.title }}</td>
                                <!-- <td>{{ slide.description }}</td> -->
                                <td><a :href="slide.link" target="_blank" rel="noopener" class="page-slug" :aria-label="`Visit link: ${slide.link}`">{{ slide.link }}</a></td>
                            </tr>
                        </tbody>
                    </table>

                    <EditSlide v-if="showModal.edit" :pages="pages" :slide="currentSlide" :images="images" @cancelEdit="cancelEdit()"/>
                    <Newslide v-if="showModal.new" :pages="pages" :images="images" @refreshImages="getImages" @cancelNew="newSlide()"/>
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
    props: {
        pages : Array,
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
            if (confirm("Are you sure you want to delete this page? All child pages will also be deleted.")) {
                axios.delete(`/cms/slides/delete/${slide.id}`)
                .then(response => {
                    this.$inertia.visit('/cms/slides', { method: 'get' });
                })
                .catch(error => {
                    console.log('error');
                })
            }
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

.slide-list {
    width: 100%;
    .table-head {
        padding-bottom: 10px;
        th {
            text-align: left;
        }
    }
}

.slide-item-img {
    width: 60px;
    aspect-ratio: 1/1;
    object-fit: cover;
}
</style>