<template>
    <ul class="slide-list">
      <li class="slide-item" v-for="slide in slides" :key="slide.id">
          <p class="slide-title">{{ slide.title }}</p>
          <p class="slide-desc">{{ slide.description }}</p>
          <p class="slide-link">{{ slide.link }}</p>
          <img :src="slide.image" :alt="slide.image_alt">
      </li>
    </ul>
    <Newslide/>
  </template>
  
  <script>
  import axios from 'axios';
  import NewSlide from './Newslide.vue';
  
  export default {
      components: {
          NewSlide,
      },
      data() {
          return {
              slides: [],
              images: [],
          }
      },
      mounted() {
          this.fetchslides();
      },
      methods: {
          // Get the list of slides
          fetchslides() {
              axios.get('/api/slides')
              .then((response) => {
                  this.slides = response.data.slides; 
              })
              .catch((error) => {
                  console.error('Error fetching slides:', error);
              });
              axios.get('/api/images/all')
              .then((response) => {
                  this.images = response.data.images; 
              })
              .catch((error) => {
                  console.error('Error fetching images:', error);
              });
           }
      }
  }
  </script>
  
  <style scoped>
  .slide-list {
      .slide-item {
          display: flex;
          justify-content: space-between;
      }
  }
  </style>