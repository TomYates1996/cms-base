<template>
    <div class="edit-widget">
        <h2>Edit Footer</h2>
        <button @click="imageList()" class="add-img">Logo</button>
        <div v-if="showImageGrid" class="image-grid">
            <img class="new-slide-img-option" @click="addImageToSlide(image, index)" v-for="(image, index) in images" :key="image.id" :src="'/' + image.image_path" alt="">
            <NewImage @refreshImages="getImages()"/>
        </div>
        <!-- Section Select -->
        <div class="form-slide-link form-field">
            <label for="footer-section">Section</label>
            <select id="footer-section" name="footer-section" v-model="footer.section" required aria-label="Select section for the footer" aria-required="true">
              <option value="primary">Primary</option>
              <option value="secondary">Secondary</option>
              <option value="footer">Footer</option>
            </select>
          </div>

          <ul v-if="footer.social_media">
            <li v-for="social in footer.social_media" :key="social.id">
              {{ social.label }}
              <font-awesome-icon :icon="['fab', social.icon]" />
              <button><font-awesome-icon :icon="['fas', 'trash']" @click="removeSocial(social)" /></button>
            </li>
          </ul>

          <button class="btn-default" @click.prevent="showAdd = !showAdd">Add Social Media</button>
          <AddList v-if="showAdd" :items="socialMedia" :type="'social'" @created="$emit('created', $event)" @selected="addSocial"/>

        <!-- Footer Widget -->
        <div class="footer-cta">
            <ul class="current-ctas">
                <li v-for="cta in footer.widgets" :key="cta.id">
                    <p>{{ cta.title }}</p>
                    <button class="btn-default" @click.prevent="toggleShowEditCTA(cta)">{{ showEditCTA ? 'Cancel CTA' : 'Edit CTA' }}</button>
                </li>
            </ul>
            <form v-if="showEditCTA" @submit.prevent="saveEditCTA()" class="edit-page-info form tab-inners" aria-labelledby="form-header">
                <fieldset class="form-inner">
                    <legend id="form-header" class="form-title">Edit CTA</legend>
                    <label for="title">Title: 
                        <input type="text" id="title" v-model="editCTA.title">
                    </label>
                    <label for="description">Description: 
                        <input type="text" id="description" v-model="editCTA.description">
                    </label>
                    <button type="submit" class="btn-default" aria-label="Add CTA">Save Changes</button>
                    <button type="button" @click="cancelAdd()" class="btn-default cancel-update-slide" aria-label="Cancel Editing CTA">Cancel</button>
                </fieldset>
            </form>
            <button class="btn-default" @click.prevent="showAddCTA = !showAddCTA">{{ showAddCTA ? 'Cancel CTA' : 'Add CTA' }}</button>
            <form v-if="showAddCTA" @submit.prevent="addCTA()" class="edit-page-info form tab-inners" aria-labelledby="form-header">
                <fieldset class="form-inner">
                    <legend id="form-header" class="form-title">Add a New CTA</legend>
                    <label for="title">Title: 
                        <input type="text" id="title" v-model="newCTA.title">
                    </label>
                    <label for="description">Description: 
                        <input type="text" id="description" v-model="newCTA.description">
                    </label>
                    <button type="submit" class="btn-default" aria-label="Add CTA">Save CTA</button>
                    <button type="button" @click="cancelAdd()" class="btn-default cancel-update-slide" aria-label="Cancel Adding CTA">Cancel</button>
                </fieldset>
            </form>
        </div>
  
        <button @click="saveEdit()">Save Changes</button>
        <button @click="cancelEdit()">Cancel</button>
    </div>
</template>
  
  <script>
import NewImage from '../cms/slides/images/NewImage.vue';
import AddList from '../cms/reusable/AddList.vue';
import axios from 'axios';

  export default {
    components: {
        NewImage,
        AddList,
    },
    props: {
        images: Array,
        footer: Object,
        socialMedia: Array,
        pages: Object,
    },
    data() {
        return {
            showImageGrid: false,
            showAdd: false,
            showAddCTA: false,
            showEditCTA: false,
            newCTA: {
                type : 'cta'
            },
            editCTA: {},
        }
    },
    methods: {
        // async addCTA() {
        //     const response = await axios.post('/api/store/cta', {
        //         footer_id: this.footer.id,
        //         title: this.newCTA.title,
        //         type: 'cta',
        //         description: this.newCTA.description,
        //     });
        //     this.footer.widgets.push(response.data.widget);
        //     this.showAddCTA = false;
        // },
        addCTA() {
            if (this.footer.widgets && this.footer.widgets.length > 0) {
                this.footer.widgets.push(this.newCTA);
            } else {
                this.footer.widgets = [this.newCTA];
            }
            // this.footer.widgets = 
            //     footer_id: this.footer.id,
            //     title: this.newCTA.title,
            //     type: 'cta',
            //     description: this.newCTA.description,
        },
        getImages() {
            this.$emit('getImages')
        },
        imageList() {
            this.getImages();
            this.showImageGrid = true;
        },
        addImageToSlide(image, index) {
            this.footer.logo = this.images[index];
            this.showImageGrid = false;
        },
        saveEdit() {
            this.$emit('saveEdit')
        },
        cancelEdit() {
            this.$emit('cancelEdit')
        },
        addSocial(item) {
            this.showAdd = false;
            if (this.footer.social_media) {
                this.footer.social_media.push(item);
            } else {
                this.footer.social_media = [item]
            }
        },
        removeSocial(social) {
            let index = this.footer.social_media.findIndex(item => item === social);
            if (index !== -1) {
                this.footer.social_media.splice(index, 1);
            }
        },
        toggleShowEditCTA(cta) {
            this.editCTA = cta;
            this.showEditCTA = true;
        },
        saveEditCTA() {
            const index = this.footer.widgets.findIndex(el => el.id === this.editCTA.id);
            if (index !== -1) {
                this.footer.widgets.splice(index, 1, { ...this.editCTA });
            }
        },
    },
    watch: {
        'footer.section'(newValue, oldValue) {
            this.footer.pages = this.pages[newValue];
        }
    },
}
</script>
  
  <style>
    
  </style>