<template>
   <div class="content">
        <div class="slide-link">
            <label for="link">Link</label>
            <input id="link" required type="text" v-model="newHeader.link" >
        </div>
        <button @click="imageList()" class="add-img">Logo</button>
        <div v-if="showImageGrid" class="image-grid">
            <img class="new-slide-img-option" @click="addImageToSlide(image, index)" v-for="(image,index) in images" :key="image.id" :src="'/' + image.image_path" alt="">
            <NewImage @refreshImages="getImages()"/>
        </div>
        <select name="section" id="" v-model="newHeader.section" required>
            <option value="primary">Primary</option>
            <option value="secondary">Secondary</option>
            <option value="footer">Footer</option>
        </select>
    <button @click="addHeader()">Add Header</button>
        <button @click="cancelAdd()">Cancel</button>
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
    },
    data() {
        return {
            newHeader: {},
            showImageGrid: false,
        }
    },
    methods: {
        addHeader() {
            this.$emit('addHeader', this.newHeader)
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
</style>