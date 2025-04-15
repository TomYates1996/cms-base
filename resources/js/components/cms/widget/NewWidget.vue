<template>
   <div class="modal-content">
    <h3>Select Widget Type</h3>
        
    <select v-model="newWidget.type" >
      <option v-for="widget in this.$widgetOptions" :key="widget.id" :value="widget.name">{{ widget.label }}</option>
    </select>
  
    <div class="widget-title">
      <label for="widget-title">Widget Title</label>
      <input id="widget-title" name="widget-title" type="text" v-model="newWidget.title" />
    </div>
        
    <h3>Selected Slides</h3>
    <ul class="slide-list">
      <li v-for="slide in slides.filter(slide => slide.selected)" :key="slide.id">
        <input type="checkbox" v-model="slide.selected">
        <img class="slide-list-img" v-if="slide.image" :src="'/' + slide.image.image_path" :alt="slide.image.image_alt">
        <p>{{ slide.title }}</p>
        <button class="edit-slide" @click="editSlide(slide)">Edit slide</button>
      </li>
    </ul>
    
    <h3>Select Slides</h3>
    <ul class="slide-list">
      <li v-for="slide in slides.filter(slide => !slide.selected)" :key="slide.id">
        <input type="checkbox" v-model="slide.selected">
        <img class="slide-list-img" v-if="slide.image" :src="'/' + slide.image.image_path" :alt="slide.image.image_alt">
        <p>{{ slide.title }}</p>
        <button class="edit-slide" @click="editSlide(slide)">Edit slide</button>
      </li>
    </ul>
        
    <button @click="addWidget()">Add Widget</button>
        <button @click="cancelAdd()">Cancel</button>
    </div>
</template>

<script>
export default {
    props: {
        slides: Array,
        page: Object,
    },
    data() {
        return {
            newWidget: {},
        }
    },
    methods: {
        addWidget() {
            this.newWidget.slides = this.slides.filter(slide => slide.selected);
            this.newWidget.page_id = this.page.id;
            
            this.$emit('addWidget', this.newWidget)
        },
        cancelAdd() {
            this.$emit('cancelAdd')
        },
    },
}
</script>

<style>

</style>