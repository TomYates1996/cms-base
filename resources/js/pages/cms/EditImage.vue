<template>
    <form class="new-image" @submit.prevent="updateImage()" aria-label="Edit image details">
         <div class="slide-title">
             <label for="title">Image Title</label>
             <input id="title" autofocus type="text" required v-model="form.title" autocomplete="off">
         </div>
         <div class="slide-credits">
             <label for="credits">Credits</label>
             <input id="credits" type="text" v-model="form.credits" autocomplete="off">
         </div>
         <div>
            <div class="custom-file-upload">
                <input
                    type="file"
                    id="photo"
                    ref="photo"
                    @change="uploadImage($event)"
                    accept="image/*"
                    aria-label="Upload a new image"
                    class="hidden-file-input"
                />
                <label for="photo" class="upload-label" tabindex="0">
                Choose Image
                </label>
                <span v-if="form.image && form.image.name" class="file-name">
                {{ form.image.name }}
                </span>
            </div>
             <div v-if="form.image_path" class="image-preview-con">
                 <img :src="'/' + form.image_path" alt="Form image section" class="preview-image" />
             </div>
             <div v-if="imagePreview" class="image-preview-con">
                 <img :src="imagePreview" alt="Image Preview" class="preview-image" />
             </div>
         </div>
         <div class="slide-image_alt">
             <label for="image_alt">Image alt text</label>
             <input id="image_alt" required type="text" v-model="form.image_alt" autocomplete="off" >
         </div>
         <button type="submit" class="" tabindex="0" :disabled="form.processing" aria-label="Submit image updates">
             <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
             Update Image
         </button>
        <button type="button" aria-label="Cancel edit image" @click.prevent="closeEditImage()" class="cancel-new-image">Cancel</button>
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
             'image_path' : '',
             'id' : '',
         });
   
         return { form } 
     },
     props: {
        image: Object,
     },
     components: {
         LoaderCircle,
     },
     data() {
         return {
             imagePreview: null,
            };
        },
        created() {
           this.form.title = this.image.title;
           this.form.credits = this.image.credits;
           this.form.image_path = this.image.image_path;
           this.form.image_alt = this.image.image_alt;
           this.form.id = this.image.id;
        },
    emits: ['refreshImages'],
     methods: {
        closeEditImage() {
            this.$inertia.visit('/cms/images');
        },
         uploadImage(event) {
             this.form.image = event.target.files[0];
             this.updatePreview(this.form.image);
         },
         updateImage () {
            console.log(this.form);
            
             this.form.post(route('images.update'), {
             onSuccess: (response) => {
                this.$inertia.visit('/cms/images');
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
                 this.form.image_path = '';
             }
         },
     },
   
   }
   </script>
   
   <style scoped>
 .new-image {
    position: fixed;
    top: 0px;
    left: 0px;
    height: 100%;
    width: 100%;
    background-color: var(--white);
    overflow: scroll;
 }

 .hidden-file-input {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0,0,0,0);
  border: 0;
}

.upload-label {
  display: inline-block;
  padding: 0.5rem 1rem;
  background-color: var(--sea-green);
  color: white;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.upload-label:focus {
  outline: 2px solid var(--black);
}

.file-name {
  margin-left: 1rem;
  font-size: 14px;
}
   </style>