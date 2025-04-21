<template>
    <div class="page">
        <div class="page-left">
            <OptionsBar/>
        </div>
        <div class="page-right">
            <div class="image-grid">
                <div class="image-item" v-for="image in images" :key="image.id">
                    <img class="" @click="editImage(image)" :src="'/' + image.image_path" :alt="image.image_alt">
                    <button class="edit-image" @click="editImage(image)" aria-label="Edit item"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button>
                </div>
                <NewImage @refreshImages="refreshImages()"/>
            </div>
        </div>
    </div>
</template>

<script>
import NewImage from '@/components/cms/slides/images/NewImage.vue';
import OptionsBar from '@/components/cms/structure/OptionsBar.vue';

export default {
    components: {
        OptionsBar,
        NewImage,
    },  
    props: {
        images: Array,
    },
    methods: {
        refreshImages() {
            this.$inertia.visit('/cms/images');
        },
        editImage(item) {
            this.$inertia.visit('/cms/images/edit', {
                data: { image_id: item.id }
            });
        },
    },
}
</script>

<style scoped>
.page {
    display: flex;
    .page-left {
        width: 25%;
    }
    .page-right {
        width: 75%;;
    }
}
    .image-grid {
        height: 100%;
        width: 100%;
        background-color: var(--white);
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        padding: 20px;
        overflow: scroll;
    }
    .image-item {
        position: relative;
        img {
            width: 100%;
            object-fit: cover;
            aspect-ratio: 1/1;
        }
        .edit-image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: var(--white);
            height: 30px;
            width: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            border: 1px solid var(--black);
            transition: 0.2s ease;
        }
        @media (hover:hover) {
            .edit-image:hover {
                background-color: var(--sea-green);
                color: var(--white);
                border-color: var(--white);
            }
        }
    }
</style>