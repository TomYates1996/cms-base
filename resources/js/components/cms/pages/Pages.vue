<template>
  <ul class="page-list">
    <div class="page-item table-head">
        <p>Edit Page</p>
        <p>Title</p>
        <p>Slug</p>
        <p>Show in Nav</p>
        <p>Created By</p>
    </div>
    <li class="page-item" v-for="page in pages" :key="page.id">
        <EditPage :page="page"/>
        <a :href="page.slug" class="page-title">{{ page.title }}</a>
        <a :href="page.slug" class="page-slug">{{ page.slug }}</a>
        <p class="page-slug">{{ page.show_in_nav ? 'Show' : 'Hide' }}</p>
        <p>{{ page.created_by }}</p>
    </li>
  </ul>
  <NewPage/>
</template>

<script>
import axios from 'axios';
import EditPage from './EditPage.vue';
import NewPage from './NewPage.vue';

export default {
    components: {
        EditPage,
        NewPage,
    },
    data() {
        return {
            pages: []
        }
    },
    mounted() {
        this.fetchPages();
    },
    methods: {
        // Get the list of pages
        fetchPages() {
            axios.get('/api/pages')
            .then((response) => {
                this.pages = response.data.pages; 
            })
            .catch((error) => {
                console.error('Error fetching pages:', error);
            });
         }
    }
}
</script>

<style scoped>
.page-list {
    .page-item {
        display: flex;
        justify-content: space-between;
    }
}
</style>