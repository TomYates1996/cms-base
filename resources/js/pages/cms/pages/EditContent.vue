<template>
  <div class="page-wrapper">
    <div class="page-left detail">
      <button @click="savePage()" class="save-page">Save Page</button>
      
            <div v-for="(widget, index) in localWidgets" :key="index" class="widget-options">
                <p>{{ widget.type }}</p>
                <button @click="editWidget(index)" class="edit-btn"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button>
                <button @click="orderUp(index)" :disabled="index === 0" class="edit-btn"><font-awesome-icon :icon="['fas', 'angle-up']" /></button>
                <button @click="orderDown(index)" :disabled="index === localWidgets.length - 1" class="edit-btn"><font-awesome-icon :icon="['fas', 'angle-down']" /></button>
                <button @click="deleteWidget(widget.id)" class="delete-btn"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
            </div>
            <button @click="openAddWidgetModal">Add a new widget</button>
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
        
                <button @click="addWidget">Add Widget</button>
                <button @click="closeModal">Cancel</button>
              </div>
            </div>
            <EditSlide v-if="showEditSlide" :slide="slideToEdit"/>
        </div>
        <div class="page-right">
              <div class="edit-widget" v-if="showEditWidget">
                Edit Widget
                <select v-model="widgetInfo.type" >
                    <option v-for="widget in widgetOptions" :key="widget.id" :value="widget.name">{{ widget.label }}</option>
                </select>
      
                <div class="edit-title">
                    <label for="edit-title">Widget Title</label>
                    <input id="edit-title" name="edit-title" type="text" v-model="widgetInfo.title" />
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
            <div v-if="!showEditWidget" class="widget-container">
                <component v-for="widget in localWidgets" :key="widget.id" 
                :is="widget.type"
                :widget="widget"
                />
            </div>
        </div>
    </div>
  </template>
  
<script>
import { defineAsyncComponent } from 'vue';
import { router } from '@inertiajs/vue3'
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';
import EditSlide from '@/components/cms/slides/EditSlide.vue';

export default {
    setup(){
        const formSlide = useForm({
            'title' : '',
            'image_path' : '',
            'image_alt' : '',
            'description' : '',
            'link' : '',
        });

        return { formSlide } 
    },
    props: {
        page: Object,
        widgets: Array,
    },
    components: {
      useForm,
      EditSlide,
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
        showEditSlide: false,
        slideToEdit: {},
        images: [],
      };
    },
    created() {
        this.localWidgets = this.widgets;
        this.getImages();
    },
    computed: {
        widgetOptions() {
            return this.$widgetOptions;
        },
    },
    methods: {
      getImages() {
            axios.get('/api/images/all')
            .then((response) => {
                this.images = response.data.images; 
                this.fetchSlides();   
            })
        },
      editSlide(slide) {
        
        this.showEditSlide = true;
        this.slideToEdit = slide;
      },
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
        this.slides.forEach (slide => {
          if (this.widgetInfo.slides.some(infoSlide => infoSlide.id === slide.id)) {
            slide.selected = true;
              }
            })
            this.editIndex = index;
          },
          saveEdit() {
            this.selectedSlides = this.slides.filter(slide => slide.selected);
            this.widgetInfo.slides = this.selectedSlides;
            this.localWidgets[this.editIndex] = this.widgetInfo;
            this.showEditWidget = false;
            this.slides.forEach(slide => {
              slide.selected = false; 
            });
          },
          cancelEdit() {
            this.widgetInfo = this.localWidgets[this.editIndex];
            this.showEditWidget = false;
            this.slides.forEach(slide => {
              slide.selected = false; 
            });
        },
        deleteWidget(widgetId) {
            if (confirm('Are you sure you want to delete this widget?')) {
              this.localWidgets = this.localWidgets.filter(widget => widget.id !== widgetId);
            }
          },
          openAddWidgetModal() {
        this.showModal = true;
        // this.fetchSlides();  
      },
      closeModal() {
        this.showModal = false;
        this.slides.forEach(slide => {
            slide.selected = false; 
        });
        this.widgetTitle = ''
        this.selectedWidgetType = ''
        this.selectedSlides = []
      },
      fetchSlides() {
        axios.get('/api/slides')
            .then((response) => {
                this.slides = response.data.slides;
            })
            .catch((error) => {
                console.error('Error fetching slides:', error);
            });
      },
      addWidget() {
        // try {
          const pageId = this.page.id; 
          
          this.selectedSlides = this.slides.filter(slide => slide.selected);

          this.widgetInfo = {
              page_id: pageId,
              title: this.widgetTitle,
              type: this.selectedWidgetType,
              slides: this.selectedSlides,
          }
            this.localWidgets.push(this.widgetInfo);

            this.slides.forEach(slide => {
              slide.selected = false; 
            });
            this.widgetTitle = ''
            this.selectedWidgetType = ''
            this.selectedSlides = []
        
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
      savePage() {
        router.post(`/cms/pages/save`, { widgets: this.localWidgets, page_id: this.page.id }, {
            onSuccess: () => {
              router.get('/cms/pages');
            }
          }
        );
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
    .edit-slide-form {
      position: fixed;
      top: 0px;
      left: 0px;
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: var(--white);
    }
  </style>
  