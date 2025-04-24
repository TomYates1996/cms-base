<template>
    <div class="edit-widget form tab-inners">
      <fieldset class="form-inner">
        <legend class="form-title">Edit Header</legend>
        <div class="form-slide-link form-field">
          <label for="header-link">Link</label>
          <input id="header-link" name="header-link" type="text" v-model="header.link" required aria-required="true" />
        </div>
        <div class="form-slide-image form-field">
          <button type="button" @click="imageList()" class="btn-default add-img" aria-label="Select a logo image">Logo</button>
          <div v-if="showImageGrid" class="image-grid" role="grid" aria-labelledby="image-grid-label">
            <span id="image-grid-label" class="sr-only">Select an image</span>
            <img v-for="(image, index) in images" :key="image.id" class="new-slide-img-option" @click="addImageToSlide(image, index)" :src="'/' + image.image_path" :alt="'Select ' + image.name" role="gridcell" />
            <NewImage @refreshImages="getImages" />
          </div>
        </div>
        <div class="form-slide-link form-field">
          <label for="section-select">Section</label>
          <select id="section-select" name="section" v-model="header.section" required aria-label="Select section for the header" aria-required="true">
            <option value="primary">Primary</option>
            <option value="secondary">Secondary</option>
            <option value="footer">Footer</option>
          </select>
        </div>
        <div class="form-actions btn-row">
          <button type="button" @click="saveEdit()" class="btn-default" aria-label="Save header changes">Save Changes</button>
          <button type="button" @click="cancelEdit()" class="btn-default cancel-update-slide" aria-label="Cancel editing header">Cancel</button>
        </div>
      </fieldset>
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
        header: Object,
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
            this.header.logo = this.images[index];
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
  
<style scoped>
    .edit-widget {
        padding: 20px;
    }
    
</style>