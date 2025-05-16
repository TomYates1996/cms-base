<template>
  <form class="edit-page-info form" @submit.prevent="createSlide()" aria-label="Create a new slide">
    <fieldset class="form-inner">
        <legend class="form-title">Create Slide</legend>

        <div class="form-slide-title form-field">
            <label for="title">Title</label>
            <input id="title" name="title" type="text" required v-model="form.title" autofocus aria-required="true" />
        </div>

        <div class="form-slide-description form-field">
        <label for="description">Description</label>
        <input id="description" name="description" type="text" required v-model="form.description" aria-required="true" />
        </div>

        <div class="form-slide-link form-field form-wrap">
            <div class="link-upper">
                <label>Link</label>
                <div>
                    <label>
                    <input type="radio" value="page" v-model="linkType" />
                    Internal page
                    </label>
                    <label>
                    <input type="radio" value="custom" v-model="linkType" />
                    Custom URL
                    </label>
                </div>
            </div>

            <div v-if="linkType === 'page'" class="link-option page-option">
                <label for="link">Select a Page</label>
                <select id="link" name="link" required v-model="form.link" aria-required="true">
                <option disabled value="">Please select a page</option>
                <option v-for="page in pages" :key="page.id" :value="'/' + page.slug">
                    {{ page.title }}
                </option>
                </select>
            </div>

            <div v-else class="link-option">
                <label for="custom-link">Custom URL</label>
                <input id="custom-link" name="custom-link" type="text" required v-model="form.link" placeholder="https://example.com" aria-required="true" />
            </div>
        </div>

        <div class="form-slide-image form-field">
        <button type="button" class="btn-default add-img" @click="imageList()"
                aria-controls="image-grid" aria-expanded="showImageGrid.toString()">
            Select Image
        </button>
        </div>

        <div v-if="showImageGrid" id="image-grid" class="image-grid" role="region" aria-label="Image selection">
        <img v-for="image in images" :key="image.id" class="new-slide-img-option"
            @click="addImageToSlide(image)" :src="'/' + image.image_path"
            :alt="image.image_alt || 'Slide image option'" role="button" tabindex="0" />
        <NewImage @refreshImages="refreshImages()" />
        </div>

        <div v-if="imagePreview" class="image-preview form-field" role="region" aria-label="Selected image preview">
        <p class="sr-only">Preview Image</p>
        <img :src="imagePreview" alt="Preview of selected slide image" />
        </div>

        <button type="submit" class="btn-default" tabindex="5"
                :disabled="form.processing" :aria-busy="form.processing.toString()">
        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
        Create Slide
        </button>

        <button type="button" class="btn-default cancel-new-slide"
                @click="cancelNew()" aria-label="Cancel slide creation">
        Cancel
        </button>
    </fieldset>
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
        pages: Array,
    },
    data() {
        return {
            imagePreview: null, 
            showImageGrid: false,
            showNewSlide: false,
            linkType: 'page',
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
        cancelNew() {
            this.$emit('cancelNew');
        },
        imageList() {
            this.showImageGrid = true;
        },
        addImageToSlide(image) {
            this.form.image_id = image.id;
            this.imagePreview = '/' + image.image_path;
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
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    background-color: var(--white);
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    padding: 20px;
    overflow: scroll;
}

.new-slide-img-option {
    width: 100%;
    object-fit: cover;
    aspect-ratio: 1/1;
}

.image-preview {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

.image-preview img {
    max-width: 200px;
    object-fit: contain;
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