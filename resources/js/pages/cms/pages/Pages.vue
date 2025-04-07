<template>
    <ul class="page-list">
        <div class="page-item table-head">
            <p>Edit Details</p>
            <p>Edit Content</p>
            <p>Title</p>
            <p>Slug</p>
            <p>Show in Nav</p>
            <p>Created By</p>
        </div>
        <li class="page-item" v-for="page in pages" :key="page.id">
            <Link v-if="$page.props.auth.user" :href="`/cms/pages/edit/${page.id}`" method="get" class="option">
                <font-awesome-icon :icon="['fas', 'keyboard']" />
            </Link>
            <Link v-if="$page.props.auth.user" :href="`/cms/pages/edit-content/${page.id}`" method="get" class="option">
                <font-awesome-icon :icon="['fas', 'pen-to-square']" />
            </Link>
            <a :href="page.slug" class="page-title">{{ page.title }}</a>
            <a :href="page.slug" class="page-slug">{{ page.slug }}</a>
            <p class="page-slug">{{ page.show_in_nav ? 'Show' : 'Hide' }}</p>
            <p>{{ page.created_by }}</p>
        </li>
    </ul>
    <NewPage/>
</template>
  
<script>  
import { Head, Link } from '@inertiajs/vue3';
import EditPage from '@/components/cms/pages/EditPage.vue';
import NewPage from '@/components/cms/pages/NewPage.vue';
import axios from 'axios';
  
export default {
    components: {
        Link,
        EditPage,
        NewPage,
    },
    props: {
        pages: Array,
    },
    // data() {
    //     return {
    //         pages: []
    //     }
    // },
    // mounted() {
    //     this.fetchPages();
    // },
    // methods: {
    //     // Get the list of pages
    //     fetchPages() {
    //         axios.get('/cms/pages')
    //         .then((response) => {
    //             this.pages = response.data.pages; 
    //         })
    //         .catch((error) => {
    //             console.error('Error fetching pages:', error);
    //         });
    //     }
    // }
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