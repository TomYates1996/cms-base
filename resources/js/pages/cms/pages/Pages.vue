<template>
    <ul class="page-list">
        <div class="page-item table-head">
            <p>Details</p>
            <p>Title</p>
            <p>Slug</p>
            <p>Show in Nav</p>
            <p>Created By</p>
        </div>
        <li class="page-item" v-for="page in pages" :key="page.id">
            <div class="options-section">
                <Link v-if="$page.props.auth.user" :href="`/cms/pages/edit/${page.slug}`" method="get" class="option">
                    <font-awesome-icon :icon="['fas', 'keyboard']" />
                </Link>
                <Link v-if="$page.props.auth.user" :href="`/cms/pages/edit-content/${page.slug}`" method="get" class="option">
                    <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                </Link>
                <Link v-if="$page.props.auth.user" :href="`/cms/pages/children/${page.slug}`" method="get" class="option">
                    <font-awesome-icon :icon="['fas', 'children']" />
                </Link>
                <button v-if="$page.props.auth.user" @click="deletePage(page.id)" class="option">
                    <font-awesome-icon :icon="['fas', 'trash-can']" />
                </button>
            </div>
            <a :href="'/' + page.slug" class="page-title">{{ page.title }}</a>
            <a :href="page.slug" class="page-slug">{{ page.slug }}</a>
            <p class="page-slug">{{ page.show_in_nav ? 'Show' : 'Hide' }}</p>
            <p>{{ page.created_by }}</p>
        </li>
    </ul>
    <NewPage :parent="parent"/>
    <Link 
        v-if="$page.props.auth.user && parent"
        href="/cms/pages/"
        method="get"
        class="option"
    >
        Return to Parent
    </Link>
</template>
  
<script>  
import { Head, Link } from '@inertiajs/vue3';
import EditPage from '@/components/cms/pages/EditPage.vue';
import NewPage from '@/components/cms/pages/NewPage.vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3'
  
export default {
    components: {
        Link,
        EditPage,
        NewPage,
    },
    props: {
        pages: Array,
        parent: Object,
    },
    // data() {
    //     return {
    //         pages: []
    //     }
    // },
    // mounted() {
    //     this.fetchPages();
    // },
    methods: {
        deletePage(page_id) {
            if (confirm("Are you sure you want to delete this page?")) {
                router.delete(`/cms/pages/delete/${page_id}`, {
                onSuccess: () => {
                    // this.$inertia.get('/cms/pages'); 
                },
                onError: (errors) => {
                    console.log('Error deleting page:', errors);
                }
                });
            }
        }
    }
}
</script>  
  
<style scoped>
.page-list {
    .page-item {
        display: grid;
        grid-template-columns: repeat( 5, 1fr);
        padding: 8px 20px;
        .options-section {
            display: flex;
            gap: 20px;
        }
    }
    .table-head {
        border-bottom: 1px solid var(--black);
    }
}
</style>