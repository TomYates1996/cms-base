<template>
    <div class="section-new-footer">
      <div class="tab-links tab-list btn-row">
        <button :class="newTab ? 'active btn-alt no-hover' : 'inactive btn-default'" @click.prevent="newTab = true">Add New</button>
        <button :class="newTab ? 'inactive btn-default' : 'active btn-alt no-hover'" @click.prevent="newTab = false">Saved Options</button>
      </div>
  
      <!-- Saved Footers -->
      <ul class="saved-item-list tab-inners" v-if="!newTab">
        <li v-for="(item, index) in savedFooters" :key="index" class="saved-item">
          <button type="button" class="add-saved-element" @click="savedFooter(item)" aria-label="Add saved footer to page">
            <span class="visually-hidden">Footer -> {{ item.type }} - {{ item.name }}</span>
            <span aria-hidden="true"><font-awesome-icon :icon="['fas', 'plus']" /></span>
          </button>
          <button @click.prevent="deleteSaved('footers', index)" class="delete-btn">
            <font-awesome-icon :icon="['fas', 'trash-can']" />
          </button>
        </li>
      </ul>
  
      <!-- Add New Footer -->
      <form v-if="newTab" @submit.prevent="addFooter" class="edit-page-info form tab-inners" aria-labelledby="form-footer">
        <fieldset class="form-inner">
          <legend id="form-footer" class="form-title">Add a New Footer</legend>
  
          <!-- Image Picker -->
          <div class="form-slide-image form-field">
            <button type="button" @click="imageList()" class="btn-default add-img" aria-label="Select a logo image">Logo</button>
            <div v-if="showImageGrid" class="image-grid" role="grid" aria-labelledby="footer-image-grid-label">
              <span id="footer-image-grid-label" class="sr-only">Select an image</span>
              <img
                class="new-slide-img-option"
                @click="addImageToSlide(image, index)"
                v-for="(image, index) in images"
                :key="image.id"
                :src="'/' + image.image_path"
                :alt="'Select ' + image.name"
                role="gridcell"
              />
              <NewImage @refreshImages="getImages()" />
            </div>
          </div>
  
          <!-- Section Select -->
          <div class="form-slide-link form-field">
            <label for="footer-section">Section</label>
            <select id="footer-section" name="footer-section" v-model="newFooter.section" required aria-label="Select section for the footer" aria-required="true">
              <option value="primary">Primary</option>
              <option value="secondary">Secondary</option>
              <option value="footer">Footer</option>
            </select>
          </div>
  
          <!-- Actions -->
          <div class="form-actions btn-row">
            <button type="submit" class="btn-default" aria-label="Add Footer">Add Footer</button>
            <button type="button" @click="cancelAdd()" class="btn-default cancel-update-slide" aria-label="Cancel Adding Footer">Cancel</button>
          </div>
        </fieldset>
      </form>
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
         savedFooters: Array,
     },
     data() {
         return {
             newFooter: {},
             showImageGrid: false,
             newTab: true,
         }
     },
     methods: {
         addFooter() {
             this.$emit('addFooter', this.newFooter)
         },
         cancelAdd() {
             this.$emit('cancelAdd')
         },
         getImages() {
             this.$emit('getImages')
         },
         imageList() {
             this.getImages();
             this.showImageGrid = true;
         },
         addImageToSlide(image, index) {
             this.newFooter.logo = this.images[index];
             this.showImageGrid = false;
         },
         savedFooter(item) {
            this.newFooter = item;
            this.addFooter();
         },
     },
 }
 </script>
 
 <style>
     .image-grid {
         position: fixed;
         top: 0px;
         left: 0px;
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
     .section-new-footer {
        padding: 20px;
     }
 </style>