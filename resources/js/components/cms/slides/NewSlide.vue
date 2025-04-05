<template>
   <form class="new-slide" @submit.prevent="createSlide()">
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
        <div>
            <input type="file" ref="photo" @input="uploadImage($event);"  accept="image/*" >
            <div v-if="imagePreview" class="image-preview-con">
                <img :src="imagePreview" alt="Image Preview" class="preview-image" />
            </div>
        </div>
        <div class="slide-image_alt">
            <label for="image_alt">Image alt text</label>
            <input id="image_alt" required type="text" v-model="form.image_alt" >
        </div>
        <button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            Create Slide
        </button>
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
        });
  
        return { form } 
    },
    components: {
        LoaderCircle,
    },
    data() {
        return {
            imagePreview: null, 
        };
    },
    methods: {
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

  </style>