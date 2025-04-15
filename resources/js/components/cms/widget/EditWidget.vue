<template>
  <div class="edit-widget">
        Edit Widget
        <select v-model="widget.type" >
            <option v-for="widget in this.$widgetOptions" :key="widget.id" :value="widget.name">{{ widget.label }}</option>
        </select>
      
        <div class="edit-title">
            <label for="edit-title">Widget Title</label>
            <input id="edit-title" name="edit-title" type="text" v-model="widget.title" />
        </div>
            
        <h3>Selected Slides</h3>
        <ul class="slide-list">
            <li v-for="slide in slides.filter(slide => slide.selected)" :key="slide.id">
                <input type="checkbox" v-model="slide.selected">
                <img class="slide-list-img" v-if="slide.image" :src="'/' + slide.image.image_path" :alt="slide.image.image_alt">
                <p>{{ slide.title }}</p>
            </li>
        </ul>
            
        <h3>Select Slides</h3>
        <ul class="slide-list">
            <li v-for="slide in slides.filter(slide => !slide.selected)" :key="slide.id">
                <input type="checkbox" v-model="slide.selected">
                <img class="slide-list-img" v-if="slide.image" :src="'/' + slide.image.image_path" :alt="slide.image.image_alt">
                <p>{{ slide.title }}</p>
            </li>
        </ul>

        <button @click="saveEdit()">Save Changes</button>
        <button @click="cancelEdit()">Cancel</button>
    </div>
</template>

<script>
export default {
    props: {
        widget: Object,
        slides: Array,
    },
    methods: {
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
   .slide-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      li {
        display: flex;
        align-items: center;
        gap: 10px;
        .slide-list-img {
          aspect-ratio: 1/1;
          width: 70px;
        }
      }
    }
</style>