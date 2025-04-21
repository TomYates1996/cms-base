<template>
    <div class="page-wrap">
        <div class="page-left">
            <table v-if="!showModal.edit.details && !showModal.new" class="page-list" role="table" aria-label="Page List">
                <thead>
                    <tr class="table-head">
                        <th scope="col">Details</th>
                        <th scope="col">Title</th>
                        <th scope="col">Slug</th>
                        <th scope="col">Show in Nav</th>
                        <th scope="col">Created By</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="page in pages" :key="page.id" class="page-item">
                        <td class="options-section" aria-label="Actions for page">
                            <button v-if="$page.props.auth.user" class="option" @click="editDetails(page)" aria-label="Edit details for {{ page.title }} page"><font-awesome-icon :icon="['fas', 'keyboard']" /></button>
                            <Link v-if="$page.props.auth.user" :href="`/cms/pages/edit-content/${page.slug}`" method="get" class="option" role="button" aria-label="Edit content for {{ page.title }}"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></Link>
                            <Link v-if="$page.props.auth.user" :href="`/cms/pages/children/${page.slug}`" method="get" class="option" role="button" aria-label="View children for {{ page.title }}"><font-awesome-icon :icon="['fas', 'children']" /></Link>
                            <button v-if="$page.props.auth.user" class="option" @click="deletePage(page.id)" aria-label="Delete {{ page.title }} page"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
                        </td>
                        <td><a :href="'/' + page.slug" class="page-title" aria-label="Visit public page: {{ page.title }}">{{ page.title }}</a></td>
                        <td><a :href="page.slug" class="page-slug" aria-label="Visit {{ page.title }}">{{ page.slug }}</a></td>
                        <td class="page-slug" aria-label="Show in nav">{{ page.show_in_nav ? 'Yes' : 'No' }}</td>
                        <td aria-label="Created by">{{ page.created_by }}</td>
                    </tr>
                </tbody>
            </table>

            <EditPage @cancelNew="toggleEdit" v-if="showModal.edit.details" :page="currentPage"/>
            <NewPage v-if="showModal.new" :parent="parent" :section="section" @cancelNew="toggleNew"/>
        </div>
        <div class="page-right">
            <button @click="toggleNew()" :aria-expanded="showModal.new.toString()" :aria-controls="'newPageForm'" class="btn-default">
                {{ showModal.new ? 'Cancel' : 'New Page' }}
            </button>
            <Link v-if="$page.props.auth.user && parent" :href="'/cms/pages/' + parent.section" method="get" role="button" class="btn-default" aria-label="Return to parent page">
                Parent
            </Link>
        </div>
    </div>
</template>
  
<script>  
import { Head, Link } from '@inertiajs/vue3';
import EditPage from '@/components/cms/pages/EditPage.vue';
import NewPage from '@/components/cms/pages/NewPage.vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3'
import CMSLayout from '@/layouts/CMSLayout.vue';

  
export default {
    layout: CMSLayout,
    components: {
        Link,
        EditPage,
        NewPage,
    },
    props: {
        pages: Array,
        parent: Object,
        section: String,
    },
    data() {
        return {
            showModal: {
                edit: {},
                new: false,
            },
            showForm : false,
            currentPage: {},       
        }
    },
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
        },
        editDetails(page) {
            this.currentPage = page;
            this.showModal.edit.details = true;
            this.showModal.new = false;
        },
        toggleNew() {
            this.showModal.new = !this.showModal.new;
            this.showModal.edit.details = false;
        },
        toggleEdit() {
            this.showModal.edit.details = false;
        },
    }
}
</script>  
  
<style scoped>
.page-list {
    width: 100%;
}
.table-head {
    border-bottom: 1px solid var(--black);
}
.page-wrap {
    display: flex;
    gap: 20px;
    .page-left {
        width: 80%;
    }
    .page-right {
        width: 20%;
        border-left: 2px solid var(--black);
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
}
</style>