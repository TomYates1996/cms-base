<template>
    <div class="section-new-header">
        <div class="tab-links tab-list btn-row">
          <button :class="newTab ? 'active btn-alt no-hover' : 'inactive btn-default'" @click.prevent="newTab = true">Add New</button>
          <button :class="newTab ? 'inactive btn-default' : 'active btn-alt no-hover'" @click.prevent="newTab = false">Saved Options</button>
        </div>
        <ul class="saved-item-list tab-inners" v-if="!newTab">
            <li v-for="(item, index) in savedHeaders" :key="index" class="saved-item">
                <button type="button" class="add-saved-element" @click="savedHeader(item)" aria-label="Add saved header to page">
                    <span class="visually-hidden">Header -> {{ item.type }} - {{ item.name }}</span>
                    <span aria-hidden="true"><font-awesome-icon :icon="['fas', 'plus']" /></span>
                </button>
                <button @click.prevent="deleteSaved('headers', index)" class="delete-btn"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
            </li>
        </ul>
        <form v-if="newTab" @submit.prevent="addHeader" class="edit-page-info form tab-inners" aria-labelledby="form-header">
            <fieldset class="form-inner">
                <legend id="form-header" class="form-title">Add a New Header</legend>
    
                <div class="form-slide-link form-field">
                    <label for="section">Link</label>
                    <select id="header-logo-link" name="header-logo-link" v-model="newHeader.link" aria-label="Select link for the header logo" aria-required="false">
                        <optgroup
                        v-for="(sectionPages, sectionName) in pages" :label="sectionName" :key="sectionName"
                        >
                        <option
                            v-for="page in sectionPages"
                            :key="page.id"
                            :value="page.slug"
                        >
                            {{ page.slug }}
                        </option>
                        </optgroup>
                    </select>
                    </div>
    
                <div class="form-slide-image form-field">
                <button type="button" @click="imageList()" class="btn-default add-img" aria-label="Select a logo image">Logo</button>
                <div v-if="showImageGrid" class="image-grid" role="grid" aria-labelledby="image-grid-label">
                    <span id="image-grid-label" class="sr-only">Select an image</span>
                    <img class="new-slide-img-option" @click="addImageToSlide(image, index)" v-for="(image, index) in images" :key="image.id" :src="'/' + image.image_path" :alt="'Select ' + image.name" role="gridcell" />
                    <NewImage @refreshImages="getImages" />
                </div>
                </div>
    
                <div class="form-slide-link form-field">
                <label for="section">Section</label>
                <select id="section" name="section" v-model="newHeader.section" required aria-label="Select section for the header" aria-required="true">
                    <option value="primary">Primary</option>
                    <option value="secondary">Secondary</option>
                    <option value="footer">Footer</option>
                </select>
                </div>
    
                <div class="form-actions btn-row">
                <button type="submit" class="btn-default" aria-label="Add Header">Add Header</button>
                <button type="button" @click="cancelAdd()" class="btn-default cancel-update-slide" aria-label="Cancel Adding Header">Cancel</button>
                </div>
            </fieldset>
        </form>
    </div>
</template>

<script>
import NewImage from '../cms/slides/images/NewImage.vue';


export default {
    components: {
        NewImage
    },
    props: {
        images: Array,
        savedHeaders: Array,
        pages: Object,
    },
    data() {
        return {
            newHeader: {},
            showImageGrid: false,
            newTab: true,
        }
    },
    emits: [
        'cancelAdd',    
        'addHeader',  
        'getImages',    
        'deleteSaved'   
    ],
    methods: {
        addHeader() {
            this.$emit('addHeader', this.newHeader)
        },
        savedHeader(item) {
            this.newHeader = item;
            this.addHeader();
        },
        deleteSaved(type, index) {
            this.$emit('deleteSaved', type, index )
        },
        cancelAdd() {
            this.$emit('cancelAdd')
        },
        getImages() {
            this.$emit('getImages')
        },
        imageList() {
            this.getImages();
            this.showImageGrid = true;
        },
        addImageToSlide(image, index) {
            this.newHeader.logo = this.images[index];
            this.showImageGrid = false;
        },
    },
}
</script>

<style>
    .image-grid {
        position: fixed;
        top: 0px;
        left: 0px;
        height: 100%;
        width: 100%;
        background-color: var(--white);
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        padding: 20px;
        overflow: scroll;
    }
    .new-slide-img-option {
        width: 100%;
        object-fit: cover;
        aspect-ratio: 1/1;
    }

    .btn-row {
        display: flex;
        gap: 10px;
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
.section-new-header {
    padding: 20px
}
.tab-inners{
  border: 2px solid var(--black);
  border-radius: 6px;
  padding: 20px;
    .form-inner {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
        gap: 10px;
        padding: 20px 0px;
        .form-title {
            font-size: 22px;
            font-weight: 600;
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
    }
}
</style>