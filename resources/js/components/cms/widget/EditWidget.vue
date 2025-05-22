<template>
 <div class="modal-content">
  <div class="tab-inners">
    <div v-if="showSlideList" class="slides-buttons" role="tablist" aria-label="Tabs for toggling between saved and new widgets">
      <button class="btn-default" @click.prevent="addSlides()" role="button">Confirm add</button>
      <button class="btn-default" @click.prevent="cancelSlides()" role="button">Cancel</button>
    </div>
    <form v-if="!showSlideList" class="form form-new-widget" aria-labelledby="select-widget-title">
      <fieldset class="form-inner">
        <legend id="select-widget-title" class="form-title">Edit Widget</legend>
  
        <div class="widget-type-select form-field">
          <label for="widget-type">Select Widget Type</label>
          <select id="widget-type" v-model="widget.type" aria-required="true" required>
            <option v-for="widget in widgetOptions.filter(w => w.showWidget)" :key="widget.id" :value="{ name: widget.name, variant: widget.variant }">{{ widget.label }}</option>
          </select>
        </div>
        <div v-if="widget.type.variant && widgetOptions.find(option => option.variant === widget.type.variant)?.hasHeader" class="widget-title form-field">
          <label for="widget-title">Widget Title</label>
          <input id="widget-title" name="widget-title" type="text" v-model="widget.title" aria-required="true" required />
        </div>
        <div v-if="widget.type.variant && widgetOptions.find(option => option.variant === widget.type.variant)?.hasHeader" class="widget-subtitle form-field">
          <label for="widget-subtitle">Subtitle</label>
          <input id="widget-subtitle" name="widget-subtitle" type="text" v-model="widget.subtitle" aria-required="false" />
        </div>
        <div v-if="widget.type.variant && widgetOptions.find(option => option.variant === widget.type.variant)?.hasHeader" class="widget-description form-field">
          <label for="widget-description">Description</label>
          <input id="widget-description" name="widget-description" type="text" v-model="widget.description" aria-required="false" />
        </div>

        <!-- <div class="widget-description form-field">
          <label for="widget-link">Link</label>
          <input id="widget-link" name="widget-link" type="text" v-model="widget.link" aria-required="false" />
        </div> -->
        <select v-if="widget.type && widgetOptions.find(option => option.variant === widget.type.variant)?.hasSettings" v-model="widget.link">
          <optgroup v-for="(items, category) in pages" :key="category" :label="category">
            <option v-for="page in items" :key="page.id" :value="page.slug">
              {{ page.title }}
            </option>
          </optgroup>
        </select>
        <div v-if="widget.link && widget.type && widgetOptions.find(option => option.variant === widget.type.variant)?.hasSettings" class="widget-link-text form-field">
          <label for="widget-link-text">Link Text</label>
          <input id="widget-link-text" name="widget-link-text" type="text" v-model="widget.link_text" aria-required="false" />
        </div>

        <div v-if="widget.type && widgetOptions.find(option => option.variant === widget.type.variant)?.hasSettings" class="widget-slide-link-text form-field">
          <label for="widget-slide-link-text">Slide Link Text</label>
          <input id="widget-slide-link-text" name="widget-slide-link-text" type="text" v-model="widget.slide_link_text" aria-required="false" />
        </div>
        
         <div v-if="widget.type">
          <label><strong>Feed Type:</strong></label>
          <div style="margin-top: 8px;">
            <label style="margin-right: 15px;">
              <input type="radio" value="slides" v-model="widget.feed_type" />
              Slides
            </label>
            <label style="margin-right: 15px;">
              <input type="radio" value="blog" v-model="widget.feed_type" />
              Blog
            </label>
            <label style="margin-right: 15px;">
              <input type="radio" value="listings" v-model="widget.feed_type" />
              Listings
            </label>
            <label>
              <input type="radio" value="events" v-model="widget.feed_type" />
              Events
            </label>
          </div>
        </div>
  
        <section v-if="widget.feed_type === 'slides' && widget.type && widgetOptions.find(option => option.variant === widget.type.variant)?.hasSettings" class="form-field" aria-labelledby="selected-slides-heading">
          <h2 id="selected-slides-heading" class="form-subtitle">Selected Slides</h2>
          <ul class="slide-list selected-slides">
            <li v-if="initialSlides.length < 1">No slides</li>
            <li v-for="slide in initialSlides" :key="slide.id" class="slide-item" :aria-label="'Slide: ' + slide.title">
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
        
        <QuillEditor v-if="widget.type && widget.type.name === 'text'" v-model="widget.content" />
  
        <div class="form-field" v-if="widget.type && widgetOptions.find(option => option.variant === widget.type.variant)?.hasSettings">
          <button @click.prevent="showSlideListF()" class="btn-default" aria-label="Open slide selector">Select Slides</button>
        </div>

        <div v-if="widget.feed_type !== 'slides' && widget.type && widgetOptions.find(option => option.variant === widget.type.variant)?.hasHeader" class="widget-slide-count form-field">
          <label for="widget-slide-count">Amount to show</label>
          <input
            id="widget-slide-count"
            name="widget-slide-count"
            type="number"
            v-model="widget.to_show"
            aria-required="false"
        />
        </div>
  
        <div class="form-actions">
          <button type="button" @click="saveEdit()" class="btn-default" aria-label="Add Widget">Save Widget</button>
          <button type="button" @click="cancelEdit()" class="btn-default cancel-update-slide" aria-label="Cancel Adding Widget">Cancel</button>
        </div>
      </fieldset>
    </form>
  
    <div v-if="showSlideList" class="slide-box slide-popup">
     <ul class="slide-list">
        <li v-for="slide in filteredSlides" :key="slide.id" class="slide-item">
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
</div>
</template>
  

<script>
import { widgetOptions } from '@/utils/widgetOptions.js';
import QuillEditor from '../reusable/QuillEditor.vue';

export default {
    components: {
      QuillEditor,
    },
    props: {
        widget: Object,
        slides: Array,
        pages: Object,
    },
    data() {
        return {
            showSlideList: false,
            initialSlides: [],
            chosenSlides: [],
            widgetOptions,
        }
    },
    created() {
        this.initialSlides = this.widget.slides;
        this.widget.type = { name: this.widget.type, variant: this.widget.variant }
    },
    computed: {
        filteredSlides() {
            return this.slides.filter(slide => {
            return !this.initialSlides.some(initialSlide => initialSlide.id === slide.id) && 
                    !this.chosenSlides.some(chosenSlide => chosenSlide.id === slide.id);
            });
        }
    },
    methods: {
        saveEdit() {
            this.widget.variant = this.widget.type.variant;
            this.widget.type = this.widget.type.name;
            this.$emit('saveEdit', 'widgets', this.initialSlides)
        },
        cancelEdit() {
            this.$emit('cancelEdit')
        },
        addSlides() {
            this.chosenSlides.forEach(el => {
              el.selected = true
            })
            this.initialSlides = this.initialSlides.concat(this.chosenSlides);
            this.showSlideList = false;
            this.chosenSlides = [];
            
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
          this.chosenSlides.push(slide);
        },
        removeSlide(slide) {
          let index = this.initialSlides.findIndex(s => s.id === slide.id);
          slide.selected = false;
          this.initialSlides.splice(index, 1);
          console.log(this.initialSlides);
          
        },
    },
}
</script>

<style>
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