<template>
    <form class="new-slide" @submit.prevent="createSlide()" aria-label="Create new slide">
      
      <div class="slide-title">
        <label for="title">Title</label>
        <input id="title" type="text" required v-model="form.title" autocomplete="off" />
      </div>
  
      <div class="slide-description">
        <label for="description">Description</label>
        <input id="description" type="text" required v-model="form.description" autocomplete="off" />
      </div>
  
      <div class="slide-link">
        <label for="link">Link</label>
        <input id="link" type="text" required v-model="form.link" autocomplete="off" />
      </div>
  
      <button type="button" @click="imageList" class="add-img" aria-label="Open image selector">Select Image</button>
  
      <div v-if="selectedImagePath" class="slide-image-preview">
        <p>Selected Image Preview:</p>
        <img :src="selectedImagePath" alt="Selected image preview" class="preview-image" />
      </div>
  
      <div v-if="showImageGrid" class="image-grid" aria-label="Select an image from the grid">
        <img
          v-for="image in images"
          :key="image.id"
          class="new-slide-img-option"
          @click="addImageToSlide(image)"
          :src="'/' + image.image_path"
          :alt="image.image_alt || 'Slide image option'"
        />
        <NewImage @refreshImages="refreshImages" />
      </div>
  
      <button type="submit" :disabled="form.processing" aria-label="Create new slide">
        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
        Create Slide
      </button>
  
      <button type="button" @click="closeNewSlide" class="cancel-new-slide" aria-label="Cancel slide creation">
        Cancel
      </button>
    </form>
  </template>
  
  <script>
  import NewImage from '@/components/cms/slides/images/NewImage.vue';
  import CMSLayout from '@/layouts/CMSLayout.vue';
  import { useForm } from '@inertiajs/vue3';
  import { LoaderCircle } from 'lucide-vue-next';
  
  export default {
    layout: CMSLayout,
  
    setup() {
      const form = useForm({
        title: '',
        description: '',
        link: '',
        image_id: null,
      });
  
      return { form };
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
        selectedImagePath: null,
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
        this.showNewSlide = true;
      },
      closeNewSlide() {
        this.showNewSlide = false;
        this.form.reset();
        this.selectedImagePath = null;
      },
      imageList() {
        this.showImageGrid = true;
      },
      addImageToSlide(image) {
        this.form.image_id = image.id;
        this.selectedImagePath = '/' + image.image_path;
        this.showImageGrid = false;
      },
      createSlide() {
        this.form.post(route('api.slides.store'), {
          onSuccess: () => {
            this.$inertia.visit('/cms/slides');
          },
          onError: (errors) => {
            console.error('Form submission error:', errors);
          },
        });
      },
    },
  };
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
    aspect-ratio: 1 / 1;
    cursor: pointer;
  }
  
  .preview-image {
    max-width: 100%;
    height: auto;
    margin-top: 1rem;
  }
  
  .new-slide {
    height: 100%;
    width: 100%;
    background-color: var(--white);
  }
  </style>
  