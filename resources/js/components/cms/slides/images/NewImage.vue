<template>
    <form class="form new-image edit-page-info" @submit.prevent="createImage()" aria-labelledby="create-image-title">
        <fieldset class="form-inner">
            <legend id="create-image-title" class="form-title">New Image</legend>

            <div class="form-field slide-title">
                <label for="title">Image Title</label>
                <input id="title" type="text" v-model="form.title" required autofocus aria-required="true"/>
            </div>
            <div class="form-field slide-credits">
                <label for="credits">Credits</label>
                <input id="credits" type="text" v-model="form.credits" aria-required="false"/>
            </div>

            <div class="form-field">
                <label for="photo">Image Upload</label>
                <input
                    id="photo"
                    ref="photo"
                    type="file"
                    accept="image/*"
                    @input="uploadImage($event);"
                />
                <div v-if="imagePreview" class="image-preview-con" aria-live="polite">
                    <img :src="imagePreview" alt="Image Preview" class="preview-image" />
                </div>
            </div>

            <div class="form-field slide-image_alt">
                <label for="image_alt">Image Alt Text</label>
                <input
                    id="image_alt"
                    type="text"
                    v-model="form.image_alt"
                    required
                    aria-required="true"
                />
            </div>

            <div class="form-actions">
                <button
                    type="submit"
                    class="btn-default"
                    :disabled="form.processing"
                    tabindex="5"
                    aria-label="Create Image"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    <span v-else>Create Image</span>
                </button>
                <button
                    type="button"
                    class="btn-default cancel-new-image"
                    @click="closeNewImage()"
                    aria-label="Cancel Image Upload"
                >
                    Cancel
                </button>
            </div>
        </fieldset>
    </form>
</template>

   
   <script>
   import { useForm } from '@inertiajs/vue3';
   import { LoaderCircle } from 'lucide-vue-next';
   import imageCompression from 'browser-image-compression';
   
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
             showNewImage: false, 
         };
     },
    emits: ['refreshImages'],
     methods: {
        newImageToggle() {
            this.showNewImage = true;
        },
        closeNewImage() {
            this.showNewImage = false;
            this.form.reset();
        },
        async uploadImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        const options = {
            maxSizeMB: 1, 
            maxWidthOrHeight: 1920, 
            useWebWorker: true,
        };

        try {
            const compressedFile = await imageCompression(file, options);
            this.form.image = compressedFile;
            this.updatePreview(compressedFile);
        } catch (error) {
            console.error('Image compression failed:', error);
        }
        },
         createImage () {
             this.form.post(route('cms.image.store'), {
             onSuccess: (response) => {
                 this.showNewImage = false;
                this.$emit('refreshImages');                
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
.new-image {
    width: 100%;
    background-color: var(--white);
    overflow: scroll;
    padding: 20px 0px
 }
 .form-inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 10px;
    padding: 20px 0px;
    .form-title {
        font-size: 22px;
        font-weight: 600;
    }
    .form-field {
        display: flex;
        gap: 10px;
        align-items: center;
        label {
            font-size: 18px;
        }
    }
    input {
        border: 1px solid var(--black);
        padding: 4px;
        border-radius: 2px;
    }
    .form-wrap {
        flex-direction: column;
        align-items: flex-start;
        .link-upper {
            display: flex;
            gap: 20px;
            div label:last-of-type {
                margin-left: 20px;
            }
        }
        .link-option {
            display: flex;
            gap: 10px;
            align-items: center;
            select {
                padding: 4px;
            }
        }
    }
}
</style>