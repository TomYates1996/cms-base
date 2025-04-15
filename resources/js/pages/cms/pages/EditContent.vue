<template>
  <div class="page-wrapper">
    <div class="page-left detail">
      <button @click="savePage()" class="save-page">Save Page</button>
      
      <!-- Header Sidebar -->
      <div class="sidebar-option sidebar-header">
        <h5>Header</h5>
        <div v-for="(header, index) in localContent.headers" :key="index" class="widget-options">
            <p>Header - {{ header.section }}</p>
            <button @click="editElement('headers', index)" class="edit-btn"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button>
            <button @click="deleteElement('headers', index)" class="delete-btn"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
            <button @click="saveWidget(header, 'headers', index)" class="save-btn"><font-awesome-icon :icon="header.is_saved ? ['fas', 'lock'] : ['fas', 'unlock']" /></button>
        </div>
      </div>
      <button @click="openAddHeader" v-if="localContent.headers && localContent.headers.length < 1">Add Navigation</button>
      <!-- Widgets Sidebar -->
      <div class="sidebar-option sidebar-widgets">
        <h5>Widgets</h5>
        <div v-for="(widget, index) in localContent.widgets" :key="index" class="widget-options">
            <p>{{ widget.type }}</p>
            <button @click="editElement('widgets', index)" class="edit-btn"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button>
            <button @click="orderUp(index)" :disabled="index === 0" class="edit-btn"><font-awesome-icon :icon="['fas', 'angle-up']" /></button>
            <button @click="orderDown(index)" :disabled="index === localContent.widgets.length - 1" class="edit-btn"><font-awesome-icon :icon="['fas', 'angle-down']" /></button>
            <button @click="deleteElement('widgets', index)" class="delete-btn"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
            <button @click="saveWidget(widget, 'widgets', index)" class="save-btn"><font-awesome-icon :icon="widget.is_saved ? ['fas', 'lock'] : ['fas', 'unlock']" /></button>
          </div>
          
          <button @click="openAddWidgetModal">Add a new widget</button>
        </div>
        <!-- Footer Sidebar -->
        <div class="sidebar-option sidebar-footer">
          <h5>Footer</h5>
          <div v-for="(footer, index) in localContent.footers" :key="index" class="widget-options">
            <p>Footer - {{ footer.section }}</p>
            <button @click="editElement('footers', index)" class="edit-btn"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button>
            <button @click="deleteElement('footers', index)" class="delete-btn"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
            <button @click="saveWidget(footer, 'footers', index)" class="save-btn"><font-awesome-icon :icon="footer.is_saved ? ['fas', 'lock'] : ['fas', 'unlock']" /></button>
        </div>

        <button @click="openAddFooter" v-if="localContent.footers && localContent.footers.length < 1">Add Footer</button>
      </div>
            <!-- <EditSlide v-if="showEditSlide" :slide="slideToEdit"/> -->
          </div>
          <div class="page-right">
              <HamburgerHeader :pages="header.pages" v-for="(header, index) in localContent.headers" :key="index" :link="header.link" :logo="header.logo"/>
              <NewHeader v-if="showHeaderEdit" @cancelAdd="cancelAddHeader" @addHeader="addHeader" @getImages="getImages" :images="images"/>
              <NewWidget v-if="showModal.new.widget" :slides="slides" @addWidget="addWidget" @cancelAdd="cancelAdd" :page="page" />
              <EditWidget v-if="showModal.edit.widgets" :widget="widgetInfo" :slides="slides" @saveEdit="saveEdit" @cancelEdit="cancelEdit"/>
              <EditHeader v-if="showModal.edit.headers" :header="headerInfo" :images="images" @saveEdit="saveEditHeader" @cancelEdit="cancelEditHeader"/>
              <EditFooter v-if="showModal.edit.footers" :footer="footerInfo" :images="images" @saveEdit="saveEditFooter" @cancelEdit="cancelEditFooter"/>
              <NewFooter v-if="showNewFooter" @getImages="getImages" @addFooter="addFooter" @cancelAdd="cancelAddFooter" :images="images"/>
              <div v-if="!showEditWidget" class="widget-container">
                <component v-for="widget in localContent.widgets" :key="widget.id" :is="widget.type" :widget="widget"/>
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
import EditWidget from '@/components/cms/widget/EditWidget.vue';
import NewWidget from '@/components/cms/widget/NewWidget.vue';
import HamburgerHeader from '@/components/nav/HamburgerHeader.vue';
import NewHeader from '@/components/nav/NewHeader.vue';
import EditHeader from '@/components/nav/EditHeader.vue';
import NewFooter from '@/components/nav/NewFooter.vue';
import EditFooter from '@/components/nav/EditFooter.vue';

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
        pages: Object,
        page: Object,
        widgets: Array,
        headers: Array,
    },
    components: {
      EditFooter,
      NewFooter,
      EditHeader,
      NewHeader,
      HamburgerHeader,
      NewWidget,
      EditWidget,
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
        showModal: {
          edit : {},
          new: {},
        },
        showHeaderEdit: false,
        selectedWidgetType: '',  
        widgetTitle: '',
        selectedSlides: [],  
        slides: [],  
        showEditWidget : false,
        showEditHeader : false,
        widgetInfo: {},
        headerInfo: {},
        editIndex: {},
        showEditSlide: false,
        slideToEdit: {},
        images: [],
        showNewFooter: false,
        showEditFooter: false,
        footerInfo: {},
        localContent: [],
      };
    },
    created() {
        this.localContent.widgets = this.widgets;
        this.localContent.headers =this.headers;
        this.localContent.footers = this.page.footers || [];
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
          [this.localContent.widgets[index], this.localContent.widgets[index - 1]] = [this.localContent.widgets[index - 1], this.localContent.widgets[index]];
        }
      },
      orderDown(index) {
        if (index < this.localContent.widgets.length - 1) {
          [this.localContent.widgets[index], this.localContent.widgets[index + 1]] = [this.localContent.widgets[index + 1], this.localContent.widgets[index]];
        }
      },
      editElement(type, index) {
        this.showModal.edit[type] = true;
        this.itemInfo = Object.assign({}, this.localContent[type][index]);
        if (type === "widgets") {
          this.slides.forEach (slide => {
            if (this.itemInfo.slides.some(infoSlide => infoSlide.id === slide.id)) {
              slide.selected = true;
                }
          })
        }
        this.editIndex = index;
      },
      editWidget(index) {
        this.showEditWidget = true;
        this.widgetInfo = Object.assign({}, this.localContent.widgets[index]);
        this.slides.forEach (slide => {
          if (this.widgetInfo.slides.some(infoSlide => infoSlide.id === slide.id)) {
            slide.selected = true;
              }
        })
        this.editIndex = index;
      },
      editHeader(index) {
        this.showEditHeader = true;
        this.headerInfo = Object.assign({}, this.localContent.headers[index]);
        this.editIndex = index;
      },
      editFooter(index) {
        this.showEditFooter = true;
        this.footerInfo = Object.assign({}, this.localContent.footers[index]);
        this.editIndex = index;
      },
      saveEdit() {
            this.selectedSlides = this.slides.filter(slide => slide.selected);
            this.widgetInfo.slides = this.selectedSlides;
            this.localContent.widgets[this.editIndex] = this.widgetInfo;
            this.showEditWidget = false;
            this.slides.forEach(slide => {
              slide.selected = false; 
            });
          },
          cancelEdit() {
            this.widgetInfo = this.localContent.widgets[this.editIndex];
            this.showEditWidget = false;
            this.slides.forEach(slide => {
              slide.selected = false; 
            });
        },
      saveEditHeader() {
            this.headerInfo.pages = this.pages[this.headerInfo.section];
            this.localContent.headers[this.editIndex] = this.headerInfo;
            
            this.showEditHeader = false;
          },
          cancelEditHeader() {
            this.headerInfo = this.headerInfo[this.editIndex];
            this.showEditHeader = false;
          },
          openAddWidgetModal() {
        this.showModal = true;
        // this.fetchSlides();  
      },
          deleteElement(type, index) {
            if (confirm(`Are you sure you want to delete this ${type}?`)) {
              this.localContent[type].splice(index, 1);
            }
          },
      openAddHeader() {
        this.showHeaderEdit = true;
      },
      cancelAdd() {
        this.showModal = false;
        this.slides.forEach(slide => {
            slide.selected = false; 
        });
        this.widgetTitle = ''
        this.selectedWidgetType = ''
        this.selectedSlides = []
      },
      cancelAddHeader() {
        this.showHeaderEdit = false;
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
      addWidget(newWidget) {
            this.localContent.widgets.push(newWidget);
            

            this.slides.filter(slide => slide.selected).forEach(slide => {
              slide.selected = false;
            })
          this.cancelAdd();
        },
        addHeader(newHeader) {
        this.localContent.headers.push(newHeader);
        this.cancelAddHeader();
        console.log(this.localContent.headers);
        
      },
      savePage() {
        router.post(`/cms/pages/save`, { 
          widgets: this.localContent.widgets, 
          page_id: this.page.id, 
          headers: this.localContent.headers,
          footers: this.localContent.footers,
          }, {
            onSuccess: () => {
              router.get('/cms/pages');
            }
          }
        );
      },
      openAddFooter() {
        this.showNewFooter = true;
      },

      cancelAddFooter() {
        this.showNewFooter = false;
      },

      addFooter(newFooter) {
        this.localContent.footers.push(newFooter);
        this.cancelAddFooter();
      },

      saveEditFooter() {
            this.footerInfo.pages = this.pages[this.footerInfo.section];
            this.localContent.footers[this.editIndex] = this.footerInfo;
            
            this.showEditFooter = false;
          },
          cancelEditFooter() {
            this.footerInfo = this.footerInfo[this.editIndex];
            this.showEditFooter = false;
          },
     
      saveWidget(item, type, index) {
        if (!item.is_saved) {
          const name = window.prompt("Enter a name for this saved item:", item.name || "");
          if (name === null) return;
        }
        
        router.put(`/${type}/${item.id}/save`, {
          is_saved: !item.is_saved,
          name: name || null,
        }, {
        onSuccess: () => {
            this.localContent[type][index].is_saved = !this.localContent[type][index].is_saved;
          }
        });
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
    .sidebar-option h5 {
      border-bottom: 1px solid var(--black);
    }
  </style>
  