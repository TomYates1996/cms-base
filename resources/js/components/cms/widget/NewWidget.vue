<template>
<div class="modal-content">
  <div v-if="!showSlideList" class="tab-list" role="tablist" aria-label="Tabs for toggling between saved and new widgets">
    <button :class="newTab ? 'active btn-alt no-hover' : 'inactive btn-default'" @click.prevent="newTab = true" role="tab" :aria-selected="newTab ? 'true' : 'false'" aria-controls="new-tab-content" id="add-new-tab">Add New</button>
    <button :class="newTab ? 'inactive btn-default' : 'active btn-alt no-hover'" @click.prevent="newTab = false" role="tab" :aria-selected="newTab ? 'false' : 'true'" aria-controls="saved-tab-content" id="saved-options-tab">Saved Options</button>
  </div>
  <div v-if="newTab" class="tab-inners">
    <div v-if="showSlideList" class="slides-buttons" role="tablist" aria-label="Tabs for toggling between saved and new widgets">
      <button class="btn-default" @click.prevent="addSlides()" role="button">Confirm add</button>
      <button class="btn-default" @click.prevent="cancelSlides()" role="button">Cancel</button>
    </div>
    <form v-if="!showSlideList" class="form form-new-widget" aria-labelledby="select-widget-title">
      <fieldset class="form-inner">
        <legend id="select-widget-title" class="form-title">Add a New Widget</legend>
  
        <div class="widget-type-select form-field">
          <label for="widget-type">Select Widget Type</label>
          <select id="widget-type" v-model="newWidget.type" aria-required="true" required>
            <option v-for="widget in $widgetOptions" :key="widget.id" :value="widget.name">{{ widget.label }}</option>
          </select>
        </div>
  
        <div class="widget-title form-field">
          <label for="widget-title">Widget Title</label>
          <input id="widget-title" name="widget-title" type="text" v-model="newWidget.title" aria-required="true" required />
        </div>
  
        <section class="form-field" aria-labelledby="selected-slides-heading">
          <h2 id="selected-slides-heading" class="form-subtitle">Selected Slides</h2>
          <ul class="slide-list selected-slides">
            <li v-if="slides.filter(slide => slide.selected).length < 1">No slides</li>
            <li v-for="slide in slides.filter(slide => slide.selected)" :key="slide.id" class="slide-item" :aria-label="'Slide: ' + slide.title">
              <article class="slide-card">
                <img v-if="slide.image" class="slide-list-img" :src="'/' + slide.image.image_path" :alt="slide.image.image_alt || ('Image for ' + slide.title)" />
                <div class="slide-info">
                  <h3 class="slide-title">{{ slide.title }}</h3>
                  <div class="slide-actions">
                    <button type="button" class="edit-slide" @click="editSlide(slide)" :aria-label="'Edit slide ' + slide.title" title="Edit Slide"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button>
                    <button type="button" class="remove-slide-btn" @click.prevent="removeSlide(slide)" :aria-label="'Remove slide ' + slide.title + ' from selection'" title="Remove Slide">Remove</button>
                  </div>
                </div>
              </article>
            </li>
          </ul>
        </section>
  
        <div class="form-field">
          <button @click.prevent="showSlideListF()" class="btn-default" aria-label="Open slide selector">Select Slides</button>
        </div>
  
        <div class="form-actions">
          <button type="button" @click="addWidget()" class="btn-default" aria-label="Add Widget">Add Widget</button>
          <button type="button" @click="cancelAdd()" class="btn-default cancel-update-slide" aria-label="Cancel Adding Widget">Cancel</button>
        </div>
      </fieldset>
    </form>
  
    <div v-if="showSlideList" class="slide-box slide-popup">
     <ul class="slide-list">
        <li v-for="slide in slides.filter(s => !chosenSlides.some(c => c.id === s.id))" :key="slide.id" class="slide-item">
          <article class="slide-card">
                <img v-if="slide.image" class="slide-list-img" :src="'/' + slide.image.image_path" :alt="slide.image.image_alt || ('Image for ' + slide.title)" />
                <div class="slide-info">
                  <h3 class="slide-title">{{ slide.title }}</h3>
                  <div class="slide-actions">
                    <button type="button" class="edit-slide" @click="editSlide(slide)" :aria-label="'Edit slide ' + slide.title" title="Edit Slide"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button>
                    <button type="button" class="add-slide-btn" @click.prevent="addSlide(slide)" :aria-label="'Add slide ' + slide.title + ' from selection'" title="Add Slide">Add</button>
                  </div>
                </div>
            </article>
          </li>
          </ul>
    </div>
  </div>
  <div v-if="!newTab" class="tab-inners">
    <ul class="saved-item-list" >
          <li v-for="(item, index) in savedWidgets" :key="index" class="saved-item">
              <button type="button" class="add-saved-element" @click.prevent="savedWidget(item)" aria-label="Add saved widget to page">
                  <span class="visually-hidden">Widget -> {{ item.type }} - {{ item.name }}</span>
                  <span aria-hidden="true"><font-awesome-icon :icon="['fas', 'plus']" /></span>
            </button>
            <button @click.prevent="deleteSaved(item)" class="delete-btn"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
        </li>
    </ul>
  </div>

