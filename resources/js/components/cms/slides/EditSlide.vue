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
  
  export default {
    setup(){
        const form = useForm({
            'title' : '',
            'description' : '',
            'link' : '',
            'image' : null,
            'image_alt' : '',
            'id' : undefined,
        });
  
        return { form } 
    },
    components: {
        LoaderCircle,
    },
    props: {
        slide: Object,
    },
    data() {
        return {
            imagePreview: null, 
        };
    },
    created() {
        this.form.title = this.slide.title;
        this.form.description = this.slide.description;
        this.form.image = this.slide.image_path;
        this.form.image_alt = this.slide.image_alt;
        this.form.link = this.slide.link;
        this.form.id = this.slide.id;
    },
    methods: {
        uploadImage(event) {
            this.form.image = event.target.files[0];
            this.updatePreview(this.form.image);
            
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

  </style>