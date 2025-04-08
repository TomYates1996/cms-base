<template>
    <form class="new-image" @submit.prevent="createImage()">
         <div class="slide-title">
             <label for="title">Image Title</label>
             <input id="title" autofocus type="text" required v-model="form.title" >
         </div>
         <div class="slide-credits">
             <label for="credits">Credits</label>
             <input id="credits" autofocus type="text" v-model="form.credits" >
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
             Create Image
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
             'credits' : '',
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
         createImage () {
             this.form.post(route('cms.image.store'), {
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