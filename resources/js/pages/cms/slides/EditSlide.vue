<template>
    <form class="new-slide" @submit.prevent="updateSlide()">
        <div class="slide-title">
            <label for="title">Title</label>
            <input id="title" autofocus type="text" required v-model="form.title" >
        </div>
        <div class="slide-description">
            <label for="description">Description</label>
            <input id="description" required type="text" v-model="form.description" >
        </div>
        <div class="slide-link">
            Link: 
            <select name="link" id="slide-link">
                <option v-for="page in pages" :key="page.id" :value="page.slug" @click="setLink(page.slug)">{{ page.slug }}</option>
            </select>
        </div>
        <img class="current-image" :src="slideImage" alt="">
        <button @click.prevent="imageList()" class="add-img">Select Image</button>
        <div v-if="showImageGrid" class="image-grid">
            <img class="new-slide-img-option" @click.prevent="addImageToSlide(image)" v-for="image in images" :key="image.id" :src="'/' + image.image_path" alt="">
            <NewImage @refreshImages="getImages()"/>
        </div>
        <button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            Save Changes
        </button>
        <Link 
            v-if="$page.props.auth.user"
            href="/cms/slides"
            method="get"
            class="option"
        >
            Cancel Edit
        </Link>
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