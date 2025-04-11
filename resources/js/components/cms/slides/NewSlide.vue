<template>
    <button class="new-slide-toggle" @click="newSlide()">New Slide</button>
   <form v-if="showNewSlide" class="new-slide" @submit.prevent="createSlide()">
        <div class="slide-title">
            <label for="title">Title</label>
            <input id="title" autofocus type="text" required v-model="form.title" >
        </div>
        <div class="slide-description">
            <label for="description">Description</label>
            <input id="description" required type="text" v-model="form.description" >
        </div>
        <div class="slide-link">
            <label for="link">Link</label>
            <input id="link" required type="text" v-model="form.link" >
        </div>
        <button @click="imageList()" class="add-img">Select Image</button>
        <div v-if="showImageGrid" class="image-grid">
            <img class="new-slide-img-option" @click="addImageToSlide(image)" v-for="image in images" :key="image.id" :src="'/' + image.image_path" alt="">
            <NewImage @refreshImages="refreshImages()"/>
        </div>
        <button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            Create Slide
        </button>
        <button @click="closeNewSlide()" class="cancel-new-slide">Cancel</button>
    </form>
  </template>
  
  <script>
  import { useForm } from '@inertiajs/vue3';
  import { LoaderCircle } from 'lucide-vue-next';
import NewImage from './images/NewImage.vue';
  
  export default {
    setup(){
        const form = useForm({
            'title' : '',
            'description' : '',
            'link' : '',
            'image_id': null,
        });
  
        return { form } 
    },
    components: {
        LoaderCircle,
        NewImage,
    },
    props: {
        images: Array,
    },
    data() {
        return {
            imagePreview: null, 
            showImageGrid: false,
            showNewSlide: false,
        };
    },
    emits: ['refreshImages'],
    methods: {
        refreshImages() {
            this.$emit('refreshImages');
        },
        newSlide() {
            this.showNewSlide = true
        },
        closeNewSlide() {
            this.showNewSlide = false;
            this.form.reset();
        },
        imageList() {
            this.showImageGrid = true;
        },
        addImageToSlide(image) {
            this.form.image_id = image.id;
            this.showImageGrid = false;
        },
        uploadImage(event) {
            this.form.image = event.target.files[0];
            this.updatePreview(this.form.image);
            
        },
        createSlide () {
            this.form.post(route('api.slides.store'), {
            onSuccess: () => {
                this.$inertia.visit('/cms/slides');  
            },
            onError: (errors) => {
                console.log('Form submission error:', errors); 
            },
        });
        },
        updatePreview(imageFile) {
            const file = imageFile; 
            if (file) {
                const reader = new FileReader();
                reader.onloadend = () => {
                    this.imagePreview = reader.result;
                };
                reader.readAsDataURL(file);
            }
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
    .new-slide {
        position: fixed;
        top: 0px;
        left: 0px;
        height: 100%;
        width: 100%;
        background-color: var(--white);
    }
</style>