<template>
    <div class="edit-widget">
          Edit Footer
        <button @click="imageList()" class="add-img">Logo</button>
        <div v-if="showImageGrid" class="image-grid">
            <img class="new-slide-img-option" @click="addImageToSlide(image, index)" v-for="(image, index) in images" :key="image.id" :src="'/' + image.image_path" alt="">
            <NewImage @refreshImages="getImages()"/>
        </div>
        <select name="section" id="" v-model="footer.section" required>
            <option value="primary">Primary</option>
            <option value="secondary">Secondary</option>
            <option value="footer">Footer</option>
        </select>
  
          <button @click="saveEdit()">Save Changes</button>
          <button @click="cancelEdit()">Cancel</button>
      </div>
  </template>
  
  <script>
import NewImage from '../cms/slides/images/NewImage.vue';

  export default {
    components: {
        NewImage
    },
    props: {
        images: Array,
        footer: Object,
    },
    data() {
        return {
            showImageGrid: false,
        }
    },
    methods: {
        getImages() {
            this.$emit('getImages')
        },
        imageList() {
            this.getImages();
            this.showImageGrid = true;
        },
        addImageToSlide(image, index) {
            this.footer.logo = this.images[index];
            this.showImageGrid = false;
        },
        saveEdit() {
            this.$emit('saveEdit')
        },
        cancelEdit() {
            this.$emit('cancelEdit')
        },
      },
  }
  </script>
  
  <style>
    
  </style>