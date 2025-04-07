<template>
    <div>
      <button @click="openAddWidgetModal">Add Widget to Page</button>
  
      <div v-if="showModal" class="modal">
        <div class="modal-content">
          <h3>Select Widget Type</h3>
  
          <select v-model="selectedWidgetType" >
            <option v-for="widget in widgetOptions" :key="widget.id" :value="widget.name">{{ widget.label }}</option>
          </select>
  
          <h3>Select Slides</h3>
          <select v-model="selectedSlides" multiple>
            <option v-for="slide in slides" :key="slide.id" :value="slide.id">
              {{ slide.title }}
            </option>
          </select>
  
          <button @click="addWidget">Add Widget</button>
          <button @click="closeModal">Cancel</button>
        </div>
      </div>
    </div>
    <div v-for="widget in localWidgets" :key="widget.id" class="widget-container">
        <component
        :is="getWidgetComponent(widget.type)"
        :widget="widget"
        />
        <button @click="deleteWidget(widget.id)" class="delete-btn">Delete</button>
    </div>
  </template>
  
<script>
import { defineAsyncComponent } from 'vue';
import axios from 'axios';

export default {
    props: {
        page: Object,
        widgets: Array,
    },
    components: {
        defineAsyncComponent,
    },
    data() {
      return {
        showModal: false,
        selectedWidgetType: '',  
        selectedSlides: [],  
        slides: [],  
        localWidgets: [],
      };
    },
    created() {
        this.localWidgets = this.widgets;
    },
    computed: {
        widgetOptions() {
            return this.$widgetOptions;
        },
    },
    methods: {
        deleteWidget(widgetId) {
      if (confirm('Are you sure you want to delete this widget?')) {
        axios
          .delete(`/cms/pages/${this.page.id}/widgets/${widgetId}/delete`)
          .then(response => {
            this.localWidgets = this.localWidgets.filter(widget => widget.id !== widgetId);
            alert('Widget deleted');
          })
          .catch(error => {
            alert('Failed to delete widget');
          });
      }
    },
      openAddWidgetModal() {
        this.showModal = true;
        this.fetchSlides();  // Fetch slides when the modal is opened
      },
      closeModal() {
        this.showModal = false;
      },
      async fetchSlides() {
        try {
          const response = await axios.get('/api/slides');
          this.slides = response.data.slides;
        } catch (error) {
          console.error('Error fetching slides:', error);
        }
      },
      async addWidget() {
        try {
          const pageId = this.page.id; 
  
          const response = await axios.post(`/cms/pages/${pageId}/widgets`, {
            title: 'New Widget',  
            type: this.selectedWidgetType,  
            slide_ids: this.selectedSlides, 
          });
          this.localWidgets.push(response.data.widget);
          this.closeModal();
        } catch (error) {
          console.error('Error adding widget:', error);
        }
      },

      getWidgetComponent(type) {
        switch (type) {
            case 'cards_2_across':
            return defineAsyncComponent(() => import('@/Components/Widgets/Cards/Cards2Across.vue'));
            case 'cards_3_across':
            return defineAsyncComponent(() => import('@/Components/Widgets/Cards/Cards3Across.vue'));
            case 'cards_4_across':
            return defineAsyncComponent(() => import('@/Components/Widgets/Cards/Cards4Across.vue'));
            case 'imagebox_with_caption':
            return defineAsyncComponent(() => import('@/Components/Widgets/imagebox/ImageBoxWithCaption.vue'));
            default:
            return defineAsyncComponent(() => import('@/Components/Widgets/Cards/Cards2Across.vue'));
        }
        },
    },
  };
  </script>
  
  <style scoped>
  /* Add your modal and styles here */
  </style>
  