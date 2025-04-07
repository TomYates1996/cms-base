<template>
    <div class="page-wrapper">
        <div class="edit-widget" v-if="showEditWidget">
            Edit Widget
            <select v-model="widgetInfo.type" >
                <option v-for="widget in widgetOptions" :key="widget.id" :value="widget.name">{{ widget.label }}</option>
            </select>
  
            <div class="edit-title">
                <label for="edit-title">Widget Title</label>
                <input id="edit-title" name="edit-title" type="text" v-model="widgetInfo.title" />
            </div>
        
            <h3>Select Slides</h3>
            <select v-model="widgetInfo.slides" multiple>
                <option v-for="slide in slides" :key="slide.id" :value="slide.id">
                {{ slide.title }}
                </option>
            </select>
            <button @click="saveEdit()">Save Changes</button>
            <button @click="cancelEdit()">Cancel</button>
        </div>
        <div class="page-left detail">
            
            <div v-for="(widget, index) in localWidgets" :key="index" class="widget-options">
                <p>{{ widget.type }}</p>
                
                <button @click="editWidget(index)" class="edit-btn"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button>
                <button @click="orderUp(index)" :disabled="index === 0" class="edit-btn"><font-awesome-icon :icon="['fas', 'angle-up']" /></button>
                <button @click="orderDown(index)" :disabled="index === localWidgets.length - 1" class="edit-btn"><font-awesome-icon :icon="['fas', 'angle-down']" /></button>
                <button @click="deleteWidget(widget.id)" class="delete-btn"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
            </div>
            <button @click="openAddWidgetModal">Add Widget to Page</button>
            <div v-if="showModal" class="modal">
              <div class="modal-content">
                <h3>Select Widget Type</h3>
        
                <select v-model="selectedWidgetType" >
                  <option v-for="widget in widgetOptions" :key="widget.id" :value="widget.name">{{ widget.label }}</option>
                </select>
  
                <div class="widget-title">
                  <label for="widget-title">Widget Title</label>
                  <input id="widget-title" name="widget-title" type="text" v-model="widgetTitle" />
              </div>
        
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
        <div class="page-right">
            <div v-for="widget in localWidgets" :key="widget.id" class="widget-container">
                <component
                :is="widget.type"
                :widget="widget"
                />
            </div>
        </div>
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
        cards_2_across : defineAsyncComponent(() => import('@/Components/Widgets/Cards/Cards2Across.vue')),
        cards_3_across : defineAsyncComponent(() => import('@/Components/Widgets/Cards/Cards3Across.vue')),
        cards_4_across : defineAsyncComponent(() => import('@/Components/Widgets/Cards/Cards4Across.vue')),
        imagebox_with_caption : defineAsyncComponent(() => import('@/Components/Widgets/imagebox/ImageBoxWithCaption.vue')),
    },
    data() {
      return {
        showModal: false,
        selectedWidgetType: '',  
        widgetTitle: '',
        selectedSlides: [],  
        slides: [],  
        localWidgets: [],
        showEditWidget : false,
        widgetInfo: {},
        editIndex: {},
      };
    },
    created() {
        this.localWidgets = this.widgets;
        this.fetchSlides();
    },
    computed: {
        widgetOptions() {
            return this.$widgetOptions;
        },
    },
    methods: {
        orderUp(index) {
            if (index > 0) {
                [this.localWidgets[index], this.localWidgets[index - 1]] = [this.localWidgets[index - 1], this.localWidgets[index]];
            }
        },
        orderDown(index) {
            if (index < this.localWidgets.length - 1) {
                [this.localWidgets[index], this.localWidgets[index + 1]] = [this.localWidgets[index + 1], this.localWidgets[index]];
            }
        },
        editWidget(index) {
            this.showEditWidget = true;
            this.widgetInfo = Object.assign({}, this.localWidgets[index]);
            this.editIndex = index;
        },
        saveEdit() {
            this.widgetInfo.slides = this.widgetInfo.slides.map(index => this.slides[index-1]);
            this.localWidgets[this.editIndex] = this.widgetInfo;
            this.showEditWidget = false;
        },
        cancelEdit() {
            this.widgetInfo = this.localWidgets[this.editIndex];
            this.showEditWidget = false;
        },
        deleteWidget(widgetId) {
            if (confirm('Are you sure you want to delete this widget?')) {
        // axios
        //   .delete(`/cms/pages/${this.page.id}/widgets/${widgetId}/delete`)
        //   .then(response => {
            this.localWidgets = this.localWidgets.filter(widget => widget.id !== widgetId);
        //     alert('Widget deleted');
        //   })
        //   .catch(error => {
        //     alert('Failed to delete widget');
        //   });
            }
        },
      openAddWidgetModal() {
        this.showModal = true;
        this.fetchSlides();  
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
      addWidget() {
        // try {
            const pageId = this.page.id; 

            const newSlideArray = this.selectedSlides.map(index => this.slides[index-1]);

            let newWidget = {
                page_id: pageId,
                slides: newSlideArray,
                title: this.widgetTitle,
                type: this.selectedWidgetType,
            }
            
            this.localWidgets.push(newWidget);
        
            // this.localWidgets.push(response.data.widget);
        //   const response = await axios.post(`/cms/pages/${pageId}/widgets`, {
        //     title: this.widgetTitle,  
        //     type: this.selectedWidgetType,  
        //     slide_ids: this.selectedSlides, 
        //   });
          this.closeModal();
        // } catch (error) {
        //   console.error('Error adding widget:', error);
        // }
      },
    },
  };
  </script>
  
  <style scoped>
    .page-wrapper {
        display: flex;
        gap: 20px;
        .page-left {
            display: flex;
            flex-direction: column;
            border-right: 2px solid var(--black);
            width: 25%;
            gap: 10px;
            .widget-options {
                display: flex;
                gap: 8px;
            }
        }
        .page-right {
            display: flex;
            flex-direction: column;
            width: 75%;
        }
    }
  </style>
  