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
      <button @click="openAddSaved('headers')" v-if="localContent.headers.length < 1">Add a saved header</button>
      <button @click="openAddItem('headers')" v-if="localContent.headers && localContent.headers.length < 1">Add Navigation</button>
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
            <button @click="cloneWidget(widget, 'widgets', index)" class="save-btn"><font-awesome-icon :icon="['fas', 'clone']" /></button>
          </div>
          
          <button @click="openAddItem('widgets')">Add a new widget</button>
          <button @click="openAddSaved('widgets')">Add a saved widget</button>
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
        <button @click="openAddSaved('footers')" v-if="localContent.footers.length < 1">Add a saved footer</button>
        <button @click="openAddItem('footers')" v-if="localContent.footers && localContent.footers.length < 1">Add Footer</button>
      </div>
            <!-- <EditSlide v-if="showEditSlide" :slide="slideToEdit"/> -->
          </div>
          <div class="page-right">
            <ul class="saved-item-list" v-if="showModal.saved[useType]">
              <li v-for="(item, index) in localSaved[useType]" :key="index" class="saved-item">
                <button type="button" class="add-saved-element" @click="addSaved(item, useType)" aria-label="Add saved {{ useType }} to page">
                  <span class="visually-hidden">{{ useType }} -> {{ item.type }} - {{ item.name }}</span>
                  <span aria-hidden="true"><font-awesome-icon :icon="['fas', 'plus']" /></span>
                </button>
                <button @click="deleteSavedElement(useType, index)" class="delete-btn"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
              </li>
            </ul>
            <button @click="closeWhatsOpen" class="close-open" v-if="anyTrue">| Close |</button>

              <HamburgerHeader v-if="!anyTrue" :pages="header.pages" v-for="(header, index) in localContent.headers" :key="index" :link="header.link" :logo="header.logo"/>
              <NewHeader v-if="showModal.new.headers" @cancelAdd="cancelAdd('headers')" @addHeader="addHeader" @getImages="getImages" :images="images"/>
              <NewWidget v-if="showModal.new.widgets" :slides="slides" @addWidget="addWidget" @cancelAdd="cancelAdd('widgets')" :page="page" />
              <EditWidget v-if="showModal.edit.widgets" :widget="itemInfo" :slides="slides" @saveEdit="saveEdit('widgets')" @cancelEdit="cancelEdit('widgets')"/>
              <EditHeader v-if="showModal.edit.headers" :header="itemInfo" :images="images" @saveEdit="saveEdit('headers')" @cancelEdit="cancelEdit('headers')"/>
              <EditFooter v-if="showModal.edit.footers" :footer="itemInfo" :images="images" @saveEdit="saveEdit('footers')" @cancelEdit="cancelEdit('footers')"/>
              <NewFooter v-if="showModal.new.footers" @getImages="getImages" @addFooter="addFooter" @cancelAdd="cancelAdd('footers')" :images="images"/>
              <div v-if="!anyTrue" class="widget-container">
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
        footers: Array,
        savedWidgets: Array,
        savedHeaders: Array,
        savedFooters: Array,
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
          saved: {},
        },
        showHeaderEdit: false,
        selectedWidgetType: '',  
        widgetTitle: '',
        selectedSlides: [],  
        slides: [],  
        widgetInfo: {},
        headerInfo: {},
        editIndex: {},
        showEditSlide: false,
        slideToEdit: {},
        images: [],
        showNewFooter: false,
        footerInfo: {},
        localContent: [],
        localSaved: [],
        itemInfo: {},
        useType: '',
      };
    },
    created() {
        this.localContent.widgets = this.widgets;
        this.localContent.headers =this.headers;
        this.localContent.footers = this.page.footers;

        
        this.localSaved.widgets = this.savedWidgets;
        this.localSaved.headers = this.savedHeaders;
        this.localSaved.footers = this.savedFooters;
        this.getImages();
    },
    computed: {
        widgetOptions() {
            return this.$widgetOptions;
        },
        anyTrue() {
          return Object.values(this.showModal).some(section =>
            Object.values(section).some(val => val === true)
          );
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
        if (confirm('this is a saved element and editing will update all instances of the item thoughout the site')) {
          Object.keys(this.showModal.edit).forEach(key => {
            this.showModal.edit[key] = false;
          });
          this.showModal.edit[type] = true;
          this.itemInfo = JSON.parse(JSON.stringify(this.localContent[type][index]));
          if (type === "widgets") {
            this.slides.forEach (slide => {
              if (this.itemInfo.slides.some(infoSlide => infoSlide.id === slide.id)) {
                slide.selected = true;
                  }
            })
          }
          this.editIndex = index;
        }
      },
      saveEdit(type,) {
        if (type === "widgets") {
          this.selectedSlides = this.slides.filter(slide => slide.selected);
          this.itemInfo.slides = this.selectedSlides;
        } else if (type === "footers" || type === "headers") {
          this.itemInfo.pages = this.pages[this.itemInfo.section];
        }
        this.localContent[type][this.editIndex] = this.itemInfo;
        this.showModal.edit[type] = false;

        if (type === "widgets") {
        this.slides.forEach(slide => {
              slide.selected = false; 
            });
        }
        console.log(this.itemInfo.is_saved);
        
        if (this.itemInfo.is_saved) {
          console.log('its saved');
              axios.post(`/item/update-save`, {
              template_id: this.itemInfo.template_id,
              type: type,
              item: this.itemInfo,
            })
            .then(() => {
                const itemToUpdate = this.localSaved[type].find(item => item.template_id === this.itemInfo.template_id);
                if (itemToUpdate) {
                  Object.assign(itemToUpdate, this.itemInfo);
                }
                this.localContent[type] = this.localContent[type].map(item => {
                if (item.template_id === this.itemInfo.template_id) {
                    return { ...item, ...this.itemInfo };
                  }
                  return item;
                });       
            })
            .catch(error => {
              console.error('Failed:', error);
            });
        }
      },
        deleteSavedElement(type, index) {
          if (confirm(`This is a saved item and deleting will remove from all pages it is present on.`)) {
              let item = this.localSaved[type][index];
              axios.post(`/item/delete-save`, {
              template_id: item.template_id,
              type: type,
            })
            .then((response) => {
                this.deleteItems(type, this.localSaved[type][index].template_id); 
                this.localSaved[type].splice(index, 1);         
            })
            .catch(error => {
              console.error('Failed:', error);
            });
          }
        },
        deleteItems(type, templateId) {
          this.localContent[type] = this.localContent[type].filter(item => item.template_id !== templateId);
        },
        cancelEdit(type) {
            this.itemInfo = this.localContent[type][this.editIndex];
            this.showModal.edit[type] = false;
            if (type === "widgets") {
              this.slides.forEach(slide => {
                slide.selected = false; 
              });
            }
        },
          deleteElement(type, index) {
            if (confirm(`Are you sure you want to delete this ${type}?`)) {
              this.localContent[type].splice(index, 1);
            }
          },
      cancelAdd(type) {
        this.showModal.new[type] = false;
        if (type === "widgets") {
          this.slides.forEach(slide => {
              slide.selected = false; 
          });
          this.widgetTitle = ''
          this.selectedWidgetType = ''
          this.selectedSlides = []
        }
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
      openAddSaved(type) {
        this.showModal.saved[type] = true;
        this.useType = type;
      },
      addSaved(item, type) {
        console.log(item);
        
        this.localContent[type].push(item);
        this.showModal.saved[type] = false;
      },
      addWidget(newWidget) {
            this.localContent.widgets.push(newWidget);
            

            this.slides.filter(slide => slide.selected).forEach(slide => {
              slide.selected = false;
            })
          this.cancelAdd('widgets');
        },
        addHeader(newHeader) {
          newHeader.pages = this.pages[newHeader.section];
          this.localContent.headers.push(newHeader);
          this.cancelAdd('headers');
      },
      savePage() {
        router.post(`/cms/pages/save`, { 
          widgets: this.localContent.widgets, 
          page_id: this.page.id, 
          headers: this.localContent.headers,
          footers: this.localContent.footers,
          }, {
            onSuccess: () => {
              router.get(`/cms/pages/${this.page.section}`);
            }
          }
        );
      },
      openAddItem(type) {
        this.showModal.new[type] = true;
      },

      cancelAddFooter() {
        this.showModal.new.footers = false;
      },

      addFooter(newFooter) {
        this.localContent.footers.push(newFooter);
        this.cancelAddFooter();
      },

      saveWidget(item, type, index) {
        let name = '';
        if (!item.is_saved) {
          name = window.prompt("Enter a name for this saved item:", item.name || "");
          if (name === null);
        }

        if (!item.is_saved && !item.id) {
          item.is_saved = true;
          item.name = name;
            axios.post(`/${type}/create-save`, {
            item: item,
          })
          .then((response) => {
            console.log(response);
            
            const templateId = response.data.template_id;
            this.localContent[type][index].is_saved = true;
            this.localContent[type][index].name = name;
            this.localContent[type][index].template_id = templateId;
            this.localSaved[type].push(this.localContent[type][index]);
            console.log(this.localSaved[type]);
            
            
          })
          .catch(error => {
            console.error('Failed:', error);
          });
        }

        if (!this[type].find(el => el.id === item.id) || item.id === undefined) {
          item.is_saved = !item.is_saved;
          name = name || null;
          console.log('this path', item);
          return;
        } 
        
        axios.put(`/${type}/${item.id}/save`, {
          is_saved: !item.is_saved,
          name: name || null,
        })
        .then(() => {
          this.localContent[type][index].is_saved = !this.localContent[type][index].is_saved;
          this.localContent[type][index].name = name;
        })
        .catch(error => {
          console.error('Failed:', error);
        });
      },
      closeWhatsOpen() {
        for (const section in this.showModal) {
          for (const key in this.showModal[section]) {
            this.showModal[section][key] = false;
          }
        }
      },
      cloneWidget(item, type, index) {
        const targetItem = JSON.parse(JSON.stringify(this.localContent[type][index]));
        const clonedItem = JSON.parse(JSON.stringify(targetItem));
        this.localContent[type].splice(index + 1, 0, clonedItem);
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
    .saved-item-list {
      display: flex;
      flex-direction: column;
      gap: 6px;
      .saved-item {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        p {
          cursor: pointer;
        }
      }
    }
  </style>
  