</div>

</template>

<script>
import axios from 'axios';

export default {
    props: {
        slides: Array,
        page: Object,
        savedWidgets: Array,
      },
      data() {
        return {
          newWidget: {},
          newTab: true,
          showSlideList: false,
          chosenSlides: [],
        }
    },
    emits: [
        'cancelAdd',    
        'addHeader',  
        'getImages',    
        'deleteSaved'   
    ],
    methods: {
        addWidget() {
            this.newWidget.slides = this.slides.filter(slide => slide.selected);
            this.$emit('addWidget', this.newWidget)
        },
        savedWidget(item) {
            this.newWidget = item;
            this.$emit('addWidget', this.newWidget)
        },
        deleteSaved(item) {
          if (confirm("Do you want to delete or unsave the widget?\n\nClick 'OK' to delete all, 'Cancel' to simply unsave.")) {
            axios.delete(`/widget/delete/${item.id}`)
                .then(() => {
                    let index = this.savedWidgets.findIndex(savedWidget => savedWidget.id === item.id);
                    if (index !== -1) {
                        this.savedWidgets.splice(index, 1); 
                    }
                })
                .catch(error => {
                    console.error('Failed:', error);
                });
          } else {
            axios.post(`/widget/unsave/${item.id}`)
                .then(() => {
                    item.is_saved = false;
                    let index = this.savedWidgets.findIndex(savedWidget => savedWidget.id === item.id);
                    if (index !== -1) {
                        this.savedWidgets.splice(index, 1); 
                    }
                })
                .catch(error => {
                    console.error('Failed:', error);
                });
            }
        },
        cancelAdd() {
            this.$emit('cancelAdd')
        },
        addSlides() {
            this.chosenSlides.forEach(el => {
              el.selected = true
            })
            this.newWidget.slides = this.slides.filter(slide => slide.selected);
            this.showSlideList = false;
        },
        cancelSlides() {
          this.showSlideList = false;          
          this.chosenSlides = [];
        },
        showSlideListF() {
          this.showSlideList = true;
          this.chosenSlides = this.slides.filter(slide => slide.selected);
        },
        addSlide(slide) {
          let index = this.chosenSlides.findIndex(s => s.id === slide.id);
          if (index === -1) {
            this.chosenSlides.push(slide); 
          } else {
            this.chosenSlides.splice(index, 1);
          }
        },
        removeSlide(slide) {
          slide.selected = false;
        },
    },
}
</script>

<style scoped>
.slide-list.selected-slides {
  border: 2px solid var(--black);
  border-radius: 6px;
  padding: 10px;
}

.slide-list {
  li.slide-item {
    display: flex;

    .slide-list-img {
      height: 40px;
      width: 40px;
      border-radius: 5px;
    }

    .slide-card {
      display: flex;
      gap: 10px;

      .slide-info {
        display: flex;
        align-items: center;
        gap: 10px;

        .slide-actions {
          display: flex;
          gap: 10px;
        }
      }
    }
  }
}

.modal-content {
  padding: 20px;
}

.tab-list {
  display: flex;
  padding-left: 20px;
  gap: 2px;

  button {
    border-bottom: 0px;
    border-bottom-right-radius: 0px;
    border-bottom-left-radius: 0px;
  }
}

.tab-inners {
  border: 2px solid var(--black);
  border-radius: 6px;
  padding: 20px;

  .form-inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 10px;
    padding: 20px 0px 0px 0px;

    .form-title {
      font-size: 22px;
      font-weight: 600;
    }

    .widget-type-select {
      select {
        padding: 4px 10px;
      }
    }

    .form-field {
      display: flex;
      gap: 10px;
      align-items: center;

      label {
        font-size: 18px;
      }
    }

    input {
      border: 1px solid var(--black);
      padding: 4px;
      border-radius: 2px;
    }

    .form-actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      width: 100%;
      margin-top: 20px;
    }
  }
}

.slide-popup {
  padding: 20px 0px;
}

.slides-buttons {
  display: flex;
  gap: 10px;
}

</style>