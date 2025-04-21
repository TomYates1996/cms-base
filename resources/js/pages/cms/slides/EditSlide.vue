<template>
    <form class="edit-page-info form" @submit.prevent="updateSlide()" aria-label="Edit Slide">
    <fieldset class="form-inner">
        <legend class="form-title">Edit Slide</legend>

        <div class="form-slide-title form-field">
        <label for="title">Title</label>
        <input id="title" name="title" type="text" required v-model="form.title" autofocus aria-required="true" />
        </div>

        <div class="form-slide-description form-field">
        <label for="description">Description</label>
        <input id="description" name="description" type="text" required v-model="form.description" aria-required="true" />
        </div>

        <div class="form-slide-link form-field">
        <label for="slide-link">Link</label>
        <select id="slide-link" name="link" v-model="form.link" aria-required="true">
            <option v-for="page in pages" :key="page.id" :value="page.slug">{{ page.slug }}</option>
        </select>
        </div>

        <div class="form-slide-image form-field">
        <img class="current-image" :src="slideImage" alt="Current slide image" />
        <button type="button" class="btn-default add-img" @click.prevent="imageList()" aria-expanded="showImageGrid.toString()" aria-controls="imageGrid">
            Select Image
        </button>
        </div>

        <div v-if="showImageGrid" id="imageGrid" class="image-grid" aria-label="Image selection grid">
        <img
            v-for="image in images"
            :key="image.id"
            class="new-slide-img-option"
            :src="'/' + image.image_path"
            alt="Slide image option"
            role="button"
            tabindex="0"
            @click.prevent="addImageToSlide(image)"
        />
        <NewImage @refreshImages="getImages()" />
        </div>

        <button type="submit" class="btn-default mt-2 w-full" tabindex="5" :disabled="form.processing" :aria-busy="form.processing">
        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
        Save Changes
        </button>

        <Link v-if="$page.props.auth.user" href="/cms/slides" method="get" class="btn-default" aria-label="Cancel and return to slide list">
        Cancel Edit
        </Link>
    </fieldset>
    </form>
</template>
   
<script>
import NewImage from '@/components/cms/slides/images/NewImage.vue';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import axios from 'axios';
import { Head, Link } from '@inertiajs/vue3';


   
export default {
    setup(){
        const form = useForm({
            'title' : '',
            'description' : '',
            'link' : '',
            'image_id' : null,
            'id' : undefined,
        });
   
        return { form } 
    },
    components: {
        LoaderCircle,
        NewImage,
        Link,
    },
    props: {
        slide: Object,
    },
    data() {
        return {
            showImageGrid: false,
            images: [],
            slideImage: null,
            pages: [],
        }
    },
    created() {
        this.form.title = this.slide.title;
        this.form.description = this.slide.description;
        this.form.image_id = this.slide.image_id;
        this.form.link = this.slide.link;
        this.form.id = this.slide.id;
        this.getImages();
        this.imagePath();
        this.getPages()
    },
    methods: { 
        imagePath() {
            if (this.slide.image_id) {
               this.slideImage = '/' + this.images.find(image => image.id === this.slide.image_id).image_path;
               return;
            } else {
                return;
            }
        },  
        getImages() {
            axios.get('/api/images/all')
            .then((response) => {
                this.images = response.data.images;    
            })
        },
        getPages() {
            axios.get('/api/pages/all')
            .then((response) => {
                this.pages = response.data.pages;    
            })
        },
        setLink(slug) {
            this.form.link = slug;
        },
        updateSlide () {
            this.form.put(route('api.slides.update'), {
            onSuccess: () => {
                this.$inertia.visit('/cms/slides');  
            },
            onError: (errors) => {
                console.log('Form submission error:', errors); 
            },
        });
        },
        imageList() {
            this.showImageGrid = true;
        },
        addImageToSlide(image) {
            this.form.image_id = image.id;
            this.showImageGrid = false;
            this.slideImage = '/' + image.image_path;
            this.form.image_id = image.id;
        },
    },
   
}
</script>
   
<style scoped>
    .image-grid {
        position: fixed;
        top: 0px;
        left: 0px;
        height: 100%;
        width: 100%;
        background-color: var(--white);
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        padding: 20px;
        overflow: scroll;
    }
    .new-slide-img-option {
        width: 100%;
        object-fit: cover;
        aspect-ratio: 1/1;
    }
</style>